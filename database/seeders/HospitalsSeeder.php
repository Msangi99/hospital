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
                'latitude' => -6.8231,
                'longitude' => 39.2693,
                'type' => 'Level 5 Referral',
                'status' => 'Online',
            ],
            [
                'name' => 'Afya-Kwanza Specialist Clinic',
                'location' => 'Masaki, Dar es Salaam',
                'latitude' => -6.7549,
                'longitude' => 39.2674,
                'type' => 'Specialized Lab & Clinic',
                'status' => 'Online',
            ],
            [
                'name' => 'Victoria Maternity & Children Hospital',
                'location' => 'Mbezi Beach, Dar es Salaam',
                'latitude' => -6.6797,
                'longitude' => 39.2006,
                'type' => 'Maternity & Pediatric',
                'status' => 'Online',
            ],
            [
                'name' => 'Digital Health Node - Arusha',
                'location' => 'Njiro, Arusha',
                'latitude' => -3.3869,
                'longitude' => 36.6829,
                'type' => 'GPRS Integrated Node',
                'status' => 'Online',
            ],
            [
                'name' => 'Binti-Safe Hormonal Diagnostic Center',
                'location' => 'Dodoma City, Central Node',
                'latitude' => -6.1730,
                'longitude' => 35.7516,
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

