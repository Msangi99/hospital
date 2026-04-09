<?php

use App\Models\Hospital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('hospitals index lists facilities from database', function () {
    Hospital::factory()->create(['name' => 'Alpha Test Hospital', 'location' => 'Dar']);

    $response = $this->get(route('hospitals'));

    $response->assertOk();
    $response->assertSee('Alpha Test Hospital', false);
    expect($response->viewData('autoGeoEnabled'))->toBeTrue();
    $response->assertSee('semanami_hospitals_geo_v1', false);
});

test('hospitals nogeo ignores coordinates and skips distance sort', function () {
    Http::fake([
        '*' => Http::response(['elements' => []], 200),
    ]);

    Hospital::query()->delete();
    Hospital::factory()->create([
        'name' => 'Zebra Hospital',
        'location' => 'Dar',
        'latitude' => -6.8231,
        'longitude' => 39.2693,
    ]);
    Hospital::factory()->create([
        'name' => 'Alpha Clinic',
        'location' => 'Dar',
        'latitude' => -6.8231,
        'longitude' => 39.2693,
    ]);

    $response = $this->get(route('hospitals', [
        'lat' => -6.82,
        'lng' => 39.27,
        'nogeo' => '1',
    ]));

    $response->assertOk();
    $cards = $response->viewData('hospitalCards');
    expect($cards->first()->name)->toBe('Alpha Clinic');
    expect($response->viewData('autoGeoEnabled'))->toBeFalse();
});

test('hospitals index sorts by distance when lat and lng are valid', function () {
    Http::fake([
        '*' => Http::response(['elements' => []], 200),
    ]);

    Hospital::query()->delete();

    Hospital::factory()->create([
        'name' => 'Far Node',
        'latitude' => -3.3869,
        'longitude' => 36.6829,
    ]);

    Hospital::factory()->create([
        'name' => 'Near Node',
        'latitude' => -6.8231,
        'longitude' => 39.2693,
    ]);

    $response = $this->get(route('hospitals', [
        'lat' => -6.82,
        'lng' => 39.27,
    ]));

    $response->assertOk();
    expect($response->viewData('autoGeoEnabled'))->toBeFalse();
    $cards = $response->viewData('hospitalCards');
    expect($cards->first()->name)->toBe('Near Node');
    expect($cards->last()->name)->toBe('Far Node');
});

test('hospitals merges openstreetmap facilities from overpass when geo is on', function () {
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
                        'addr:city' => 'Dar es Salaam',
                    ],
                ],
            ],
        ], 200),
    ]);

    Hospital::query()->delete();
    Hospital::factory()->create([
        'name' => 'Partner X',
        'latitude' => -6.8231,
        'longitude' => 39.2693,
    ]);

    $response = $this->get(route('hospitals', [
        'lat' => -6.82,
        'lng' => 39.27,
    ]));

    $response->assertOk();
    expect($response->viewData('autoGeoEnabled'))->toBeFalse();
    $response->assertSee('OSM Test Clinic', false);
    $response->assertSee('Partner X', false);
    expect($response->viewData('overpassResultCount'))->toBe(1);

    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return str_contains($request->url(), 'overpass-api.de/api/interpreter')
            && str_contains($request->body(), 'amenity');
    });
});

test('home shows network hospitals when seeded', function () {
    Hospital::factory()->create(['name' => 'Home Listed Center']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Home Listed Center', false);
});
