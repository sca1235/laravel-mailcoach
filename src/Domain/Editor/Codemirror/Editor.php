<?php

namespace Spatie\Mailcoach\Domain\Editor\Codemirror;

use Illuminate\Contracts\View\View;
use Spatie\Mailcoach\Livewire\Editor\EditorComponent;

class Editor extends EditorComponent
{
    public function render(): View
    {
        return view('mailcoach::editors.codemirror.editor');
    }
}
