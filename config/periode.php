<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Periode API Configuration
    |--------------------------------------------------------------------------
    |
    | Base configuration for the Periode Merchant API integration.
    | All sensitive values are loaded from environment variables.
    |
    */

    'base_url' => env('PERIODE_API_URL'),

    'merchant_id' => env('PERIODE_MERCHANT_ID'),

    'api_key' => env('PERIODE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Manifests
    |--------------------------------------------------------------------------
    |
    | Manifest IDs are stored in the `manifests` database table and managed
    | from the admin panel (/admin/manifests). The booking sync reads from
    | the database, not from this config key.
    |
    */

    'manifests' => [],

    /*
    |--------------------------------------------------------------------------
    | Sync Window
    |--------------------------------------------------------------------------
    |
    | days_back   - how many past days to sync (inclusive).
    | days_forward - how many future days to sync (inclusive).
    |
    | Example:
    |   days_back = 1
    |   days_forward = 7
    | Means: yesterday, today, and the next 7 days.
    |
    */

    'sync_window' => [
        'days_back' => 7,
        'days_forward' => 7,
    ],

];

