<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoSession extends Model
{
    /** Seconds after start during which the doctor is considered "ringing" if they have not opened the room. */
    public const DOCTOR_RING_GRACE_SECONDS = 120;

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'room_id',
        'start_time',
        'doctor_joined_at',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'doctor_joined_at' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Open session, doctor has not opened the consult URL yet, and the patient request is still within the ring window.
     */
    public function doctorVideoRingIsActive(): bool
    {
        if ($this->doctor_joined_at !== null || $this->end_time !== null) {
            return false;
        }

        return (bool) ($this->start_time?->gt(now()->subSeconds(self::DOCTOR_RING_GRACE_SECONDS)));
    }
}
