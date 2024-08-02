<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Queries\SegmentsQuery;
use Spatie\Mailcoach\Http\Api\Requests\TagSegmentRequest;
use Spatie\Mailcoach\Http\Api\Resources\SegmentResource;
use Spatie\Mailcoach\Mailcoach;

class SegmentsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(EmailList $emailList)
    {
        $segmentsQuery = new SegmentsQuery($emailList);

        $this->authorize('viewAny', static::getTagSegmentClass());

        $segments = $segmentsQuery->paginate();

        return SegmentResource::collection($segments);
    }

    public function show(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('view', $segment);

        $segment->load('emailList');

        return new SegmentResource($segment);
    }

    public function store(EmailList $emailList, TagSegmentRequest $request)
    {
        $tagClass = self::getTagSegmentClass();

        $this->authorize('create', $tagClass);

        $positiveTags = self::getTagClass()::query()
            ->where('email_list_id', $emailList->id)
            ->whereIn('name', $request->validated('positive_tags', []))
            ->pluck('id');

        $negativeTags = self::getTagClass()::query()
            ->where('email_list_id', $emailList->id)
            ->whereIn('name', $request->validated('negative_tags', []))
            ->pluck('id');

        $storedConditions = [];

        if ($positiveTags->count()) {
            $storedConditions[] = [
                'value' => $positiveTags->values()->toArray(),
                'condition_key' => 'subscriber_tags',
                'comparison_operator' => $request->validated('all_positive_tags_required')
                    ? 'all'
                    : 'in',
            ];
        }

        if ($negativeTags->count()) {
            $storedConditions[] = [
                'value' => $negativeTags->values()->toArray(),
                'condition_key' => 'subscriber_tags',
                'comparison_operator' => $request->validated('all_negative_tags_required')
                    ? 'none'
                    : 'not-in',
            ];
        }

        $segment = $emailList->segments()->create([
            'name' => $request->validated('name'),
        ]);

        /** We update it like this so we bypass the cast */
        DB::connection(Mailcoach::getDatabaseConnection())->table(self::getTagSegmentTableName())
            ->where('id', $segment->id)
            ->update([
                'stored_conditions' => json_encode($storedConditions),
            ]);

        return SegmentResource::make($segment);
    }

    public function update(EmailList $emailList, TagSegment $segment, TagSegmentRequest $request)
    {
        $this->authorize('update', $segment);

        $positiveTags = self::getTagClass()::query()
            ->where('email_list_id', $emailList->id)
            ->whereIn('name', $request->validated('positive_tags', []))
            ->pluck('id');

        $negativeTags = self::getTagClass()::query()
            ->where('email_list_id', $emailList->id)
            ->whereIn('name', $request->validated('negative_tags', []))
            ->pluck('id');

        $storedConditions = [];

        if ($positiveTags->count()) {
            $storedConditions[] = [
                'value' => $positiveTags->values()->toArray(),
                'condition_key' => 'subscriber_tags',
                'comparison_operator' => $request->validated('all_positive_tags_required')
                    ? 'all'
                    : 'in',
            ];
        }

        if ($negativeTags->count()) {
            $storedConditions[] = [
                'value' => $negativeTags->values()->toArray(),
                'condition_key' => 'subscriber_tags',
                'comparison_operator' => $request->validated('all_negative_tags_required')
                    ? 'none'
                    : 'not-in',
            ];
        }

        $segment->update([
            'name' => $request->validated('name'),
        ]);

        /** We update it like this so we bypass the cast */
        DB::connection(Mailcoach::getDatabaseConnection())
            ->table(self::getTagSegmentTableName())
            ->where('id', $segment->id)
            ->update([
                'stored_conditions' => json_encode($storedConditions),
            ]);

        return SegmentResource::make($segment);
    }

    public function destroy(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('delete', $segment);

        $segment->delete();

        return $this->respondOk();
    }
}
