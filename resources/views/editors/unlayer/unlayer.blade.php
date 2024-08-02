<div class="mt-0">
    <script>
        function loadTemplate{{ $model->id }}() {
            document.getElementById('unlayer_template_error').classList.add('hidden');
            let slug = document.getElementById('unlayer_template').value;
            slug = slug.split('/').slice(-1)[0];

            fetch('https://api.graphql.unlayer.com/graphql', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    query: `
                        query StockTemplateLoad($slug: String!) {
                          StockTemplate(slug: $slug) {
                            StockTemplatePages {
                              design
                            }
                          }
                        }
                      `,
                    variables: {
                        slug: slug,
                    },
                }),
            })
                .then((res) => res.json())
                .then((result) => {
                    if (! result.data.StockTemplate) {
                        @if (config('mailcoach.unlayer.options.projectId'))
                        window.editor{{ $model->id }}.loadTemplate(slug);
                        Alpine.store('modals').close('load-unlayer-template');
                        @else
                        document.getElementById('unlayer_template_error').innerHTML = '{{ __mc('Template not found.') }}';
                        document.getElementById('unlayer_template_error').classList.remove('hidden');
                        @endif

                        return;
                    }

                    window.editor{{ $model->id }}.loadDesign(result.data.StockTemplate.StockTemplatePages[0].design);
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'load-unlayer-template-{{ $model->id }}' }}))
                });
        }

        window.init{{ $model->id }} = function() {
            document.getElementById('load-template-{{ $model->id }}').addEventListener('click', loadTemplate{{ $model->id }});

            let options = @js($options);
            options.id = 'editor{{ $model->id }}';

            const editor = unlayer.createEditor(options);

            window.editor{{ $model->id }} = editor;

            editor.loadDesign(JSON.parse(JSON.stringify(this.json).replaceAll('[[[', '&#91;&#91;&#91;')));

            if (! this.json) {
                editor.loadBlank({
                    backgroundColor: '#ffffff'
                });
            }

            editor.registerCallback('image', (file, done) => {
                let data = new FormData();
                data.append('file', file.attachments[0]);

                fetch('{{ action(\Spatie\Mailcoach\Http\Api\Controllers\UploadsController::class) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: data
                })
                .then(response => {
                    // Make sure the response was valid
                    if (response.status >= 200 && response.status < 300) {
                        return response.json()
                    }

                    let error = new Error(response.statusText);
                    error.response = response;
                    throw error
                }).then(data => done({ progress: 100, url: data.file.url }))
            });

            const mergeTags = @js(collect($replacers)->map(function (string $description, string $name) {
                return [
                    'name' => $name,
                    'value' => "@{{ $name }} "
                ];
            })->toArray());

            editor.setMergeTags(mergeTags);

            const component = this;
            editor.addEventListener('design:updated', () => {
                editor.exportHtml(function(data) {
                    component.html = data.html;
                    component.json = JSON.parse(JSON.stringify(data.design));
                });
            });

            editor.addEventListener('design:loaded', function(data) {
                editor.exportHtml(function(data) {
                    component.html = data.html;
                    component.json = data.design;
                });
            });
        }
    </script>

    <div class="max-w-full flex flex-col">
        <div wire:ignore x-data="{
            html: $wire.entangle('templateFieldValues.html').live,
            json: $wire.entangle('templateFieldValues.json').live,
            init: init{{ $model->id }},
        }" class="overflow-hidden flex-1 h-full mb-6">
            <div id="editor{{ $model->id }}" class="h-full pr-3 py-1" style="min-height: 75vh; height: 75vh"></div>
        </div>

        @isset($errors)
            @error('html')
            <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
        @endisset

        <div>
            <x-mailcoach::button-secondary x-on:click.prevent="$dispatch('open-modal', { id: 'load-unlayer-template-{{ $model->id }}' })" :label="__mc('Load Unlayer template')"/>
        </div>
    </div>
</div>

@push('modals')
    <x-mailcoach::modal :title="__mc('Load Unlayer template')" name="load-unlayer-template-{{ $model->id }}">
        <p>{!! __mc('You can load an <a class="text-blue-500" href="https://unlayer.com/templates" target="_blank">Unlayer template</a> by entering the URL') !!}</p>
        @if(config('mailcoach.unlayer.options.projectId'))
            <p>{{ __mc('A template id from your Unlayer project also works') }}</p>
        @endif

        <div>
            <x-mailcoach::text-field label="Unlayer template" name="unlayer_template" :placeholder="config('mailcoach.unlayer.options.projectId') ? __mc('URL or template id') : __mc('https://unlayer.com/templates/<template>')" />
            <p id="unlayer_template_error" class="form-error hidden mt-1" role="alert"></p>
        </div>

        <div class="form-buttons">
            <x-mailcoach::button class="mt-auto" id="load-template-{{ $model->id }}" label="Load" type="button" />
            <x-mailcoach::button-link x-on:click.prevent="$dispatch('close-modal', { id: 'load-unlayer-template-{{ $model->id }}' })" :label=" __mc('Cancel')" />
        </div>
    </x-mailcoach::modal>
@endpush
