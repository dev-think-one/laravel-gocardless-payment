<?php

namespace GoCardlessPayment\Jobs;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\GoCardlessPayment;
use Illuminate\Support\Facades\Log;

class MandateCreatedHandlerJob extends WebhookEventHandlerJob
{
    public function handle()
    {
        $mandate = GoCardlessPayment::api()->client()->mandates()->get($this->event->links->mandate);

        Log::debug(var_export($mandate, true));

        GoCardlessWebhookEventHandled::dispatch($this->event, $mandate/*TODO: add model created*/);
    }
}
