<?php

namespace GoCardlessPayment;

use GoCardlessPro\Client;

/**
 * @extends Client
 */
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

    public function originalClient(): Client
    {
        return $this->client;
    }

    public function __call($method, $parameters)
    {
        return $this->client->{$method}(...$parameters);
    }
}
