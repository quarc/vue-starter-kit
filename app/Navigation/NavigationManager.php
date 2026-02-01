<?php

namespace App\Navigation;

use App\Navigation\Contracts\NavigationSection;

class NavigationManager
{
    /**
     * Registered sections
     *
     * @var array<NavigationSection>
     */
    protected array $sections = [];

    /**
     * Register a section
     */
    public function register(NavigationSection $section): self
    {
        $this->sections[] = $section;

        return $this;
    }

    /**
     * Register an array of sections
     *
     * @param  array<NavigationSection>  $sections
     */
    public function registerMany(array $sections): self
    {
        foreach ($sections as $section) {
            $this->register($section);
        }

        return $this;
    }

    /**
     * Get all registered sections
     *
     * @return array<NavigationSection>
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Get the visible sections
     *
     * @return array<NavigationSection>
     */
    public function getVisibleSections(): array
    {
        return array_filter($this->sections, fn ($section) => $section->shouldShow());
    }

    /**
     * Convert the navigation to an array for frontend
     */
    public function toArray(): array
    {
        $sections = $this->getVisibleSections();

        usort($sections, fn ($a, $b) => $a->order() <=> $b->order());

        return [
            'sidebar' => array_map(fn ($section) => $section->toArray(), $sections),
        ];
    }
}
