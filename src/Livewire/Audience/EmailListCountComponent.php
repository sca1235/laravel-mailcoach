<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListCountComponent extends Component
{
    public int $result;

    public function mount(EmailList $emailList)
    {
        $this->result = $emailList->totalSubscriptionsCount();
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <span>…</span>
        HTML;
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
