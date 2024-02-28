<?php

namespace GoCardlessPayment;

use GoCardlessPayment\Contracts\LocalCustomerRepository;
use GoCardlessPayment\Jobs\WebhookHandlers\WebhookEventHandlerJob;
use GoCardlessPayment\Models\GoCardlessMandate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class GoCardlessPayment
{
    public static string $syncMetadataKeyName = 'crm_id';

    public static bool $useRoutes = true;

    public static bool $runsMigrations = true;

    public static array $webhookJobsMap = [];

    protected static array $models = [
        'mandate' => GoCardlessMandate::class,
    ];

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

    /**
     * @throws \Exception
     */
    public static function useModel(string $key, string $modelClass): static
    {
        if (! in_array($key, array_keys(static::$models))) {
            throw new \Exception(
                "Incorrect model key [{$key}], allowed keys are: ".implode(', ', array_keys(static::$models))
            );
        }
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new \Exception("Class should be a model [{$modelClass}]");
        }

        static::$models[$key] = $modelClass;

        return new static();
    }

    /**
     * @return class-string<Model|GoCardlessMandate>
     *
     * @throws \Exception
     */
    public static function modelClass(string $key): string
    {
        return static::$models[$key] ?? throw new \Exception(
            "Incorrect model key [{$key}], allowed keys are: ".implode(', ', array_keys(static::$models))
        );
    }

    /**
     * @return Model|GoCardlessMandate
     *
     * @throws \Exception
     */
    public static function model(string $key, array $attributes = []): Model
    {
        $modelClass = static::modelClass($key);

        /** @var Model $model */
        $model = new $modelClass($attributes);

        return $model;
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
