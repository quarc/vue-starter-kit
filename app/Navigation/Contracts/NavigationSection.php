<?php

namespace App\Navigation\Contracts;

interface NavigationSection
{
    /**
     * Get the label of the section
     */
    public function label(): string;

    /**
     * Get the items of the section
     *
     * @return array<NavigationItem>
     */
    public function items(): array;

    /**
     * Check if the section should be shown
     */
    public function shouldShow(): bool;

    /**
     * Get the order of the section
     */
    public function order(): int;
}
