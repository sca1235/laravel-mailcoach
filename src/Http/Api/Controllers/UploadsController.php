<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UploadsController
{
    use UsesMailcoachModels;

    public function __invoke(Request $request)
    {
        try {
            $data = $request->validate([
                'file' => ['nullable', 'required_without:url', 'image'],
                'url' => ['nullable', 'url', 'required_without:file'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'errors' => $e->errors(),
            ], 422);
        }

        if (isset($data['file'])) {
            /** @var \Spatie\Mailcoach\Domain\Shared\Models\Upload $upload */
            $upload = self::getUploadClass()::create();
            $media = $upload
                ->addMediaFromRequest('file')
                ->sanitizingFileName(function (string $fileName) {
                    $parts = explode('.', $fileName);
                    $extension = strtolower(array_pop($parts));

                    return Str::slug(implode($parts)).'.'.$extension;
                })
                ->toMediaCollection(
                    config('mailcoach.uploads.collection_name', 'default'),
                    config('mailcoach.uploads.disk_name'),
                );
        }

        if (isset($data['url'])) {
            /** @var \Spatie\Mailcoach\Domain\Shared\Models\Upload $upload */
            $upload = self::getUploadClass()::create();
            $media = $upload
                ->addMediaFromUrl($data['url'])
                ->toMediaCollection(
                    config('mailcoach.uploads.collection_name', 'default'),
                    config('mailcoach.uploads.disk_name'),
                );
        }

        if (! isset($media)) {
            return response()->json([
                'success' => 0,
            ]);
        }

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => strtolower($media->extension) === 'gif'
                    ? $media->getFullUrl()
                    : $media->getFullUrl('image'),
            ],
        ]);
    }
}
