@props([
    'class' => '',
    'email',
    'sender',
])
<div
    class="{{ $class }}"
    x-data="{
        url: '',
        image: '',
    }"
    x-init="async () => {
        try {
            const response = await fetch(`https://sniperl.ink/v1/render?recipient={{ $email }}&sender={{ $sender }}`);
            const data = await response.json();
            url = data.url;
            image = data.image;
        } catch (e) {}
    }"
    x-show="url"
>
    <a x-bind:href="url" target="_blank" class="input underline flex items-center font-medium gap-x-2">
        <img class="w-4" x-bind:src="image" alt="">
        {{ __mc('Check your inbox') }}
        <svg class="fill-current w-3 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 12"><path d="M10.677.5h.82v9.839h-1.64V3.297l-7.618 7.621-.58.581L.5 10.338l.58-.58L8.7 2.14H1.658V.5h9.019Z"/></svg>
    </a>
</div>
