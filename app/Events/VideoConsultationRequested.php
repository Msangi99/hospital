<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoConsultationRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $doctorId,
        public int $patientId,
        public string $patientName,
        public string $roomId,
        public int $videoSessionId,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('doctor.'.$this->doctorId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'VideoConsultationRequested';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'patient_id' => $this->patientId,
            'patient_name' => $this->patientName,
            'room_id' => $this->roomId,
            'video_session_id' => $this->videoSessionId,
            'join_url' => route('doctor.video-consult', ['room' => $this->roomId]),
        ];
    }
}
