<?php

namespace Spatie\Mailcoach\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Content\Actions\StripUtmTagsFromUrlAction;
use Throwable;

class LinkCheckComponent extends Component
{
    public string $url;

    public ?bool $status = null;

    public ?string $error = null;

    public bool $check = false;

    public string $strippedUrl;

    public function mount()
    {
        $this->strippedUrl = app(StripUtmTagsFromUrlAction::class)->execute($this->url);

        [$this->status, $this->error] = cache()->remember("link-check-{$this->strippedUrl}", now()->addHour(), function (): array {
            try {
                $request = Http::timeout(10);

                if (str_contains($this->strippedUrl, 'twitter.com')) {
                    $request->withHeader('User-Agent', 'Mozilla/5.0');
                }

                $response = $request->get($this->strippedUrl);

                return [$response->successful(), $response->reason()];
            } catch (Throwable $e) {
                return [false, $e->getMessage()];
            }
        });

        if ($this->status === false) {
            cache()->forget("link-check-{$this->strippedUrl}");
        }
    }

    public function placeholder(): string
    {
        $url = app(StripUtmTagsFromUrlAction::class)->execute($this->url);

        return <<<"blade"
        <span class="flex items-center gap-1">
            <span class="inline-flex w-4 mr-1">
                <x-heroicon-s-arrow-path class="animate-spin w-4 text-gray-400" />
            </span>
            <a target="_blank" class="link break-words" href="$url">$url</a>
        </span>
        blade;
    }

    public function render(): string
    {
        return <<<'blade'
            <span class="flex items-center gap-1">
                <span class="inline-flex w-4 mr-1">
                    <x-mailcoach::health-label title="{{ $error }}" reverse warning :test="$status" />
                </span>
                <a target="_blank" class="link break-words" href="{{ $strippedUrl }}">{{ $strippedUrl }}</a>
            </span>
        blade;
    }
}
