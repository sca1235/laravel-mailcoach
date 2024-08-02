<div class="form-grid">
    <script>
        window.debounce = function(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }
    </script>
    <div>
        @if ($model->hasTemplates())
            <x-mailcoach::template-chooser :clearable="false" wire:key="template-chooser" />
        @endif
    </div>

    @foreach($template?->fields() ?? [['name' => 'html', 'type' => 'editor']] as $index => $field)
        <x-mailcoach::editor-fields :name="$field['name']" :type="$field['type']" :label="$field['name']">
            <x-slot name="editor">
                <div
                    wire:ignore
                    wire:key="{{ $field['name'] . '-' . $index }}"
                    x-data="{
                    html: @entangle('templateFieldValues.' . $field['name']).live,
                }" x-init="
                    if (typeof html === 'object') {
                        html = html.html;
                    }

                    setupCodeMirror($refs.editor, html, window.debounce((viewUpdate) => {
                        html = viewUpdate.state.doc.toString();
                    }))
                ">
                    <div x-ref="editor" class="input bg-white p-0 overflow-scroll h-[700px]"></div>
                </div>
            </x-slot>
        </x-mailcoach::editor-fields>
    @endforeach
</div>
