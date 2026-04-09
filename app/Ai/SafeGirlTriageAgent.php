<?php

namespace App\Ai;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Promptable;

class SafeGirlTriageAgent implements Agent, Conversational
{
    use Promptable;

    /**
     * @param  array<int, array{role:string, content:string}>  $history
     */
    public function __construct(
        private readonly array $history,
        private readonly string $systemPrompt,
    ) {}

    public function instructions(): string
    {
        return $this->systemPrompt;
    }

    /**
     * @return Message[]
     */
    public function messages(): iterable
    {
        $messages = [];

        foreach ($this->history as $item) {
            $role = (string) ($item['role'] ?? 'user');
            $content = trim((string) ($item['content'] ?? ''));

            if ($content === '') {
                continue;
            }

            if ($role === 'assistant') {
                $messages[] = new AssistantMessage($content);
            } else {
                $messages[] = new UserMessage($content);
            }
        }

        return $messages;
    }
}