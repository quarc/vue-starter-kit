<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TwoFactorAuthenticationToggled extends Notification
{
    use Queueable;

    public function __construct(
        public readonly bool $enabled,
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
            'title' => $this->enabled
                ? 'Two-factor authentication enabled'
                : 'Two-factor authentication disabled',
            'body' => $this->enabled
                ? 'Your account now requires a second step when signing in.'
                : 'Two-factor authentication has been turned off for your account.',
            'icon' => $this->enabled ? 'ShieldCheck' : 'ShieldOff',
            'level' => $this->enabled ? 'success' : 'warning',
            'meta' => [
                'enabled' => $this->enabled,
            ],
        ];
    }
}
