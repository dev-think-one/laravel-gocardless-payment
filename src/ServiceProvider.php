<?php

namespace GoCardlessPayment;

use GoCardlessPayment\Contracts\LocalCustomerRepository;
use GoCardlessPayment\Repositories\LocalCustomerEloquentRepository;
use Illuminate\Contracts\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/gocardless-payment.php' => config_path('gocardless-payment.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'migrations');

            $this->registerMigrations();
        }

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gocardless-payment.php', 'gocardless-payment');

        $this->app->singletonIf(LocalCustomerRepository::class, function (Application $app) {
            return new LocalCustomerEloquentRepository(
                config('gocardless-payment.local_customer_repositories.eloquent.model'),
                config('gocardless-payment.local_customer_repositories.eloquent.key'),
            );
        });
        $this->app->bindIf(Api::class, function (Application $app) {
            return new Api(
                config('gocardless-payment.access_token'),
                config('gocardless-payment.environment'),
            );
        });
    }

    protected function registerMigrations(): void
    {
        if (GoCardlessPayment::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    protected function registerRoutes(): void
    {
        if (! GoCardlessPayment::$useRoutes) {
            return;
        }

        \Illuminate\Support\Facades\Route::group([
            'prefix' => config('gocardless-payment.web.path_prefix'),
            'as' => 'gocardless-payment.',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
