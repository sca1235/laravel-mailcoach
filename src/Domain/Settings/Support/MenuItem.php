<?php

namespace Spatie\Mailcoach\Domain\Settings\Support;

class MenuItem
{
    public function __construct(
        public string $label = '',
        public string $url = '',
        public string $icon = '',
        public bool $isForm = false,
        public bool $isDivider = false,
        public bool $isExternal = false,
        /** @var MenuItem[] */
        public array $children = [],
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function isForm(bool $isForm = true): self
    {
        $this->isForm = $isForm;

        return $this;
    }

    public function external(): self
    {
        $this->isExternal = true;

        return $this;
    }

    public function divider(): self
    {
        $this->isDivider = true;

        return $this;
    }

    public function children(MenuItem ...$children): self
    {
        $this->children = $children;

        return $this;
    }
}
