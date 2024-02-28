<?php

return [

    'access_token' => env('GOCARDLESS_ACCESS_TOKEN'),

    'environment' => env('GOCARDLESS_ENVIRONMENT', \Illuminate\Support\Str::before(env('GOCARDLESS_ACCESS_TOKEN', \GoCardlessPro\Environment::SANDBOX), '_')),

    'web' => [
        'path_prefix' => env('GOCARDLESS_WEB_PATH_PREFIX', 'gocardless'),
        'webhook_endpoint_secret' => env('GOCARDLESS_WEBHOOK_ENDPOINT_SECRET'),
    ],

    'queue' => env('GOCARDLESS_PAYMENT_QUEUE'),

    'tables' => [
        'mandates' => 'gocardless_mandates',
    ],

    'webhook_jobs' => [
        'billing_requests.created' => \GoCardlessPayment\Jobs\WebhookHandlers\BillingRequestCreatedHandlerJob::class,

        'mandates.created' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.customer_approval_granted' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.customer_approval_skipped' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.active' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.cancelled' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.failed' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.transferred' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.expired' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.submitted' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.resubmission_requested' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.reinstated' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.replaced' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.consumed' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,
        'mandates.blocked' => \GoCardlessPayment\Jobs\WebhookHandlers\MandateEventHandlerJob::class,

    ],

    'local_customer_repositories' => [
        'eloquent' => [
            'model' => env('GOCARDLESS_ELOQUENT_LOCAL_REPO_MODEL', 'App\\Models\\User'),
            'key' => 'gocardless_id',
        ],
    ],

];
