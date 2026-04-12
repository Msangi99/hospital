<?php

use App\Models\User;

test('superadmin without two factor is redirected from admin with mfa query', function (): void {
    config(['admin-security.require_superadmin_mfa' => true]);

    $admin = User::factory()->create(['role' => 'SUPERADMIN']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('profile.edit', ['admin_mfa' => '1']));
});

test('prepare admin access stores intended profile setup url', function (): void {
    config(['admin-security.require_superadmin_mfa' => true]);

    $admin = User::factory()->create(['role' => 'SUPERADMIN']);

    $this->actingAs($admin)
        ->get(route('settings.prepare-admin-access'))
        ->assertRedirect(route('password.confirm'))
        ->assertSessionHas('url.intended', route('profile.edit', ['admin_mfa' => 'setup'], false));
});
