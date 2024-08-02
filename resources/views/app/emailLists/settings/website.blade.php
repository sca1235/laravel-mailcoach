<form
    method="POST"
    class="card-grid"
    wire:submit="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::alert type="help">
        <p>
            {{ __mc('Mailcoach can create a website that displays the content of each mail you send to this list. This way, people not subscribed to your list can still read your content.') }}
        </p>
    </x-mailcoach::alert>
    <x-mailcoach::fieldset :legend="__mc('Settings')" card>
        <x-mailcoach::checkbox-field
            :label="__mc('Enable website')"
            name="has_website"
            wire:model="has_website"
        />
        <x-mailcoach::checkbox-field
            :label="__mc('Show a subscription form')"
            name="show_subscription_form_on_website"
            wire:model="show_subscription_form_on_website"
        />
        <div class="form-field">
            <label class="label" for="website_slug">{{__mc('Website URL')}}</label>
            <div class="flex items-center">
                <span class="select-none pr-3 h-10 flex flex-shrink-0 items-center text-blue-dark font-medium">{{ route('mailcoach.website', '') }}/</span>
                <input id="website_slug" class="input rounded-r-none" placeholder="/" type="text" name="website_slug" wire:model.defer="website_slug" />
                <a class="link ml-2" x-data x-tooltip="'{{ __mc('View website') }}'" href="{{ $emailList->websiteUrl() }}" target="_blank">
                    <x-heroicon-s-arrow-top-right-on-square class="w-4" />
                </a>
            </div>
            @error('emailList.website_slug')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </x-mailcoach::fieldset>
    <x-mailcoach::fieldset card :legend="__mc('Customization')">
        <x-mailcoach::color-field
            :label="__mc('Primary Color')"
            name="website_primary_color"
            wire:model="website_primary_color"
        />
        <div class="form-field">
            @error('website_theme')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <label class="label label-required" for="website_theme">
                {{ __mc('Style') }}
            </label>
            <div class="grid gap-3 items-start">
                <x-mailcoach::radio-field
                    name="website_theme"
                    option-value="default"
                    wire:model="website_theme"
                    :label="__mc('Default')"
                />
                <x-mailcoach::radio-field
                    name="website_theme"
                    option-value="serif"
                    wire:model="website_theme"
                    :label="__mc('Serif')"
                />
                <x-mailcoach::radio-field
                    name="website_theme"
                    option-value="typewriter"
                    wire:model="website_theme"
                    :label="__mc('Typewriter')"
                />
            </div>
        </div>
        <div class="gap-6">
            <div>
                <label class="label" for="image">
                    Header Image
                </label>
            </div>
            <div class="mt-2 max-w-sm">
                <x-mailcoach::upload-field
                    :label="__mc('Upload a .png or .jpg image')"
                    accept="image/png,image/jpeg"
                    wire:model="image"
                    :mimes="[
                        'image/jpeg',
                        'image/png',
                    ]"
                />
                <x-mailcoach::alert type="info" full class="mt-2">
                    {{ __mc('This image will be displayed at the top of your website. The maximum size is 2MB.') }}
                </x-mailcoach::alert>
            </div>
            <div class="flex items-center gap-4">
                <div wire:loading.delay wire:target="image">
                    <style>
                        @keyframes loadingpulse {
                            0% {
                                transform: scale(.8);
                                opacity: .75
                            }
                            100% {
                                transform: scale(1);
                                opacity: .9
                            }
                        }
                    </style>
                    <span
                        style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                        class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span
                                class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                    <span class="ml-1 text-gray-700">{{ __mc('Uploading...') }}</span>
                </div>
            </div>
        </div>
        <div>
            @if ($image)
                <img class="max-w-xs" alt="uploaded header image" src="{{ $image->temporaryUrl() }}"/>
            @elseif ($headerImageUrl = $emailList->websiteHeaderImageUrl())
                <img class="max-w-xs" alt="uploaded header image" src="{{ $headerImageUrl }}"/>
            @endif
        </div>
        <x-mailcoach::text-field
            :label="__mc('Website Title')"
            wire:model="website_title"
            name="website_title"
        />
        <x-mailcoach::markdown-field
            :label="__mc('Intro')"
            name="website_intro"
            wire:model="website_intro"
            :help="__mc('This text will be displayed at the top of the page.')"
        />
    </x-mailcoach::fieldset>
    <x-mailcoach::card class="flex items-center gap-6" buttons>
        <x-mailcoach::button :label="__mc('Save')" />
        @if ($dirty)
            <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
        @else
            <div wire:key="dirty" wire:dirty>
                <x-mailcoach::alert class="text-xs sm:text-base" type="info">{{ __mc('You have unsaved changes') }}</x-mailcoach::alert>
            </div>
        @endif
    </x-mailcoach::card>
</form>

