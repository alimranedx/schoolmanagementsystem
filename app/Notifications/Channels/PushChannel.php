<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PushChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toPush')) {
            $notification->toPush($notifiable);
            return;
        }

        Log::info('[PUSH] Notification sent.', [
            'notifiable' => method_exists($notifiable, 'getKey') ? $notifiable->getKey() : null,
            'notification' => get_class($notification),
        ]);
    }
}
