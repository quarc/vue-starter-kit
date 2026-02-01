<?php

namespace App\Navigation\Support;

use App\Navigation\Contracts\HasFeatures;
use App\Navigation\Contracts\HasPermissions;
use App\Navigation\Contracts\NavigationSection;

abstract class BaseSection implements NavigationSection
{
    use HasFeatures, HasPermissions;

    /**
     * Check if the section should be shown
     */
    public function shouldShow(): bool
    {
        return count($this->getVisibleItems()) > 0;
    }

    /**
     * Get the order of the section
     */
    public function order(): int
    {
        return 100;
    }

    /**
     * Get the visible items
     */
    protected function getVisibleItems(): array
    {
        return array_filter($this->items(), function ($item) {
            return $item->shouldShow();
        });
    }

    /**
     * Convert the section to an array
     */
    public function toArray(): array
    {
        $items = $this->getVisibleItems();

        usort($items, fn ($a, $b) => $a->order() <=> $b->order());

        return [
            'label' => $this->label(),
            'items' => array_map(fn ($item) => $item->toArray(), $items),
        ];
    }
}
