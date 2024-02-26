<?php

namespace GoCardlessPayment\Models\Traits;

trait AsGoCardlessCustomer
{
    public function getSyncKey(): ?string
    {
        return (string) $this->getKey();
    }

    public function gocardlessKeyName(): string
    {
        return config('gocardless-payment.local_customer_repositories.eloquent.key');
    }

    public function gocardlessKey(): ?string
    {
        return $this->{$this->gocardlessKeyName()};
    }

    public function hasGocardlessId(): bool
    {
        return ! is_null($this->gocardlessKey());
    }

    public function gocardlessGivenName(): ?string
    {
        return $this->first_name ?? null;
    }

    public function gocardlessFamilyName(): ?string
    {
        return $this->last_name ?? null;
    }

    public function gocardlessEmail(): ?string
    {
        return $this->email ?? null;
    }

    public function gocardlessPostalCode(): ?string
    {
        return $this->postalcode ?? null;
    }

    public function gocardlessAddressLine1(): ?string
    {
        return $this->address_line_1 ?? null;
    }

    public function gocardlessAddressLine2(): ?string
    {
        return $this->address_line_2 ?? null;
    }

    public function gocardlessAddressLine3(): ?string
    {
        return $this->address_line_3 ?? null;
    }

    public function gocardlessCity(): ?string
    {
        return $this->city ?? null;
    }

    public function gocardlessRegion(): ?string
    {
        return $this->region ?? null;
    }

    public function gocardlessCountryCode(): ?string
    {
        return $this->country_code ?? null;
    }

    public function setGocardlessKey(string $key): static
    {
        $this->fill([
            $this->gocardlessKeyName() => $key,
        ]);

        $this->save();

        return $this;
    }
}
