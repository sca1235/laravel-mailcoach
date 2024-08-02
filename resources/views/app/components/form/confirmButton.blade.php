@props([
    'confirmText' => __mc('Are you sure?'),
    'confirmLabel' => __mc('Confirm'),
    'onConfirm' => '() => $refs.form.submit()',
    'action' => null,
    'method' => 'POST',
    'class' => '',
    'disabled' => false,
    'danger' => false,
])
<form
    class="flex {{ $formClass ?? '' }}"
    method="POST"
    action="{{ $action }}"
    {{ $attributes->except('class') }}
    x-data
    x-ref="form"
>
    @csrf
    @method($method)
    <button
        x-on:click.prevent="
            confirmText = @js($confirmText);
            confirmLabel = @js($confirmLabel);
            danger = @js($danger);
            onConfirm = {{ $onConfirm }};
            $dispatch('open-modal', { id: 'confirm' });
        "
        type="submit"
        class="{{ $class ?? '' }}"
        @if($disabled) disabled @endif
    >
        {{ $slot }}
    </button>
</form>
