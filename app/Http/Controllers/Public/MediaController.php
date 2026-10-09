<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilProject;
use App\Models\EditorialItem;
use App\Models\HomepageSlide;
use App\Models\Media;
use App\Models\Official;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function show(string $locale, Media $media): Response
    {
        abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 404);
        $isPublishedOfficialPhoto = Official::query()->public()->where('photo_media_id', $media->id)->exists();
        $isPublishedEditorialImage = EditorialItem::query()->with('translations')->public()->where('featured_media_id', $media->id)->exists();
        $isPublishedSlideImage = HomepageSlide::query()->public()->where('media_id', $media->id)->exists();
        $isPreviewSlideImage = session('preview_authorized') === true && HomepageSlide::query()->whereIn('status', ['draft', 'published'])->where('is_active', true)->where('media_id', $media->id)->exists();
        $isPublishedProjectImage = CouncilProject::query()->with('translations')->public()->where('featured_media_id', $media->id)->exists();
        abort_unless($isPublishedOfficialPhoto || $isPublishedEditorialImage || $isPublishedSlideImage || $isPreviewSlideImage || $isPublishedProjectImage, 404);
        abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

        return Storage::disk(config('cms.media_disk'))->response($media->storage_path, 'image', [
            'Content-Type' => $media->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }
}
