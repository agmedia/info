<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 Data API
    |--------------------------------------------------------------------------
    |
    | GA4_PROPERTY_ID is the numeric property ID, not the G- measurement ID.
    | Credentials may be supplied either as an inline service-account JSON
    | string or as a path to a JSON file. Inline JSON takes precedence.
    |
    */

    'property_id' => env('GA4_PROPERTY_ID'),

    'credentials_json' => env('GA4_SERVICE_ACCOUNT_JSON'),

    'credentials_path' => env(
        'GA4_SERVICE_ACCOUNT_CREDENTIALS_PATH',
        env('GOOGLE_APPLICATION_CREDENTIALS')
    ),

    'data_api_base_url' => env(
        'GA4_DATA_API_BASE_URL',
        'https://analyticsdata.googleapis.com/v1beta'
    ),

    'oauth_token_url' => env(
        'GA4_OAUTH_TOKEN_URL',
        'https://oauth2.googleapis.com/token'
    ),

    'oauth_scope' => 'https://www.googleapis.com/auth/analytics.readonly',

    'cache_ttl_seconds' => (int) env('GA4_CACHE_TTL_SECONDS', 900),

    'timeout_seconds' => (int) env('GA4_HTTP_TIMEOUT_SECONDS', 15),

    'top_pages_limit' => (int) env('GA4_TOP_PAGES_LIMIT', 10),

    'breakdown_limit' => (int) env('GA4_BREAKDOWN_LIMIT', 10),
];
