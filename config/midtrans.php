<?php

return [
    'enabled' => env('MIDTRANS_ENABLED', true),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'app_name' => env('APP_NAME', 'Web Yaka'),
];
