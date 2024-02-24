<?php

namespace GoCardlessPayment;

use GoCardlessPayment\Jobs\WebhookEventHandlerJob;
use Illuminate\Support\Facades\App;

class GoCardlessPayment
{
    public static bool $useRoutes = true;

    public static array $webhookJobsMap = [];

    public static function ignoreRoutes(): static
    {
        static::$useRoutes = false;

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
        $class = static::webhookJobsMap()[$key] ?? null;
        if ($class && is_subclass_of($class, WebhookEventHandlerJob::class, true)) {
            return $class;
        }

        return null;
    }

    public static function api(): Api
    {
        return App::make(Api::class);
    }
}
