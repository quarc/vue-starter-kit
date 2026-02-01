<?php

namespace App\Providers;

use App\Composers\NotificationsNavigationComposer;
use App\Composers\SettingsNavigationComposer;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class InertiaComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // All settings pages will receive navigation data
        Inertia::composer('settings/*', SettingsNavigationComposer::class);

        // All notifications pages will receive navigation data
        Inertia::composer('notifications/*', NotificationsNavigationComposer::class);
    }
}
