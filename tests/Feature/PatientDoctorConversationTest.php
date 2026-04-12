<?php

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\MedicalProfile;
use App\Models\PatientDoctorConversation;
use App\Models\PatientDoctorConversationMessage;
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

test('conversation message ajax request returns json for live chat', function (): void {
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

    $this->actingAs($patient)
        ->post(route('portal.conversations.messages', $conversation), ['body' => 'Live json'], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('message.body', 'Live json')
        ->assertJsonPath('message.user_id', $patient->id);
});

test('participant can attach a document up to 5mb and other party can download it', function (): void {
    Storage::fake('local');

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
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $conversation = PatientDoctorConversation::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => $this->hospital->id,
    ]);

    $file = UploadedFile::fake()->create('lab-result.pdf', 400, 'application/pdf');

    $this->actingAs($patient)->post(route('portal.conversations.messages', $conversation), [
        'body' => 'Please review this PDF.',
        'attachment_kind' => 'document',
        'attachment' => $file,
    ])->assertRedirect();

    $message = PatientDoctorConversationMessage::query()->where('conversation_id', $conversation->id)->latest('id')->first();
    expect($message)->not->toBeNull()
        ->and($message->attachment_kind)->toBe('document')
        ->and($message->attachment_path)->not->toBeNull();

    Storage::disk('local')->assertExists((string) $message->attachment_path);

    $this->actingAs($doctor)
        ->get(route('portal.conversations.messages.attachment', [$conversation, $message]))
        ->assertOk();
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

test('doctor can open a patient chat with a custom title', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE', 'name' => 'Jane Patient']);
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
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    MedicalProfile::query()->create([
        'user_id' => $doctor->id,
        'staff_type' => 'MD',
        'specialization' => 'General',
        'registration_no' => 'D-1',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($doctor)->post(route('doctor.conversations.start'), [
        'patient_id' => $patient->id,
        'title' => 'Ward 3 — Jane',
    ])->assertRedirect();

    $conv = PatientDoctorConversation::query()->where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->first();
    expect($conv)->not->toBeNull()
        ->and((string) $conv->title)->toBe('Ward 3 — Jane');
});

test('nurse at same hospital can post in patient doctor thread', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$patient, $doctor, $nurse] as $user) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $user->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }
    HospitalWorkerMembership::query()->where('user_id', $patient->id)->update(['worker_role' => 'PATIENT']);

    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-9',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $conversation = PatientDoctorConversation::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => $this->hospital->id,
        'title' => 'Chart A',
    ]);

    $this->actingAs($nurse)->post(route('portal.conversations.messages', $conversation), [
        'body' => 'Vitals stable.',
    ])->assertRedirect();

    expect(PatientDoctorConversationMessage::query()->where('conversation_id', $conversation->id)->where('user_id', $nurse->id)->count())->toBe(1);
});

test('nurse cannot access conversation at hospital they do not belong to', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    $otherHospital = Hospital::factory()->create([
        'owner_user_id' => $this->owner->id,
        'name' => 'Other Site',
    ]);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $otherHospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    HospitalWorkerMembership::query()->create([
        'hospital_id' => $otherHospital->id,
        'user_id' => $doctor->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $nurse->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-2',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $conversation = PatientDoctorConversation::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => $otherHospital->id,
    ]);

    $this->actingAs($nurse)->get(route('nurse.patient-chats', ['c' => $conversation->id]))->assertForbidden();
});

test('nurse is redirected from doctor conversations to nurse patient chats', function (): void {
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $nurse->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-3',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($nurse)->get(route('doctor.conversations'))->assertRedirect(route('nurse.patient-chats'));
});

test('nurse can create patient chat with attending doctor', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $doctor = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$patient, $doctor, $nurse] as $user) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $user->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }
    HospitalWorkerMembership::query()->where('user_id', $patient->id)->update(['worker_role' => 'PATIENT']);

    MedicalProfile::query()->create([
        'user_id' => $doctor->id,
        'staff_type' => 'MD',
        'specialization' => 'General',
        'registration_no' => 'D-55',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-4',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($nurse)->post(route('nurse.patient-chats.start'), [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'title' => 'Ward chat',
    ])->assertRedirect();

    $conv = PatientDoctorConversation::query()->where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->first();
    expect($conv)->not->toBeNull()
        ->and((string) $conv->title)->toBe('Ward chat')
        ->and((int) $conv->hospital_id)->toBe((int) $this->hospital->id);
});

test('nurse cannot use doctor new chat form', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $nurse = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $patient->id,
        'worker_role' => 'PATIENT',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    HospitalWorkerMembership::query()->create([
        'hospital_id' => $this->hospital->id,
        'user_id' => $nurse->id,
        'worker_role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);
    MedicalProfile::query()->create([
        'user_id' => $nurse->id,
        'staff_type' => 'NURSE',
        'specialization' => 'RN',
        'registration_no' => 'N-4b',
        'license_copy' => 'on-file',
        'verification_status' => 'APPROVED',
        'status' => 'APPROVED',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($nurse)->post(route('doctor.conversations.start'), [
        'patient_id' => $patient->id,
        'title' => 'X',
    ])->assertForbidden();
});

test('nurse cannot create chat with another nurse as attending', function (): void {
    $patient = User::factory()->create(['role' => 'PATIENT', 'status' => 'ACTIVE']);
    $nurseA = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);
    $nurseB = User::factory()->create(['role' => 'MEDICAL_TEAM', 'status' => 'ACTIVE']);

    foreach ([$patient, $nurseA, $nurseB] as $user) {
        HospitalWorkerMembership::query()->create([
            'hospital_id' => $this->hospital->id,
            'user_id' => $user->id,
            'worker_role' => 'MEDICAL_TEAM',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);
    }
    HospitalWorkerMembership::query()->where('user_id', $patient->id)->update(['worker_role' => 'PATIENT']);

    foreach ([$nurseA, $nurseB] as $n) {
        MedicalProfile::query()->create([
            'user_id' => $n->id,
            'staff_type' => 'NURSE',
            'specialization' => 'RN',
            'registration_no' => 'N-'.$n->id,
            'license_copy' => 'on-file',
            'verification_status' => 'APPROVED',
            'status' => 'APPROVED',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->actingAs($nurseA)->post(route('nurse.patient-chats.start'), [
        'patient_id' => $patient->id,
        'doctor_id' => $nurseB->id,
        'title' => 'X',
    ])->assertStatus(422);
});
