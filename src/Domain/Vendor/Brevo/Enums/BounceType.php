<?php

namespace Spatie\Mailcoach\Domain\Vendor\Brevo\Enums;

/** reference: https://developers.brevo.com/docs/transactional-webhooks */
enum BounceType: string
{
    case Soft = 'soft_bounce';
    case Hard = 'hard_bounce';
}
