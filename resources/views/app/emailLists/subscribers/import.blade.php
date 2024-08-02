<div>
    <div>
        {{ $this->table }}
    </div>

    <x-mailcoach::fieldset card :legend="__mc('Import subscribers')" class="mt-4">
        @if ($showForm)
            <form class="form-grid" method="POST" wire:submit="startImport" x-cloak>
                @csrf

                <x-mailcoach::upload-field
                    wire:model="file"
                    accept=".csv,.txt,.xlsx"
                    :label="__mc('Upload a .csv or .xlsx file')"
                    :mimes="[
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]"
                />

                <x-mailcoach::alert type="info" full>
                    <div class="text-navy font-normal text-sm">
                        {!! __mc('Include the following columns: email, first name, last name, and tags. We also support <a href=":url" target="_blank">Mailchimp CSV exports</a>.', ['url' => 'https://mailchimp.com/help/view-export-contacts/#View_or_export_an_audience']) !!}
                    </div>
                </x-mailcoach::alert>

                <hr class="border-snow">

                <h2 class="text-lg">{{ __mc('Import settings') }}</h2>

                <div class="form-field">
                    @error('replace_tags')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="flex">
                        <label class="label" for="tags_mode">
                            {{ __mc('Importing tags of existing subscribers') }}
                        </label>
                    </div>
                    <div class="grid gap-3 items-start">
                        <x-mailcoach::radio-field
                            name="replace_tags"
                            wire:model="replaceTags"
                            option-value="append"
                            :label="__mc('Append any new tags in the import')"
                        />
                        <x-mailcoach::radio-field
                            name="replace_tags"
                            wire:model="replaceTags"
                            option-value="replace"
                            :label="__mc('Replace all tags by the tags specified in the import')"
                        />
                    </div>
                </div>

                <div class="form-field">
                    <label class="label">
                        {{ __mc('Missing or matching subscribers') }}
                    </label>
                    <div class="grid gap-3 items-start">
                        <x-mailcoach::checkbox-field
                            name="subscribeUnsubscribed"
                            wire:model.live="subscribeUnsubscribed"
                            :label="__mc('Re-subscribe unsubscribed emails')"
                        />
                    </div>
                    @if ($subscribeUnsubscribed)
                        <x-mailcoach::alert type="warning">
                            {{ __mc('Make sure you have proper consent of the subscribers you\'re resubscribing.') }}
                        </x-mailcoach::alert>
                    @endif

                    <div class="grid gap-3 items-start">
                        <x-mailcoach::checkbox-field
                            name="unsubscribeMissing"
                            wire:model.live="unsubscribeMissing"
                            :label="__mc('Unsubscribe missing emails')"
                        />
                    </div>

                    @if ($unsubscribeMissing)
                        <x-mailcoach::alert type="warning">
                            {{ __mc('This is a dangerous operation, make sure you upload the correct import list') }}
                        </x-mailcoach::alert>
                    @endif
                </div>

                <div class="form-field">
                    <label class="label">
                        {{ __mc('Notification') }}
                    </label>
                    <div class="grid gap-3 items-start">
                        <x-mailcoach::checkbox-field
                            name="sendNotification"
                            wire:model.live="sendNotification"
                            :label="__mc('Send an email notification when the import is complete')"
                        />
                    </div>
                </div>

                <hr class="border-snow">

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <x-mailcoach::button type="submit" :label="__mc('Import subscribers')" :disabled="!$file" />
                        <div wire:loading.delay wire:target="file">
                            <style>
                                @keyframes loadingpulse {
                                    0%   {transform: scale(.8); opacity: .75}
                                    100% {transform: scale(1); opacity: .9}
                                }
                            </style>
                            <span
                                style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                                class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                            <span class="ml-1 text-gray-700">Uploading...</span>
                        </div>
                    </div>

                    <div class="text-sm flex items-center gap-x-6">
                        <a href="#" wire:click.prevent="downloadExample" class="underline">{{ __mc('Download example file') }}</a>
                        <a href="https://mailcoach.app/resources/learn-mailcoach/features/email-lists#content-importing" target="_blank" class="underline">{{ __mc('View documentation') }}</a>
                    </div>
                </div>
            </form>
        @else
            <div>
                <x-mailcoach::button wire:click.prevent="$set('showForm', true)" :label="__mc('New import')" />
            </div>
        @endif
    </x-mailcoach::fieldset>
</div>
