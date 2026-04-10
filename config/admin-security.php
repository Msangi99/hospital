<?php

return [
    'require_superadmin_mfa' => env('ADMIN_REQUIRE_SUPERADMIN_MFA', true),

    'ip_allowlist' => [
        'enabled' => env('ADMIN_IP_ALLOWLIST_ENABLED', false),
        'allowed_ips' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('ADMIN_IP_ALLOWLIST', ''))
        ))),
    ],
];

