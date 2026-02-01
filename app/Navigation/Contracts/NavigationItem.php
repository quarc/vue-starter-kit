<?php

namespace App\Navigation\Contracts;

interface NavigationItem
{
    /**
     * Get the unique name of the item
     */
    public function name(): string;

    /**
     * Get the label of the item for display
     */
    public function label(): string;

    /**
     * Get the icon (lucide icon name)
     */
    public function icon(): ?string;

    /**
     * Get the route
     */
    public function route(): ?string;

    /**
     * Get the children items
     *
     * @return array<NavigationItem>
     */
    public function children(): array;

    /**
     * Get the badge (for display the quantity/status)
     */
    public function badge(): string|int|null;

    /**
     * Get the description of the item
     */
    public function description(): ?string;

    /**
     * Check if the item should be shown
     */
    public function shouldShow(): bool;

    /**
     * Get the order of the item
     */
    public function order(): int;

    /**
     * Convert the item to an array for frontend
     */
    public function toArray(): array;
}
