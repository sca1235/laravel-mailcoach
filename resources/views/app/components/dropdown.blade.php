<div
    x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }"
    x-on:keydown.escape.prevent.stop="close($refs.button)"
    x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
    x-id="['dropdown-button']"
    class="relative dropdown"
>
    <button
        x-ref="button"
        x-on:click="toggle()"
        :aria-expanded="open"
        :aria-controls="$id('dropdown-button')"
        type="button"
        class="{{ $triggerClass ?? 'text-blue-700 hover:text-blue-800' }} @if(! isset($trigger)) px-2 @endif"
        :class="open ? 'z-20' : 'z-10'"
    >
        {{ $trigger }}
    </button>
    <div
        x-ref="panel"
        x-anchor.offset.10="$refs.button"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-on:click.outside="close($refs.button)"
        x-on:click="close($refs.button)"
        :id="$id('dropdown-button')"
        style="display: none;"
        class="z-50 dropdown-list {{ $listClass ?? '' }} {{ isset($direction) ? 'dropdown-list-' . $direction : '' }}">
        {{ $slot }}
    </div>
</div>
