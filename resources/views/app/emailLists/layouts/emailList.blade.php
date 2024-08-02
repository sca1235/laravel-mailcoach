<x-mailcoach::layout
    :originTitle="$originTitle ?? $emailList->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
    :hideCard="isset($hideCard) ? true : false"
    :create="$create ?? null"
    :create-text="$createText ?? null"
    :create-data="$createData ?? []"
>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-x-6 w-full h-12">
                <a wire:navigate x-data x-tooltip="'{{ $originTitle ?? __mc('Back to lists') }}'" href="{{ $originHref ?? route('mailcoach.emailLists') }}">
                    <svg class="w-5 h-5 md:w-7 md:h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30"><g clip-path="url(#clip0_386_1588)"><path fill="#fff" d="M30 15a15 15 0 1 0-30 0 15 15 0 0 0 30 0Z"/><path fill="#C2C0BC" d="M6.973 15.996a1.4 1.4 0 0 1 0-1.986l6.562-6.569a1.406 1.406 0 0 1 1.986 1.986l-4.16 4.16 10.67.007c.78 0 1.407.627 1.407 1.406 0 .78-.627 1.406-1.407 1.406h-10.67l4.16 4.16a1.406 1.406 0 0 1-1.986 1.986l-6.562-6.556Z"/></g><defs><clipPath id="clip0_386_1588"><path fill="#fff" d="M0 0h30v30H0z"/></clipPath></defs></svg>
                </a>
                <span class="markup-h1 font-title flex items-center gap-x-1">
                    @if ($originTitle ?? '')
                        <span class="truncate flex items-center gap-x-1">
                            <a href="{{ $originHref ?? route('mailcoach.emailLists') }}" class="opacity-50">
                                {{ $originTitle }}
                            </a>
                            <span class="opacity-50"> / </span>
                            {{ $title }}
                        </span>
                    @else
                        {{ $emailList->name }}
                    @endif
                </span>
            </div>
        </div>
        @if (Route::is('mailcoach.emailLists.summary'))
            @can('create', \Spatie\Mailcoach\Mailcoach::getCampaignClass())
                <a class="button button-tertiary ml-2" href="#" x-on:click.prevent="$dispatch('open-modal', { id: 'create-campaign' })">
                    <x-heroicon-s-plus class="w-4" />
                    {!! str_replace(' ', '&nbsp;', __mc('New campaign')) !!}
                </a>
                <x-mailcoach::modal :title="__mc('Create campaign')" name="create-campaign">
                    @livewire('mailcoach::create-campaign', [
                        'emailList' => $emailList,
                    ])
                </x-mailcoach::modal>
            @endcan
            @can('create', \Spatie\Mailcoach\Mailcoach::getAutomationClass())
                <a href="#" class="button button-tertiary ml-2" x-on:click.prevent="$dispatch('open-modal', { id: 'create-automation' })">
                    <x-heroicon-s-plus class="w-4" />
                    {!! str_replace(' ', '&nbsp;', __mc('New automation')) !!}
                </a>
                <x-mailcoach::modal :title="__mc('Create automation')" name="create-automation">
                    @livewire('mailcoach::create-automation', [
                        'emailList' => $emailList,
                    ])
                </x-mailcoach::modal>
            @endcan
        @endif
        @if(Route::is('mailcoach.emailLists.subscribers'))
        <a class="button button-tertiary mr-2" href="{{ route('mailcoach.emailLists.import-subscribers', $emailList) }}">
            {!! str_replace(' ', '&nbsp;', __mc('Import subscribers')) !!}
        </a>
        @endif
    </x-slot>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            <x-mailcoach::navigation-group
                :title="__mc('Performance')"
                :href="route('mailcoach.emailLists.summary', $emailList)"
            />
            <x-mailcoach::navigation-group
                :title="__mc('Subscribers')"
                :active="Route::is('mailcoach.emailLists.subscriber*')"
                :href="route('mailcoach.emailLists.subscribers', $emailList)"
            >
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscribers', $emailList)">
                    {{ __mc('Overview') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.import-subscribers', $emailList)">
                    {{ __mc('Imports') }}
                </x-mailcoach::navigation-item>
                @if ($emailList->subscriberExports()->count())
                    <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.subscriber-exports', $emailList)">
                        {{ __mc('Exports') }}
                    </x-mailcoach::navigation-item>
                @endif
            </x-mailcoach::navigation-group>
            <x-mailcoach::navigation-group
                :title="__mc('Tags')"
                :active="Route::is('mailcoach.emailLists.tags.*')"
                :href="route('mailcoach.emailLists.tags', $emailList) . '?type=default'"
            />
            <x-mailcoach::navigation-group
                :active="Route::is('mailcoach.emailLists.segments.*')"
                :href="route('mailcoach.emailLists.segments', $emailList)"
                :title="__mc('Segments')"
            />
            <x-mailcoach::navigation-group :title="__mc('Settings')" :href="route('mailcoach.emailLists.general-settings', $emailList)">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.general-settings', $emailList)">
                    {{ __mc('General') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.onboarding', $emailList)">
                    {{ __mc('Onboarding') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.website', $emailList)">
                    {{ __mc('Website') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            @include('mailcoach::app.emailLists.layouts.partials.afterLastTab')
        </x-mailcoach::navigation>
    </x-slot>

    @if (!Route::is('mailcoach.emailLists.subscriber*') && !Route::is('mailcoach.emailLists.import-subscribers') && $emailList->allSubscriptionsCount() === 0)
        <x-mailcoach::alert type="help" class="mb-6">
            {!! __mc('This list is empty. <a href=":url">Add some subscribers</a>', ['url' => route('mailcoach.emailLists.subscribers', $emailList)]) !!}
        </x-mailcoach::alert>
    @endif

    {{ $slot }}
</x-mailcoach::layout>
