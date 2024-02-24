<?php

return [

    'access_token' => env('GOCARDLESS_ACCESS_TOKEN'),

    'environment' => env('GOCARDLESS_ENVIRONMENT', \Illuminate\Support\Str::before(env('GOCARDLESS_ACCESS_TOKEN', \GoCardlessPro\Environment::SANDBOX), '_')),

];
