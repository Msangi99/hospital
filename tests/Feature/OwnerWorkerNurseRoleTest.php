<?php

use App\Models\Hospital;
use App\Models\HospitalWorkerMembership;
use App\Models\User;
use App\Services\ConversationAccess;

test('hospital owner can add nurse worker with medical team login role', function (): void {
    $owner = User::factory()->create(['role' => 'HOSPITAL_OWNER', 'status' => 'ACTIVE']);
    $hospital = Hospital::factory()->create(['owner_user_id' => $owner->id]);

    $email = 'nurse-worker-'.uniqid('', true).'@example.com';

    $this->actingAs($owner)->post(route('owner.workers.store'), [
        'name' => 'Nurse Example',
        'email' => $email,
        'phone' => null,
        'worker_role' => 'NURSE',
        'status' => 'ACTIVE',
        'password' => 'password123',
    ])->assertRedirect(route('owner.workers'));

    $user = User::query()->where('email', $email)->firstOrFail();
    expect($user->role)->toBe('MEDICAL_TEAM');

    $membership = HospitalWorkerMembership::query()
        ->where('user_id', $user->id)
        ->where('hospital_id', $hospital->id)
        ->firstOrFail();

    expect($membership->worker_role)->toBe('NURSE')
        ->and(ConversationAccess::isStaffNurse($user))->toBeTrue();

    $profilePage = $this->actingAs($user)->get(route('doctor.complete-profile'));
    $profilePage->assertOk();
    expect($profilePage->getContent())->toMatch('/<option[^>]*value="NURSE"[^>]*selected/s');
});
