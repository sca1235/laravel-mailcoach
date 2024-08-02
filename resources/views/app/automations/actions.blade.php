<form
    wire:submit.prevent="save(new URLSearchParams(new FormData($event.target)).toString())"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
>
    <style data-navigate-track>
        body {
            overscroll-behavior-y: none;
            overscroll-behavior-x: none;
            max-height: 100vh;
            overflow: hidden;
        }
    </style>
    <div
        x-data="{
            pan: null,
        }"
        @wheel.passive="(e) => {
            if (! pan) return;

            if (
                (e.target && ['CODE', 'PRE'].includes(e.target.nodeName))
                || (e.target && e.target.parentNode && ['CODE', 'PRE'].includes(e.target.parentNode.nodeName))
                || (e.target && e.target.parentNode && e.target.parentNode.parentNode && ['CODE', 'PRE'].includes(e.target.parentNode.parentNode.nodeName))
            ) {
                return;
            }

            if (e.target.classList.contains('choices__item')) {
                return;
            }

            const transforms = pan.getTransform();

            try {
                pan.moveTo(
                   transforms.x - e.deltaX,
                   transforms.y - e.deltaY,
                )
            } catch(e) {}
        }"
        x-init="() => {
            pan = panzoom($refs.container, {
                maxZoom: 1,
                minZoom: 1,
                bounds: true,
                boundsPadding: 0.99,
                transformOrigin: {x: 0.5, y: 0.5},
                beforeWheel: function (e) {
                    return true;
                },
                beforeMouseDown: function(e) {
                    if (
                        (e.target && ['CODE', 'PRE'].includes(e.target.nodeName))
                        || (e.target && e.target.parentNode && ['CODE', 'PRE'].includes(e.target.parentNode.nodeName))
                        || (e.target && e.target.parentNode && e.target.parentNode.parentNode && ['CODE', 'PRE'].includes(e.target.parentNode.parentNode.nodeName))
                    ) {
                        return true;
                    }

                    return false;
                },
                filterKey: function(/* e, dx, dy, dz */) {
                    // don't let panzoom handle this event:
                    return true;
                },
                onTouch: function(e) {
                    return false; // tells the library to not preventDefault.
                }
            });

            if ($refs.container.offsetWidth > $refs.container.parentElement.offsetWidth) {
                pan.moveTo(- $refs.container.querySelector('.card').getBoundingClientRect().left / 2, 0);
            }
        }"
    >
        <div class="relative card-grid w-full min-w-full overflow-hidden h-full h-[calc(100vh-30rem)] sm:h-[calc(100vh-22rem)] border border-sand-bleak rounded-t">
            <div class="flex flex-col justify-center bg-pattern p-8" wire:ignore.self x-ref="container">
                <x-mailcoach::fieldset card class="min-w-[32rem] mx-auto md:p-6 mt-6 gap-y-4">
                    <x-slot:legend>
                        <header class="flex items-center space-x-2 text-base">
                            {{ __mc('Trigger') }}
                        </header>
                    </x-slot:legend>
                    <x-mailcoach::select-field
                        name="selectedTrigger"
                        :options="$triggerOptions"
                        placeholder="Select a trigger"
                        required
                        wire:model.live="selectedTrigger"
                    />

                    @if ($selectedTrigger && $selectedTrigger::getComponent())
                        @livewire($selectedTrigger::getComponent(), [
                            'triggerClass' => $automation->triggerClass(),
                            'automation' => $automation,
                        ], key($selectedTrigger))
                    @endif

                    @foreach ($errors->all() as $message)
                        <p class="form-error mb-1" role="alert">{{ $message }}</p>
                    @endforeach
                </x-mailcoach::fieldset>

                <div wire:ignore>
                    <livewire:mailcoach::automation-builder name="default" :automation="$automation" :actions="$actions" />
                </div>

                <div class="bg-sky-light text-navy rounded-full px-6 py-3 mb-6 inline-block mx-auto">{{ __mc('Finish') }}</div>
            </div>
        </div>

        <div class="card rounded-t-none -mt-px md:p-6">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <x-mailcoach::button :label="__mc('Save actions')" :disabled="count($editingActions) > 0" />
                @if (count($editingActions) > 0)
                    <x-mailcoach::alert type="info" class="-ml-3">
                        {{ __mc('Save your individual actions first') }}
                    </x-mailcoach::alert>
                @elseif ($unsavedChanges)
                    <x-mailcoach::alert class="-ml-3" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
                @endif
            </div>
        </div>
    </div>
</form>
