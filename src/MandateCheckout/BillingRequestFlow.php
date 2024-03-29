<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;
use Illuminate\Support\Arr;

class BillingRequestFlow implements \JsonSerializable
{
    use Makeable;

    protected array $params = [];

    protected ?string $language = null;

    protected ?ReturnUrls $returnUrls = null;

    protected ?PrefilledCustomer $prefilledCustomer = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function returnUrls(?ReturnUrls $returnUrls = null): static
    {
        $this->returnUrls = $returnUrls;

        return $this;
    }

    public function prefilledCustomer(?PrefilledCustomer $prefilledCustomer): static
    {
        $this->prefilledCustomer = $prefilledCustomer;

        return $this;
    }

    /**
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639_language_codes
     */
    public function language(?string $language = null): static
    {
        $this->language = $language;

        return $this;
    }

    public function setBillingRequestId(string $billingResponseId): static
    {
        Arr::set($this->params, 'links.billing_request', $billingResponseId);

        return $this;
    }

    public function jsonSerialize(): array
    {
        $params = [];

        if ($this->language) {
            $params['language'] = $this->language;
        }

        if ($this->prefilledCustomer) {
            $params['prefilled_customer'] = $this->prefilledCustomer->jsonSerialize();
        }

        if ($this->returnUrls) {
            $params = array_merge($params, $this->returnUrls->jsonSerialize());
        }

        return array_merge($this->params, $params);
    }
}
