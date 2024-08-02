<x-mailcoach::fieldset class="{{ $class ?? '' }}" card :legend="__mc('Usage in Mailcoach API')">
    <div class="max-w-full overflow-hidden">
        <x-mailcoach::alert type="help" :full="false">
            {!! __mc('Whenever you need to specify <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
            'resourceName' => $resourceName,
            'resource' => $resource,
        ]) !!}
            <p class="mt-4">
                <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$uuid"></x-mailcoach::code-copy>
            </p>
        </x-mailcoach::alert>
    </div>
</x-mailcoach::fieldset>
