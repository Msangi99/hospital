<?php

use App\Models\User;

test('medical team can open video requests page', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-requests'))
        ->assertOk();
});

test('patients cannot open doctor video requests page', function () {
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($patient)
        ->get(route('doctor.video-requests'))
        ->assertForbidden();
});
