<?php

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\PatientDoctorConversation;
use App\Models\PatientDoctorConversationMessage;
use App\Models\User;

beforeEach(function (): void {
    $this->owner = User::factory()->create([
        'role' => 'HOSPITAL_OWNER',
        'status' => 'ACTIVE',
    ]);
    $this->hospital = Hospital::factory()->create([
        'owner_user_id' => $this->owner->id,
        'name' => 'Test Memorial Hospital',
    ]);
});

test('patient can start conversation with doctor at shared hospital', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $doctor->id,
        'worker_role' => 'DOCTOR',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $response = $this->actingAs($patient)->post(route('patient.conversations.start'), [
        'doctor_id' => $doctor->id,
    ]);

    $response->assertRedirect();
    $conv = PatientDoctorConversation::query()->where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->first();
    expect($conv)->not->toBeNull()
        ->and((int) $conv->hospital_id)->toBe((int) $this->hospital->id);
});

test('patient cannot start conversation when doctor shares no hospital', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $this->actingAs($patient)->post(route('patient.conversations.start'), [
        'doctor_id' => $doctor->id,
    ])->assertForbidden();
});

test('patient and doctor can post messages on their conversation', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $doctor->id,
        'worker_role' => 'DOCTOR',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $conversation = PatientDoctorConversation::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => $this->hospital->id,
    ]);

    $this->actingAs($patient)->post(route('portal.conversations.messages', $conversation), [
        'body' => 'Hello from patient',
    ])->assertRedirect();

    $this->actingAs($doctor)->post(route('portal.conversations.messages', $conversation), [
        'body' => 'Hello from doctor',
    ])->assertRedirect();

    expect(PatientDoctorConversationMessage::query()->where('conversation_id', $conversation->id)->count())->toBe(2);
});

test('stranger cannot post to conversation', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $other = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);

    $conversation = PatientDoctorConversation::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => $this->hospital->id,
    ]);

    $this->actingAs($other)->post(route('portal.conversations.messages', $conversation), [
        'body' => 'Hack',
    ])->assertForbidden();
});
