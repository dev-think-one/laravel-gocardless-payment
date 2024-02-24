<?php

namespace GoCardlessPayment;

use Illuminate\Support\Facades\App;

class GoCardlessPayment
{
    public static bool $useRoutes = true;

    public static function ignoreRoutes(): static
    {
        static::$useRoutes = false;

        return new static;
    }

    public function api(): Api
    {
        return App::make(Api::class);
    }
}
