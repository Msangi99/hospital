<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:PATIENT,MEDICAL_TEAM,AMBULANCE,FACILITY'],
            'password' => $this->passwordRules(),
        ])->validate();

        $email = (string) $input['email'];
        $role = (string) $input['role'];

        // Legacy parity: old system escalated this specific email to SUPERADMIN.
        // Keep this controlled by env so it can be disabled in production.
        $legacyAdminEmail = (string) env('LEGACY_SUPERADMIN_EMAIL', 'admin@semanami.com');
        if ($legacyAdminEmail !== '' && strcasecmp($email, $legacyAdminEmail) === 0) {
            $role = 'SUPERADMIN';
        }

        return User::create([
            'name' => $input['full_name'],
            'full_name' => $input['full_name'],
            'email' => $email,
            'phone' => $input['phone'] ?? null,
            'role' => $role,
            'password' => $input['password'],
        ]);
    }
}
