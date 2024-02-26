<?php

namespace GoCardlessPayment\Tests\Webhook;

use GoCardlessPayment\Tests\TestCase;
use Illuminate\Support\Facades\Log;

class ErrorOnWrongSecretTest extends TestCase
{
    /** @test */
    public function webhook_creates_customer_value()
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'hash_equals(): Argument #2 ($user_string) must be of type string, null given') !== false;
            });

        $this->postJson('gocardless/webhook', [
            'events' => [
                [
                    'id' => 'EV01H8ZGCY3SRV',
                    'resource_type' => 'billing_requests',
                    'action' => 'created',
                    'created_at' => '2024-02-26T12:52:40.430Z',
                    'metadata' => [
                        'crm_id' => '123',
                    ],
                    'links' => [
                        'billing_request' => 'BRQ0005RMJWKHHB',
                        'customer' => 'CU0013RGDEZWTZ',
                    ],
                    'details' => [
                        'cause' => 'billing_request_created',
                        'description' => 'This billing request has been created.',
                        'origin' => 'api',
                    ],
                ],
            ],
        ]);
    }
}
