<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\SosRequest;
use Illuminate\Support\Collection;

class AmbulanceSosNearestHospitalsService
{
    /**
     * Pick up to {@see $limit} nearest hospitals with coordinates; prefer those within {@see $preferWithinKm}.
     *
     * @return list<array{id: int, name: string, distance_km: float}>
     */
    public function hospitalsToAlert(SosRequest $sos, int $limit = 5, float $preferWithinKm = 120.0): array
    {
        $lat = (float) $sos->latitude;
        $lng = (float) $sos->longitude;

        /** @var Collection<int, Hospital> $candidates */
        $candidates = Hospital::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->get(['id', 'name', 'latitude', 'longitude']);

        if ($candidates->isEmpty()) {
            return [];
        }

        $ranked = $candidates
            ->map(function (Hospital $h) use ($lat, $lng) {
                $km = Hospital::haversineDistanceKm($lat, $lng, (float) $h->latitude, (float) $h->longitude);

                return [
                    'id' => (int) $h->id,
                    'name' => (string) $h->name,
                    'distance_km' => round($km, 2),
                ];
            })
            ->sortBy('distance_km')
            ->values();

        $within = $ranked->filter(fn (array $row) => $row['distance_km'] <= $preferWithinKm)->take($limit)->values();
        if ($within->isNotEmpty()) {
            return $within->all();
        }

        return $ranked->take($limit)->all();
    }
}
