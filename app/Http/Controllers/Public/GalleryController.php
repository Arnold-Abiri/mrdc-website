<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function index(): Response
    {
        $galleries = Gallery::query()
            ->with(['translations', 'media', 'coverMedia'])
            ->public()
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('album_date')
            ->orderBy('title')
            ->get();

        return Inertia::render('Galleries', [
            'galleries' => $galleries->map(function (Gallery $gallery): array {
                $images = $this->activeImages($gallery);
                $cover = $gallery->coverMedia instanceof Media && $gallery->coverMedia->status === 'active'
                    ? $gallery->coverMedia
                    : $images->first();

                return [
                    ...$gallery->toLocalizedArray(['slug', 'title', 'summary', 'location']),
                    'album_date' => $gallery->album_date?->format('Y-m-d'),
                    'is_featured' => (bool) $gallery->is_featured,
                    'image_count' => $images->count(),
                    'cover_url' => $cover instanceof Media ? public_route('managed-media.show', $cover->id) : null,
                    'cover_alt' => $cover instanceof Media ? ($cover->alt_text ?: $gallery->title) : null,
                ];
            })->values(),
        ]);
    }

    public function show(string $locale, string $slug): Response
    {
        $gallery = Gallery::query()
            ->with(['translations', 'media', 'coverMedia', 'department'])
            ->public()
            ->where('slug', $slug)
            ->firstOrFail();

        $images = $this->activeImages($gallery)->map(fn (Media $media): array => [
            'url' => public_route('managed-media.show', $media->id),
            'alt' => $media->alt_text ?: $gallery->title,
            'caption' => $media->pivot->caption ?: $media->caption,
            'width' => $media->width,
            'height' => $media->height,
        ])->values();

        return Inertia::render('Gallery', [
            'gallery' => [
                ...$gallery->toLocalizedArray(['slug', 'title', 'summary', 'description', 'location']),
                'album_date' => $gallery->album_date?->format('Y-m-d'),
                'department' => $gallery->department?->name,
            ],
            'images' => $images,
        ]);
    }

    /**
     * @return Collection<int, Media>
     */
    private function activeImages(Gallery $gallery): Collection
    {
        return $gallery->media->where('status', 'active')->values();
    }
}
