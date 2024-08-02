@php($wireModelAttribute = collect($attributes)->first(fn (string $value, string $attribute) => str_starts_with($attribute, 'wire:model')))
<div x-cloak x-data="{
    imageUrl: @entangle($wireModelAttribute),
    error: '',
    loading: false,
    dropping: false,
    filename: '',
    fileChosen(event) {
        this.loading = true;
        this.error = '';
        let file = event.target.files[0];

        this.upload(file);
    },
    upload(file) {
        if (file.size > 1024 * 1024 * 2) {
            this.loading = false;
            this.error = 'File cannot be larger than 2MB.';
            return;
        }

        if (file.type.split('/')[0] !== 'image') {
            this.loading = false;
            this.error = 'File must be an image.';
            return;
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
        }).then(response => response.json())
          .then(({ success, file }) => {
              this.loading = false;
              if (! success) {
                  this.error = 'Something went wrong';
                  return;
              }

              this.imageUrl = file.url;
          });
    }
}">
    <div class="flex gap-6" x-show="!imageUrl">
        <div class="relative w-full">
            <label
                for="file"
                class="flex items-center justify-center p-9 border-2 border-sand-bleak rounded cursor-pointer"
                x-bind:class="[
                    imageUrl ? 'bg-sand-extra-light' : 'border-dotted',
                    dropping ? 'bg-sand-extra-light' : '',
                ]"
                x-on:dragover.prevent="dropping = true"
                x-on:dragleave.prevent="dropping = false"
                x-on:drop.prevent="(e) => {
                    error = '';
                    dropping = false;
                    const file = e.dataTransfer.files[0];

                    if (! file) return;

                    upload(file);
                }"
            >
                <div class="w-full text-center flex flex-col items-center justify-center gap-1">
                    <span x-show="!filename" wire:loading.remove wire:target="file,_startUpload" class="font-medium text-base">{{ __('Upload an image') }}</span>
                    <span x-show="!filename" wire:loading.remove wire:target="file,_startUpload" class="text-sm text-blue underline">{{ __mc('Drop your file or click to browse') }}</span>
                    <span x-show="error" x-text="error" class="text-red"></span>
                    <span wire:loading.flex wire:target="file,_startUpload" class="items-center gap-x-1 font-medium">
                        <svg class="w-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 24"><g fill="#131C2E" clip-path="url(#clip0_1014_5003)"><path d="M12.5 3c-4.969 0-9 4.031-9 9s4.031 9 9 9a8.994 8.994 0 0 0 7.73-4.387l.004.004a1.5 1.5 0 0 0 2.682 1.34A11.991 11.991 0 0 1 12.5 24c-6.628 0-12-5.372-12-12s5.372-12 12-12c-.83 0-1.5.67-1.5 1.5S11.67 3 12.5 3Z" opacity=".4"/><path d="M11 1.5c0-.83.67-1.5 1.5-1.5 6.628 0 12 5.372 12 12 0 2.184-.586 4.238-1.608 6a1.5 1.5 0 0 1-2.597-1.5A8.967 8.967 0 0 0 21.5 12c0-4.969-4.031-9-9-9-.83 0-1.5-.67-1.5-1.5Z"/></g><defs><clipPath id="clip0_1014_5003"><path fill="#fff" d="M.5 0h24v24H.5z"/></clipPath></defs></svg>
                        {{ __mc('Uploading') }}
                    </span>
                    <span class="w-full flex justify-between" x-show="filename">
                        <span x-text="filename"></span>
                        <button class="hover:text-red" wire:click="removeFile" type="button">
                            <x-heroicon-s-x-circle class="w-4" />
                        </button>
                    </span>
                </div>
            </label>
            <input
                id="file"
                class="absolute opacity-0 w-[0.1px] h-[0.1px]"
                x-on:change="(e) => {
                    error = '';
                    filename = e.target.value.split( '\\' ).pop();
                    fileChosen(e);
                }"
                accept="image/png,image/jpg,image/jpeg,image/gif"
                type="file"
            />
            @error('file')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-1" x-show="loading">
            <style>
                @keyframes loadingpulse {
                    0%   {transform: scale(.8); opacity: .75}
                    100% {transform: scale(1); opacity: .9}
                }
            </style>
            <span
                style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
            <span class="ml-1 text-gray-700">Uploading...</span>
        </div>
    </div>
    <template x-if="imageUrl">
        <div class="relative max-w-sm">
            <img :src="imageUrl"
                 class="object-cover rounded-md border border-gray-200 w-full card p-0"
            >
            <a href="" x-on:click.prevent="imageUrl = ''; filename = '';" class="link-danger mt-1 inline-block">
                {{ __mc('Remove image') }}
            </a>
        </div>
    </template>
</div>
