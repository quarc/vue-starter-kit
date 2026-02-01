<?php

namespace App\Navigation\Items;

use App\Navigation\Support\BaseItem;

class DynamicItem extends BaseItem
{
    public function __construct(
        protected string $name,
        protected string $label,
        protected ?string $icon = null,
        protected ?string $route = null,
        protected array $children = [],
        protected ?string $permission = null,
        protected ?string $feature = null,
        protected string|int|null $badge = null,
        protected ?string $description = null,
        protected int $order = 100,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function route(): ?string
    {
        return $this->route;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function badge(): string|int|null
    {
        return $this->badge;
    }

    public function order(): int
    {
        return $this->order;
    }
}
