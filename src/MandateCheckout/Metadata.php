<?php

namespace GoCardlessPayment\MandateCheckout;

use GoCardlessPayment\Makeable;

class Metadata implements \JsonSerializable
{
    use Makeable;

    public array $data = [];

    /**
     * @throws \Exception
     */
    public function add(string $key, string $value): static
    {
        if (count($this->data) >= 3) {
            throw new \Exception('Max 3 items allowed in metadata.');
        }
        if (strlen($key) > 50) {
            throw new \Exception('Key length max - 50 chars.');
        }
        if (strlen($value) > 500) {
            throw new \Exception('Value length max - 500 chars.');
        }

        $this->data[$key] = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_filter($this->data, fn ($i) => ! is_null($i));
    }
}
