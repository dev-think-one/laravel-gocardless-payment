<?php

namespace GoCardlessPayment\Jobs;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\GoCardlessPayment;
use GoCardlessPayment\Models\GoCardlessMandate;
use GoCardlessPro\Resources\Mandate;
use Illuminate\Support\Arr;

/**
 * General handler for all mandate related events.
 */
class MandateEventHandlerJob extends WebhookEventHandlerJob
{
    public function handle()
    {
        /** @var Mandate $mandate */
        $mandate = GoCardlessPayment::api()->mandates()->get($this->event->links->mandate);

        $reflect = new \ReflectionClass($mandate);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED);

        $data = [];
        foreach ($props as $prop) {
            $data[$prop->getName()] = $prop->getValue($mandate);
        }

        $model = GoCardlessMandate::updateOrCreate(
            [
                'id' => $mandate->id,
            ],
            [
                'customer_id' => Arr::get($mandate->links, 'customer'),
                'creditor_id' => Arr::get($mandate->links, 'creditor'),
                'customer_bank_account_id' => Arr::get($mandate->links, 'customer_bank_account'),
                'status' => $mandate->status,
                'reference' => $mandate->reference,
                'data' => $data,
            ]
        );

        GoCardlessWebhookEventHandled::dispatch($this->event, $mandate, $model);
    }
}
