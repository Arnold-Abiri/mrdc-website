<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\EditorialItem;
use Inertia\Inertia;
use Inertia\Response;

class EditorialController extends Controller
{
    public function newsIndex(): Response
    {
        return Inertia::render('EditorialList', ['type' => 'News', 'items' => EditorialItem::query()->with('translations')->public()->where('type', 'news')->orderByDesc('published_at')->get(['slug', 'title', 'summary', 'published_at', 'featured_media_id'])->map(fn (EditorialItem $item): array => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null])]);
    }

    public function newsShow(string $locale, string $slug): Response
    {
        $item = EditorialItem::query()->with('translations')->public()->where('type', 'news')->with(['documents' => fn ($query) => $query->public()])->where('slug', $slug)->firstOrFail();

        return Inertia::render('EditorialDetail', ['type' => 'News', 'item' => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'body', 'published_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null], 'documents' => $item->documents->map(fn (Document $document) => $document->toLocalizedArray(['slug', 'title']))]);
    }

    public function noticesIndex(): Response
    {
        return Inertia::render('EditorialList', ['type' => 'Notices', 'items' => EditorialItem::query()->with('translations')->public()->where('type', 'notice')->orderByDesc('published_at')->get(['slug', 'title', 'summary', 'published_at', 'featured_media_id', 'expires_at'])->map(fn (EditorialItem $item): array => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null])]);
    }

    public function noticesShow(string $locale, string $slug): Response
    {
        $item = EditorialItem::query()->with('translations')->public()->where('type', 'notice')->with(['documents' => fn ($query) => $query->public()])->where('slug', $slug)->firstOrFail();

        return Inertia::render('EditorialDetail', ['type' => 'Notice', 'item' => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'body', 'published_at', 'expires_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null], 'documents' => $item->documents->map(fn (Document $document) => $document->toLocalizedArray(['slug', 'title']))]);
    }
}
