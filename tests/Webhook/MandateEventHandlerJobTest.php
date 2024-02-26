<?php

namespace GoCardlessPayment\Tests\Webhook;

use GoCardlessPayment\Api;
use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\Events\GoCardlessWebhookEventReceived;
use GoCardlessPayment\Models\GoCardlessMandate;
use GoCardlessPro\Resources\Mandate;
use GoCardlessPro\Services\MandatesService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;

class MandateEventHandlerJobTest extends WebhookTestCase
{
    protected array $mandateResponse = [
        'id' => 'MD000ZBYHHDTJT',
        'created_at' => '2024-02-26T15:37:37.316Z',
        'reference' => 'CLI-TKN7C',
        'status' => 'pending_submission',
        'scheme' => 'bacs',
        'next_possible_charge_date' => '2024-03-01',
        'payments_require_approval' => false,
        'metadata' => [
            'baz' => 'quix',
        ],
        'links' => [
            'customer_bank_account' => 'BA000Z9776P6HM',
            'creditor' => 'CR00007Q5YXWY0',
            'customer' => 'CU0013RPKWNPV6',
        ],
        'consent_parameters' => null,
        'verified_at' => null,
        'funds_settlement' => 'managed',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /** @test */
    public function webhook_creates_row()
    {
        $this->instance(
            Api::class,
            Mockery::mock(Api::class, function (MockInterface $mock) {
                $mock->shouldReceive('mandates')->andReturn(
                    Mockery::mock(MandatesService::class, function (MockInterface $mock) {
                        $mock->shouldReceive('get')
                            ->withArgs(function (string $id) {
                                return $id === Arr::get($this->mandateResponse, 'id');
                            })->andReturn(new \GoCardlessPro\Resources\Mandate((object) $this->mandateResponse));

                    })

                );
            })
        );

        $this->postJson('gocardless/webhook', [
            'events' => [
                [
                    'id' => 'EV02H8ZGCY3SRV',
                    'resource_type' => 'mandates',
                    'action' => 'created',
                    'created_at' => '2024-02-26T12:52:40.430Z',
                    'metadata' => [
                        'foo' => 'bar',
                    ],
                    'links' => [
                        'mandate' => Arr::get($this->mandateResponse, 'id'),
                    ],
                    'details' => [
                        'cause' => 'mandate_created',
                        'description' => 'Example',
                        'origin' => 'api',
                    ],
                ],
            ],
        ]);

        Event::assertDispatched(function (GoCardlessWebhookEventReceived $event) {
            return $event->event->id === 'EV02H8ZGCY3SRV';
        });
        Event::assertDispatched(function (GoCardlessWebhookEventHandled $event) {

            $this->assertCount(2, $event->args);

            $this->assertInstanceOf(Mandate::class, $event->args[0]);
            /** @var Mandate $mandateResponse */
            $mandateResponse = $event->args[0];

            $this->assertInstanceOf(GoCardlessMandate::class, $event->args[1]);
            /** @var GoCardlessMandate $mandateModel */
            $mandateModel = $event->args[1];

            $this->assertEquals(Arr::get($this->mandateResponse, 'id'), $mandateResponse->id);
            $this->assertEquals(Arr::get($this->mandateResponse, 'id'), $mandateModel->getKey());
            $this->assertTrue($mandateModel->exists);

            return $event->event->id === 'EV02H8ZGCY3SRV';
        });
    }

    /** @test */
    public function webhook_override_row()
    {
        $model = GoCardlessMandate::factory()->create(['id' => Arr::get($this->mandateResponse, 'id')]);

        $this->assertEquals(1, GoCardlessMandate::count());

        $this->instance(
            Api::class,
            Mockery::mock(Api::class, function (MockInterface $mock) {
                $mock->shouldReceive('mandates')->andReturn(
                    Mockery::mock(MandatesService::class, function (MockInterface $mock) {
                        $mock->shouldReceive('get')
                            ->withArgs(function (string $id) {
                                return $id === Arr::get($this->mandateResponse, 'id');
                            })->andReturn(new \GoCardlessPro\Resources\Mandate((object) $this->mandateResponse));

                    })

                );
            })
        );

        $this->postJson('gocardless/webhook', [
            'events' => [
                [
                    'id' => 'EV02H8ZGCY3SRV',
                    'resource_type' => 'mandates',
                    'action' => 'created',
                    'created_at' => '2024-02-26T12:52:40.430Z',
                    'metadata' => [
                        'foo' => 'bar',
                    ],
                    'links' => [
                        'mandate' => Arr::get($this->mandateResponse, 'id'),
                    ],
                    'details' => [
                        'cause' => 'mandate_created',
                        'description' => 'Example',
                        'origin' => 'api',
                    ],
                ],
            ],
        ]);

        Event::assertDispatched(function (GoCardlessWebhookEventReceived $event) {
            return $event->event->id === 'EV02H8ZGCY3SRV';
        });
        Event::assertDispatched(function (GoCardlessWebhookEventHandled $event) {

            $this->assertCount(2, $event->args);

            $this->assertInstanceOf(Mandate::class, $event->args[0]);
            /** @var Mandate $mandateResponse */
            $mandateResponse = $event->args[0];

            $this->assertInstanceOf(GoCardlessMandate::class, $event->args[1]);
            /** @var GoCardlessMandate $mandateModel */
            $mandateModel = $event->args[1];

            $this->assertEquals(Arr::get($this->mandateResponse, 'id'), $mandateResponse->id);
            $this->assertEquals(Arr::get($this->mandateResponse, 'id'), $mandateModel->getKey());
            $this->assertTrue($mandateModel->exists);

            return $event->event->id === 'EV02H8ZGCY3SRV';
        });

        $this->assertEquals(1, GoCardlessMandate::count());

        $model->refresh();

        $this->assertEquals(Arr::get($this->mandateResponse, 'links.customer'), $model->customer_id);
    }
}
