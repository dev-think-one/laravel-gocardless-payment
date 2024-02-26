<?php

namespace GoCardlessPayment\Jobs;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use Illuminate\Support\Facades\Log;

class BillingRequestCreatedHandlerJob extends WebhookEventHandlerJob
{
    public function handle()
    {
        $gocardlessCustomerId = $this->event->links?->customer;
        $crmContactId = $this->event->metadata?->crm_contact;

        if (! $gocardlessCustomerId || ! $crmContactId) {
            Log::debug('BillingRequestCreatedHandlerJob $event not contains required references to customer and crm_contact');
        }

        // TODO: add customer to model

        GoCardlessWebhookEventHandled::dispatch($this->event/*TODO: add contact model*/);
    }
}
