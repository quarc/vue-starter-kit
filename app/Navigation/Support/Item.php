<?php

namespace App\Navigation\Support;

use Illuminate\Support\Str;

class Item extends BaseItem
{
    public function __construct(
        public string $label,
        public string $href,
        public ?string $icon = null,
        public ?string $name = null,
    ) {}

    public function name(): string
    {
        return $this->name ?? Str::slug($this->label);
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
        return $this->href;
    }
}
