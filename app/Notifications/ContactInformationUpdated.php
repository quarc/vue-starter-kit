<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactInformationUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly array $changedFields,
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
            'title' => 'Account details updated',
            'body' => 'Your account information was updated: :fields',
            'icon' => 'UserCog',
            'level' => 'info',
            'meta' => [
                'fields' => $this->changedFields,
            ],
        ];
    }
}
