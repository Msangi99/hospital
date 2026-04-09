<?php

namespace App\Services;

use App\Ai\SafeGirlTriageAgent;
use App\Models\AiSetting;
use Illuminate\Support\Facades\Crypt;

class SafeGirlAiService
{
    /**
     * @param  array<int, array{role:string, content:string}>  $history
     * @return array{type:string,assistant_message:string,possible_condition:?string,urgency:?string,advice:array<int,string>,red_flags:array<int,string>}
     */
    public function respond(array $history): array
    {
        $setting = AiSetting::query()->firstOrCreate(
            ['context' => 'safe_girl'],
            [
                'provider' => 'openai',
                'model' => 'gpt-4o-mini',
                'is_enabled' => false,
            ]
        );

        if (! $setting->is_enabled || ! $setting->api_key_encrypted) {
            return [
                'type' => 'question',
                'assistant_message' => __('safe_girl.ai_disabled_reply'),
                'possible_condition' => null,
                'urgency' => null,
                'advice' => [],
                'red_flags' => [],
            ];
        }

        $provider = (string) $setting->provider;
        $model = (string) $setting->model;

        $apiKey = null;

        try {
            $apiKey = Crypt::decryptString((string) $setting->api_key_encrypted);
        } catch (\Throwable) {
            return [
                'type' => 'question',
                'assistant_message' => __('safe_girl.ai_key_invalid_reply'),
                'possible_condition' => null,
                'urgency' => null,
                'advice' => [],
                'red_flags' => [],
            ];
        }

        config(["ai.providers.{$provider}.key" => $apiKey]);

        $prompt = (string) ($setting->system_prompt ?: __('safe_girl.ai_default_system_prompt'));

        $agent = new SafeGirlTriageAgent($history, $prompt);

        $response = $agent->prompt(
            __('safe_girl.ai_task_prompt'),
            provider: $provider,
            model: $model,
        );

        $json = $this->extractJson((string) $response->text);

        if (! is_array($json)) {
            return [
                'type' => 'question',
                'assistant_message' => __('safe_girl.ai_parse_fallback'),
                'possible_condition' => null,
                'urgency' => null,
                'advice' => [],
                'red_flags' => [],
            ];
        }

        $type = (string) ($json['type'] ?? 'question');
        if (! in_array($type, ['question', 'conclusion'], true)) {
            $type = 'question';
        }

        $assistantMessage = trim((string) ($json['assistant_message'] ?? ''));
        if ($assistantMessage === '') {
            $assistantMessage = __('safe_girl.ai_parse_fallback');
            $type = 'question';
        }

        $advice = array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), (array) ($json['advice'] ?? [])),
            fn ($v) => $v !== ''
        ));

        $redFlags = array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), (array) ($json['red_flags'] ?? [])),
            fn ($v) => $v !== ''
        ));

        $possibleCondition = trim((string) ($json['possible_condition'] ?? ''));

        $urgency = trim((string) ($json['urgency'] ?? ''));
        if ($urgency === '') {
            $urgency = null;
        }

        return [
            'type' => $type,
            'assistant_message' => $assistantMessage,
            'possible_condition' => $possibleCondition !== '' ? $possibleCondition : null,
            'urgency' => $urgency,
            'advice' => $advice,
            'red_flags' => $redFlags,
        ];
    }

    /**
     * @return array<string,mixed>|null
     */
    private function extractJson(string $text): ?array
    {
        $trimmed = trim($text);

        if ($trimmed !== '' && str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
            $data = json_decode($trimmed, true);
            return is_array($data) ? $data : null;
        }

        if (preg_match('/\{.*\}/s', $text, $m) === 1) {
            $data = json_decode($m[0], true);
            return is_array($data) ? $data : null;
        }

        return null;
    }
}