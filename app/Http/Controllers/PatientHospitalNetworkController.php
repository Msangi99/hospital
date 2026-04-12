<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\User;
use App\Services\HospitalNetworkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientHospitalNetworkController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $patient */
        $patient = $request->user();
        abort_unless((string) $patient->role === 'PATIENT', 403);

        $hospitals = HospitalNetworkService::hospitalsWithBrowseableMedicalTeam()->get();

        $linkedHospitalIds = array_flip(HospitalNetworkService::activeHospitalIdsForUser($patient));

        return view('role.patient.hospitals', [
            'hospitals' => $hospitals,
            'linkedHospitalIds' => $linkedHospitalIds,
        ]);
    }

    public function join(Request $request, Hospital $hospital): RedirectResponse
    {
        /** @var User $patient */
        $patient = $request->user();
        abort_unless((string) $patient->role === 'PATIENT', 403);

        if (! HospitalNetworkService::hospitalHasActiveMedicalTeamLink((int) $hospital->id)) {
            abort(404);
        }

        HospitalWorkerMembership::query()->updateOrCreate(
            [
                'hospital_id' => $hospital->id,
                'user_id' => $patient->id,
            ],
            [
                'worker_role' => 'PATIENT',
                'status' => 'ACTIVE',
                'joined_at' => now(),
            ],
        );

        return redirect()
            ->route('patient.hospitals')
            ->with('status', __('roleui.patient_hospitals_joined_flash', ['name' => $hospital->name]));
    }
}
