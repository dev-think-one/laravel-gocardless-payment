<?php

namespace GoCardlessPayment\Jobs;

use GoCardlessPro\Resources\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class WebhookEventHandlerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
