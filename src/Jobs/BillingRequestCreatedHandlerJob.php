<?php

namespace GoCardlessPayment\Jobs;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\GoCardlessPayment;
use Illuminate\Support\Facades\Log;

class BillingRequestCreatedHandlerJob extends WebhookEventHandlerJob
{
    public function handle()
    {
        $metadataKeyName = GoCardlessPayment::$syncMetadataKeyName;

        $gocardlessCustomerId = $this->event->links?->customer;
        $syncKey = $this->event->metadata?->$metadataKeyName;

        if (! $gocardlessCustomerId || ! $syncKey) {
            Log::debug("BillingRequestCreatedHandlerJob event object not contains required references to customer or {$metadataKeyName}");

            return;
        }

        $localCustomer = GoCardlessPayment::localCustomerRepository()->findLocalCustomerBySyncKey($syncKey);
        if (! $localCustomer) {
            Log::debug("Local customer not found to attach key [{$metadataKeyName}:{$syncKey}]");

            return;
        }

        $localCustomer->setGocardlessKey($gocardlessCustomerId);

        GoCardlessWebhookEventHandled::dispatch($this->event, $localCustomer);
    }
}
