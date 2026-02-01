<?php

return [

    /*
    переклади все на англійську мову
    |--------------------------------------------------------------------------
    | General layout modes
    |--------------------------------------------------------------------------
    |
    | What you most often want to change:
    | - where navigation (sidebar or topbar)
    | - app_width (container or full)
    |
    */

    'layout' => [

        // Where the main navigation lives:
        // - sidebar  — classic left sidebar
        // - topbar   — top menu (header)
        'navigation' => 'sidebar',

        // Main content width:
        // - container  — centered container, max-width
        // - full       — full width
        'app_width' => 'full',

        // Settings navigation layout:
        // - vertical   — classic sidebar
        // - horizontal — top tabs
        'settings_navigation' => 'vertical',
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding: logo, name, what to show
    |--------------------------------------------------------------------------
    */

    'brand' => [

        // Show brand icon
        'show_icon' => true,

        // Show brand name
        'show_name' => true,

        // Theme setting
        // - default
        // - shadcn
        // - fluent
        // - sharp
        // - corporate
        // - claymorphism
        // - doom-64
        // - modern-minimal
        // - neo-brutalism
        'theme' => 'shadcn',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar settings
    |--------------------------------------------------------------------------
    */

    'sidebar' => [

        // Sidebar variant:
        // - sidebar
        // - floating
        // - inset
        'variant' => 'inset',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI toggles
    |--------------------------------------------------------------------------
    */

    'toggles' => [

        // Show slogan
        'show_slogan' => true,
    ],
];
