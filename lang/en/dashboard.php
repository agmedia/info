<?php

return [
    'analytics' => [
        'provider' => 'Google Analytics 4',
        'unknown_source' => 'Unknown source',
        'reasons' => [
            'invalid_period' => 'The selected period is invalid, so analytics cannot currently be loaded.',
            'not_configured' => 'Google Analytics reporting has not yet been connected to the admin dashboard.',
            'invalid_configuration' => 'The GA4 Property ID is not configured correctly on the server.',
            'credentials_unavailable' => 'GA4 credentials are not available on the server.',
            'authentication_failed' => 'Google Analytics rejected the credentials. Check the service account and its access rights.',
            'report_request_failed' => 'Google Analytics did not return a report. Please try again later.',
            'unavailable' => 'Web analytics is currently unavailable.',
        ],
        'setup' => [
            'measurement' => 'enter a GA4 Measurement ID in the G-XXXXXXXXXX format to enable measurement on the public site.',
            'credentials' => 'set the GA4 Property ID and service-account JSON or credentials file path with analytics read access.',
        ],
        'notes' => [
            'new_users' => ':count new user|:count new users',
            'engagement' => 'Share of engaged sessions',
            'duration' => ':seconds s average session',
            'pages_per_session' => ':count pages per session',
        ],
        'chart' => [
            'visitors' => 'Visitors',
            'sessions' => 'Sessions',
        ],
        'devices' => [
            'desktop' => 'Desktop',
            'mobile' => 'Mobile',
            'tablet' => 'Tablet',
            'smart tv' => 'Smart TV',
            'other' => 'Other',
        ],
    ],
];
