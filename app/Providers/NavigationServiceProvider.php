<?php

namespace App\Providers;

use App\Navigation\NavigationManager;
use App\Navigation\Sections\MainSection;
use Illuminate\Support\ServiceProvider;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NavigationManager::class, function ($app) {
            $manager = new NavigationManager;

            $manager->registerMany($this->registerNavigationSections());

            return $manager;
        });
    }

    /**
     * Register navigation sections.
     */
    private function registerNavigationSections(): array
    {
        return [
            new MainSection,
            // new \App\Navigation\Sections\PresentationSection(),
        ];
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
