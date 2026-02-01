<?php

namespace App\Listeners;

use App\Notifications\NewLoginDetected;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;

class SendNewLoginDetectedNotification
{
    private const REQUEST_FLAG = 'login_notification_fired';

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $request = request();

        if ($request?->attributes->get(self::REQUEST_FLAG)) {
            return;
        }

        $ipAddress = $request?->ip() ?? '0.0.0.0';
        $userAgent = $request?->userAgent();
        $agentDetails = $this->parseAgent($userAgent);

        if (method_exists($event->user, 'notify')) {
            $event->user->notify(new NewLoginDetected(
                ipAddress: $ipAddress,
                agent: $agentDetails,
                guard: $event->guard,
            ));
        }

        $request?->attributes->set(self::REQUEST_FLAG, true);
    }

    protected function parseAgent(?string $userAgent): array
    {
        if (! $userAgent) {
            return [];
        }

        $agent = new Agent;
        $agent->setUserAgent($userAgent);

        return [
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()) ?: null,
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'is_desktop' => $agent->isDesktop(),
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
        ];
    }
}
