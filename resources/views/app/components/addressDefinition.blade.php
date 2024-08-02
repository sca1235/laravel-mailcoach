@if(count($addresses))
    <x-mailcoach::checklist-item
        neutral
        :label="$label"
    >
        <ul>
            @foreach($addresses as $address)
                <li>
                    {{ $address['email'] }}
                    @if ($address['name'])
                        <span class="text-gray-500">
                    ({{ $address['name'] }})
                    </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </x-mailcoach::checklist-item>
@endif
