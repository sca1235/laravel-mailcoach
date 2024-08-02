<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Subscribe to email list') }}
        <span class="form-legend-accent">
            @if ($email_list_id)
                @php($emailList = \Spatie\Mailcoach\Mailcoach::getEmailListClass()::find($email_list_id))
                @if ($emailList)
                    <a target="_blank" class="inline-flex items-center gap-x-1" href="{{ route('mailcoach.emailLists.summary', $emailList) }}">{{ optional($emailList)->name }} <x-heroicon-s-arrow-top-right-on-square class="w-4" /></a>
                @endif
            @endif
        </span>
    </x-slot>

    <x-slot name="content">
        @if ($skip_confirmation)
            <div class="mt-1 flex items-center">
                <x-mailcoach::rounded-icon type="success" icon="heroicon-s-check" class="mr-2" /> {{ __mc('Skipping double opt-in') }}
            </div>
        @endif
        @if ($forward_tags)
            <div class="mt-1 flex items-center">
                <x-mailcoach::rounded-icon type="success" icon="heroicon-s-check" class="mr-2" /> {{ __mc('Forwarding existing tags') }}
            </div>
        @endif
        @if ($new_tags)
            <div class="mt-1 flex items-center">
                <x-mailcoach::rounded-icon type="success" icon="heroicon-s-check" class="mr-2" /> {{ __mc('Adding extra tags: ') }} {{ $new_tags }}
            </div>
        @endif
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12">
            <x-mailcoach::select-field
                :label="__mc('Email list')"
                name="email_list_id"
                wire:model="email_list_id"
                :options="$emailListOptions"
            />
        </div>

        @if (isset($emailList) && $emailList->requires_confirmation)
            <div class="col-span-12">
                <x-mailcoach::checkbox-field
                    :label="__mc('Skip double opt-in')"
                    :checked="$skip_confirmation"
                    name="skip_confirmation"
                    wire:model="skip_confirmation"
                />
            </div>
        @endif

        <div class="col-span-12">
            <x-mailcoach::checkbox-field
                :label="__mc('Forward existing tags')"
                :checked="$forward_tags"
                name="forward_tags"
                wire:model="forward_tags"
            />
        </div>

        <div class="col-span-12">
            <x-mailcoach::text-field
                id="tags"
                :label="__mc('Add extra tags')"
                name="new_tags"
                wire:model="new_tags"
            />
        </div>
    </x-slot>

</x-mailcoach::automation-action>
