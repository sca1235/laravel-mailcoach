<div class="max-w-lg">
    <p>
        Send an authenticated <code>POST</code> request to the following endpoint with an array of subscriber ids, make sure you've set up the <a href="https://mailcoach.app/docs/v4/mailcoach/using-the-api/introduction" target="_blank">Mailcoach API</a>.
    </p>
    @php($url = action('\\' . \Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController::class, [$this->automation]))
    <x-mailcoach::code
        click-to-copy
        code-class="bg-sand-extra-light"
        class="my-4 border border-snow rounded-md"
        :code="$url"
    />

    <p class="mt-4">Example POST request:</p>
    <x-mailcoach::code
        click-to-copy
        class="my-4 bg-sand-extra-light [&>pre]:bg-sand-extra-light border border-snow rounded-md"
        lang="shell">$ MAILCOACH_TOKEN="your API token"
$ curl -x POST {{ action('\\' . \Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController::class, [$this->automation]) }} \
-H "Authorization: Bearer $MAILCOACH_TOKEN" \
-H 'Accept: application/json' \
-H 'Content-Type: application/json'
-d '{"subscribers":[1, 2, 3]}'</x-mailcoach::code>

    <p class="my-4">The automation will only trigger for subscribed subscribers of the automation's email list & segment.</p>
</div>
