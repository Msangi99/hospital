<?php

namespace App\Events;

use App\Models\SosRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AmbulanceSosCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  list<array{id: int, name: string, distance_km: float}>  $hospitalsAlerted
     */
    public function __construct(
        public SosRequest $sos,
        public array $hospitalsAlerted,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        $ids = array_values(array_unique(array_map(fn (array $h) => (int) $h['id'], $this->hospitalsAlerted)));

        return array_map(
            fn (int $hospitalId) => new PrivateChannel('hospital.'.$hospitalId.'.ambulance'),
            $ids,
        );
    }

    public function broadcastAs(): string
    {
        return 'AmbulanceSosCreated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $nearest = $this->hospitalsAlerted[0] ?? null;

        return [
            'sos_request_id' => (int) $this->sos->id,
            'phone' => (string) $this->sos->phone,
            'address' => $this->sos->address,
            'latitude' => (float) $this->sos->latitude,
            'longitude' => (float) $this->sos->longitude,
            'nearest_hospital' => $nearest,
            'hospitals_alerted' => $this->hospitalsAlerted,
            'open_url' => route('ambulance.portal.dashboard'),
        ];
    }
}
