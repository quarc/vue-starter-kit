<?php

namespace App\Listeners;

use App\Notifications\TwoFactorAuthenticationToggled;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;

class SendTwoFactorEnabledNotification
{
    /**
     * Handle the event.
     */
    public function handle(TwoFactorAuthenticationConfirmed $event): void
    {
        if (method_exists($event->user, 'notify')) {
            $event->user->notify(new TwoFactorAuthenticationToggled(true));
        }
    }
}
