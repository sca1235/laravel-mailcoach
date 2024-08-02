<div class="{{ $class ?? '' }} gap-y-1 flex flex-col w-full px-6 py-3 sm:py-0 text-center">
    <div class="{{ $numClass ?? 'text-3xl sm:text-[36px] font-semibold font-title' }}">
        {{ $prefix ?? '' }}
        {!! $stat ?? 0 !!}{{ $suffix ?? ''}}
</div>
<div class="text-xs sm:text-sm flex justify-center mt-1">
@if($href ?? null)
    <a class="text-blue-dark underline" href="{{$href}}">{!! $label !!}</a>
@else
    {!! $label !!}
@endif
</div>
</div>
