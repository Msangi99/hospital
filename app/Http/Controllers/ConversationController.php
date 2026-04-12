<?php

namespace App\Http\Controllers;

use App\Events\ConversationMessageSent;
use App\Models\PatientDoctorConversation;
use App\Models\PatientDoctorConversationMessage;
use App\Models\User;
use App\Services\ConversationAccess;
use App\Services\HospitalNetworkService;
use App\Support\SafeBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * @return array<int, int>
     */
    private function patientHospitalIds(User $patient): array
    {
        return HospitalNetworkService::activeHospitalIdsForUser($patient);
    }

    public function patientIndex(Request $request): View
    {
        /** @var User $patient */
        $patient = $request->user();
        $hospitalIds = $this->patientHospitalIds($patient);

        $doctors = collect();
        if ($hospitalIds !== []) {
            $doctors = User::query()
                ->where('role', 'MEDICAL_TEAM')
                ->where('status', 'ACTIVE')
                ->whereHas('hospitalMemberships', function ($q) use ($hospitalIds): void {
                    $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE');
                })
                ->with(['hospitalMemberships' => function ($q) use ($hospitalIds): void {
                    $q->whereIn('hospital_id', $hospitalIds)->where('status', 'ACTIVE')->with('hospital:id,name');
                }])
                ->orderBy('name')
                ->get(['id', 'name']);

            $doctors = $doctors->map(function (User $doctor) use ($hospitalIds) {
                $shared = $doctor->hospitalMemberships->first(fn ($m) => in_array((int) $m->hospital_id, $hospitalIds, true));
                $doctor->setAttribute('hospital_display_name', $shared?->hospital?->name ?? '—');

                return $doctor;
            });
        }

        $conversations = PatientDoctorConversation::query()
            ->where('patient_id', $patient->id)
            ->with(['doctor:id,name', 'hospital:id,name'])
            ->orderByDesc('updated_at')
            ->limit(80)
            ->get();

        $activeId = (int) $request->query('c', 0);
        $active = $activeId > 0
            ? PatientDoctorConversation::query()
                ->where('patient_id', $patient->id)
                ->whereKey($activeId)
                ->with(['messages' => fn ($q) => $q->orderBy('id')->with('user:id,name'), 'doctor:id,name', 'hospital:id,name'])
                ->first()
            : null;

        return view('role.patient.conversations', [
            'doctors' => $doctors,
            'conversations' => $conversations,
            'active' => $active,
            'activeId' => $activeId,
        ]);
    }

    public function doctorIndex(Request $request): View|RedirectResponse
    {
        /** @var User $doctor */
        $doctor = $request->user();

        if (ConversationAccess::isStaffNurse($doctor)) {
            return redirect()->route('nurse.patient-chats', $request->query());
        }

        $conversations = PatientDoctorConversation::query()
            ->where('doctor_id', $doctor->id)
            ->with(['patient:id,name', 'hospital:id,name'])
            ->orderByDesc('updated_at')
            ->limit(80)
            ->get();

        $activeId = (int) $request->query('c', 0);
        $active = $activeId > 0
            ? PatientDoctorConversation::query()
                ->where('doctor_id', $doctor->id)
                ->whereKey($activeId)
                ->with(['messages' => fn ($q) => $q->orderBy('id')->with('user:id,name'), 'patient:id,name', 'hospital:id,name'])
                ->first()
            : null;

        $startablePatients = HospitalNetworkService::assignablePatientsQueryForDoctor($doctor)
            ->limit(500)
            ->get(['id', 'name']);

        return view('role.doctor.conversations', [
            'conversations' => $conversations,
            'active' => $active,
            'activeId' => $activeId,
            'startablePatients' => $startablePatients,
        ]);
    }

    public function nurseIndex(Request $request): View
    {
        /** @var User $nurse */
        $nurse = $request->user();
        abort_unless(ConversationAccess::isStaffNurse($nurse), 403);

        $hospitalIds = HospitalNetworkService::activeHospitalIdsForUser($nurse);
        abort_if($hospitalIds === [], 403);

        $conversations = PatientDoctorConversation::query()
            ->whereIn('hospital_id', $hospitalIds)
            ->with(['patient:id,name', 'doctor:id,name', 'hospital:id,name'])
            ->orderByDesc('updated_at')
            ->limit(150)
            ->get();

        $activeId = (int) $request->query('c', 0);
        $active = null;
        if ($activeId > 0) {
            $candidate = PatientDoctorConversation::query()
                ->whereKey($activeId)
                ->with(['messages' => fn ($q) => $q->orderBy('id')->with('user:id,name'), 'patient:id,name', 'doctor:id,name', 'hospital:id,name'])
                ->first();
            abort_if(! $candidate, 404);
            abort_unless(ConversationAccess::canParticipate($nurse, $candidate), 403);
            $active = $candidate;
        }

        $startablePatients = HospitalNetworkService::assignablePatientsQueryForNurse($nurse)
            ->limit(500)
            ->get(['id', 'name']);
        $attendingDoctors = HospitalNetworkService::assignableAttendingDoctorsQueryForNurse($nurse)
            ->limit(200)
            ->get(['id', 'name']);

        return view('role.nurse.patient-chats', [
            'conversations' => $conversations,
            'active' => $active,
            'activeId' => $activeId,
            'startablePatients' => $startablePatients,
            'attendingDoctors' => $attendingDoctors,
        ]);
    }

    public function nurseStart(Request $request): RedirectResponse
    {
        /** @var User $nurse */
        $nurse = $request->user();
        abort_unless(ConversationAccess::isStaffNurse($nurse), 403);

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:users,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:120'],
        ]);

        $patient = User::query()->findOrFail((int) $data['patient_id']);
        $doctor = User::query()->findOrFail((int) $data['doctor_id']);

        abort_unless((string) ($patient->role ?? '') === 'PATIENT', 422);
        abort_unless((string) ($doctor->role ?? '') === 'MEDICAL_TEAM', 422);
        abort_if(ConversationAccess::isStaffNurse($doctor), 422);

        abort_unless(
            HospitalNetworkService::assignablePatientsQueryForNurse($nurse)->whereKey($patient->id)->exists(),
            403
        );
        abort_unless(
            HospitalNetworkService::assignableAttendingDoctorsQueryForNurse($nurse)->whereKey($doctor->id)->exists(),
            403
        );

        $sharedHospitalId = HospitalNetworkService::firstSharedActiveHospitalIdAmongUsers([$nurse, $patient, $doctor]);
        abort_if($sharedHospitalId === null, 403);

        $incomingTitle = trim((string) ($data['title'] ?? ''));
        $titleToStore = $incomingTitle !== '' ? $incomingTitle : null;

        $conversation = PatientDoctorConversation::query()->firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
            ],
            [
                'hospital_id' => $sharedHospitalId,
                'title' => $titleToStore,
            ],
        );

        if ((int) ($conversation->hospital_id ?? 0) !== (int) $sharedHospitalId) {
            $conversation->hospital_id = $sharedHospitalId;
        }
        if ($titleToStore !== null) {
            $conversation->title = $titleToStore;
        }
        $conversation->save();

        return redirect()->route('nurse.patient-chats', ['c' => $conversation->id]);
    }

    public function doctorStart(Request $request): RedirectResponse
    {
        /** @var User $doctor */
        $doctor = $request->user();
        abort_if(ConversationAccess::isStaffNurse($doctor), 403);

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:120'],
        ]);

        $patient = User::query()->findOrFail((int) $data['patient_id']);
        abort_unless((string) ($patient->role ?? '') === 'PATIENT', 422);

        abort_unless(
            HospitalNetworkService::assignablePatientsQueryForDoctor($doctor)->whereKey($patient->id)->exists(),
            403
        );

        $sharedHospitalId = HospitalNetworkService::firstSharedActiveHospitalId($doctor, $patient);
        abort_if($sharedHospitalId === null, 403);

        $incomingTitle = trim((string) ($data['title'] ?? ''));
        $titleToStore = $incomingTitle !== '' ? $incomingTitle : null;

        $conversation = PatientDoctorConversation::query()->firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
            ],
            [
                'hospital_id' => $sharedHospitalId,
                'title' => $titleToStore,
            ],
        );

        if ((int) ($conversation->hospital_id ?? 0) !== (int) $sharedHospitalId) {
            $conversation->hospital_id = $sharedHospitalId;
        }
        if ($titleToStore !== null) {
            $conversation->title = $titleToStore;
        }
        $conversation->save();

        return redirect()->route('doctor.conversations', ['c' => $conversation->id]);
    }

    public function updateConversationTitle(Request $request, PatientDoctorConversation $conversation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless((int) $conversation->doctor_id === (int) $user->id, 403);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
        ]);
        $t = trim((string) ($data['title'] ?? ''));
        $conversation->title = $t === '' ? null : $t;
        $conversation->save();

        return back();
    }

    public function patientStart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        /** @var User $patient */
        $patient = $request->user();
        $doctor = User::query()->findOrFail((int) $data['doctor_id']);
        abort_unless((string) $doctor->role === 'MEDICAL_TEAM', 422);

        $hospitalIds = $this->patientHospitalIds($patient);
        abort_if($hospitalIds === [], 403);

        $sharedHospitalId = HospitalNetworkService::firstSharedActiveHospitalId($patient, $doctor);
        abort_if($sharedHospitalId === null, 403);

        $conversation = PatientDoctorConversation::query()->firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
            ],
            ['hospital_id' => $sharedHospitalId],
        );

        if ((int) ($conversation->hospital_id ?? 0) !== $sharedHospitalId) {
            $conversation->hospital_id = $sharedHospitalId;
            $conversation->save();
        }

        return redirect()->route('patient.conversations', ['c' => $conversation->id]);
    }

    public function messageAttachment(Request $request, PatientDoctorConversation $conversation, PatientDoctorConversationMessage $message)
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(ConversationAccess::canParticipate($user, $conversation), 403);
        abort_if((int) $message->conversation_id !== (int) $conversation->id, 404);
        abort_if(! $message->hasAttachment(), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($message->attachment_path), 404);

        $name = $message->attachment_original_name ?? 'attachment';
        $mime = $message->attachment_mime ?? 'application/octet-stream';
        $disposition = $message->attachment_kind === 'voice' ? 'inline' : 'attachment';

        return $disk->response($message->attachment_path, $name, [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition.'; filename="'.$this->asciiFilename($name).'"',
        ]);
    }

    public function storeMessage(Request $request, PatientDoctorConversation $conversation): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(ConversationAccess::canParticipate($user, $conversation), 403);

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
            $attachmentPath = $file->store('conversation-messages/'.$conversation->id, 'local');
        }

        $message = PatientDoctorConversationMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $body === '' ? null : $body,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_mime' => $attachmentMime,
            'attachment_kind' => $attachmentKind,
            'attachment_size' => $attachmentSize,
        ]);

        $conversation->touch();

        $patient = User::query()->find($conversation->patient_id);
        $doctor = User::query()->find($conversation->doctor_id);
        if ($patient && $doctor) {
            $freshHospitalId = HospitalNetworkService::firstSharedActiveHospitalId($patient, $doctor);
            if ($freshHospitalId !== null && (int) ($conversation->hospital_id ?? 0) !== (int) $freshHospitalId) {
                $conversation->hospital_id = $freshHospitalId;
                $conversation->save();
            }
        }

        SafeBroadcast::dispatch(new ConversationMessageSent($message));

        if ($request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $this->messagePayloadForClient($message, $user, $conversation),
            ]);
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function messagePayloadForClient(PatientDoctorConversationMessage $message, User $user, PatientDoctorConversation $conversation): array
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
                ? route('portal.conversations.messages.attachment', [
                    'conversation' => $conversation->id,
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
