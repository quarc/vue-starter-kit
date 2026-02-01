<?php

namespace App\Listeners;

use App\Notifications\TwoFactorAuthenticationToggled;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

class SendTwoFactorDisabledNotification
{
    /**
     * Handle the event.
     */
    public function handle(TwoFactorAuthenticationDisabled $event): void
    {
        if (method_exists($event->user, 'notify')) {
            $event->user->notify(new TwoFactorAuthenticationToggled(false));
        }
    }
}
