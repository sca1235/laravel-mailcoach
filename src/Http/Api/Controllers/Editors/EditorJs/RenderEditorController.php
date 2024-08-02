<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\Editors\EditorJs;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Editor\EditorJs\Editor;

class RenderEditorController
{
    public function __invoke(Request $request)
    {
        $data = $request->all();

        $html = Editor::renderBlocks($data['blocks']);

        return response()->json(['html' => $html]);
    }
}
