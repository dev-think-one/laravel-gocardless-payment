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

    protected ?Links $links = null;

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

    public function links(?Links $links): static
    {
        $this->links = $links;

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

        if ($this->links) {
            $params['links'] = $this->links->jsonSerialize();
        }

        return array_merge($this->params, array_filter($params, fn ($i) => ! is_null($i) && (is_array($i) && ! empty($i))));
    }
}
