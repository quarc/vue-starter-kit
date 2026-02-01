<?php

namespace App\Navigation\Sections;

use App\Navigation\Support\BaseSection;
use App\Navigation\Support\Item;

class UserNotificationsPage extends BaseSection
{
    public function label(): string
    {
        return __('Notifications');
    }

    public function items(): array
    {
        return [
            new Item(
                label: __('Notifications'),
                href: route('notifications.index'),
                icon: 'bell',
            ),
            new Item(
                label: __('Archive'),
                href: route('notifications.archive'),
                icon: 'archive',
            ),
        ];
    }
}
