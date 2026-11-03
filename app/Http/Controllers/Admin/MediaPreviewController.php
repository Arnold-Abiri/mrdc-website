<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaPreviewController extends Controller
{
    public function page(Media $media): Response
    {
        Gate::authorize('view', $media);
        $disk = Storage::disk(config('cms.media_disk'));
        abort_unless($disk->exists($media->storage_path), 404);

        $media->loadMissing(['department', 'uploader']);

        $fileUrl = route('admin.media.file', $media->id);
        $isImage = str_starts_with($media->mime_type, 'image/');

        $formattedSize = match (true) {
            $media->size >= 1048576 => number_format($media->size / 1048576, 2).' MB',
            $media->size >= 1024 => number_format($media->size / 1024, 1).' KB',
            $media->size > 0 => $media->size.' bytes',
            default => 'Unknown',
        };

        $fileExtension = pathinfo($media->original_filename, PATHINFO_EXTENSION);

        $aspectRatio = null;
        if ($media->width && $media->height) {
            $gcd = function (int $a, int $b) use (&$gcd): int {
                return $b === 0 ? $a : $gcd($b, $a % $b);
            };
            $divisor = $gcd((int) $media->width, (int) $media->height);
            $w = (int) $media->width / $divisor;
            $h = (int) $media->height / $divisor;
            $aspectRatio = ($w <= 16 && $h <= 16) ? "{$w}:{$h}" : number_format($media->width / $media->height, 2).':1';
        }

        return response()->view('admin.media-preview', [
            'media' => $media,
            'fileUrl' => $fileUrl,
            'isImage' => $isImage,
            'formattedSize' => $formattedSize,
            'fileExtension' => strtoupper($fileExtension),
            'aspectRatio' => $aspectRatio,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request, Media $media): Response
    {
        Gate::authorize('view', $media);
        $disk = Storage::disk(config('cms.media_disk'));
        abort_unless($disk->exists($media->storage_path), 404);

        return $disk->response($media->storage_path, $media->original_filename, [
            'Content-Type' => $media->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'private, no-store',
        ], $request->boolean('download') ? 'attachment' : 'inline');
    }
}
