<?php

namespace GoCardlessPayment;

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

            $this->registerMigrations();
        }

    }

    public function register()
    {
        $this->app->bind(GoCardlessPayment::class, function (Application $app) {
            return new GoCardlessPayment();
        });
        $this->app->bind(Api::class, function (Application $app) {
            return new Api();
        });

        $this->mergeConfigFrom(__DIR__.'/../config/gocardless-payment.php', 'gocardless-payment');
    }

    protected function registerMigrations()
    {
        //
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
