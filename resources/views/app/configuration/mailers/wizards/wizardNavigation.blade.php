<nav class="flex items-center w-full gap-x-8" aria-label="Tabs">
    @foreach($steps as $index => $step)
        <div class="relative w-48
            group inline-flex flex-col items-center gap-2 transition-colors duration-300
            {{ $step->isCurrent() ? 'text-blue-dark font-medium' : '' }}
            {{ $step->isPrevious() || $this->mailer()->ready_for_use ? 'hover:text-blue-dark cursor-pointer' : '' }}
        "
            @if ($step->isPrevious() || $this->mailer()->ready_for_use)
                wire:click="{{ $step->show() }}"
            @endif
        >
            <div class="relative z-10 {{ $step->isCurrent() || $step->isPrevious() ? 'bg-blue-dark text-white' : 'bg-sky-extra-light' }} text-navy w-6 h-6 leading-none text-xs rounded-full flex items-center justify-center">{{ $index + 1 }}</div>
            <span>{{ $step->label }}</span>
            @unless($loop->first)
            <div class="h-px {{ $step->isCurrent() || $step->isPrevious() ? 'bg-blue-dark' : 'bg-sky-extra-light' }} w-56 absolute top-0 mt-3 right-0 mr-24 z-0"></div>
            @endunless
        </div>
    @endforeach
</nav>
