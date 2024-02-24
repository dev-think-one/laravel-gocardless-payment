<?php

namespace GoCardlessPayment\Http\Controllers;

use GoCardlessPayment\Events\GoCardlessWebhookEventReceived;
use GoCardlessPayment\GoCardlessPayment;
use GoCardlessPro\Resources\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $jobClass = GoCardlessPayment::getWebhookJob("{$event->resource_type}-{$event->action}");
        if ($jobClass) {
            $jobClass::dispatch($event)->onQueue(config('gocardless-payment.queue'));
        }
    }
}
