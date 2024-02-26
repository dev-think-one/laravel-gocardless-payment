<?php

namespace GoCardlessPayment\Contracts;

interface GoCardlessCustomer
{
    /**
     * Duplication form model contract, to prevent create own name.
     * Used to match local customer instance with GoCardless instance.
     */
    public function getSyncKey(): ?string;

    public function gocardlessKey(): ?string;

    public function hasGocardlessId(): bool;

    public function gocardlessGivenName(): ?string;

    public function gocardlessFamilyName(): ?string;

    public function gocardlessEmail(): ?string;

    public function gocardlessPostalCode(): ?string;

    public function gocardlessAddressLine1(): ?string;

    public function gocardlessAddressLine2(): ?string;

    public function gocardlessAddressLine3(): ?string;

    public function gocardlessCity(): ?string;

    public function gocardlessRegion(): ?string;

    public function gocardlessCountryCode(): ?string;

    public function setGocardlessKey(string $key): static;
}
