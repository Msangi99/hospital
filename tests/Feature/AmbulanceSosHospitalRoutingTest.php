<?php

use App\Events\AmbulanceSosCreated;
use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\SosRequest;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('sos submission stores nearest hospital and broadcasts to nearby hospitals', function (): void {
    Event::fake([AmbulanceSosCreated::class]);

    $owner = User::factory()->create(['role' => 'HOSPITAL_OWNER', 'status' => 'ACTIVE']);

    $far = Hospital::factory()->create([
        'owner_user_id' => $owner->id,
        'name' => 'Far Hospital',
        'latitude' => -10.0,
        'longitude' => 35.0,
    ]);

    $near = Hospital::factory()->create([
        'owner_user_id' => $owner->id,
        'name' => 'Near Hospital',
        'latitude' => -6.8,
        'longitude' => 39.28,
    ]);

    $patientLat = -6.7924;
    $patientLng = 39.2083;

    $this->post(route('ambulance.sos'), [
        'latitude' => $patientLat,
        'longitude' => $patientLng,
        'address' => 'Dar test',
        'phone' => '+255700000001',
    ])->assertRedirect(route('ambulance'));

    $sos = SosRequest::query()->latest('id')->first();
    expect($sos)->not->toBeNull()
        ->and((int) $sos->nearest_hospital_id)->toBe((int) $near->id)
        ->and($sos->alerted_hospital_ids)->toBeArray()->not->toBeEmpty();

    Event::assertDispatched(AmbulanceSosCreated::class, function (AmbulanceSosCreated $e) use ($near): bool {
        return (int) $e->sos->nearest_hospital_id === (int) $near->id
            && $e->hospitalsAlerted !== [];
    });
});

test('ambulance crew without overlapping hospital cannot claim routed sos', function (): void {
    $owner = User::factory()->create(['role' => 'HOSPITAL_OWNER', 'status' => 'ACTIVE']);

    $h1 = Hospital::factory()->create([
        'owner_user_id' => $owner->id,
        'latitude' => -6.8,
        'longitude' => 39.28,
    ]);

    $h2 = Hospital::factory()->create([
        'owner_user_id' => $owner->id,
        'latitude' => -10.0,
        'longitude' => 35.0,
    ]);

    $crew = User::factory()->create(['role' => 'AMBULANCE', 'status' => 'ACTIVE']);

    HospitalWorkerMembership::query()->create([
        'hospital_id' => $h2->id,
        'user_id' => $crew->id,
        'worker_role' => 'AMBULANCE',
        'status' => 'ACTIVE',
        'joined_at' => now(),
    ]);

    $sos = SosRequest::query()->create([
        'user_id' => null,
        'phone' => '+255700000002',
        'latitude' => -6.79,
        'longitude' => 39.21,
        'address' => 'Scene',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'status' => SosRequest::STATUS_RECEIVED,
        'nearest_hospital_id' => $h1->id,
        'alerted_hospital_ids' => [(int) $h1->id],
    ]);

    $this->actingAs($crew)
        ->post(route('ambulance.portal.claim', $sos))
        ->assertRedirect(route('ambulance.portal.dashboard'))
        ->assertSessionHas('error');

    expect((string) $sos->fresh()->status)->toBe(SosRequest::STATUS_RECEIVED)
        ->and($sos->fresh()->assigned_user_id)->toBeNull();
});
