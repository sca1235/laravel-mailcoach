<div class="grid gap-y-3">
    <x-mailcoach::date-time-field
        :label="__mc('Date')"
        name="date"
        :value="$automation->getTrigger()->date ?? null"
        required
    />
    <x-mailcoach::select-field
        name="repeat"
        :label="__mc('Repeat')"
        wire:model="repeat"
        :sort="false"
        :options="[
            '' => __mc('Don\'t repeat'),
            'daily' => __mc('Daily'),
            'monthly' => __mc('Monthly'),
            'yearly' => __mc('Yearly'),
        ]"
    />
    @if ($repeat)
        <x-mailcoach::alert type="info">
            {{ __mc('Repeating will pick up new subscribers added after the initial date.') }}
        </x-mailcoach::alert>
    @endif
</div>
