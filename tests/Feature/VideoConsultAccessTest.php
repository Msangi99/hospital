<?php

use App\Models\User;

test('guests are redirected to login when visiting legacy video consult url', function () {
    $this->get(route('video-consult'))
        ->assertRedirect(route('login'));
});

test('patients can open video consult inside portal', function () {
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($patient)
        ->get(route('patient.video-consult'))
        ->assertOk();
});

test('medical team can open video consult inside portal', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-consult'))
        ->assertOk();
});

test('legacy video consult url redirects patient to portal video page', function () {
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($patient)
        ->get(route('video-consult'))
        ->assertRedirect(route('patient.video-consult'));
});

test('legacy video consult url redirects doctor to portal video page', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($doctor)
        ->get(route('video-consult'))
        ->assertRedirect(route('doctor.video-consult'));
});

test('authenticated users without patient or doctor role cannot access legacy video consult', function () {
    $owner = User::factory()->create([
        'role' => 'HOSPITAL_OWNER',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($owner)
        ->get(route('video-consult'))
        ->assertForbidden();
});

test('patients cannot open doctor video consult url', function () {
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($patient)
        ->get(route('doctor.video-consult'))
        ->assertForbidden();
});

test('doctors cannot open patient video consult url', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($doctor)
        ->get(route('patient.video-consult'))
        ->assertForbidden();
});
