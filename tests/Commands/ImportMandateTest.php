<?php

namespace GoCardlessPayment\Tests\Commands;

use GoCardlessPayment\Api;
use GoCardlessPayment\Models\GoCardlessMandate;
use GoCardlessPayment\Tests\Fixtures\Models\User;
use GoCardlessPayment\Tests\TestCase;
use GoCardlessPro\Services\MandatesService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;

class ImportMandateTest extends TestCase
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
    public function import_create_row()
    {
        $mandateId = Arr::get($this->mandateResponse, 'id');

        User::factory()->withCustomerId(Arr::get($this->mandateResponse, 'links.customer'))->create();

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

        $this->assertFalse(GoCardlessMandate::query()->whereKey($mandateId)->exists());

        $this->artisan("gocardless-payment:import:mandate {$mandateId}")
            ->assertSuccessful();

        $this->assertTrue(GoCardlessMandate::query()->whereKey($mandateId)->exists());
    }

    /** @test */
    public function import_override_row()
    {
        $mandateId = Arr::get($this->mandateResponse, 'id');

        User::factory()->withCustomerId(Arr::get($this->mandateResponse, 'links.customer'))->create();

        $model = GoCardlessMandate::factory()->create(['id' => Arr::get($this->mandateResponse, 'id')]);

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

        $this->artisan("gocardless-payment:import:mandate {$mandateId}")
            ->assertSuccessful();

        $model->refresh();

        $this->assertEquals(Arr::get($this->mandateResponse, 'links.customer'), $model->customer_id);
    }

    /** @test */
    public function import_error_if_customer_not_exists_in_local_storage()
    {
        $mandateId = Arr::get($this->mandateResponse, 'id');

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

        $this->artisan("gocardless-payment:import:mandate {$mandateId}")
            ->assertFailed();

        $this->assertFalse(GoCardlessMandate::query()->whereKey($mandateId)->exists());
    }
}
