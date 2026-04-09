<?php

namespace App\Services;

use App\Models\Hospital;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OverpassInterpreterClient
{
    /**
     * Query OpenStreetMap (via Overpass) for hospitals, clinics, and similar facilities near a point.
     *
     * @return Collection<int, object{
     *     name: string,
     *     location: string,
     *     type: string,
     *     status: string,
     *     distance_km: float,
     *     from_osm: true,
     *     latitude: float,
     *     longitude: float,
     *     osm_type: string,
     *     osm_id: int,
     *     osm_url: string
     * }>
     */
    public function healthFacilitiesAround(float $lat, float $lng): Collection
    {
        $radius = max(500, (int) config('overpass.radius_meters', 15000));
        $maxResults = max(1, (int) config('overpass.max_results', 80));
        $url = (string) config('overpass.interpreter_url');
        $timeout = max(5, (int) config('overpass.timeout', 28));
        $userAgent = (string) config('overpass.user_agent');

        $query = $this->buildQuery($lat, $lng, $radius);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post($url, [
                    'data' => $query,
                ]);
        } catch (\Throwable $e) {
            Log::warning('overpass.request_failed', [
                'message' => $e->getMessage(),
            ]);

            return collect();
        }

        if (! $response->successful()) {
            Log::warning('overpass.http_error', [
                'status' => $response->status(),
            ]);

            return collect();
        }

        $json = $response->json();
        if (! is_array($json) || ! isset($json['elements']) || ! is_array($json['elements'])) {
            return collect();
        }

        return collect($json['elements'])
            ->map(fn (mixed $el) => is_array($el) ? $this->elementToPlace($el, $lat, $lng) : null)
            ->filter()
            ->unique(fn (object $p) => $p->osm_type.'-'.$p->osm_id)
            ->sortBy(fn (object $p) => $p->distance_km)
            ->take($maxResults)
            ->values();
    }

    private function buildQuery(float $lat, float $lng, int $radiusMeters): string
    {
        $r = $radiusMeters;

        return <<<OVERPASS
[out:json][timeout:25];
(
  nwr["amenity"="hospital"](around:{$r},{$lat},{$lng});
  nwr["amenity"="clinic"](around:{$r},{$lat},{$lng});
  nwr["amenity"="doctors"](around:{$r},{$lat},{$lng});
  nwr["healthcare"="hospital"](around:{$r},{$lat},{$lng});
  nwr["healthcare"="centre"](around:{$r},{$lat},{$lng});
  nwr["healthcare"="clinic"](around:{$r},{$lat},{$lng});
);
out center tags;
OVERPASS;
    }

    private function elementToPlace(array $el, float $userLat, float $userLng): ?object
    {
        $type = (string) ($el['type'] ?? '');
        if (! in_array($type, ['node', 'way', 'relation'], true)) {
            return null;
        }

        $plat = null;
        $plng = null;

        if ($type === 'node') {
            $plat = isset($el['lat']) ? (float) $el['lat'] : null;
            $plng = isset($el['lon']) ? (float) $el['lon'] : null;
        } elseif (isset($el['center']) && is_array($el['center'])) {
            $plat = isset($el['center']['lat']) ? (float) $el['center']['lat'] : null;
            $plng = isset($el['center']['lon']) ? (float) $el['center']['lon'] : null;
        }

        if ($plat === null || $plng === null) {
            return null;
        }

        /** @var array<string, string> $tags */
        $tags = [];
        if (isset($el['tags']) && is_array($el['tags'])) {
            foreach ($el['tags'] as $k => $v) {
                if (is_string($k) && (is_string($v) || is_numeric($v))) {
                    $tags[$k] = (string) $v;
                }
            }
        }

        $name = $tags['name'] ?? $tags['name:en'] ?? $tags['name:sw'] ?? '';
        $name = trim($name);
        if ($name === '') {
            $name = __('hospitals.osm_unnamed');
        }

        $location = $this->formatAddress($tags);
        $amenityKey = $tags['amenity'] ?? $tags['healthcare'] ?? 'healthcare';
        $amenityKey = preg_replace('/[^a-z0-9_]/i', '', (string) $amenityKey) ?: 'healthcare';
        $transKey = 'hospitals.osm_amenity_'.$amenityKey;
        $typeLabel = __($transKey);
        if ($typeLabel === $transKey) {
            $typeLabel = ucfirst(str_replace('_', ' ', (string) $amenityKey));
        }

        $id = isset($el['id']) ? (int) $el['id'] : 0;
        if ($id <= 0) {
            return null;
        }

        $distanceKm = Hospital::haversineDistanceKm($userLat, $userLng, $plat, $plng);

        return (object) [
            'name' => $name,
            'location' => $location,
            'type' => $typeLabel,
            'status' => 'OSM',
            'distance_km' => $distanceKm,
            'from_osm' => true,
            'latitude' => $plat,
            'longitude' => $plng,
            'osm_type' => $type,
            'osm_id' => $id,
            'osm_url' => 'https://www.openstreetmap.org/'.$type.'/'.$id,
        ];
    }

    /**
     * @param  array<string, string>  $tags
     */
    private function formatAddress(array $tags): string
    {
        $parts = array_filter([
            $tags['addr:street'] ?? null,
            $tags['addr:suburb'] ?? $tags['addr:district'] ?? null,
            $tags['addr:city'] ?? $tags['addr:place'] ?? null,
            $tags['addr:country'] ?? null,
        ], fn (?string $p) => $p !== null && $p !== '');

        if ($parts !== []) {
            return implode(', ', $parts);
        }

        return __('hospitals.osm_location_unknown');
    }
}
