<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLoginDetected extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly string $ipAddress,
        public readonly array $agent = [],
        public readonly ?string $guard = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New sign-in detected',
            'body' => 'We noticed a login from :ip using :agent',
            'icon' => 'ShieldAlert',
            'level' => 'warning',
            'meta' => [
                'ip_address' => $this->ipAddress,
                'agent' => $this->agent,
                'guard' => $this->guard,
            ],
        ];
    }
}
