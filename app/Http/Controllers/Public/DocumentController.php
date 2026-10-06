<?php

namespace App\Http\Controllers\Public;

use App\Models\Media;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $category = $request->query('category');
                $year = $request->query('year');
                $departmentId = $request->query('department');
                $keyword = trim((string) $request->query('q', ''));
                abort_unless($category === null || in_array($category, Document::CATEGORIES, true), 422);
                abort_unless($year === null || (is_numeric($year) && (int) $year >= 1990 && (int) $year <= (int) now()->format('Y') + 1), 422);

                $documents = Document::query()->with('translations')->public()
                    ->when($category, fn ($query) => $query->where('category', $category))
                    ->when($year, fn ($query) => $query->whereYear('reference_date', (int) $year))
                    ->when($departmentId, fn ($query) => $query->where('department_id', (int) $departmentId))
                    ->when($keyword !== '', function ($query) use ($keyword): void {
                        $escaped = addcslashes($keyword, '%_\\');
                        $query->where(fn ($builder) => $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%'));
                    })
                    ->orderByDesc('published_at')
                    ->get(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count']);
                $years = Document::query()->with('translations')->public()->whereNotNull('reference_date')->orderByDesc('reference_date')->pluck('reference_date')->map(fn ($date): int => (int) substr((string) $date, 0, 4))->unique()->values();

                return Inertia::render('Documents', ['documents' => $documents, 'categories' => Document::CATEGORIES, 'years' => $years, 'filters' => ['category' => $category, 'year' => $year, 'department' => $departmentId, 'q' => $keyword]]);
    }

    public function show(string $locale, string $slug): \Inertia\Response
    {
        $document = Document::query()->with('translations')->public()->where('slug', $slug)->firstOrFail();

                return Inertia::render('Document', ['document' => [...$document->toLocalizedArray(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count', 'current_version']), 'reference_year' => $document->referenceYear()]]);
    }

    public function download(string $locale, string $slug): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $document = Document::query()->with('translations')->public()->where('slug', $slug)->with('media')->firstOrFail();
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