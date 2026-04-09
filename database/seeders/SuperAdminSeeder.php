<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed or update the default superadmin credential.
     */
    public function run(): void
    {
        $name = (string) env('SUPERADMIN_NAME', 'Platform Superadmin');
        $email = (string) env('SUPERADMIN_EMAIL', 'admin@semanami.com');
        $password = (string) env('SUPERADMIN_PASSWORD', 'ChangeMe123!');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'full_name' => $name,
                'role' => 'SUPERADMIN',
                'status' => 'ACTIVE',
                'password' => Hash::make($password),
            ]
        );
    }
}
