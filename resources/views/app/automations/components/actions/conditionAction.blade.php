<div>
    <x-mailcoach::fieldset card class="md:p-6 w-[32rem] mx-auto gap-y-4" :focus="$editing">
        <x-slot name="legend">
            <header class="flex flex-col items-center justify-center gap-2 text-base">
                @if ($action['class']::getIcon())
                    <div class="w-5 h-5 bg-blue-dark text-white rounded-full flex items-center justify-center">
                        <x-icon :name="$action['class']::getIcon()" class="w-3" />
                    </div>
                @endif
                <span class="font-normal whitespace-nowrap">
                    {{ $action['class']::getName() }} &mdash; {{ __mc('Check for') }}
                    <span class="form-legend-accent">
                        {{ $length }} {{ __mc_choice(\Illuminate\Support\Str::singular($unit).'|'.\Illuminate\Support\Str::plural($unit), (int) $length) }}
                    </span>
                </span>
            </header>
        </x-slot>

        <div class="" x-data="{ editing: @entangle('editing').live}">
            @if (!$editing && $condition)
                <p class="text-blue-dark font-medium text-center">{{ $condition::getName() }}:</p>
                <p class="text-blue-dark font-medium text-center"><span class="font-semibold">{!! nl2br($condition::getDescription($conditionData)) !!}</span></p>
            @endif

            <div x-show="editing" class="form-grid">
                <div class="form-actions">
                    <div class="col-span-8">
                        <x-mailcoach::text-field
                            :label="__mc('Duration')"
                            :required="true"
                            name="length"
                            wire:model.live="length"
                            type="number"
                        />
                    </div>
                    <div class="col-span-4 sm:col-span-4">
                        <x-mailcoach::select-field
                            :label="__mc('Unit')"
                            :required="true"
                            name="unit"
                            wire:model="unit"
                            :sort="false"
                            :options="
                            collect($units)
                                ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                                ->toArray()
                        "
                        />
                    </div>

                    <div class="col-span-12">
                        <x-mailcoach::select-field
                            :label="__mc('Condition')"
                            :required="true"
                            name="condition"
                            wire:model="condition"
                            :placeholder="__mc('Select a condition')"
                            :options="$conditionOptions"
                        />
                    </div>

                    @switch ($condition)
                        @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition::class)
                            <div class="col-span-12">
                                <x-mailcoach::tags-field
                                    :label="__mc('Tag')"
                                    name="conditionData.tag"
                                    wire:model="conditionData.tag"
                                    :multiple="false"
                                    :clearable="false"
                                    :tags="$automation->emailList?->tags()->pluck('name')->toArray() ?? []"
                                />
                            </div>
                            @break
                        @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\AttributeCondition::class)
                            <div class="col-span-12 flex items-start gap-x-2">
                                <x-mailcoach::text-field
                                    :label="__mc('Attribute')"
                                    name="conditionData.attribute"
                                    wire:model="conditionData.attribute"
                                />
                                <div class="mt-7">
                                    <x-mailcoach::select-field
                                        name="conditionData.comparison"
                                        wire:model="conditionData.comparison"
                                        :placeholder="__mc('Select a comparison')"
                                        :sort="false"
                                        :options="\Spatie\Mailcoach\Domain\Automation\Support\Conditions\AttributeCondition::getComparisons()"
                                    />
                                </div>
                                @if (! in_array($conditionData['comparison'], ['empty', 'not_empty']))
                                    <x-mailcoach::text-field
                                        :label="__mc('Value')"
                                        name="conditionData.value"
                                        wire:model="conditionData.value"
                                    />
                                @else
                                    @php($this->conditionData['value'] = null)
                                @endif
                            </div>
                            @break
                        @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail::class)
                            <div class="col-span-12">
                                <x-mailcoach::select-field
                                    :label="__mc('Automation mail')"
                                    name="conditionData.automation_mail_id"
                                    wire:model="conditionData.automation_mail_id"
                                    :placeholder="__mc('Select a mail')"
                                    :options="
                                    \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()->orderBy('name')->pluck('name', 'id')
                                "
                                />
                            </div>
                            @break
                        @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail::class)
                            <div class="col-span-12">
                                <x-mailcoach::select-field
                                    :label="__mc('Automation mail')"
                                    name="conditionData.automation_mail_id"
                                    wire:model="conditionData.automation_mail_id"
                                    :placeholder="__mc('Select a mail')"
                                    :required="true"
                                    :options="
                                    \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                "
                                />
                            </div>

                            @if ($conditionData['automation_mail_id'])
                                <div class="col-span-12">
                                    <x-mailcoach::select-field
                                        :label="__mc('Link')"
                                        name="conditionData.automation_mail_link_url"
                                        wire:model="conditionData.automation_mail_link_url"
                                        :placeholder="__mc('Select a link')"
                                        :required="false"
                                        :options="
                                        ['' => __mc('Any link')] +
                                        \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::find($conditionData['automation_mail_id'])
                                            ->htmlLinks()
                                            ->mapWithKeys(fn ($url) => [$url => $url])
                                            ->toArray()
                                    "
                                    />
                                </div>
                            @endif
                            @break
                    @endswitch
                </div>
            </div>
        </div>

        <dl class="-mb-6 -mx-6 px-6 py-2 flex items-center justify-start text-xs rounded-b-xl bg-white border-t border-sand-bleak">
            <div class="flex items-center gap-4">
                @if ($editing)
                    <x-mailcoach::button type="button" wire:key="save-{{ $index }}" wire:click="save" class="text-xs py-1 px-3 h-6">
                        <x-slot:icon>
                            <x-heroicon-s-check class="w-3.5" />
                        </x-slot:icon>
                        {{ __mc('Save') }}
                    </x-mailcoach::button>
                @elseif ($editable)
                    <button type="button" class="flex items-center gap-x-1 hover:text-blue-dark" wire:key="edit-{{ $index }}" wire:click="edit">
                        <x-heroicon-s-pencil-square class="w-3.5" />
                        {{ __mc('Edit') }}
                    </button>
                @endif
                @if (!$editing && $deletable)
                    @if (count($yesActions) > 0 || count($noActions) > 0)
                        <div x-data x-tooltip="'{{ __mc('Delete actions in branches first.') }}'">
                            <button class="opacity-75 flex items-center gap-x-1" type="button" disabled>
                                <x-heroicon-s-trash class="w-3.5" />
                                {{ __mc('Delete') }}
                            </button>
                        </div>
                    @else
                        <x-mailcoach::confirm-button class="flex items-center gap-x-1 hover:text-red" :confirm-text="__mc('Are you sure you want to delete this action?')" on-confirm="() => $wire.delete()">
                            <x-heroicon-s-trash class="w-3.5" />
                            {{ __mc('Delete') }}
                        </x-mailcoach::confirm-button>
                    @endif
                @endif
            </div>
        </dl>
    </x-mailcoach::fieldset>

    <div class="flex flex-col items-center">
        <div class="w-[2px] bg-{{ $bg }} h-8"></div>
    </div>

    <div class="flex">
        <div class="flex flex-grow flex-col items-center min-w-[34rem]">
            <div class="ml-auto w-1/2 bg-{{ $bg }} h-[2px]"></div>
            <div class="w-[2px] bg-{{ $bg }} h-2"></div>
            <div class="bg-green-light text-navy-dark rounded-full px-6 py-3">{{ __mc('Yes') }}</div>
            <div class="flex flex-col" wire:ignore>
                <livewire:mailcoach::automation-builder name="{{ $uuid }}-yes-actions" :automation="$automation" :actions="$yesActions" />
            </div>
            <div class="w-[2px] bg-sand flex-1"></div>
            <div class="ml-auto w-1/2 bg-sand h-[2px]"></div>
        </div>

        <div class="flex flex-grow flex-col items-center min-w-[34rem]">
            <div class="mr-auto w-1/2 bg-{{ $bg }} h-[2px]"></div>
            <div class="w-[2px] bg-{{ $bg }} h-2"></div>
            <div class="bg-red-light text-navy-dark rounded-full px-6 py-3">{{ __mc('No') }}</div>
            <div class="flex flex-col" wire:ignore>
                <livewire:mailcoach::automation-builder name="{{ $uuid }}-no-actions" :automation="$automation" :actions="$noActions" />
            </div>
            <div class="w-[2px] bg-sand flex-1"></div>
            <div class="mr-auto w-1/2 bg-sand h-[2px]"></div>
        </div>
    </div>
</div>

