<?php
$routeBase = $model instanceof \Spatie\Mailcoach\Domain\Campaign\Models\Campaign
                ? 'mailcoach.campaigns.'
                : 'mailcoach.automations.mails.';
?>
<table class="w-full">
    @if ($model instanceof \Spatie\Mailcoach\Domain\Campaign\Models\Campaign && $model->isSplitTested())
        <tr>
            <td class="pt-6 text-center" colspan="2">
                <div class="mx-auto w-8 h-8 rounded-full inline-flex items-center justify-center text-sm leading-none font-semibold bg-sky-extra-light">
                    {{ $index + 1 }}
                </div>
            </td>
        </tr>
    @endif
    <x-mailcoach::checklist-item
        :test="$contentItem->subject"
        :label="__mc('Subject')"
        :value="$contentItem->subject ?: __mc('Subject is empty')"
        :edit-link="route($routeBase . 'settings', $model)"
    />
    @if($contentItem->html && $contentItem->hasValidHtml())
        <x-mailcoach::checklist-item
            warning
            :test="$contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $contentItem->sizeInKb() < 102"
            :label="__mc('Content')"
            :edit-link="route($routeBase . 'content', $model)"
        >
            <x-slot:value>
                @if (! $contentItem->htmlContainsUnsubscribeUrlPlaceHolder())
                    <p class="markup-code">
                        {{ __mc("Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                    </p>
                    <p class="markup-code">
                        {!! __mc('Consider adding the <code>&#123;&#123; unsubscribeUrl &#125;&#125;</code> placeholder.') !!}
                    </p>
                @endif

                @if ($contentItem->sizeInKb() >= 102)
                    <p class="markup-code">
                        {{ __mc("Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your campaign.", ['size' => "{$contentItem->sizeInKb()}kb"]) }}
                    </p>
                @endif

                @if ($contentItem->hasValidHtml() && $contentItem->htmlContainsUnsubscribeUrlPlaceHolder() && $contentItem->sizeInKb() < 102)
                    <p class="markup-code">
                        {{ __mc('No problems detected!') }}
                    </p>
                @endif

                <div class="mt-4 flex items-center gap-x-3">
                    <x-mailcoach::button-link
                        x-on:click="$dispatch('open-modal', { id: 'preview-{{ $contentItem->uuid }}' })"
                        :label="__mc('Preview')"
                    />
                    @if ($model->getMailerKey())
                        <x-mailcoach::button-link
                            x-on:click="$dispatch('open-modal', { id: 'send-test-{{ $contentItem->uuid }}' })"
                            :label="__mc('Send Test')"
                        />
                    @endif
                </div>

                <x-mailcoach::preview-modal
                    :id="'preview-'. $contentItem->uuid"
                    :title="__mc('Preview') . ' - ' . $contentItem->subject"
                    :html="$contentItem->html"
                />

                <x-mailcoach::modal :title="__mc('Send Test')" name="send-test-{{ $contentItem->uuid }}" :dismissable="true">
                    <livewire:mailcoach::send-test :model="$contentItem"/>
                </x-mailcoach::modal>
            </x-slot:value>
        </x-mailcoach::checklist-item>
    @else
        <x-mailcoach::checklist-item
            :test="false"
            :label="__mc('Content')"
            :edit-link="route($routeBase . 'content', $model)"
        >
            <x-slot:value>
                @if($contentItem->html)
                    @if (! $contentItem->hasValidHtml())
                        <p>{{ __mc('HTML is invalid') }}</p>
                        <p>{!! $contentItem->htmlError() !!}</p>
                    @endif
                @else
                    {{ __mc('Content is missing') }}
                @endif
            </x-slot:value>
        </x-mailcoach::checklist-item>
    @endif

    <x-mailcoach::checklist-item
        :label="__mc('Links')"
        neutral
        icon="heroicon-s-link"
    >
        <x-slot:value>
            @php($tags = [])
            @php($links = $contentItem->htmlLinks())
            @if (count($links))
                <div x-data="{ collapsed: @js(count($links) > 5) }">
                    <p class="mb-4">
                        {{ __mc_choice(":count link was found in your content, make sure it is valid.|:count links were found in your content, make sure they are valid.", count($links), ['count' => count($links)]) }}
                        <span class="inline-flex gap-x-2" x-cloak>
                            <button class="button-link flex items-center p-0 !m-0" type="button" x-show="collapsed" x-on:click="collapsed = !collapsed">
                                {{ __mc('Show links') }}
                            </button>
                            <button class="button-link flex items-center p-0 !m-0" type="button" x-show="!collapsed" x-on:click="collapsed = !collapsed">
                                {{ __mc('Hide links') }}
                            </button>
                        </span>
                    </p>
                    <ul class="grid gap-2" x-show="!collapsed" x-collapse>
                        @foreach ($links as $index => $link)
                            @php($key = $contentItem->id . $link)
                            <li>
                                <livewire:mailcoach::link-check lazy :url="$link" wire:key="{{ $key }}"/>
                                @php($tags[] = \Spatie\Mailcoach\Domain\Content\Support\LinkHasher::hash($model, $link))
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="markup-code">
                    {{ __mc("No links were found in your content.") }}
                </p>
            @endif
        </x-slot:value>
    </x-mailcoach::checklist-item>

    @if(method_exists($model, 'tracking'))
        @php([$openTracking, $clickTracking] = $model->tracking())
    @endif
    @if ($contentItem->add_subscriber_tags || $contentItem->add_subscriber_link_tags)
        <x-mailcoach::checklist-item
            :label="__mc('Tags')"
            neutral
            icon="heroicon-s-tag"
        >
            <x-slot:value>
                <p class="mb-4">
                    {{ __mc("The following tags will be added to subscribers when they open or click the email:") }}
                </p>
                @if ($model instanceof \Spatie\Mailcoach\Domain\Campaign\Models\Campaign && is_null($openTracking) && is_null($clickTracking))
                    <p class="mb-4">
                        {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $model->getMailerKey()]) !!}
                    </p>
                @endif
                <ul class="flex flex-wrap gap-2">
                    @if ($contentItem->add_subscriber_tags)
                        <li>
                            <x-mailcoach::tag neutral size="xs">
                                {{ ($model instanceof \Spatie\Mailcoach\Domain\Campaign\Models\Campaign ? 'campaign' : 'automation-mail') . "-{$model->uuid}-opened" }}
                            </x-mailcoach::tag>
                        </li>
                        <li>
                            <x-mailcoach::tag neutral size="xs">
                                {{ ($model instanceof \Spatie\Mailcoach\Domain\Campaign\Models\Campaign ? 'campaign' : 'automation-mail') . "-{$model->uuid}-clicked" }}
                            </x-mailcoach::tag>
                        </li>
                    @endif
                    @if ($contentItem->add_subscriber_link_tags)
                        @foreach ($tags as $tag)
                            <li>
                                <x-mailcoach::tag size="xs">
                                    {{ $tag }}
                                </x-mailcoach::tag>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </x-slot:value>
        </x-mailcoach::checklist-item>
    @endif
</table>
