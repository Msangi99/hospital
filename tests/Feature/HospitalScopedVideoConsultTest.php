<?php

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\User;
use App\Models\VideoSession;

beforeEach(function (): void {
    $this->owner = User::factory()->create([
        'role' => 'HOSPITAL_OWNER',
        'status' => 'ACTIVE',
    ]);
    $this->hospital = Hospital::factory()->create([
        'owner_user_id' => $this->owner->id,
        'name' => 'Scoped Video Hospital',
    ]);
});

test('patient video consult assigns only in-network doctor and sets hospital', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $inNetwork = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $outOfNetwork = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$patient, $inNetwork] as $user) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $user->id,
            'worker_role' => $user->role === 'PATIENT' ? 'PATIENT' : 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }

    HospitalWorkerMembership::query()->create([
        'hospital_id' => Hospital::factory()->create(['owner_user_id' => $this->owner->id])->id,
        'user_id' => $outOfNetwork->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $this->actingAs($patient)->get(route('patient.video-consult'))->assertOk();
    expect(VideoSession::query()->where('patient_id', $patient->id)->count())->toBe(0);

    $this->actingAs($patient)->post(route('patient.video-consult.start'))->assertRedirect();

    $session = VideoSession::query()->where('patient_id', $patient->id)->latest('id')->first();
    expect($session)->not->toBeNull()
        ->and((int) $session->doctor_id)->toBe((int) $inNetwork->id)
        ->and((int) $session->hospital_id)->toBe((int) $this->hospital->id);
});

test('patient without hospital membership gets video session without doctor', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);

    $this->actingAs($patient)->get(route('patient.video-consult'))->assertOk();
    expect(VideoSession::query()->where('patient_id', $patient->id)->count())->toBe(0);

    $this->actingAs($patient)->post(route('patient.video-consult.start'))->assertRedirect();

    $session = VideoSession::query()->where('patient_id', $patient->id)->latest('id')->first();
    expect($session)->not->toBeNull()
        ->and($session->doctor_id)->toBeNull()
        ->and($session->hospital_id)->toBeNull();
});
