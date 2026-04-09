<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'name' => 'SemaNami Advanced Medical Center',
                'location' => 'Posta Mpya, Dar es Salaam',
                'type' => 'Level 5 Referral',
                'status' => 'Online',
            ],
            [
                'name' => 'Afya-Kwanza Specialist Clinic',
                'location' => 'Masaki, Dar es Salaam',
                'type' => 'Specialized Lab & Clinic',
                'status' => 'Online',
            ],
            [
                'name' => 'Victoria Maternity & Children Hospital',
                'location' => 'Mbezi Beach, Dar es Salaam',
                'type' => 'Maternity & Pediatric',
                'status' => 'Online',
            ],
            [
                'name' => 'Digital Health Node - Arusha',
                'location' => 'Njiro, Arusha',
                'type' => 'GPRS Integrated Node',
                'status' => 'Online',
            ],
            [
                'name' => 'Binti-Safe Hormonal Diagnostic Center',
                'location' => 'Dodoma City, Central Node',
                'type' => 'Specialized Lab',
                'status' => 'Offline',
            ],
        ];

        foreach ($defaults as $row) {
            Hospital::query()->updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}

