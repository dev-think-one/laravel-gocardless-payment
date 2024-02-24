<?php

namespace GoCardlessPayment;

use Illuminate\Support\Facades\Facade;

class GoCardless extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GoCardlessPayment::class;
    }
}
