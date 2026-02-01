<?php

namespace App\Composers;

use App\Navigation\Sections\UserNotificationsPage;
use Inertia\ResponseFactory;

class NotificationsNavigationComposer
{
    /**
     * Compose the data for the Inertia component.
     *
     * @param  Inertia\ResponseFactory  $inertia
     * @return void
     */
    public function compose(ResponseFactory $inertia)
    {
        $section = new UserNotificationsPage;

        $inertia->with('notificationsNavigation', $section->toArray());
    }
}
