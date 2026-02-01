<?php

namespace App\Composers;

use App\Navigation\Sections\UserSettingsPage;
use Inertia\ResponseFactory;

class SettingsNavigationComposer
{
    /**
     * Compose the data for the Inertia component.
     *
     * @param  Inertia\ResponseFactory  $inertia
     * @return void
     */
    public function compose(ResponseFactory $inertia)
    {
        $section = new UserSettingsPage;

        $inertia->with('settingsNavigation', $section->toArray());
    }
}
