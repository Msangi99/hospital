<?php

namespace App\Models;

use Database\Factories\HospitalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    /** @use HasFactory<HospitalFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'name',
        'location',
        'address_line',
        'city',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'type',
        'contact_phone',
        'contact_email',
        'website',
        'license_number',
        'has_emergency_services',
        'description',
        'status',
        'verification_status',
        'verified_at',
        'verified_by_user_id',
        'verification_note',
        'kyc_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'kyc_submitted_at' => 'datetime',
            'has_emergency_services' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Great-circle distance in kilometers (WGS84 sphere).
     */
    public static function haversineDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthKm = 6371.0;
        $dLat = deg2rad($toLat - $fromLat);
        $dLng = deg2rad($toLng - $fromLng);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($dLng / 2) ** 2;
        $a = min(1.0, max(0.0, $a));

        return $earthKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function workerMemberships(): HasMany
    {
        return $this->hasMany(HospitalWorkerMembership::class);
    }
}
