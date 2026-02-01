<?php

namespace App\Navigation\Sections;

use App\Navigation\Support\BaseSection;
use App\Navigation\Support\Item;

class UserSettingsPage extends BaseSection
{
    public function label(): string
    {
        return __('Settings');
    }

    public function items(): array
    {
        return [
            new Item(
                label: __('Profile'),
                href: route('profile.edit'),
                icon: 'user',
            ),
            new Item(
                label: __('Password'),
                href: route('user-password.edit'),
                icon: 'lock',
            ),
            new Item(
                label: __('Two-Factor Auth'),
                href: route('two-factor.show'),
                icon: 'shield-check',
            ),
            new Item(
                label: __('Language & Region'),
                href: route('language-and-region.edit'),
                icon: 'globe',
            ),
            new Item(
                label: __('Date & Time'),
                href: route('date-time.edit'),
                icon: 'calendar-clock',
            ),
        ];
    }
}
