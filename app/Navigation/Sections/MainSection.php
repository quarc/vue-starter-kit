<?php

namespace App\Navigation\Sections;

use App\Navigation\Items\DashboardItem;
use App\Navigation\Support\BaseSection;

class MainSection extends BaseSection
{
    public function label(): string
    {
        return __('Platform');
    }

    public function items(): array
    {
        return [
            new DashboardItem,
        ];
    }

    public function order(): int
    {
        return 10;
    }
}
