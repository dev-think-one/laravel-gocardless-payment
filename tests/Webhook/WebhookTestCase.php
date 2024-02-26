<?php

namespace GoCardlessPayment\Tests\Webhook;

use GoCardlessPayment\Tests\TestCase;

class WebhookTestCase extends TestCase
{
    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $computedSignature = hash_hmac('sha256', json_encode($data), config('gocardless-payment.web.webhook_endpoint_secret'));

        $headers['Webhook-Signature'] = $computedSignature;

        return parent::postJson($uri, $data, $headers, $options);
    }
}
