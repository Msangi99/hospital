<?php

namespace App\Http\Controllers;

use App\Events\VideoConsultationRequested;
use App\Models\User;
use App\Models\VideoSession;
use App\Services\HospitalNetworkService;
use App\Support\SafeBroadcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoConsultController extends Controller
{
    public function showPatient(Request $request): View
    {
        return $this->renderVideoConsult($request, 'role.patient.video-consult');
    }

    public function showDoctor(Request $request): View|RedirectResponse
    {
        if (trim((string) $request->query('room', '')) === '') {
            return redirect()->route('doctor.video-requests');
        }

        return $this->renderVideoConsult($request, 'role.doctor.video-consult');
    }

    public function startPatientVideo(Request $request): RedirectResponse
    {
        /** @var User $patient */
        $patient = $request->user();
        abort_unless((string) $patient->role === 'PATIENT', 403);

        $existing = VideoSession::query()
            ->where('patient_id', $patient->id)
            ->whereNull('end_time')
            ->latest('id')
            ->first();

        if ($existing) {
            return redirect()->route('patient.video-consult', ['room' => $existing->room_id]);
        }

        $roomName = 'SemaNami-Room-'.md5((string) $patient->id.'-'.(string) now()->timestamp);
        $assignedDoctor = HospitalNetworkService::assignableDoctorForPatientVideo($patient);

        $hospitalId = null;
        if ($assignedDoctor !== null) {
            $hospitalId = HospitalNetworkService::firstSharedActiveHospitalId($patient, $assignedDoctor);
        }

        $session = VideoSession::query()->create([
            'patient_id' => (int) $patient->id,
            'doctor_id' => $assignedDoctor?->id,
            'hospital_id' => $hospitalId,
            'room_id' => $roomName,
            'start_time' => now(),
            'end_time' => null,
        ]);

        if ($assignedDoctor) {
            SafeBroadcast::dispatch(new VideoConsultationRequested(
                doctorId: (int) $assignedDoctor->id,
                patientId: (int) $patient->id,
                patientName: (string) $patient->name,
                roomId: $roomName,
                videoSessionId: (int) $session->id,
            ));
        }

        return redirect()->route('patient.video-consult', ['room' => $roomName]);
    }

    private function renderVideoConsult(Request $request, string $view): View
    {
        /** @var User $user */
        $user = $request->user();
        $role = (string) ($user->role ?? '');
        $requestedRoom = trim((string) $request->query('room', ''));

        $roomName = '';
        $videoAlert = (string) __('roleui.video_alert_default');
        $assignedDoctor = null;
        $assignedPatient = null;
        $jitsiActive = false;
        $showVideoConsultTopAlert = true;
        $patientOpenSession = null;
        $patientOpenSessionDoctor = null;

        if ($requestedRoom !== '') {
            $session = VideoSession::query()
                ->where('room_id', $requestedRoom)
                ->first();

            abort_if($session === null, 404);

            if ($role === 'PATIENT' && (int) $session->patient_id !== (int) $user->id) {
                abort(403);
            }
            if ($role === 'MEDICAL_TEAM' && (int) $session->doctor_id !== (int) $user->id) {
                abort(403);
            }

            if ($role === 'MEDICAL_TEAM' && $session->doctor_joined_at === null) {
                $session->doctor_joined_at = now();
                $session->save();
            }

            $roomName = (string) $session->room_id;
            if ($session->doctor_id) {
                $assignedDoctor = User::query()->find($session->doctor_id);
            }
            if ($session->patient_id) {
                $assignedPatient = User::query()->find((int) $session->patient_id);
            }
            $jitsiActive = true;
            if ($role === 'MEDICAL_TEAM') {
                $showVideoConsultTopAlert = false;
            } else {
                $videoAlert = (string) __('roleui.video_alert_patient_room_connected');
            }
        } elseif ($role === 'PATIENT') {
            $patientOpenSession = VideoSession::query()
                ->where('patient_id', $user->id)
                ->whereNull('end_time')
                ->latest('id')
                ->first();

            if ($patientOpenSession?->doctor_id) {
                $patientOpenSessionDoctor = User::query()->find((int) $patientOpenSession->doctor_id);
            }

            $videoAlert = $patientOpenSession
                ? (string) __('roleui.video_patient_has_active_hint')
                : (string) __('roleui.video_patient_idle');
        } elseif ($role === 'MEDICAL_TEAM') {
            $videoAlert = (string) __('roleui.video_doctor_idle_hint');
        }

        return view($view, [
            'roomName' => $roomName,
            'videoAlert' => $videoAlert,
            'assignedDoctor' => $assignedDoctor,
            'assignedPatient' => $assignedPatient,
            'jitsiActive' => $jitsiActive,
            'showVideoConsultTopAlert' => $showVideoConsultTopAlert,
            'patientOpenSession' => $patientOpenSession,
            'patientOpenSessionDoctor' => $patientOpenSessionDoctor,
        ]);
    }
}
