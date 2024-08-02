@assets
<style>
    /* Override the styles set by Filament for EasyMDE */
    .EasyMDEContainer .editor-toolbar button:before {
        -webkit-mask-image: none !important;
        mask-image: none !important;
        display: none;
        content: '';
    }

    .cm-s-easymde .cm-header-1 {
        font-size: 1.875rem
    }

    .cm-s-easymde .cm-header-2 {
        font-size: 1.5rem
    }

    .cm-s-easymde .cm-header-3 {
        font-size: 1.25rem
    }

    .cm-s-easymde .cm-header-4 {
        font-size: 1.125rem
    }

    .cm-s-easymde .cm-header-5 {
        font-size:1.125rem
    }

    .cm-s-easymde .cm-header-6 {
        font-size:1rem
    }

    .cm-s-easymde .cm-comment {
        background: none;
    }

    .cm-keyword {color: #708;}
    .cm-atom {color: #219;}
    .cm-number {color: #164;}
    .cm-def {color: #00f;}
    .cm-variable,
    .cm-punctuation,
    .cm-property,
    .cm-operator {}
    .cm-variable-2 {color: #05a;}
    .cm-formatting-list, .cm-formatting-list + .cm-variable-2 {color: #000;}
    .cm-variable-3, .cm-s-default .cm-type {color: #085;}
    .cm-comment {color: #a50;}
    .cm-string {color: #a11;}
    .cm-string-2 {color: #f50;}
    .cm-meta {color: #555;}
    .cm-qualifier {color: #555;}
    .cm-builtin {color: #30a;}
    .cm-bracket {color: #997;}
    .cm-tag {color: #170;}
    .cm-attribute {color: #00c;}
    .cm-hr {color: #999;}
    .cm-link {color: #00c;}
</style>
<script>
    window.debounce = function (func, timeout = 300){
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }
</script>
@endassets
<div class="form-grid">
    <script>
        window.init{{ $model->id }} = function() {
            let editor = new EasyMDE({
                autoDownloadFontAwesome: true,
                element: this.$refs.editor,
                uploadImage: true,
                placeholder: '{{ __mc('Start writing…') }}',
                initialValue: this.markdown,
                spellChecker: false,
                autoSave: false,
                direction: '{{ config('mailcoach.markdown.options.text_direction', 'ltr') }}',
                status: [{
                            className: "upload-image",
                            defaultValue: ''
                        }],
                toolbar: [
                    "heading", "bold", "italic", "link",
                    "|",
                    "quote", "unordered-list", "ordered-list", "table",
                    "|",
                    {
                        name: "upload-image",
                        action: EasyMDE.drawUploadedImage,
                        className: "fa fa-image",
                    },
                    "undo",
                    "redo",
                ],
                imageAccept: 'image/png, image/jpeg, image/gif, image/avif',
                imageUploadFunction: function(file, onSuccess, onError) {
                    if (file.size > 1024 * 1024 * 2) {
                        return onError('File cannot be larger than 2MB.');
                    }

                    if (file.type.split('/')[0] !== 'image') {
                        return onError('File must be an image.');
                    }

                    const data = new FormData();
                    data.append('file', file);

                    fetch('{{ action(\Spatie\Mailcoach\Http\Api\Controllers\UploadsController::class) }}', {
                        method: 'POST',
                        body: data,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
                    })
                        .then(response => response.json())
                        .then(({ success, file }) => {
                            if (! success) {
                                return onError();
                            }

                            onSuccess(file.url);
                        });
                },
            });

            // "Create Link (Ctrl-K)": highlight URL instead of label:
            editor.codemirror.on('changes', (instance, changes) => {
                try {
                    // Grab the last change from the buffered list. I assume the
                    // buffered one ('changes', instead of 'change') is more efficient,
                    // and that "Create Link" will always end up last in the list.
                    const lastChange = changes[changes.length - 1];
                    if (lastChange.origin === '+input') {
                        // https://github.com/Ionaru/easy-markdown-editor/blob/8fa54c496f98621d5f45f57577ce630bee8c41ee/src/js/easymde.js#L765
                        const EASYMDE_URL_PLACEHOLDER = '(https://)';

                        // The URL placeholder is always placed last, so just look at the
                        // last text in the array to also cover the multi-line case:
                        const urlLineText = lastChange.text[lastChange.text.length - 1];

                        if (urlLineText.endsWith(EASYMDE_URL_PLACEHOLDER) && urlLineText !== '[]' + EASYMDE_URL_PLACEHOLDER) {
                            const from = lastChange.from;
                            const to = lastChange.to;
                            const isSelectionMultiline = lastChange.text.length > 1;
                            const baseIndex = isSelectionMultiline ? 0 : from.ch;

                            // Everything works fine for the [Ctrl-K] case, but for the
                            // [Button] case, this handler happens before the original
                            // code, thus our change got wiped out.
                            // Add a small delay to handle that case.
                            setTimeout(() => {
                                instance.setSelection(
                                    { line: to.line, ch: baseIndex + urlLineText.lastIndexOf('(') + 1 },
                                    { line: to.line, ch: baseIndex + urlLineText.lastIndexOf(')') }
                                );
                            }, 25);
                        }
                    }
                } catch (err) {
                    // Do nothing (revert to original behavior)
                }
            });

            editor.codemirror.on("change", debounce(() => {
                this.markdown = editor.value();
                renderToHtml(this);
            }));

            function renderToHtml(instance) {
                fetch('{{ route('mailcoach-markdown-editor.render-markdown') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        markdown: instance.markdown,
                    })
                })
                    .then(response => response.text())
                    .then(html => instance.html = html);
            }
        }
    </script>
    @if ($model->hasTemplates())
        <x-mailcoach::template-chooser :clearable="false" />
    @endif

    @foreach($template?->fields() ?? [['name' => 'html', 'type' => 'editor']] as $field)
        <x-mailcoach::editor-fields :name="$field['name']" :type="$field['type']" :label="$field['name'] === 'html' ? 'Markdown' : null">
            <x-slot name="editor">
                <div class="markup markup-editor markup-lists markup-links markup-code"
                    wire:ignore x-data="{
                    html: @entangle('templateFieldValues.' . $field['name'] . '.html').live,
                    markdown: @entangle('templateFieldValues.' . $field['name'] . '.markdown').live,
                    init: init{{ $model->id }},
                }">
                    <textarea x-ref="editor"></textarea>
                </div>
            </x-slot>
        </x-mailcoach::editor-fields>
    @endforeach

    <div class="-mt-4 flex gap-4">
        <a class="text-navy underline" href="https://www.markdownguide.org/basic-syntax/" target="_blank">Markdown syntax</a>
    </div>
</div>
