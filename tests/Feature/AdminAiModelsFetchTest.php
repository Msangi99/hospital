<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('superadmin can list openai models when api responds', function () {
    Http::fake([
        'api.openai.com/*' => Http::response([
            'data' => [
                ['id' => 'gpt-4o'],
                ['id' => 'gpt-4o-mini'],
            ],
        ], 200),
    ]);

    $admin = User::factory()->withTwoFactor()->create(['role' => 'SUPERADMIN']);
    $this->actingAs($admin);

    $response = $this->postJson(route('admin.ai-settings.models'), [
        'provider' => 'openai',
        'api_key' => 'sk-test',
        'use_saved_key' => false,
    ]);

    $response->assertOk();
    $models = $response->json('models');
    expect($models)->toContain('gpt-4o');
    expect($models)->toContain('gpt-4o-mini');
});

test('non superadmin cannot list ai models', function () {
    $user = User::factory()->create(['role' => 'PATIENT']);
    $this->actingAs($user);

    $this->postJson(route('admin.ai-settings.models'), [
        'provider' => 'openai',
        'api_key' => 'sk-test',
    ])->assertForbidden();
});

test('requires api key when not ollama and no saved key', function () {
    $admin = User::factory()->withTwoFactor()->create(['role' => 'SUPERADMIN']);
    $this->actingAs($admin);

    $this->postJson(route('admin.ai-settings.models'), [
        'provider' => 'openai',
        'api_key' => '',
        'use_saved_key' => false,
    ])->assertStatus(422)
        ->assertJsonFragment(['message' => __('roleui.ai_models_key_required')]);
});
