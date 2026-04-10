<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SafeGirlWebhookService
{
    /**
     * GET webhook?message=… and return assistant text from JSON like [{"output":"…"}].
     */
    public function fetchAssistantReply(string $message): ?string
    {
        $url = trim((string) config('safe_girl.webhook_url', ''));

        if ($url === '') {
            return null;
        }

        $timeout = max(5, (int) config('safe_girl.webhook_timeout', 120));

        try {
            $response = Http::timeout($timeout)->get($url, [
                'message' => $message,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (is_array($data) && $data !== [] && array_is_list($data)) {
                $first = $data[0];
                if (is_array($first)) {
                    $output = trim((string) ($first['output'] ?? ''));
                    if ($output !== '') {
                        return $output;
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    public function notifyWithMessage(string $message): void
    {
        $url = trim((string) config('safe_girl.webhook_url', ''));

        if ($url === '') {
            return;
        }

        $timeout = max(5, (int) config('safe_girl.webhook_timeout', 120));

        try {
            Http::timeout($timeout)->get($url, [
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
