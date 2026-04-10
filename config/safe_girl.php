<?php

return [

    'webhook_url' => env(
        'SAFE_GIRL_WEBHOOK_URL',
        'https://bot.tanishiahardware.co.tz/webhook/8def43bd-c44a-4aa8-b14d-d5946b175cc5'
    ),

    'webhook_timeout' => (int) env('SAFE_GIRL_WEBHOOK_TIMEOUT', 120),

];
