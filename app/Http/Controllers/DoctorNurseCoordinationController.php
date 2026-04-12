<?php

namespace App\Http\Controllers;

use App\Events\DoctorNurseCoordinationMessageSent;
use App\Models\DoctorNurseCoordinationChat;
use App\Models\DoctorNurseCoordinationMessage;
use App\Models\User;
use App\Services\ConversationAccess;
use App\Services\DoctorNurseCoordinationAccess;
use App\Services\HospitalNetworkService;
use App\Support\SafeBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorNurseCoordinationController extends Controller
{
    public function doctorIndex(Request $request): View|RedirectResponse
    {
        /** @var User $doctor */
        $doctor = $request->user();
        if (ConversationAccess::isStaffNurse($doctor)) {
            return redirect()->route('nurse.doctor-coordination', $request->query());
        }

        $chats = DoctorNurseCoordinationChat::query()
            ->where('doctor_id', $doctor->id)
            ->with(['nurse:id,name', 'patient:id,name'])
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        $activeId = (int) $request->query('cc', 0);
        $active = null;
        if ($activeId > 0) {
            $candidate = DoctorNurseCoordinationChat::query()
                ->whereKey($activeId)
                ->where('doctor_id', $doctor->id)
                ->with(['messages' => fn ($q) => $q->orderBy('id')->with('user:id,name'), 'nurse:id,name', 'patient:id,name', 'hospital:id,name'])
                ->first();
            abort_if(! $candidate, 404);
            $active = $candidate;
        }

        $nurses = HospitalNetworkService::assignableNursesQueryForDoctor($doctor)->get(['id', 'name']);
        $patients = HospitalNetworkService::assignablePatientsQueryForDoctor($doctor)->get(['id', 'name']);

        return view('role.doctor.nurse-coordination', [
            'chats' => $chats,
            'active' => $active,
            'activeId' => $activeId,
            'nurses' => $nurses,
            'patients' => $patients,
        ]);
    }

    public function doctorStart(Request $request): RedirectResponse
    {
        /** @var User $doctor */
        $doctor = $request->user();
        abort_if(ConversationAccess::isStaffNurse($doctor), 403);

        $data = $request->validate([
            'nurse_id' => ['required', 'integer', 'exists:users,id'],
            'patient_id' => ['nullable', 'integer', 'exists:users,id'],
            'patient_context' => ['nullable', 'string', 'max:255'],
        ]);

        $nurse = User::query()->findOrFail((int) $data['nurse_id']);
        abort_unless(
            HospitalNetworkService::assignableNursesQueryForDoctor($doctor)->whereKey($nurse->id)->exists(),
            403
        );
        abort_unless(ConversationAccess::isStaffNurse($nurse), 403);

        $patient = null;
        if (! empty($data['patient_id'])) {
            $patient = User::query()->findOrFail((int) $data['patient_id']);
            abort_unless((string) ($patient->role ?? '') === 'PATIENT', 422);
            abort_unless(
                HospitalNetworkService::assignablePatientsQueryForDoctor($doctor)->whereKey($patient->id)->exists(),
                403
            );
        }

        $incomingContext = trim((string) ($data['patient_context'] ?? ''));
        if ($patient !== null) {
            $label = trim((string) ($patient->name ?? ''));
            if ($incomingContext !== '') {
                $label = $incomingContext;
            }
        } else {
            $label = $incomingContext;
        }

        if ($label === '') {
            throw ValidationException::withMessages([
                'patient_context' => [__('roleui.coordination_patient_label_required')],
            ]);
        }

        $usersForHospital = [$doctor, $nurse];
        if ($patient !== null) {
            $usersForHospital[] = $patient;
        }
        $hospitalId = HospitalNetworkService::firstSharedActiveHospitalIdAmongUsers($usersForHospital);
        abort_if($hospitalId === null, 403);

        $chat = DoctorNurseCoordinationChat::query()->create([
            'doctor_id' => $doctor->id,
            'nurse_id' => $nurse->id,
            'patient_id' => $patient?->id,
            'patient_context' => $label,
            'hospital_id' => $hospitalId,
        ]);

        return redirect()->route('doctor.nurse-coordination', ['cc' => $chat->id]);
    }

    public function nurseIndex(Request $request): View|RedirectResponse
    {
        /** @var User $nurse */
        $nurse = $request->user();
        if (! ConversationAccess::isStaffNurse($nurse)) {
            return redirect()->route('doctor.nurse-coordination', $request->query());
        }

        $chats = DoctorNurseCoordinationChat::query()
            ->where('nurse_id', $nurse->id)
            ->with(['doctor:id,name', 'patient:id,name'])
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        $activeId = (int) $request->query('cc', 0);
        $active = null;
        if ($activeId > 0) {
            $candidate = DoctorNurseCoordinationChat::query()
                ->whereKey($activeId)
                ->where('nurse_id', $nurse->id)
                ->with(['messages' => fn ($q) => $q->orderBy('id')->with('user:id,name'), 'doctor:id,name', 'patient:id,name', 'hospital:id,name'])
                ->first();
            abort_if(! $candidate, 404);
            $active = $candidate;
        }

        return view('role.nurse.doctor-coordination', [
            'chats' => $chats,
            'active' => $active,
            'activeId' => $activeId,
        ]);
    }

    public function storeMessage(Request $request, DoctorNurseCoordinationChat $chat): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless((string) ($user->role ?? '') === 'MEDICAL_TEAM', 403);
        abort_unless(DoctorNurseCoordinationAccess::canParticipate($user, $chat), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment_kind' => ['nullable', 'string', 'in:voice,document'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ]);

        $body = trim((string) ($data['body'] ?? ''));
        $file = $request->file('attachment');

        if ($file === null && $body === '') {
            throw ValidationException::withMessages([
                'body' => [__('roleui.conversations_body_or_file_required')],
            ]);
        }

        if ($file !== null) {
            $kind = (string) ($data['attachment_kind'] ?? '');
            if (! in_array($kind, ['voice', 'document'], true)) {
                throw ValidationException::withMessages([
                    'attachment_kind' => [__('roleui.conversations_kind_required')],
                ]);
            }
            $this->assertAttachmentMimeMatchesKind($file, $kind);
        }

        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentMime = null;
        $attachmentKind = null;
        $attachmentSize = null;

        if ($file !== null) {
            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentMime = (string) ($file->getMimeType() ?: $file->getClientMimeType());
            $attachmentKind = (string) $data['attachment_kind'];
            $attachmentSize = (int) $file->getSize();
            $attachmentPath = $file->store('coordination-messages/'.$chat->id, 'local');
        }

        $message = DoctorNurseCoordinationMessage::query()->create([
            'coordination_chat_id' => $chat->id,
            'user_id' => $user->id,
            'body' => $body === '' ? '' : $body,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_mime' => $attachmentMime,
            'attachment_kind' => $attachmentKind,
            'attachment_size' => $attachmentSize,
        ]);
        $chat->touch();

        SafeBroadcast::dispatch(new DoctorNurseCoordinationMessageSent($message));

        if ($request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $this->messagePayloadForClient($message, $user, $chat),
            ]);
        }

        return back();
    }

    public function messageAttachment(Request $request, DoctorNurseCoordinationChat $chat, DoctorNurseCoordinationMessage $message)
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless((string) ($user->role ?? '') === 'MEDICAL_TEAM', 403);
        abort_unless(DoctorNurseCoordinationAccess::canParticipate($user, $chat), 403);
        abort_if((int) $message->coordination_chat_id !== (int) $chat->id, 404);
        abort_if(! $message->hasAttachment(), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists((string) $message->attachment_path), 404);

        $name = $message->attachment_original_name ?? 'attachment';
        $mime = $message->attachment_mime ?? 'application/octet-stream';
        $disposition = $message->attachment_kind === 'voice' ? 'inline' : 'attachment';

        return $disk->response((string) $message->attachment_path, $name, [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition.'; filename="'.$this->asciiFilename((string) $name).'"',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function messagePayloadForClient(DoctorNurseCoordinationMessage $message, User $user, DoctorNurseCoordinationChat $chat): array
    {
        $message->loadMissing('user:id,name');

        return [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'user_name' => (string) ($message->user?->name ?? $user->name),
            'body' => (string) ($message->body ?? ''),
            'created_at' => $message->created_at?->toIso8601String(),
            'has_attachment' => $message->hasAttachment(),
            'attachment_kind' => $message->attachment_kind,
            'attachment_name' => $message->attachment_original_name,
            'attachment_url' => $message->hasAttachment()
                ? route('portal.coordination.messages.attachment', [
                    'chat' => $chat->id,
                    'message' => $message->id,
                ])
                : null,
        ];
    }

    private function assertAttachmentMimeMatchesKind(UploadedFile $file, string $kind): void
    {
        $mime = strtolower((string) ($file->getMimeType() ?: $file->getClientMimeType()));

        if ($kind === 'voice') {
            $ok = str_starts_with($mime, 'audio/')
                || $mime === 'video/webm'
                || str_contains($mime, 'webm');

            if (! $ok) {
                throw ValidationException::withMessages([
                    'attachment' => [__('roleui.conversations_voice_invalid')],
                ]);
            }

            return;
        }

        $allowed = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
        ];

        if (! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages([
                'attachment' => [__('roleui.conversations_document_invalid')],
            ]);
        }
    }

    private function asciiFilename(string $name): string
    {
        $ascii = preg_replace('/[^\x20-\x7E]+/', '_', $name) ?? $name;

        return $ascii !== '' ? $ascii : 'file';
    }
}
