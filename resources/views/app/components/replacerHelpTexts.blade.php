@if ($replacerHelpTexts())
    <div>
        <x-mailcoach::button-link
            :label="__mc('Cheat sheet')"
            x-on:click.prevent="$dispatch('open-modal', { id: 'placeholders' })"
        />
    </div>

    <x-mailcoach::modal slide-over width="3xl" :dismissable="true" :title="__mc('Placeholder cheat sheet')" name="placeholders">
        <x-mailcoach::alert type="info" class="markup-code">
            {{ __mc('You can use following placeholders in the subject and copy:') }}
        </x-mailcoach::alert>
            <dl class="mt-4 markup-dl markup-code">
                @foreach($replacerHelpTexts as $replacerName => $replacerDescription)
                    <dt x-data="{ value: '@{{ ' + @js($replacerName) + ' }}' }"><code @click="() => {
                        $clipboard(value);
                        value = '{{ __mc('Copied!') }}';
                        setTimeout(() => {
                            value = '@{{ ' + @js($replacerName) + ' }}';
                        }, 2000);
                    }" x-text="value"></code></dt>
                    <dd>{{ $replacerDescription }}</dd>
                @endforeach
            </dl>
    </x-mailcoach::modal>
@endif
