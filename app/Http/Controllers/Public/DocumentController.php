<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->query('category');
        $year = $request->query('year');
        $departmentId = $request->query('department');
        $keyword = trim((string) $request->query('q', ''));
        abort_unless($category === null || in_array($category, Document::CATEGORIES, true), 422);
        abort_unless($year === null || (is_numeric($year) && (int) $year >= 1990 && (int) $year <= (int) now()->format('Y') + 1), 422);

        $documents = Document::query()->with(['translations', 'media:id,size,mime_type,original_filename', 'department:id,name,public_name'])->public()
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($year, fn ($query) => $query->whereYear('reference_date', (int) $year))
            ->when($departmentId, fn ($query) => $query->where('department_id', (int) $departmentId))
            ->when($keyword !== '', function ($query) use ($keyword): void {
                $escaped = addcslashes($keyword, '%_\\');
                $query->where(fn ($builder) => $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%'));
            })
            ->orderByDesc('published_at')
            ->get(['id', 'slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count', 'media_id', 'department_id'])
            ->map(function (Document $doc): array {
                $bytes = $doc->media?->size ?? 0;
                $formattedSize = $bytes >= 1048576
                    ? number_format($bytes / 1048576, 2).' MB'
                    : ($bytes >= 1024 ? number_format($bytes / 1024, 1).' KB' : ($bytes > 0 ? $bytes.' B' : 'PDF'));

                return [
                    'slug' => $doc->slug,
                    'title' => $doc->title,
                    'description' => $doc->description,
                    'category' => $doc->category,
                    'published_at' => $doc->published_at?->toIso8601String(),
                    'reference_date' => $doc->reference_date?->format('Y-m-d'),
                    'download_count' => (int) $doc->download_count,
                    'file_size' => $formattedSize,
                    'mime_type' => $doc->media?->mime_type ?? 'application/pdf',
                    'department' => $doc->department?->public_name ?? $doc->department?->name ?? null,
                ];
            });

        $years = Document::query()->with('translations')->public()->whereNotNull('reference_date')->orderByDesc('reference_date')->pluck('reference_date')->map(fn ($date): int => (int) substr((string) $date, 0, 4))->unique()->values();

        // Calculate category document counts for quick badges
        $categoryCounts = Document::query()->public()->selectRaw('category, count(*) as aggregate')->groupBy('category')->pluck('aggregate', 'category')->all();

        return Inertia::render('Documents', [
            'documents' => $documents,
            'categories' => Document::CATEGORIES,
            'category_counts' => $categoryCounts,
            'years' => $years,
            'total_count' => Document::query()->public()->count(),
            'filters' => [
                'category' => $category,
                'year' => $year,
                'department' => $departmentId,
                'q' => $keyword,
            ],
        ]);
    }

    public function show(string $locale, string $slug): Response
    {
        $document = Document::query()->with(['translations', 'media', 'department'])->public()->where('slug', $slug)->firstOrFail();

        $bytes = $document->media?->size ?? 0;
        $formattedSize = $bytes >= 1048576
            ? number_format($bytes / 1048576, 2).' MB'
            : ($bytes >= 1024 ? number_format($bytes / 1024, 1).' KB' : ($bytes > 0 ? $bytes.' B' : 'PDF'));

        $related = Document::query()->with(['media'])->public()
            ->where('id', '!=', $document->id)
            ->where('category', $document->category)
            ->limit(3)
            ->get(['slug', 'title', 'category', 'download_count'])
            ->map(fn ($d) => [
                'slug' => $d->slug,
                'title' => $d->title,
                'category' => $d->category,
                'download_count' => (int) $d->download_count,
            ]);

        $hasFile = $document->media instanceof Media && Storage::disk(config('cms.media_disk'))->exists($document->media->storage_path);

        return Inertia::render('Document', [
            'document' => [
                ...$document->toLocalizedArray(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count', 'current_version']),
                'has_file' => $hasFile,
                'file_size' => $formattedSize,
                'mime_type' => $document->media?->mime_type ?? 'application/pdf',
                'department' => $document->department?->public_name ?? $document->department?->name ?? null,
            ],
            'reference_year' => $document->referenceYear(),
            'has_file' => $hasFile,
            'related' => $related,
        ]);
    }

    public function download(string $locale, string $slug): StreamedResponse
    {
        $document = Document::query()->with('translations')->public()->where('slug', $slug)->with('media')->first();
        if (! $document instanceof Document && session('preview_authorized') === true) {
            // Stakeholder preview: allow downloading draft guides from a signed preview session.
            $document = Document::query()->where('slug', $slug)->where('status', 'draft')->with('media')->first();
        }
        abort_unless($document instanceof Document, 404);
        $media = $document->media;
        abort_unless($media instanceof Media, 404);
        abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);
        $document->increment('download_count');
        DB::table('document_downloads')->insert(['document_id' => $document->id, 'version_number' => $document->current_version, 'downloaded_at' => now()]);

        return Storage::disk(config('cms.media_disk'))->download(
            $media->storage_path,
            $media->original_filename,
            ['Content-Type' => $media->mime_type, 'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "default-src 'none'; sandbox"]
        );
    }
}
