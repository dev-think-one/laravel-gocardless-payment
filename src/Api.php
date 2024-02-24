<?php

namespace GoCardlessPayment;

use GoCardlessPro\Client;

class Api
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'access_token' => config('gocardless-payment.access_token'),
            'environment' => config('gocardless-payment.environment'),
        ]);
    }

    public function client(): Client
    {
        return $this->client;
    }
}
