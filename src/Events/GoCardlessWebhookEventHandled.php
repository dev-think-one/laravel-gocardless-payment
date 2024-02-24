<?php

namespace GoCardlessPayment\Events;

use GoCardlessPro\Resources\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoCardlessWebhookEventHandled
{
    use Dispatchable, SerializesModels;

    public Event $event;

    public array $args;

    public function __construct(Event $event, ...$args)
    {
        $this->event = $event;
        $this->args = $args;
    }
}
