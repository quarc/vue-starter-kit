<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SessionsTerminated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly int $count,
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
            'title' => 'Sessions terminated',
            'body' => 'You signed out :count other session|You signed out :count other sessions',
            'icon' => 'LogOut',
            'level' => 'info',
            'meta' => [
                'count' => $this->count,
            ],
        ];
    }
}
