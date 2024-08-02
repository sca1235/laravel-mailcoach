<!-- bg-sand -->
<div class="flex flex-col items-center">
    @include('mailcoach::app.automations.components.actionDropdown', ['index' => 0])

    @foreach ($actions as $index => $action)
        @if ($action['class']::getComponent())
            @livewire($action['class']::getComponent(), array_merge([
                'index' => $index,
                'builderName' => $name,
                'uuid' => $action['uuid'],
                'action' => $action,
                'automation' => $automation,
            ], ($action['data'] ?? [])), key($index . $action['uuid']))
        @else
            @livewire('mailcoach::automation-action', array_merge([
                'index' => $index,
                'builderName' => $name,
                'uuid' => $action['uuid'],
                'action' => $action,
                'automation' => $automation,
                'editable' => false,
            ], ($action['data'] ?? [])), key($index . $action['uuid']))
        @endif

        @unless($loop->last)
            @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
        @endunless
    @endforeach

    @if (count($actions))
        @include('mailcoach::app.automations.components.actionDropdown', ['index' => ($index ?? 0) + 1])
    @endif
</div>
