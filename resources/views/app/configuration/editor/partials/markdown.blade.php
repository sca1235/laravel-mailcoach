<p class="markup-links">{!! __mc('The Markdown editor uses <a href=":link">EasyMDE</a> under the hood. It also offers image uploads.', ['link' => 'https://github.com/Ionaru/easy-markdown-editor']) !!}</p>

<x-mailcoach::alert type="info">
    {{ __mc('The Markdown editor stores content in a structured way. When switching from or to this editor, content in existing templates and draft campaigns will be lost.') }}
</x-mailcoach::alert>

<div>
    <x-mailcoach::select-field
        :label="__mc('Text direction')"
        name="editorSettings.text_direction"
        wire:model.lazy="editorSettings.text_direction"
        :options="[
            'ltr' => __mc('Left-to-right'),
            'rtl' => __mc('Right-to-left'),
        ]"
    />
</div>
