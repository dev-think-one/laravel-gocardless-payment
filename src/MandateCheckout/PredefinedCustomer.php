<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class PredefinedCustomer implements \JsonSerializable
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

    public function setFamilyName(?string $familyName): static
    {
        $this->familyName = $familyName;

        $this->companyName = null;

        return $this;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        $this->givenName = null;
        $this->familyName = null;

        return $this;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function setAddressLine1(?string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function setAddressLine3(?string $addressLine3): static
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setCountryCode(?string $countryCode): static
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
