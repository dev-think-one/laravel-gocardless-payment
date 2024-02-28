<?php

namespace GoCardlessPayment\Tests;

use GoCardlessPayment\GoCardlessPayment;

class ApiTest extends TestCase
{
    /** @test */
    public function api_class_is_decorator()
    {
        $client = GoCardlessPayment::api();

        $this->assertInstanceOf(\GoCardlessPro\Client::class, $client->originalClient());

        $this->assertInstanceOf(\GoCardlessPro\Services\WebhooksService::class, $client->webhooks());
    }
}
