<x-mailcoach::layout :origin-title="__mc('Transactional')">
    <div class="flex flex-col gap-y-20">
        <div>
            @include('mailcoach::app.partials.header', [
                'title' => __mc('Emails'),
                'create' => 'transactional-template',
                'createText' => __mc('New transactional mail'),
            ])
            <livewire:mailcoach::transactional-mail-templates />
        </div>

        <div>
            @include('mailcoach::app.partials.header', [
                'title' => __mc('Log'),
            ])
            <livewire:mailcoach::transactional-mails />
        </div>
    </div>
</x-mailcoach::layout>
