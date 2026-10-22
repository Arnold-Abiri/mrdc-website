<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilProject;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\HomepageSlide;
use App\Models\Media;
use App\Models\Official;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    private const RESPONSIVE_WIDTHS = [640, 1024, 1600];

    public function show(Request $request, string $locale, Media $media): Response
    {
        abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 404);
        $isPublishedOfficialPhoto = Official::query()->public()->where('photo_media_id', $media->id)->exists();
        $isPublishedEditorialImage = EditorialItem::query()->with('translations')->public()->where('featured_media_id', $media->id)->exists();
        $isPublishedSlideImage = HomepageSlide::query()->public()->where('media_id', $media->id)->exists();
        $isPreviewSlideImage = session('preview_authorized') === true && HomepageSlide::query()->whereIn('status', ['draft', 'published'])->where('is_active', true)->where('media_id', $media->id)->exists();
        $isPublishedProjectImage = CouncilProject::query()->with('translations')->public()->where('featured_media_id', $media->id)->exists();
        $isPublishedGalleryImage = Gallery::query()->public()->where(fn ($query) => $query->where('cover_media_id', $media->id)->orWhereHas('media', fn ($images) => $images->where('media.id', $media->id)))->exists();
        $isPreviewGalleryImage = session('preview_authorized') === true && Gallery::query()->whereIn('status', ['draft', 'published'])->where(fn ($query) => $query->where('cover_media_id', $media->id)->orWhereHas('media', fn ($images) => $images->where('media.id', $media->id)))->exists();
        $isSiteLogo = SiteSetting::query()->where('key', 'logo_media_id')->where('value', (string) $media->id)->exists();
        abort_unless($isPublishedOfficialPhoto || $isPublishedEditorialImage || $isPublishedSlideImage || $isPreviewSlideImage || $isPublishedProjectImage || $isPublishedGalleryImage || $isPreviewGalleryImage || $isSiteLogo, 404);
        abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

        $disk = Storage::disk(config('cms.media_disk'));
        $width = (int) $request->query('w', 0);
        $path = $media->storage_path;
        $mime = $media->mime_type;

        if ($width > 0 && str_starts_with($mime, 'image/') && $mime !== 'image/svg+xml' && $mime !== 'image/gif') {
            $width = $this->nearestWidth($width);
            $variant = $this->responsiveVariant($disk, $media, $width);
            if ($variant !== null) {
                $path = $variant;
            }
        }

        $lastModified = $disk->lastModified($path);
        $etag = sprintf('"%s-%s"', $media->id, md5($path.$lastModified));

        $response = $disk->response($path, 'image', [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ], 'inline');

        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setImmutable();
        $response->setEtag($etag);
        $response->setLastModified(\DateTimeImmutable::createFromFormat('U', (string) $lastModified) ?: null);

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response;
    }

    private function nearestWidth(int $requested): int
    {
        $nearest = self::RESPONSIVE_WIDTHS[0];
        foreach (self::RESPONSIVE_WIDTHS as $candidate) {
            if ($candidate >= $requested) {
                return $candidate;
            }
            $nearest = $candidate;
        }

        return $nearest;
    }

    /**
     * Build (once) and cache a downscaled variant of the image using GD.
     *
     * @return string|null storage path of the variant, or null when resizing is not possible.
     */
    private function responsiveVariant(object $disk, Media $media, int $width): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagescale')) {
            return null;
        }

        $extension = strtolower(pathinfo($media->storage_path, PATHINFO_EXTENSION));
        $variant = sprintf('responsive/%d-w%d.%s', $media->id, $width, $extension === '' ? 'webp' : $extension);

        if ($disk->exists($variant)) {
            return $variant;
        }

        try {
            $original = $disk->get($media->storage_path);
        } catch (\Throwable) {
            return null;
        }

        $source = @imagecreatefromstring($original);
        if ($source === false) {
            return null;
        }

        $originalWidth = imagesx($source);
        if ($originalWidth <= $width) {
            imagedestroy($source);

            return null;
        }

        $scaled = imagescale($source, $width);
        imagedestroy($source);
        if ($scaled === false) {
            return null;
        }

        ob_start();
        $mime = $media->mime_type;
        if ($mime === 'image/png') {
            imagepng($scaled, null, 6);
        } elseif ($mime === 'image/jpeg') {
            imagejpeg($scaled, null, 82);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($scaled, null, 82);
        } else {
            imagejpeg($scaled, null, 82);
            $variant = preg_replace('/\.[a-z0-9]+$/i', '.jpg', $variant) ?? $variant;
        }
        $encoded = (string) ob_get_clean();
        imagedestroy($scaled);

        if ($encoded === '') {
            return null;
        }

        $disk->put($variant, $encoded);

        return $variant;
    }
}
