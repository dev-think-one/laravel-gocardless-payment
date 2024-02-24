<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

/**
 * @see https://developer.gocardless.com/api-reference#billing-request-flows-create-a-billing-request-flow
 */
class ReturnUrls implements \JsonSerializable
{
    use Makeable;

    public readonly string $successUrl;

    public ?string $cancelUrl = null;

    public function __construct(string $successUrl, ?string $cancelUrl = null)
    {
        $this->successUrl = $successUrl;
        $this->cancelUrl = $cancelUrl;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'redirect_uri' => $this->successUrl,
            'exit_uri' => $this->successUrl ?: $this->cancelUrl,
        ], fn ($i) => ! is_null($i));
    }
}
