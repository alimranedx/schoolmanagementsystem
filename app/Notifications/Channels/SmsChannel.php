<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toSms')) {
            $notification->toSms($notifiable);
            return;
        }

        Log::info('[SMS] Notification sent.', [
            'notifiable' => method_exists($notifiable, 'getKey') ? $notifiable->getKey() : null,
            'notification' => get_class($notification),
        ]);
    }
}
