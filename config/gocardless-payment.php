<?php

return [

    'access_token' => env('GOCARDLESS_ACCESS_TOKEN'),

    'environment' => env('GOCARDLESS_ENVIRONMENT', \Illuminate\Support\Str::before(env('GOCARDLESS_ACCESS_TOKEN', \GoCardlessPro\Environment::SANDBOX), '_')),

    'web' => [
        'path_prefix' => env('GOCARDLESS_WEB_PATH_PREFIX', 'gocardless'),
        'webhook_endpoint_secret' => env('GOCARDLESS_WEBHOOK_ENDPOINT_SECRET'),
    ],

];
