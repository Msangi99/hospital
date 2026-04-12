<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $role = (string) ($user->role ?? '');
        if ($role === 'SUPERADMIN') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($role === 'HOSPITAL_OWNER') {
            return redirect()->intended(route('owner.dashboard'));
        }

        if ($role === 'MEDICAL_TEAM') {
            $hasProfile = $user->medicalProfile()->exists();
            if (! $hasProfile) {
                return redirect()->intended(route('doctor.complete-profile'));
            }

            return redirect()->intended(route('doctor.dashboard'));
        }

        if ($role === 'FACILITY') {
            return redirect()->intended(route('facility.dashboard'));
        }

        if ($role === 'AMBULANCE') {
            return redirect()->intended(route('ambulance.portal.dashboard'));
        }

        return redirect()->intended(route('patient.dashboard'));
    }
}
