<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\Media;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TenderController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenders', ['tenders' => Tender::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['id', 'slug', 'reference', 'title', 'category', 'closes_at'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title', 'category', 'closes_at']), 'display_status' => $tender->displayStatus()])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $tender = Tender::query()->with('translations')->public()->with(['document', 'department.translations', 'department'])->where('slug', $slug)->firstOrFail();
        $document = $tender->document;

        return Inertia::render('Tender', ['tender' => [...$tender->toLocalizedArray(['slug', 'reference', 'title', 'category', 'description', 'opens_at', 'closes_at', 'contact_instructions', 'award_status', 'awarded_to', 'awarded_at', 'award_amount', 'award_reference', 'award_remarks']), 'display_status' => $tender->displayStatus(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null], 'department' => $tender->department instanceof Department ? $tender->department->translated('public_name') : null]);
    }

    public function download(string $locale, string $slug, Request $request)
    {
        $tender = Tender::query()->public()->with(['document.media'])->where('slug', $slug)->firstOrFail();
        $document = $tender->document;
        abort_unless($document instanceof Document && $document->status === 'published', 404, 'Tender document not available.');

        $media = $document->media;
        abort_unless($media instanceof Media, 404, 'Tender file not found.');
        abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'representative_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $tender->bidders()->create([
            'company_name' => $validated['company_name'],
            'representative_name' => $validated['representative_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'downloaded_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $document->increment('download_count');

        return Storage::disk(config('cms.media_disk'))->download(
            $media->storage_path,
            $media->original_filename,
            ['Content-Type' => $media->mime_type, 'X-Content-Type-Options' => 'nosniff']
        );
    }
}
