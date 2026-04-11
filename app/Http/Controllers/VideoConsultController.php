<?php

namespace App\Http\Controllers;

use App\Events\VideoConsultationRequested;
use App\Models\User;
use App\Models\VideoSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoConsultController extends Controller
{
    public function showPatient(Request $request): View
    {
        return $this->renderVideoConsult($request, 'role.patient.video-consult');
    }

    public function showDoctor(Request $request): View
    {
        return $this->renderVideoConsult($request, 'role.doctor.video-consult');
    }

    private function renderVideoConsult(Request $request, string $view): View
    {
        /** @var User $user */
        $user = $request->user();
        $role = (string) ($user->role ?? '');
        $requestedRoom = trim((string) $request->query('room', ''));

        $roomName = '';
        $videoAlert = 'Video room ready.';
        $assignedDoctor = null;

        if ($requestedRoom !== '') {
            $session = VideoSession::query()
                ->where('room_id', $requestedRoom)
                ->first();

            if ($session) {
                if ($role === 'PATIENT' && (int) $session->patient_id !== (int) $user->id) {
                    abort(403);
                }
                if ($role === 'MEDICAL_TEAM' && (int) $session->doctor_id !== (int) $user->id) {
                    abort(403);
                }

                $roomName = (string) $session->room_id;
                if ($session->doctor_id) {
                    $assignedDoctor = User::query()->find($session->doctor_id);
                }
                $videoAlert = $role === 'MEDICAL_TEAM'
                    ? 'New patient call request received. Join now.'
                    : 'Connected to your requested consultation room.';
            }
        }

        if ($roomName === '' && $role === 'PATIENT') {
            $openSession = VideoSession::query()
                ->where('patient_id', $user->id)
                ->whereNull('end_time')
                ->latest('id')
                ->first();

            if ($openSession) {
                $roomName = (string) $openSession->room_id;
                if ($openSession->doctor_id) {
                    $assignedDoctor = User::query()->find($openSession->doctor_id);
                    $videoAlert = 'Reconnect successful. Your doctor request is still active.';
                } else {
                    $videoAlert = 'Reconnect successful. Searching for available doctor.';
                }
            } else {
                $roomName = 'SemaNami-Room-'.md5((string) $user->id.'-'.(string) now()->timestamp);
                $assignedDoctor = User::query()
                    ->where('role', 'MEDICAL_TEAM')
                    ->where('status', 'ACTIVE')
                    ->inRandomOrder()
                    ->first();

                $session = VideoSession::query()->create([
                    'patient_id' => (int) $user->id,
                    'doctor_id' => $assignedDoctor?->id,
                    'room_id' => $roomName,
                    'start_time' => now(),
                    'end_time' => null,
                ]);

                $videoAlert = $assignedDoctor
                    ? 'Call request sent. Dr. '.$assignedDoctor->name.' has been invited to join.'
                    : 'Call request created. No active doctor found right now.';

                if ($assignedDoctor) {
                    event(new VideoConsultationRequested(
                        doctorId: (int) $assignedDoctor->id,
                        patientId: (int) $user->id,
                        patientName: (string) $user->name,
                        roomId: $roomName,
                        videoSessionId: (int) $session->id,
                    ));
                }
            }
        }

        if ($roomName === '') {
            $roomName = 'SemaNami-Room-'.md5((string) $user->id.'-'.(string) now()->timestamp);
        }

        return view($view, [
            'roomName' => $roomName,
            'videoAlert' => $videoAlert,
            'assignedDoctor' => $assignedDoctor,
        ]);
    }
}
