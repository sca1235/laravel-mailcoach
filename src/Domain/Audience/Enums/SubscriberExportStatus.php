<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum SubscriberExportStatus: string
{
    case Pending = 'pending';
    case Exporting = 'exporting';
    case Completed = 'completed';
    case Failed = 'failed';
}
