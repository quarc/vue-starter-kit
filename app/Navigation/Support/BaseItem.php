<?php

namespace App\Navigation\Support;

use App\Navigation\Contracts\HasFeatures;
use App\Navigation\Contracts\HasPermissions;
use App\Navigation\Contracts\NavigationItem;

abstract class BaseItem implements NavigationItem
{
    use HasFeatures, HasPermissions;

    /**
     * Permission for checking
     */
    protected ?string $permission = null;

    /**
     * Feature flag for checking
     */
    protected ?string $feature = null;

    /**
     * Get the icon
     */
    public function icon(): ?string
    {
        return null;
    }

    /**
     * Get the route
     */
    public function route(): ?string
    {
        return null;
    }

    /**
     * Get the children items
     */
    public function children(): array
    {
        return [];
    }

    /**
     * Get the badge
     */
    public function badge(): string|int|null
    {
        return null;
    }

    /**
     * Get the description
     */
    public function description(): ?string
    {
        return null;
    }

    /**
     * Check if the item should be shown
     */
    public function shouldShow(): bool
    {
        // Check the permission
        if (! $this->checkPermission($this->permission)) {
            return false;
        }

        // Check the feature flag
        if (! $this->checkFeature($this->feature)) {
            return false;
        }

        // If there are children, check if at least one visible
        $children = $this->children();
        if (! empty($children)) {
            $visibleChildren = array_filter($children, fn ($child) => $child->shouldShow());

            if (empty($this->route()) && empty($visibleChildren)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the order of the item
     */
    public function order(): int
    {
        return 100;
    }

    /**
     * Convert the item to an array
     */
    public function toArray(): array
    {
        $children = array_filter($this->children(), fn ($child) => $child->shouldShow());

        usort($children, fn ($a, $b) => $a->order() <=> $b->order());

        $route = $this->route();

        if ($route && ! str_starts_with($route, 'http') && ! str_starts_with($route, '/')) {
            try {
                $route = route($route);
            } catch (\Exception $e) {
                $route = null;
            }
        }

        return [
            'name' => $this->name(),
            'label' => $this->label(),
            'icon' => $this->icon(),
            'route' => $route,
            'children' => array_map(fn ($child) => $child->toArray(), $children),
            'badge' => $this->badge(),
            'description' => $this->description(),
        ];
    }
}
