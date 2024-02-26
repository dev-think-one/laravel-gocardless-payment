<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Api;
use GoCardlessPayment\Contracts\GoCardlessCustomer;
use GoCardlessPayment\GoCardlessPayment;
use GoCardlessPayment\Makeable;
use Illuminate\Support\Arr;

/**
 * Builder encapsulate all requests exchanges to create checkout page url for Mandate creation.
 *
 * @see https://developer.gocardless.com/billing-requests/setting-up-a-dd-mandate
 */
class MandateCheckoutPage
{
    use Makeable;

    protected Api $client;

    protected ?GoCardlessCustomer $gocardlessCustomer = null;

    protected BillingRequest $billingRequest;

    protected BillingRequestFlow $billingRequestFlow;

    public function __construct(BillingRequest $billingRequest, BillingRequestFlow $billingRequestFlow)
    {
        $this->client = GoCardlessPayment::api();

        $this->billingRequest = $billingRequest;
        $this->billingRequestFlow = $billingRequestFlow;
    }

    public function useCustomer(GoCardlessCustomer $gocardlessCustomer): static
    {
        $this->gocardlessCustomer = $gocardlessCustomer;

        return $this;
    }

    /**
     * @throws \GoCardlessPro\Core\Exception\InvalidStateException
     */
    protected function sendBillingRequest(): \GoCardlessPro\Resources\BillingRequest
    {
        $params = $this->billingRequest->jsonSerialize();
        if ($this->gocardlessCustomer) {
            $keyName = GoCardlessPayment::$syncMetadataKeyName;

            Arr::set($params, "metadata.{$keyName}", $this->gocardlessCustomer->getSyncKey());
            if ($gocardlessKey = $this->gocardlessCustomer->gocardlessKey()) {
                Arr::set($params, 'links.customer', $gocardlessKey);
            }

            // Additionally we provide sync keys in mandate and payment requests for more informational object
            // inside GoCardless dashboard
            if (Arr::has($params, 'mandate_request')) {
                Arr::set($params, "mandate_request.metadata.{$keyName}", $this->gocardlessCustomer->getSyncKey());
            }
            if (Arr::has($params, 'payment_request')) {
                Arr::set($params, "payment_request.metadata.{$keyName}", $this->gocardlessCustomer->getSyncKey());
            }
        }

        return $this->client->billingRequests()->create([
            'params' => $params,
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function sendBillingFlowRequest(string $billingResponseId): \GoCardlessPro\Resources\BillingRequestFlow
    {
        if ($this->gocardlessCustomer) {
            $this->billingRequestFlow->prefilledCustomer(
                PrefilledCustomer::make()
                    ->givenName($this->gocardlessCustomer->gocardlessGivenName())
                    ->familyName($this->gocardlessCustomer->gocardlessFamilyName())
                    ->email($this->gocardlessCustomer->gocardlessEmail())
                    ->postalCode($this->gocardlessCustomer->gocardlessPostalCode())
                    ->addressLine1($this->gocardlessCustomer->gocardlessAddressLine1())
                    ->addressLine2($this->gocardlessCustomer->gocardlessAddressLine2())
                    ->addressLine3($this->gocardlessCustomer->gocardlessAddressLine3())
                    ->city($this->gocardlessCustomer->gocardlessCity())
                    ->region($this->gocardlessCustomer->gocardlessRegion())
                    ->countryCode($this->gocardlessCustomer->gocardlessCountryCode())
            );
        }

        return $this->client->billingRequestFlows()->create([
            'params' => $this->billingRequestFlow->setBillingRequestId($billingResponseId)->jsonSerialize(),
        ]);
    }

    /**
     * @throws \GoCardlessPro\Core\Exception\InvalidStateException|\Exception
     */
    protected function sendRequest(): \GoCardlessPro\Resources\BillingRequestFlow
    {
        $response = $this->sendBillingRequest();

        return $this->sendBillingFlowRequest($response->id);
    }

    /**
     * @throws \GoCardlessPro\Core\Exception\InvalidStateException
     */
    public function requestCheckoutUrl(): string
    {
        return $this->sendRequest()->authorisation_url;
    }
}
