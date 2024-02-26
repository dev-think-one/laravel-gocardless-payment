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

        'mandates.created' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.customer_approval_granted' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.customer_approval_skipped' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.active' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.cancelled' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.failed' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.transferred' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.expired' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.submitted' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.resubmission_requested' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.reinstated' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.replaced' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.consumed' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,
        'mandates.blocked' => \GoCardlessPayment\Jobs\MandateEventHandlerJob::class,

    ],

    'local_customer_repositories' => [
        'eloquent' => [
            'model' => env('GOCARDLESS_ELOQUENT_LOCAL_REPO_MODEL', 'App\\Models\\User'),
            'key' => 'gocardless_id',
        ],
    ],

];
