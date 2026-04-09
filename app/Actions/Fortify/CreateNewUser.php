<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        $role = (string) ($input['role'] ?? 'PATIENT');
        if (! in_array($role, ['PATIENT', 'HOSPITAL_OWNER'], true)) {
            $role = 'PATIENT';
        }

        Validator::make($input, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'in:PATIENT,HOSPITAL_OWNER'],
            'hospital_name' => ['nullable', 'required_if:role,HOSPITAL_OWNER', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        $email = (string) $input['email'];

        return DB::transaction(function () use ($input, $email, $role) {
            $user = User::create([
                'name' => $input['full_name'],
                'full_name' => $input['full_name'],
                'email' => $email,
                'phone' => $input['phone'] ?? null,
                'role' => $role,
                'status' => $role === 'HOSPITAL_OWNER' ? 'PENDING' : 'ACTIVE',
                'password' => $input['password'],
            ]);

            if ($role === 'HOSPITAL_OWNER') {
                Hospital::query()->create([
                    'owner_user_id' => $user->id,
                    'name' => (string) $input['hospital_name'],
                    'location' => 'Pending update',
                    'type' => 'Hospital',
                    'status' => 'Offline',
                    'verification_status' => 'PENDING',
                ]);
            }

            return $user;
        });
    }
}
