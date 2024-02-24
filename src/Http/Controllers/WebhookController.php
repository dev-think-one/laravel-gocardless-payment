<?php

namespace GoCardlessPayment\Http\Controllers;

use GoCardlessPayment\Events\GoCardlessWebhookEventHandled;
use GoCardlessPayment\Events\GoCardlessWebhookEventReceived;
use GoCardlessPayment\GoCardless;
use GoCardlessPro\Resources\Event;
use GoCardlessPro\Resources\Mandate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    /**
     * @see https://developer.gocardless.com/getting-started/staying-up-to-date-with-webhooks/#processing_events
     */
    public function handleWebhook(Request $request)
    {
        $requestSignature = $request->header('Webhook-Signature');

        try {
            /** @var Event[] $events */
            /** @psalm-suppress UndefinedDocblockClass */
            $events = \GoCardlessPro\Webhook::parse(
                $request->getContent(),
                $requestSignature,
                config('gocardless-payment.web.webhook_endpoint_secret')
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            throw $e;
        }

        foreach ($events as $event) {
            $this->handleEvent($event);
        }

        return new Response;
    }

    protected function handleEvent(Event $event): void
    {
        GoCardlessWebhookEventReceived::dispatch($event);

        $method = 'handle'.Str::ucfirst(Str::camel($event->resource_type)).Str::ucfirst(Str::camel($event->action));

        if (method_exists($this, $method)) {

            try {
                $response = $this->{$method}($event);

                GoCardlessWebhookEventHandled::dispatch($event, ...$response);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    protected function handleMandatesCreated(Event $event): array
    {
        /** @var Mandate $mandate */
        $mandate = GoCardless::api()->client()->mandates()->get($event->links->mandate);

        Log::debug(var_export($mandate, true));

        return [
            $mandate,
            // TODO: add crated model
        ];
    }
}
