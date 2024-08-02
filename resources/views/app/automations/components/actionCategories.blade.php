<div class="grid items-start justify-start gap-x-16 gap-y-8 md:grid-cols-[auto,auto]">
    @foreach ($actionOptions as $category => $actions)
        <div>
            <h4 class="ml-4 mb-2 text-lg flex items-center gap-2">
                {{ \Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum::from($category)->label() }}
            </h4>
            <ul>
                @foreach ($actions as $action)
                    <li>
                        <a class="!flex items-center gap-x-3 link py-2 whitespace-nowrap" href="#" wire:click.prevent="addAction('{{ addslashes($action) }}', {{ $index ?? 0 }})">
                            @if ($action::getIcon())
                            <x-icon :name="$action::getIcon()" class="w-4" />
                            @endif
                            {{ $action::getName() }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
