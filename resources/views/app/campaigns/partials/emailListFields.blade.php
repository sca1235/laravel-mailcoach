@php use Illuminate\Support\Arr; @endphp
@props([
    'wiremodel' => null,
])
<x-mailcoach::fieldset card :legend="__mc('Audience')">
    @if($emailLists->count())
        <x-mailcoach::select-field
            :label="__mc('List')"
            name="{{ $wiremodel ? $wiremodel . '.' : '' }}email_list_id"
            wire:model.live="{{ $wiremodel ? $wiremodel . '.' : '' }}email_list_id"
            :options="$emailLists->pluck('name', 'id')"
            required
        />

        @if($segmentable->usingCustomSegment())
            <x-mailcoach::alert type="info">
                {{ __mc('Using custom segment') }} {{ $segmentable->getSegment()->description() }}.
            </x-mailcoach::alert>
        @else
            <div class="form-field">
                @error('segment')
                <p class="form-error">{{ $message }}</p>
                @enderror
                <label class="label label-required" for="segment">
                    {{ __mc('Segment') }}
                </label>
                <div class="grid gap-3 items-start">
                    <x-mailcoach::radio-field
                            name="segment"
                            option-value="entire_list"
                            wire:model.live="segment"
                            :label="__mc('Entire list')"
                    />
                    <div class="w-full">
                        <div class="flex-shrink-0">
                            <x-mailcoach::radio-field
                                    name="segment"
                                    wire:model.live="segment"
                                    option-value="segment"
                                    :label="__mc('Use segment')"
                            />
                        </div>
                        @if ($segment !== 'entire_list')
                            <div class="w-full">
                                @php
                                $listId = $wiremodel
                                    ? $$wiremodel->email_list_id
                                    : $email_list_id;

                                $list = Arr::first($segmentsData, fn(array $list) => (int) $list['id'] === (int) $listId, $segmentsData[0]);
                                @endphp
                                @if (count($list['segments']))
                                    <div class="mt-3">
                                        <x-mailcoach::select-field
                                            name="{{ $wiremodel ? $wiremodel . '.' : '' }}segment_id"
                                            wire:model.live="{{ $wiremodel ? $wiremodel . '.' : '' }}segment_id"
                                            :options="$list['segments']"
                                            :placeholder="__mc('Select a segment')"
                                        />
                                        @error(($wiremodel ? $wiremodel . '.' : '') . 'segment_id')
                                        <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <a href="{{ $list['createSegmentUrl'] }}">
                                            <x-mailcoach::button-secondary>
                                                <x-slot:icon>
                                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><path d="M15.11 8A7.112 7.112 0 1 1 .889 8 7.112 7.112 0 0 1 15.11 8Zm-3.737-.667H8.666V4.627a.667.667 0 0 0-1.333 0v2.706H4.626a.667.667 0 0 0 0 1.334h2.707v2.706a.667.667 0 0 0 1.333 0V8.667h2.707a.667.667 0 1 0 0-1.334Z"/></svg>
                                                </x-slot:icon>
                                                {{ __mc('Create a segment first') }}
                                            </x-mailcoach::button-secondary>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <x-mailcoach::alert type="warning">
            {!! __mc('You\'ll need to create a list first. <a class="link" href=":url">Create one here</a>', ['url' => route('mailcoach.emailLists')]) !!}
        </x-mailcoach::alert>
    @endif
</x-mailcoach::fieldset>
