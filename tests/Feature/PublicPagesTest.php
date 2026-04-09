<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('about page renders migrated content and shared top navigation', function () {
    $response = $this->get(route('about'));

    $response->assertOk();
    $response->assertSee('GPRS Tracking: Active', false);
    $response->assertSee('About SemaNami', false);
    $response->assertSee('SemaNami Strategy', false);
    $response->assertSee('Universal Access', false);
});