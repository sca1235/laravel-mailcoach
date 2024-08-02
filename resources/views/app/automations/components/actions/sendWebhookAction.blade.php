<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Send webhook') }}
        <span class="form-legend-accent">
            @if ($url)
                to {{ $url }}
            @endif
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12">
            <x-mailcoach::alert class="max-w-md" type="help" full>
                <p>
                    {!! __mc('These webhooks will use the same signature validation as documented for the <a href=":url" target="_blank">event webhooks</a>', [
                        'url' => 'https://mailcoach.app/resources/learn-mailcoach/advanced/webhooks#content-configuring-webhooks',
                    ]) !!}
                </p>
                <details>
                    <summary>{{ __mc('Example payload') }}</summary>
                    <x-mailcoach::code class="max-w-sm" lang="json">{
    "automation_name": "{{ $automation->name }}",
    "automation_uuid": "{{ $automation->uuid }}",
    "subscriber": {
    "email_list_uuid": "{{ $automation->emailList->uuid }}",
    "email": "john@doe.com",
    "first_name": null,
    "last_name": null,
    "extra_attributes": [],
    "tags": [],
    "uuid": "{{ \Illuminate\Support\Str::uuid() }}",
    "subscribed_at": "{{ now()->startOfSecond()->toJSON() }}",
    "unsubscribed_at": null,
    "created_at": "{{ now()->startOfSecond()->toJSON() }}",
    "updated_at": "{{ now()->startOfSecond()->toJSON() }}"
}</x-mailcoach::code>
                </details>
            </x-mailcoach::alert>
        </div>
        <div class="col-span-12">
            <x-mailcoach::text-field
                :label="__mc('Url')"
                name="url"
                wire:model="url"
            />
        </div>

        <div class="col-span-12">
            <x-mailcoach::text-field
                :label="__mc('Secret')"
                name="secret"
                wire:model="secret"
            />
        </div>
    </x-slot>
</x-mailcoach::automation-action>
