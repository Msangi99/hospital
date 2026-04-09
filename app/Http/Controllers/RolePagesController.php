<?php

namespace App\Http\Controllers;

use App\Models\MedicalProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RolePagesController extends Controller
{
    public function adminDashboard(): View
    {
        return view('role.admin.dashboard');
    }

    public function doctorDashboard(): View
    {
        return view('role.doctor.dashboard');
    }

    public function doctorAppointments(): View
    {
        return view('role.doctor.appointments');
    }

    public function doctorPatients(): View
    {
        return view('role.doctor.patients');
    }

    public function doctorCompleteProfile(): View
    {
        return view('role.doctor.complete-profile');
    }

    public function doctorCompleteProfileSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_type' => ['required', 'string', 'max:50'],
            'specialization' => ['required', 'string', 'max:255'],
            'registration_no' => ['required', 'string', 'max:100'],
            'license_copy' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('license_copy')->store('licenses', 'public');

        MedicalProfile::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'staff_type' => $data['staff_type'],
                'specialization' => $data['specialization'],
                'registration_no' => $data['registration_no'],
                'license_copy' => $path,
                'verification_status' => 'PENDING',
                'status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        return redirect()->route('doctor.dashboard')->with('status', __('roleui.profile_submitted'));
    }

    public function patientDashboard(): View
    {
        return view('role.patient.dashboard');
    }

    public function facilityDashboard(): View
    {
        return view('role.facility.dashboard');
    }
}

