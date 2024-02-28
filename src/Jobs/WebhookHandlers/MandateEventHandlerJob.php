<?php

namespace GoCardlessPayment\Jobs\WebhookHandlers;

use GoCardlessPayment\Actions\Imports\ImportMandateAction;
use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;

/**
 * General handler for all mandate related events.
 */
class MandateEventHandlerJob extends WebhookEventHandlerJob
{
    public function handle(): void
    {
        $mandateId = $this->event->links->mandate;

        $model = ImportMandateAction::make($mandateId)->execute();

        GoCardlessWebhookEventHandled::dispatch($this->event, $model);
    }
}
