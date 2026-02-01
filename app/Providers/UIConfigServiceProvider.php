<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class UIConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share('layout', function () {
            return array_merge(config('ui.layout'), [
                'title' => config('app.name'),
                'slogan' => config('app.slogan'),
            ]);
        });

        Inertia::share('brand', function () {
            return config('ui.brand');
        });

        Inertia::share('sidebar', function () {
            return config('ui.sidebar');
        });

        Inertia::share('toggles', function () {
            return config('ui.toggles');
        });
    }
}
