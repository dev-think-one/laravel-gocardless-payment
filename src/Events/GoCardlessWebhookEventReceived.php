<?php

namespace GoCardlessPayment\Events;

use GoCardlessPro\Resources\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoCardlessWebhookEventReceived
{
    use Dispatchable, SerializesModels;

    public Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
