<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class SegmentPopulationCountComponent extends Component
{
    public ?int $result = null;

    public bool $readyToLoad = false;

    public TagSegment $segment;

    protected $listeners = [
        'segmentUpdated' => 'refreshResult',
    ];

    public function mount(TagSegment $segment)
    {
        $this->segment = $segment;
        $this->refreshResult();
    }

    public function refreshResult(): void
    {
        $this->result = $this->segment->getSubscribersQuery()->count();
    }

    public function placeholder(): string
    {
        return <<<'blade'
            <span>
                ...
            </span>
        blade;
    }

    public function render(): string
    {
        return <<<'blade'
            <span title="{{ number_format($result) }}">
                {{ Str::shortNumber($result) }}
            </span>
        blade;
    }
}
