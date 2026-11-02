<?php

return [
    'payments' => [
        // 将来Square実装へ差し替える前提の設定
        'driver' => env('FANCLUB_PAYMENT_DRIVER', 'dummy_square'),
        'square' => [
            'application_id' => env('SQUARE_APPLICATION_ID'),
            'access_token' => env('SQUARE_ACCESS_TOKEN'),
            'environment' => env('SQUARE_ENVIRONMENT', 'sandbox'),
            'location_id' => env('SQUARE_LOCATION_ID'),
            'webhook_signature_key' => env('SQUARE_WEBHOOK_SIGNATURE_KEY'),
        ],
    ],
];

