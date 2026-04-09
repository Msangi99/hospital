<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenStreetMap Overpass API
    |--------------------------------------------------------------------------
    |
    | Public interpreter endpoint (fair-use: identify your app via user_agent).
    | @see https://wiki.openstreetmap.org/wiki/Overpass_API
    |
    */

    'interpreter_url' => env('OVERPASS_INTERPRETER_URL', 'https://overpass-api.de/api/interpreter'),

    'timeout' => (int) env('OVERPASS_TIMEOUT', 28),

    'radius_meters' => (int) env('OVERPASS_RADIUS_METERS', 15000),

    'max_results' => (int) env('OVERPASS_MAX_RESULTS', 80),

    'user_agent' => env('OVERPASS_USER_AGENT', 'SemaNami/1 (+https://www.openstreetmap.org/copyright)'),

];
