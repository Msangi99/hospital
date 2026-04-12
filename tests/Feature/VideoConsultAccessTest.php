<?php

use App\Models\User;
use App\Models\VideoSession;

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

test('medical team without room is sent to video requests hub', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-consult'))
        ->assertRedirect(route('doctor.video-requests'));
});

test('medical team with valid room can open video consult', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $room = 'Test-Room-'.uniqid();
    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => $room,
        'start_time' => now(),
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-consult', ['room' => $room]))
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

test('doctor in active room sees patient name on card and no top consult alert strip', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
        'name' => 'CardPatient Alpha',
    ]);

    $room = 'Test-Room-'.uniqid();
    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => $room,
        'start_time' => now(),
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-consult', ['room' => $room]))
        ->assertOk()
        ->assertSee('CardPatient Alpha', false)
        ->assertDontSee((string) __('roleui.video_consult_alert_kicker'), false);

    expect(VideoSession::query()->where('room_id', $room)->value('doctor_joined_at'))->not->toBeNull();
});

test('patient in active room sees doctor name on card', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
        'name' => 'CardDoctor Beta',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    $room = 'Test-Room-'.uniqid();
    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => $room,
        'start_time' => now(),
        'end_time' => null,
    ]);

    $this->actingAs($patient)
        ->get(route('patient.video-consult', ['room' => $room]))
        ->assertOk()
        ->assertSee('CardDoctor Beta', false);
});

test('doctor video requests lists unanswered stale sessions as missed', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => 'Stale-Room-'.uniqid(),
        'start_time' => now()->subMinutes(5),
        'doctor_joined_at' => null,
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-requests'))
        ->assertOk()
        ->assertSee((string) __('roleui.video_requests_status_missed'), false);
});

test('doctor appointments page does not replay incoming video toast after doctor has joined the room', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => 'Joined-Room-'.uniqid(),
        'start_time' => now(),
        'doctor_joined_at' => now(),
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.appointments'))
        ->assertOk()
        ->assertDontSee('doctorVideoToastShow', false);
});

test('doctor appointments page does not inject video toast when ring window has expired', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => 'Stale-Room-'.uniqid(),
        'start_time' => now()->subMinutes(5),
        'doctor_joined_at' => null,
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.appointments'))
        ->assertOk()
        ->assertDontSee('doctorVideoToastShow', false);
});

test('doctor appointments page injects video toast while request is still ringing', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => 'Ring-Room-'.uniqid(),
        'start_time' => now(),
        'doctor_joined_at' => null,
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.appointments'))
        ->assertOk()
        ->assertSee('doctorVideoToastShow', false);
});

test('doctor video requests shows join banner while a session is still ringing', function () {
    $doctor = User::factory()->create([
        'role' => 'MEDICAL_TEAM',
        'status' => 'ACTIVE',
    ]);
    $patient = User::factory()->create([
        'role' => 'PATIENT',
        'status' => 'ACTIVE',
    ]);

    VideoSession::query()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'hospital_id' => null,
        'room_id' => 'Fresh-Room-'.uniqid(),
        'start_time' => now(),
        'doctor_joined_at' => null,
        'end_time' => null,
    ]);

    $this->actingAs($doctor)
        ->get(route('doctor.video-requests'))
        ->assertOk()
        ->assertSee((string) __('roleui.video_alert_doctor_join_request'), false)
        ->assertSee((string) __('roleui.video_requests_status_ringing'), false);
});
