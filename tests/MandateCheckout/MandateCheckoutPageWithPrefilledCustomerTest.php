<?php

namespace GoCardlessPayment\Tests\MandateCheckout;

use GoCardlessPayment\Api;
use GoCardlessPayment\MandateCheckout\BillingRequest;
use GoCardlessPayment\MandateCheckout\BillingRequestFlow;
use GoCardlessPayment\MandateCheckout\MandateCheckoutPage;
use GoCardlessPayment\MandateCheckout\MandateRequest;
use GoCardlessPayment\MandateCheckout\ReturnUrls;
use GoCardlessPayment\Tests\Fixtures\Models\User;
use GoCardlessPayment\Tests\TestCase;
use GoCardlessPro\Services\BillingRequestFlowsService;
use GoCardlessPro\Services\BillingRequestsService;
use Illuminate\Support\Arr;
use Mockery;
use Mockery\MockInterface;

class MandateCheckoutPageWithPrefilledCustomerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->count(5)->create();
        $customerId = 'CUS123FOOBAR';
        $this->user = User::factory()->withAddress()->withCustomerId($customerId)->create();
    }

    /** @test */
    public function checkout_page_url()
    {
        $this->instance(
            Api::class,
            Mockery::mock(Api::class, function (MockInterface $mock) {
                $mock->shouldReceive('billingRequests')->once()->andReturn(
                    Mockery::mock(BillingRequestsService::class, function (MockInterface $mock) {
                        $mock->shouldReceive('create')->once()
                            ->withArgs(function (array $data) {
                                $this->assertEquals('bacs', Arr::get($data, 'params.mandate_request.scheme'));
                                $this->assertEquals('when_available', Arr::get($data, 'params.mandate_request.verify'));
                                $this->assertEquals($this->user->getKey(), Arr::get($data, 'params.mandate_request.metadata.crm_id'));

                                $this->assertEquals($this->user->getKey(), Arr::get($data, 'params.metadata.crm_id'));

                                $this->assertEquals($this->user->gocardlessKey(), Arr::get($data, 'params.links.customer'));

                                return true;
                            })->andReturn(new \GoCardlessPro\Resources\BillingRequest((object) ['id' => 'REQ123FOO']));

                    })

                );

                $mock->shouldReceive('billingRequestFlows')->once()->andReturn(
                    Mockery::mock(BillingRequestFlowsService::class, function (MockInterface $mock) {
                        $mock->shouldReceive('create')->once()
                            ->withArgs(function (array $data) {
                                $this->assertEquals('REQ123FOO', Arr::get($data, 'params.links.billing_request'));

                                $this->assertEquals($this->user->gocardlessGivenName(), Arr::get($data, 'params.prefilled_customer.given_name'));
                                $this->assertEquals($this->user->gocardlessFamilyName(), Arr::get($data, 'params.prefilled_customer.family_name'));
                                $this->assertEquals($this->user->gocardlessEmail(), Arr::get($data, 'params.prefilled_customer.email'));
                                $this->assertEquals($this->user->gocardlessCity(), Arr::get($data, 'params.prefilled_customer.city'));
                                $this->assertEquals($this->user->gocardlessAddressLine1(), Arr::get($data, 'params.prefilled_customer.address_line1'));
                                $this->assertEquals($this->user->gocardlessAddressLine2(), Arr::get($data, 'params.prefilled_customer.address_line2'));
                                $this->assertEquals($this->user->gocardlessAddressLine3(), Arr::get($data, 'params.prefilled_customer.address_line3'));
                                $this->assertEquals($this->user->gocardlessRegion(), Arr::get($data, 'params.prefilled_customer.region'));
                                $this->assertEquals($this->user->gocardlessPostalCode(), Arr::get($data, 'params.prefilled_customer.postal_code'));
                                $this->assertEquals($this->user->gocardlessCountryCode(), Arr::get($data, 'params.prefilled_customer.country_code'));

                                return true;
                            })->andReturn(new \GoCardlessPro\Resources\BillingRequestFlow((object) ['authorisation_url' => 'http://foo.bar/baz']));
                    })
                );
            })
        );

        $url = MandateCheckoutPage::make(
            BillingRequest::make()
                ->mandateRequest(
                    MandateRequest::make()
                        ->scheme('bacs')
                        ->verifyWhenAvailable()
                ),
            BillingRequestFlow::make()
                ->returnUrls(ReturnUrls::make('https://success.link', 'https://exit.one'))
        )->useCustomer($this->user)->requestCheckoutUrl();

        $this->assertEquals('http://foo.bar/baz', $url);
    }
}
