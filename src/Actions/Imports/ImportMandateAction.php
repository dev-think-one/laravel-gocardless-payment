<?php

namespace GoCardlessPayment\Actions\Imports;

use GoCardlessPayment\GoCardlessPayment;
use GoCardlessPayment\Makeable;
use GoCardlessPayment\Models\GoCardlessMandate;
use GoCardlessPro\Resources\Mandate;

class ImportMandateAction
{
    use Makeable;

    public readonly string $mandateId;

    public function __construct(string $mandateId)
    {
        $this->mandateId = $mandateId;
    }

    public function execute(): GoCardlessMandate
    {
        /** @var Mandate $mandate */
        $mandate = GoCardlessPayment::api()->mandates()->get($this->mandateId);

        $customerId = data_get($mandate->links, 'customer');

        $localCustomer = GoCardlessPayment::localCustomerRepository()->findLocalCustomer($customerId);
        if (! $localCustomer) {
            throw new \Exception("Customer [{$customerId}] not found in local storage.");
        }

        $reflect = new \ReflectionClass($mandate);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED);

        $data = [];
        foreach ($props as $prop) {
            $data[$prop->getName()] = $prop->getValue($mandate);
        }

        return GoCardlessPayment::modelClass('mandate')::updateOrCreate(
            [
                'id' => $mandate->id,
            ],
            [
                'customer_id' => $customerId,
                'creditor_id' => data_get($mandate->links, 'creditor'),
                'customer_bank_account_id' => data_get($mandate->links, 'customer_bank_account'),
                'status' => $mandate->status,
                'reference' => $mandate->reference,
                'data' => $data,
            ]
        );
    }
}
