<?php

namespace GoCardlessPayment;

use GoCardlessPro\Client;

class Api
{
    protected Client $client;

    public function __construct(string $accessToken, string $environment)
    {
        $this->client = new Client([
            'access_token' => $accessToken,
            'environment' => $environment,
        ]);
    }

    public function client(): Client
    {
        return $this->client;
    }
}
