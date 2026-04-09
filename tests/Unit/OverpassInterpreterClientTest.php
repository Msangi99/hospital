<?php

use App\Services\OverpassInterpreterClient;
use Illuminate\Support\Facades\Http;

test('overpass client returns facilities when http is faked', function () {
    Http::fake([
        '*' => Http::response([
            'elements' => [
                [
                    'type' => 'node',
                    'id' => 4242,
                    'lat' => -6.821,
                    'lon' => 39.270,
                    'tags' => [
                        'amenity' => 'hospital',
                        'name' => 'OSM Test Clinic',
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = app(OverpassInterpreterClient::class);
    $rows = $client->healthFacilitiesAround(-6.82, 39.27);

    expect($rows)->toHaveCount(1);
    expect($rows->first()->name)->toBe('OSM Test Clinic');
    expect($rows->first()->latitude)->toBe(-6.821);
    expect($rows->first()->longitude)->toBe(39.270);
});
