<?php

namespace GoCardlessPayment;

use Illuminate\Support\Facades\App;

class GoCardlessPayment
{
    public function api(): Api
    {
        return App::make(Api::class);
    }
}
