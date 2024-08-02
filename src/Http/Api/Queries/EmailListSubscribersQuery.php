<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Queries\Filters\SearchFilter;
use Spatie\Mailcoach\Http\Api\Queries\Filters\SubscriberStatusFilter;
use Spatie\Mailcoach\Mailcoach;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListSubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        $subscribersQuery = self::getSubscriberClass()::query()
            ->where('email_list_id', $emailList->id)
            ->with('emailList', 'tags');

        parent::__construct($subscribersQuery, $request);

        $this
            ->allowedSorts('created_at', 'updated_at', 'subscribed_at', 'unsubscribed_at', 'email', 'first_name', 'last_name', 'id')
            ->allowedFilters(
                AllowedFilter::callback('email', function (Builder $query, $value) {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }

                    $value = trim($value ?? '');

                    return $query->where('email', $value);
                }),
                AllowedFilter::callback('tagType', function (Builder $query, $value) {
                    // Nothing
                }),
                AllowedFilter::callback('tags', function (Builder $query, $value) use ($emailList, $request) {
                    $value = Arr::wrap($value);

                    $tagIds = self::getTagClass()::query()
                        ->where('email_list_id', $emailList->id)
                        ->where(function (Builder $query) use ($value) {
                            $query->whereIn('uuid', $value)
                                ->orWhereIn('name', $value);
                        })
                        ->pluck('id');

                    if (! $tagIds->count()) {
                        return;
                    }

                    $request ??= request();

                    $prefix = DB::getTablePrefix();

                    if (($request->get('filter')['tagType'] ?? 'any') === 'all') {
                        $query->where(
                            DB::connection(Mailcoach::getDatabaseConnection())->table('mailcoach_email_list_subscriber_tags')
                                ->selectRaw('count(*)')
                                ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                                ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $tagIds->toArray()),
                            '>=', $tagIds->count()
                        );

                        return;
                    }

                    $query->addWhereExistsQuery(DB::connection(Mailcoach::getDatabaseConnection())->table('mailcoach_email_list_subscriber_tags')
                        ->where(self::getSubscriberTableName().'.id', DB::raw($prefix.'mailcoach_email_list_subscriber_tags.subscriber_id'))
                        ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $tagIds->toArray())
                    );
                }),
                AllowedFilter::custom('search', new SearchFilter()),
                AllowedFilter::custom('status', new SubscriberStatusFilter())
            );

        $request?->input('filter.status') === SubscriptionStatus::Unsubscribed
            ? $this->defaultSort('-unsubscribed_at')
            : $this->defaultSort('-created_at', '-id');
    }
}
