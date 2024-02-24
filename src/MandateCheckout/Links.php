<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class Links implements \JsonSerializable
{
    use Makeable;

    public array $data = [];

    public function add(string $key, string $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function addCreditor(string $value): static
    {
        return $this->add('creditor', $value);
    }

    public function addCustomer(string $value): static
    {
        return $this->add('customer', $value);
    }

    public function jsonSerialize(): array
    {
        return array_filter($this->data, fn ($i) => ! is_null($i));
    }
}
