<?php

namespace App\Navigation\Items;

use App\Navigation\Support\BaseItem;

class DashboardItem extends BaseItem
{
    public function name(): string
    {
        return 'dashboard';
    }

    public function label(): string
    {
        return __('Dashboard');
    }

    public function icon(): ?string
    {
        return 'layout-dashboard';
    }

    public function route(): ?string
    {
        return 'dashboard';
    }

    public function order(): int
    {
        return 10;
    }
}
