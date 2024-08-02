<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Manipulations;
use Spatie\Mailcoach\Domain\Shared\Models\Concerns\UsesDatabaseConnection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Upload extends Model implements HasMedia
{
    use HasUuid;
    use InteractsWithMedia;
    use UsesDatabaseConnection;

    public $table = 'mailcoach_uploads';

    public $guarded = [];

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && strtolower($media->extension) === 'gif') {
            return;
        }

        if (! class_exists(Manipulations::class)) {
            $this
                ->addMediaConversion('image')
                ->keepOriginalImageFormat()
                ->fit(
                    Fit::Max,
                    config('mailcoach.uploads.max_width', 1500),
                    config('mailcoach.uploads.max_height', 1500)
                )
                ->nonQueued();

            return;
        }

        $this
            ->addMediaConversion('image')
            ->keepOriginalImageFormat()
            ->fit(
                Manipulations::FIT_MAX,
                config('mailcoach.uploads.max_width', 1500),
                config('mailcoach.uploads.max_height', 1500)
            )
            ->nonQueued();
    }
}
