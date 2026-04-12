<?php

use App\Models\DoctorNurseCoordinationChat;
use App\Models\DoctorNurseCoordinationMessage;
use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->owner = User::factory()->create([
        'role' => 'HOSPITAL_OWNER',
        'status' => 'ACTIVE',
    ]);
    $this->hospital = Hospital::factory()->create([
        'owner_user_id' => $this->owner->id,
        'name' => 'Coord Hospital',
    ]);
});

test('doctor can start coordination chat with nurse and patient label', function (): void {
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$doctor, $nurse] as $u) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $u->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }

    MedicalProfile::query()->create([
        'user_id' => $doctor->id,
        'staff_type' => 'MD',
        'specialization' => 'General',
        'registration_no' => 'D-1',
        'license_copy' => 'x',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-1',
        'license_copy' => 'x',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($doctor)->post(route('doctor.nurse-coordination.start'), [
        'nurse_id' => $nurse->id,
        'patient_context' => 'Jane Doe — Room 12',
    ])->assertRedirect();

    $chat = DoctorNurseCoordinationChat::query()->where('doctor_id', $doctor->id)->where('nurse_id', $nurse->id)->first();
    expect($chat)->not->toBeNull()
        ->and((string) $chat->patient_context)->toBe('Jane Doe — Room 12')
        ->and((int) $chat->hospital_id)->toBe((int) $this->hospital->id);
});

test('doctor and nurse can exchange coordination messages', function (): void {
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$doctor, $nurse] as $u) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $u->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }

    MedicalProfile::query()->create([
        'user_id' => $doctor->id,
        'staff_type' => 'MD',
        'specialization' => 'General',
        'registration_no' => 'D-2',
        'license_copy' => 'x',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-2',
        'license_copy' => 'x',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $chat = DoctorNurseCoordinationChat::query()->create([
        'doctor_id' => $doctor->id,
        'nurse_id' => $nurse->id,
        'patient_id' => null,
        'patient_context' => 'Post-op follow-up',
        'hospital_id' => $this->hospital->id,
    ]);

    $this->actingAs($doctor)
        ->post(route('portal.coordination.messages', $chat), ['body' => 'Please check vitals.'], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('ok', true);

    $this->actingAs($nurse)
        ->post(route('portal.coordination.messages', $chat), ['body' => 'Done, all stable.'], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])
        ->assertOk();

    expect(DoctorNurseCoordinationMessage::query()->where('coordination_chat_id', $chat->id)->count())->toBe(2);
});

test('stranger cannot post to coordination chat', function (): void {
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $other = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$doctor, $nurse] as $u) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $u->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }

    $chat = DoctorNurseCoordinationChat::query()->create([
        'doctor_id' => $doctor->id,
        'nurse_id' => $nurse->id,
        'patient_id' => null,
        'patient_context' => 'X',
        'hospital_id' => $this->hospital->id,
    ]);

    $this->actingAs($other)->post(route('portal.coordination.messages', $chat), [
        'body' => 'hack',
    ])->assertForbidden();
});

test('doctor can send coordination message with document attachment', function (): void {
    Storage::fake('local');

    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$doctor, $nurse] as $u) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $u->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }

    foreach ([$doctor, $nurse] as $u) {
        MedicalProfile::query()->create([
            'user_id' => $u->id,
            'staff_type' => $u->id === $doctor->id ? 'MD' : 'NURSE',
            'specialization' => 'X',
            'registration_no' => 'R-'.$u->id,
            'license_copy' => 'x',
            'verification_status' => 'APPROVED',
            'status' => 'APPROVED',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $chat = DoctorNurseCoordinationChat::query()->create([
        'doctor_id' => $doctor->id,
        'nurse_id' => $nurse->id,
        'patient_id' => null,
        'patient_context' => 'Labs',
        'hospital_id' => $this->hospital->id,
    ]);

    $file = UploadedFile::fake()->create('note.pdf', 120, 'application/pdf');

    $this->actingAs($doctor)
        ->post(route('portal.coordination.messages', $chat), [
            'body' => 'Please review.',
            'attachment' => $file,
            'attachment_kind' => 'document',
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('message.has_attachment', true)
        ->assertJsonPath('message.attachment_kind', 'document');

    $msg = DoctorNurseCoordinationMessage::query()->where('coordination_chat_id', $chat->id)->first();
    expect($msg)->not->toBeNull()
        ->and($msg->hasAttachment())->toBeTrue()
        ->and((string) $msg->attachment_kind)->toBe('document');

    $this->actingAs($nurse)
        ->get(route('portal.coordination.messages.attachment', ['chat' => $chat, 'message' => $msg]))
        ->assertOk();
});
