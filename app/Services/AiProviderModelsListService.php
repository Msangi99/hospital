<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AiProviderModelsListService
{
    /**
     * @return array<int, string>
     */
    public function list(string $provider, string $apiKey): array
    {
        $provider = strtolower(trim($provider));

        return match ($provider) {
            'openai' => $this->openAiCompatibleModels(
                (string) config('ai.providers.openai.url', 'https://api.openai.com/v1'),
                $apiKey
            ),
            'groq' => $this->openAiCompatibleModels(
                (string) config('ai.providers.groq.url', 'https://api.groq.com/openai/v1'),
                $apiKey
            ),
            'deepseek' => $this->openAiCompatibleModels('https://api.deepseek.com/v1', $apiKey),
            'mistral' => $this->openAiCompatibleModels('https://api.mistral.ai/v1', $apiKey),
            'openrouter' => $this->openRouterModels($apiKey),
            'xai' => $this->openAiCompatibleModels('https://api.x.ai/v1', $apiKey),
            'gemini' => $this->geminiModels($apiKey),
            'ollama' => $this->ollamaModels($apiKey),
            default => throw new \InvalidArgumentException('Unsupported provider: '.$provider),
        };
    }

    /**
     * @return array<int, string>
     */
    private function openAiCompatibleModels(string $baseUrl, string $apiKey): array
    {
        $response = Http::timeout(45)
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])
            ->acceptJson()
            ->get(rtrim($baseUrl, '/').'/models');

        $this->throwIfFailed($response, 'OpenAI-compatible');

        $json = $response->json();
        $data = $json['data'] ?? [];

        $ids = [];
        foreach ($data as $row) {
            if (is_array($row) && isset($row['id']) && is_string($row['id']) && $row['id'] !== '') {
                $ids[] = $row['id'];
            }
        }

        return $this->sortUnique($ids);
    }

    /**
     * @return array<int, string>
     */
    private function openRouterModels(string $apiKey): array
    {
        $response = Http::timeout(45)
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Referer' => (string) config('app.url', ''),
                'X-Title' => (string) config('app.name'),
            ])
            ->acceptJson()
            ->get('https://openrouter.ai/api/v1/models');

        $this->throwIfFailed($response, 'OpenRouter');

        $json = $response->json();
        $data = $json['data'] ?? [];

        $ids = [];
        foreach ($data as $row) {
            if (is_array($row) && isset($row['id']) && is_string($row['id']) && $row['id'] !== '') {
                $ids[] = $row['id'];
            }
        }

        return $this->sortUnique($ids);
    }

    /**
     * @return array<int, string>
     */
    private function geminiModels(string $apiKey): array
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models';

        $response = Http::timeout(45)
            ->acceptJson()
            ->get($url, ['key' => $apiKey]);

        $this->throwIfFailed($response, 'Gemini');

        $json = $response->json();
        $models = $json['models'] ?? [];

        $ids = [];
        foreach ($models as $row) {
            if (! is_array($row)) {
                continue;
            }
            $methods = $row['supportedGenerationMethods'] ?? [];
            if (is_array($methods) && ! in_array('generateContent', $methods, true)) {
                continue;
            }
            $name = $row['name'] ?? '';
            if (! is_string($name) || $name === '') {
                continue;
            }
            if (str_starts_with($name, 'models/')) {
                $ids[] = substr($name, strlen('models/'));
            } else {
                $ids[] = $name;
            }
        }

        return $this->sortUnique($ids);
    }

    /**
     * @return array<int, string>
     */
    private function ollamaModels(string $apiKey): array
    {
        $base = rtrim((string) config('ai.providers.ollama.url', 'http://127.0.0.1:11434'), '/');

        $request = Http::timeout(45)->acceptJson();

        if ($apiKey !== '') {
            $request = $request->withHeaders(['Authorization' => 'Bearer '.$apiKey]);
        }

        $response = $request->get($base.'/api/tags');

        $this->throwIfFailed($response, 'Ollama');

        $json = $response->json();
        $models = $json['models'] ?? [];

        $ids = [];
        foreach ($models as $row) {
            if (is_array($row) && isset($row['name']) && is_string($row['name']) && $row['name'] !== '') {
                $ids[] = $row['name'];
            }
        }

        return $this->sortUnique($ids);
    }

    /**
     * @param  array<int, string>  $ids
     * @return array<int, string>
     */
    private function sortUnique(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, fn ($v) => is_string($v) && $v !== '')));
        sort($ids, SORT_NATURAL | SORT_FLAG_CASE);

        return $ids;
    }

    private function throwIfFailed(Response $response, string $label): void
    {
        if ($response->successful()) {
            return;
        }

        $body = $response->body();
        $snippet = strlen($body) > 300 ? substr($body, 0, 300).'…' : $body;

        throw new \RuntimeException($label.' models request failed: HTTP '.$response->status().' '.$snippet);
    }
}
