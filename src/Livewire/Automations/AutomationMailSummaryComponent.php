<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailSummaryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    protected Collection $stats;

    protected int $limit;

    public int $failedSendsCount = 0;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('view', $this->mail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations'));
    }

    public function render(): View
    {
        $this->stats = $this->createStats();

        $this->limit = (ceil(max($this->stats->max('opens'), $this->stats->max('clicks')) * 1.1 / 10) * 10) ?: 1;

        $this->failedSendsCount = $this->mail->contentItem->sends()->failed()->count();

        return view('mailcoach::app.automations.mails.summary')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Performance'),
                'mail' => $this->mail,
            ]);
    }

    public function limit(): int
    {
        return $this->limit;
    }

    protected function createStats(): Collection
    {
        $start = $this->mail->created_at->toImmutable();

        $contentItem = $this->mail->contentItem;

        if ($contentItem->opens()->count() > 0 && $start > $contentItem->opens()->first()->created_at) {
            $start = $contentItem->opens()->first()->created_at->toImmutable();
        }

        $openTable = static::getOpenTableName();
        $clickTable = static::getClickTableName();

        return Collection::times(24)->map(function (int $number) use ($start, $openTable, $clickTable) {
            $datetime = $start->addHours($number - 1);

            return [
                'label' => $datetime->format('H:i'),
                'opens' => $this->mail->contentItem->opens()->whereBetween("{$openTable}.created_at", [$datetime, $datetime->addHour()])->count(),
                'clicks' => $this->mail->contentItem->clicks()->whereBetween("{$clickTable}.created_at", [$datetime, $datetime->addHour()])->count(),
            ];
        });
    }
}
