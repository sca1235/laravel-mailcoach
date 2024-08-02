<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Actions\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Template\Actions\UpdateTemplateAction;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Queries\TemplatesQuery;
use Spatie\Mailcoach\Http\Api\Requests\TemplateRequest;
use Spatie\Mailcoach\Http\Api\Resources\TemplateResource;

class TemplatesController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(TemplatesQuery $templatesQuery)
    {
        $this->authorize('viewAny', static::getTemplateClass());

        $templates = $templatesQuery->paginate();

        return TemplateResource::collection($templates);
    }

    public function show(Template $template)
    {
        $this->authorize('view', $template);

        return new TemplateResource($template);
    }

    public function store(
        TemplateRequest $request,
        CreateTemplateAction $createTemplateAction
    ) {
        $this->authorize('create', static::getTemplateClass());

        $template = $createTemplateAction->execute($request->validated());

        return new TemplateResource($template);
    }

    public function update(
        Template $template,
        TemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $this->authorize('update', $template);

        $template = $updateTemplateAction->execute($template, $request->validated());

        return new TemplateResource($template);
    }

    public function destroy(Template $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        return $this->respondOk();
    }
}
