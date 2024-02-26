<?php

namespace GoCardlessPayment\Tests\Webhook;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\Events\GoCardlessWebhookEventReceived;
use GoCardlessPayment\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Event;

class BillingRequestCreatedHandlerJobTest extends WebhookTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        User::factory()->count(rand(1, 20))->create();
    }

    /** @test */
    public function webhook_creates_customer_value()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertNull($user->gocardlessKey());

        $this->postJson('gocardless/webhook', [
            'events' => [
                [
                    'id' => 'EV01H8ZGCY3SRV',
                    'resource_type' => 'billing_requests',
                    'action' => 'created',
                    'created_at' => '2024-02-26T12:52:40.430Z',
                    'metadata' => [
                        'crm_id' => $user->getKey(),
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

        Event::assertDispatched(function (GoCardlessWebhookEventReceived $event) {
            return $event->event->id === 'EV01H8ZGCY3SRV';
        });
        Event::assertDispatched(function (GoCardlessWebhookEventHandled $event) {
            return $event->event->id === 'EV01H8ZGCY3SRV';
        });

        $user->refresh();

        $this->assertEquals('CU0013RGDEZWTZ', $user->gocardlessKey());
    }

    /** @test */
    public function webhook_overrides_customer_value()
    {
        /** @var User $user */
        $user = User::factory()->withCustomerId('FOO-BAR')->create();

        $this->assertEquals('FOO-BAR', $user->gocardlessKey());

        $this->postJson('gocardless/webhook', [
            'events' => [
                [
                    'id' => 'EV01H8ZGCY3SRV',
                    'resource_type' => 'billing_requests',
                    'action' => 'created',
                    'created_at' => '2024-02-26T12:52:40.430Z',
                    'metadata' => [
                        'crm_id' => $user->getKey(),
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

        Event::assertDispatched(function (GoCardlessWebhookEventReceived $event) {
            return $event->event->id === 'EV01H8ZGCY3SRV';
        });
        Event::assertDispatched(function (GoCardlessWebhookEventHandled $event) {
            return $event->event->id === 'EV01H8ZGCY3SRV';
        });

        $user->refresh();

        $this->assertEquals('CU0013RGDEZWTZ', $user->gocardlessKey());
    }
}
