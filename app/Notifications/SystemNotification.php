<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public array $extra = []
    ) {}

    public function via(object $notifiable): array
    {
        // We support mail + database out of the box.
        // SMS and Push are logged for now via custom channels; later integrate real providers.
        return ['mail', 'database', \App\Notifications\Channels\SmsChannel::class, \App\Notifications\Channels\PushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->line($this->message);

        foreach ($this->extra as $key => $value) {
            $mail->line(ucfirst((string) $key) . ': ' . (is_scalar($value) ? $value : json_encode($value)));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'extra' => $this->extra,
        ];
    }

    // SMS stub channel: logs instead of sending via provider (e.g., Twilio)
    public function toSms(object $notifiable): void
    {
        Log::info('[SMS] ' . $this->title . ' - ' . $this->message, $this->extra);
    }

    // Push stub channel: logs; later you can integrate web push or FCM/APNs
    public function toPush(object $notifiable): void
    {
        Log::info('[PUSH] ' . $this->title . ' - ' . $this->message, $this->extra);
    }
}
