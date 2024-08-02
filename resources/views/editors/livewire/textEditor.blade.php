<div class="form-grid">
    @if ($model->hasTemplates())
        <x-mailcoach::template-chooser />
    @endif

    @foreach($template?->fields() ?? [['name' => 'html', 'type' => 'editor']] as $field)
        <x-mailcoach::editor-fields :name="$field['name']" :type="$field['type']">
            <x-slot name="editor">
                <textarea

                    class="input input-html"
                    rows="15"
                    wire:model.lazy="templateFieldValues.{{ $field['name'] }}"
                ></textarea>
            </x-slot>
        </x-mailcoach::editor-fields>
    @endforeach
</div>
