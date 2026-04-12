<?php

use App\Models\AiSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('superadmin can open and update ai settings page', function () {
    $admin = User::factory()->withTwoFactor()->create(['role' => 'SUPERADMIN']);
    $this->actingAs($admin);

    $this->get(route('admin.ai-settings'))
        ->assertOk();

    $this->post(route('admin.ai-settings.update'), [
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'api_key' => 'test-key-123',
        'is_enabled' => '1',
        'system_prompt' => 'Test prompt',
    ])->assertRedirect();

    $row = AiSetting::query()->where('context', 'safe_girl')->first();

    expect($row)->not->toBeNull();
    expect($row->provider)->toBe('openai');
    expect($row->model)->toBe('gpt-4o-mini');
    expect($row->is_enabled)->toBeTrue();
    expect($row->api_key_encrypted)->not->toBeNull();
});
