<?php

namespace App\Support;

use Illuminate\Broadcasting\BroadcastException;

final class SafeBroadcast
{
    /**
     * Fire a broadcastable event without failing the HTTP request when the broadcaster is down.
     */
    public static function dispatch(object $event): void
    {
        try {
            event($event);
        } catch (BroadcastException $e) {
            report($e);
        }
    }
}
