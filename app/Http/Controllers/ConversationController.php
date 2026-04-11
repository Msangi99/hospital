<?php

namespace App\Http\Controllers;

use App\Events\ConversationMessageSent;
use App\Models\HospitalWorkerMembership;
use App\Models\PatientDoctorConversation;
use App\Models\PatientDoctorConversationMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * @return array<int, int>
     */
    private function patientHospitalIds(User $patient): array
    {
        return HospitalWorkerMembership::query()
            ->where('user_id', $patient->id)
            ->where('status', 'ACTIVE')
            ->pluck('hospital_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
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

    public function doctorIndex(Request $request): View
    {
        /** @var User $doctor */
        $doctor = $request->user();

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

        return view('role.doctor.conversations', [
            'conversations' => $conversations,
            'active' => $active,
            'activeId' => $activeId,
        ]);
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

        $sharedHospitalId = HospitalWorkerMembership::query()
            ->where('user_id', $doctor->id)
            ->whereIn('hospital_id', $hospitalIds)
            ->where('status', 'ACTIVE')
            ->value('hospital_id');

        abort_if($sharedHospitalId === null, 403);

        $conversation = PatientDoctorConversation::query()->firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
            ],
            ['hospital_id' => (int) $sharedHospitalId],
        );

        return redirect()->route('patient.conversations', ['c' => $conversation->id]);
    }

    public function storeMessage(Request $request, PatientDoctorConversation $conversation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_if(
            (int) $conversation->patient_id !== (int) $user->id && (int) $conversation->doctor_id !== (int) $user->id,
            403
        );

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = PatientDoctorConversationMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $conversation->touch();

        event(new ConversationMessageSent($message));

        return back();
    }
}
