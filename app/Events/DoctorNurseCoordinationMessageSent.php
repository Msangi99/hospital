<?php

namespace App\Events;

use App\Models\DoctorNurseCoordinationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DoctorNurseCoordinationMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DoctorNurseCoordinationMessage $message,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('doctor-nurse-coordination.'.$this->message->coordination_chat_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DoctorNurseCoordinationMessageSent';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->message->loadMissing('user:id,name');

        $chatId = (int) $this->message->coordination_chat_id;

        return [
            'id' => $this->message->id,
            'coordination_chat_id' => $chatId,
            'user_id' => $this->message->user_id,
            'user_name' => (string) ($this->message->user?->name ?? ''),
            'body' => (string) ($this->message->body ?? ''),
            'created_at' => $this->message->created_at?->toIso8601String(),
            'has_attachment' => $this->message->hasAttachment(),
            'attachment_kind' => $this->message->attachment_kind,
            'attachment_name' => $this->message->attachment_original_name,
            'attachment_url' => $this->message->hasAttachment()
                ? route('portal.coordination.messages.attachment', [
                    'chat' => $chatId,
                    'message' => $this->message->id,
                ])
                : null,
        ];
    }
}
