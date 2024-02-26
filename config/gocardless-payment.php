<?php

return [

    'access_token' => env('GOCARDLESS_ACCESS_TOKEN'),

    'environment' => env('GOCARDLESS_ENVIRONMENT', \Illuminate\Support\Str::before(env('GOCARDLESS_ACCESS_TOKEN', \GoCardlessPro\Environment::SANDBOX), '_')),

    'web' => [
        'path_prefix' => env('GOCARDLESS_WEB_PATH_PREFIX', 'gocardless'),
        'webhook_endpoint_secret' => env('GOCARDLESS_WEBHOOK_ENDPOINT_SECRET'),
    ],

    'queue' => env('GOCARDLESS_PAYMENT_QUEUE'),

    'webhook_jobs' => [
        'billing_requests.created' => \GoCardlessPayment\Jobs\BillingRequestCreatedHandlerJob::class,
        'mandates.created' => \GoCardlessPayment\Jobs\MandateCreatedHandlerJob::class,
    ],

    'local_customer_repositories' => [
        'eloquent' => [
            'model' => env('GOCARDLESS_ELOQUENT_LOCAL_REPO_MODEL', 'App\\Models\\User'),
            'key' => 'gocardless_id',
        ],
    ],

];
