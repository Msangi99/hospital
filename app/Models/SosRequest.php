<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SosRequest extends Model
{
    public const STATUS_RECEIVED = 'RECEIVED';

    public const STATUS_DISPATCHED = 'DISPATCHED';

    public const STATUS_EN_ROUTE = 'EN_ROUTE';

    public const STATUS_ON_SCENE = 'ON_SCENE';

    public const STATUS_TRANSPORTING = 'TRANSPORTING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'user_id',
        'phone',
        'latitude',
        'longitude',
        'address',
        'ip_address',
        'user_agent',
        'nearest_hospital_id',
        'alerted_hospital_ids',
        'status',
        'assigned_user_id',
        'dispatched_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'alerted_hospital_ids' => 'array',
            'dispatched_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function nearestHospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'nearest_hospital_id');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }

    public static function crewProgressOrder(): array
    {
        return [
            self::STATUS_DISPATCHED,
            self::STATUS_EN_ROUTE,
            self::STATUS_ON_SCENE,
            self::STATUS_TRANSPORTING,
            self::STATUS_COMPLETED,
        ];
    }
}
