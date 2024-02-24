<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class MandateRequest implements \JsonSerializable
{
    use Makeable;

    protected array $params = [];

    protected ?string $authorisationSource = null;

    protected ?string $currencyCode = null;

    protected ?string $verify = null;

    protected ?string $scheme = null;

    protected ?Metadata $metadata = null;

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @see https://www.moderntreasury.com/learn/sec-codes
     */
    public function authorisationSource(?string $authorisationSource): static
    {
        $this->authorisationSource = $authorisationSource;

        return $this;
    }

    /**
     * @see https://en.wikipedia.org/wiki/ISO_4217#Active_codes
     */
    public function currency(?string $currencyCode): static
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function metadata(?Metadata $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * A bank payment scheme. Currently, “ach”, “autogiro”, “bacs”, “becs”, “becs_nz”, “betalingsservice”,
     *  “faster_payments”, “pad”, “pay_to” and “sepa_core” are supported.
     */
    public function scheme(?string $scheme): static
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function verify(?string $verify): static
    {
        $this->verify = $verify;

        return $this;
    }

    public function verifyWhenAvailable(): static
    {
        return $this->verify('when_available');
    }

    public function jsonSerialize(): array
    {
        $params = array_filter([
            'authorisation_source' => $this->authorisationSource,
            'currency' => $this->currencyCode,
            'scheme' => $this->scheme,
            'verify' => $this->verify,
        ], fn ($i) => ! is_null($i));

        if ($this->metadata) {
            $params['metadata'] = $this->metadata->jsonSerialize();
        }

        return array_merge($this->params, $params);
    }
}
