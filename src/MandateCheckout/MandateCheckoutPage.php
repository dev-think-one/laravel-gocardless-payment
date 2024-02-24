<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\GoCardlessPayment;
use GoCardlessPayment\Makeable;
use GoCardlessPro\Client;

/**
 * Builder encapsulate all requests exchanges to create checkout page url for Mandate creation.
 *
 * @see https://developer.gocardless.com/billing-requests/setting-up-a-dd-mandate
 */
class MandateCheckoutPage
{
    use Makeable;

    protected Client $client;

    protected BillingRequest $billingRequest;

    protected BillingRequestFlow $billingRequestFlow;

    public function __construct(BillingRequest $billingRequest, BillingRequestFlow $billingRequestFlow)
    {
        $this->client = GoCardlessPayment::api()->client();

        $this->billingRequest = $billingRequest;
        $this->billingRequestFlow = $billingRequestFlow;
    }

    /**
     * @throws \GoCardlessPro\Core\Exception\InvalidStateException
     */
    protected function sendBillingRequest(): \GoCardlessPro\Resources\BillingRequest
    {
        return $this->client->billingRequests()->create([
            'params' => $this->billingRequest->jsonSerialize(),
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function sendBillingFlowRequest(string $billingResponseId): \GoCardlessPro\Resources\BillingRequestFlow
    {
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
