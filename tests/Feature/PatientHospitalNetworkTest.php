<?php

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\User;

test('patient can view hospitals that have an active medical team', function () {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $hospital = Hospital::factory()->create(['name' => 'Linkable General']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $hospital->id,
        'user_id' => $doctor->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $this->actingAs($patient)
        ->get(route('patient.hospitals'))
        ->assertOk()
        ->assertSee('Linkable General', false);
});

test('patient can link account to a hospital with medical staff', function () {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $hospital = Hospital::factory()->create();

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $hospital->id,
        'user_id' => $doctor->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $this->actingAs($patient)
        ->post(route('patient.hospitals.join', $hospital))
        ->assertRedirect(route('patient.hospitals'));

    $this->assertDatabaseHas('hospital_worker_memberships', [
        'hospital_id' => $hospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);
});

test('patient cannot join a hospital that has no active medical team link', function () {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $hospital = Hospital::factory()->create();

    $this->actingAs($patient)
        ->post(route('patient.hospitals.join', $hospital))
        ->assertNotFound();
});
