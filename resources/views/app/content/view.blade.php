<div class="card-grid">
    <div class="grid gap-6 grid-cols-{{ $model->contentItems->count() > 1 ? '2' : '1' }}">
        @foreach ($model->contentItems as $index => $contentItem)
            <x-mailcoach::fieldset card :legend="$model->contentItems->count() > 1 ? __mc('Variant ') . $index+1 : __mc('Content')">
                <x-mailcoach::text-field
                    name="subject"
                    :label="__mc('Subject line')"
                    :value="$contentItem->subject"
                    readonly
                />

                <div>
                    <label class="label mb-3">{{ __mc('Preview') }}</label>
                    <div class="rounded border border-sand-bleak overflow-scroll max-h-[50rem]">
                        <x-mailcoach::web-view :id="$contentItem->id" :html="$contentItem->webview_html ?? $contentItem->email_html ?? $contentItem->html" />
                    </div>
                </div>

                <div class="w-full max-w-full overflow-hidden">
                    <label class="label mb-6">{{ __mc('HTML') }}</label>
                    <div class="w-full max-w-full overflow-scroll max-h-[50rem]">
                        <x-mailcoach::code
                            lang="html"
                            class="border border-snow rounded"
                            :code="$contentItem->webview_html ?? $contentItem->email_html ?? $contentItem->html"
                        />
                    </div>
                </div>
            </x-mailcoach::fieldset>
        @endforeach
    </div>
</div>
