<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class PrefilledCustomer implements \JsonSerializable
{
    use Makeable;

    protected ?string $givenName = null;

    protected ?string $familyName = null;

    protected ?string $email = null;

    protected ?string $companyName = null;

    protected ?string $city = null;

    protected ?string $addressLine1 = null;

    protected ?string $addressLine2 = null;

    protected ?string $addressLine3 = null;

    protected ?string $region = null;

    protected ?string $postalCode = null;

    protected ?string $countryCode = null;

    public function __construct(?string $email = null, ?string $givenName = null, ?string $familyName = null)
    {
        $this->email = $email;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
    }

    public function givenName(?string $givenName): static
    {
        $this->givenName = $givenName;

        $this->companyName = null;

        return $this;
    }

    public function familyName(?string $familyName): static
    {
        $this->familyName = $familyName;

        $this->companyName = null;

        return $this;
    }

    public function email(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * WARNING: Company name overrides first and last name.
     */
    public function companyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        $this->givenName = null;
        $this->familyName = null;

        return $this;
    }

    public function city(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function addressLine1(?string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function addressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function addressLine3(?string $addressLine3): static
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    public function region(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function postalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function countryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'given_name' => $this->givenName,
            'family_name' => $this->familyName,
            'email' => $this->email,
            'company_name' => $this->companyName,
            'city' => $this->city,
            'address_line1' => $this->addressLine1,
            'address_line2' => $this->addressLine2,
            'address_line3' => $this->addressLine3,
            'region' => $this->region,
            'postal_code' => $this->postalCode,
            'country_code' => $this->countryCode,
        ], fn ($i) => ! is_null($i));
    }
}
