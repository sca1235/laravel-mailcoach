<x-mailcoach::card>
    <table>
        <x-mailcoach::checklist-item
            neutral
            :label="__mc('Sent at')"
            :value="$webhookLog->created_at"
        ></x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            :label="__mc('Status code')"
            :test="$webhookLog->wasSuccessful()"
            :value="$webhookLog->status_code"
        ></x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            neutral
            :label="__mc('Event type')"
            :value="$webhookLog->event_type"
        ></x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            neutral
            :label="__mc('Attempt')"
            :value="$webhookLog->attempt ?? __mc('Manual')"
        ></x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            neutral
            :label="__mc('URL')"
            :value="$webhookLog->webhook_url"
        ></x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            neutral
            :label="__mc('Payload')"
        >
            <x-mailcoach::code lang="json" :code="json_encode($webhookLog->payload, JSON_PRETTY_PRINT)" />
        </x-mailcoach::checklist-item>
        <x-mailcoach::checklist-item
            :border="false"
            neutral
            :label="__mc('Response')"
        >
            <x-mailcoach::code lang="json" :code="$this->getPrintableResponse()" />
        </x-mailcoach::checklist-item>
    </table>
</x-mailcoach::card>
