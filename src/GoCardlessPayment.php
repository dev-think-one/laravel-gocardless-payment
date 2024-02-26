<?php

namespace GoCardlessPayment;

use GoCardlessPayment\Contracts\LocalCustomerRepository;
use GoCardlessPayment\Jobs\WebhookEventHandlerJob;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class GoCardlessPayment
{
    public static string $syncMetadataKeyName = 'crm_id';

    public static bool $useRoutes = true;

    public static bool $runsMigrations = true;

    public static array $webhookJobsMap = [];

    public static function ignoreRoutes(): static
    {
        static::$useRoutes = false;

        return new static;
    }

    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    public static function webhookJobsMap(?array $map = null, $merge = true): array
    {
        $map = config('gocardless-payment.webhook_jobs');

        if (is_array($map)) {
            static::$webhookJobsMap = $merge && static::$webhookJobsMap
                ? $map + static::$webhookJobsMap : $map;
        }

        return static::$webhookJobsMap;
    }

    /**
     * @return class-string<WebhookEventHandlerJob>|null
     */
    public static function getWebhookJob(string $key): ?string
    {
        $class = Arr::get(static::webhookJobsMap(), $key);

        if ($class && is_subclass_of($class, WebhookEventHandlerJob::class, true)) {
            return $class;
        }

        return null;
    }

    public static function localCustomerRepository(): LocalCustomerRepository
    {
        return App::make(LocalCustomerRepository::class);
    }

    public static function api(): Api
    {
        return App::make(Api::class);
    }
}
