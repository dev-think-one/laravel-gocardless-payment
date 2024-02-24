<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class BillingRequest implements \JsonSerializable
{
    use Makeable;

    protected array $params = [];

    protected bool $fallbackEnabled = false;

    protected ?MandateRequest $mandateRequest = null;

    protected ?Metadata $metadata = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @see https://developer.gocardless.com/api-reference#billing-requests-create-a-billing-request
     */
    public function enableFallback(bool $fallbackEnabled = true): static
    {
        $this->fallbackEnabled = $fallbackEnabled;

        return $this;
    }

    public function mandateRequest(?MandateRequest $mandateRequest = null): static
    {
        $this->mandateRequest = $mandateRequest;

        return $this;
    }

    public function metadata(?Metadata $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $params = [];

        if ($this->fallbackEnabled) {
            $params['fallback_enabled'] = $this->fallbackEnabled;
        }

        if ($this->mandateRequest) {
            $params['mandate_request'] = $this->mandateRequest->jsonSerialize();
        }

        if ($this->metadata) {
            $params['metadata'] = $this->metadata->jsonSerialize();
        }

        return array_merge($this->params, $params);
    }
}
