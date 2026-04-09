<?php

use App\Models\AiSetting;
use App\Models\User;
use App\Services\SafeGirlAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('safe-girl ai chat returns fallback when disabled', function () {
    $user = User::factory()->create(['role' => 'PATIENT']);
    $this->actingAs($user);

    AiSetting::query()->create([
        'context' => 'safe_girl',
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'is_enabled' => false,
    ]);

    $response = $this->postJson(route('safe-girl.ai-chat'), [
        'message' => 'I have lower abdominal pain for 2 days',
        'history' => [],
    ]);

    $response->assertOk();
    $response->assertJsonPath('type', 'question');
    $response->assertJsonStructure([
        'assistant_message',
        'type',
        'possible_condition',
        'urgency',
        'advice',
        'red_flags',
    ]);
});

test('safe-girl ai chat returns graceful fallback when service throws', function () {
    $user = User::factory()->create(['role' => 'PATIENT']);
    $this->actingAs($user);

    $mock = new class extends SafeGirlAiService
    {
        public function respond(array $history): array
        {
            throw new RuntimeException('simulated provider outage');
        }
    };

    $this->app->instance(SafeGirlAiService::class, $mock);

    $response = $this->postJson(route('safe-girl.ai-chat'), [
        'message' => 'I feel dizzy',
        'history' => [],
    ]);

    $response->assertOk();
    $response->assertJsonPath('assistant_message', __('safe_girl.ai_error_reply'));
    $response->assertJsonPath('type', 'question');
});