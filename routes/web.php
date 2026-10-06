<?php

use App\Http\Controllers\PublicEnquiryController;
use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'services' => Service::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary']),
        'documents' => Document::query()->public()->orderByDesc('published_at')->limit(5)->get(['slug', 'title', 'description']),
        'news' => EditorialItem::query()->public()->where('type', 'news')->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
        'notices' => EditorialItem::query()->public()->where('type', 'notice')->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
        'departments' => Department::query()->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']),
        'contacts' => PublicContact::query()->public()->orderBy('display_order')->limit(4)->get(['office', 'type', 'value']),
        'officials' => Official::query()->public()->orderBy('display_order')->orderBy('name')->limit(3)->get(['slug', 'name', 'title']),
        'ward_count' => Ward::query()->public()->count(),
    ]);
})->name('home');

Route::post('/locale', function (Request $request) {
    $data = $request->validate(['locale' => ['required', 'in:en,sn']]);
    $request->session()->put('public_locale', $data['locale']);

    return back();
})->name('locale.update');

Route::get('/contact', [PublicEnquiryController::class, 'create'])->name('contact.create');
Route::post('/contact', [PublicEnquiryController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/search', function (Request $request) {
    $rawQuery = $request->query('q', '');
    abort_unless(is_string($rawQuery), 422);
    $query = trim($rawQuery);
    abort_if(mb_strlen($query) > 100, 422);

    $pages = $query === '' ? collect() : Page::query()->public()
        ->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')
                ->orWhere('summary', 'like', '%'.$escaped.'%');
        })
        ->orderBy('title')->limit(20)->get(['slug', 'title', 'summary', 'verification_status']);

    $documents = $query === '' ? collect() : Document::query()->public()
        ->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')
                ->orWhere('description', 'like', '%'.$escaped.'%');
        })
        ->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);

    $pageResults = $pages->map(fn (Page $page): array => [
        'type' => 'Page', 'title' => $page->title, 'summary' => $page->summary,
        'url' => route('pages.show', $page->slug),
        'is_review_content' => $page->verification_status !== 'publishable',
    ]);
    $documentResults = $documents->map(fn (Document $document): array => [
        'type' => 'Document', 'title' => $document->title, 'summary' => $document->description,
        'url' => route('documents.show', $document->slug), 'is_review_content' => false,
    ]);
    $services = $query === '' ? collect() : Service::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'summary']);
    $serviceResults = $services->map(fn (Service $service): array => [
        'type' => 'Service', 'title' => $service->name, 'summary' => $service->summary,
        'url' => route('services.show', $service->slug), 'is_review_content' => false,
    ]);
    $departments = $query === '' ? collect() : Department::query()->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('public_name', 'like', '%'.$escaped.'%')->orWhere('public_summary', 'like', '%'.$escaped.'%')->orWhere('public_description', 'like', '%'.$escaped.'%');
    })->orderBy('public_display_order')->orderBy('public_name')->limit(20)->get(['id', 'public_name', 'public_summary']);
    $departmentResults = $departments->map(fn (Department $department): array => [
        'type' => 'Department', 'title' => $department->public_name, 'summary' => $department->public_summary,
        'url' => route('departments.public.show', $department->id), 'is_review_content' => false,
    ]);
    $editorial = $query === '' ? collect() : EditorialItem::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('body', 'like', '%'.$escaped.'%');
    })->orderByDesc('published_at')->limit(20)->get(['type', 'slug', 'title', 'summary']);
    $editorialResults = $editorial->map(fn (EditorialItem $item): array => [
        'type' => $item->type === 'news' ? 'News' : 'Notice', 'title' => $item->title, 'summary' => $item->summary,
        'url' => route($item->type === 'news' ? 'news.show' : 'notices.show', $item->slug), 'is_review_content' => false,
    ]);
    $wards = $query === '' ? collect() : Ward::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%')->orWhere('boundaries_description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'description']);
    $wardResults = $wards->map(fn (Ward $ward): array => [
        'type' => 'Ward', 'title' => $ward->name, 'summary' => $ward->description,
        'url' => route('wards.show', $ward->slug), 'is_review_content' => false,
    ]);
    $officials = $query === '' ? collect() : Official::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('title', 'like', '%'.$escaped.'%')->orWhere('biography', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'title', 'biography']);
    $officialResults = $officials->map(fn (Official $official): array => [
        'type' => 'Official', 'title' => $official->name, 'summary' => $official->title,
        'url' => route('officials.show', $official->slug), 'is_review_content' => false,
    ]);

    return Inertia::render('Search', ['query' => $query, 'results' => $pageResults->concat($documentResults)->concat($serviceResults)->concat($departmentResults)->concat($editorialResults)->concat($wardResults)->concat($officialResults)->take(20)->values()]);
})->name('search');

Route::get('/coming-soon', function () {
    $topics = ['council', 'media', 'engagement', 'search', 'tenders', 'vacancies', 'news', 'events', 'services', 'projects', 'tourism', 'contact', 'privacy', 'terms', 'accessibility', 'water', 'roads', 'health', 'environment', 'development', 'community'];
    $topic = request()->query('topic');

    return Inertia::render('ComingSoon', [
        'topic' => is_string($topic) && in_array($topic, $topics, true) ? ucfirst($topic) : 'This section',
    ]);
})->name('coming-soon');

Route::get('/sitemap.xml', function () {
    $url = htmlspecialchars(route('home'), ENT_XML1, 'UTF-8');

    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.$url.'</loc></url></urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

Route::get('/pages/{slug}', function (string $slug) {
    $page = Page::query()->public()->where('slug', $slug)->firstOrFail();

    return Inertia::render('CmsPage', [
        'page' => [...$page->only(['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => $page->verification_status !== 'publishable'],
    ]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('pages.show');

Route::get('/documents', function () {
    $documents = Document::query()->public()->orderByDesc('published_at')->get(['slug', 'title', 'description', 'category', 'published_at']);

    return Inertia::render('Documents', ['documents' => $documents]);
})->name('documents.index');

Route::get('/documents/{slug}', function (string $slug) {
    $document = Document::query()->public()->where('slug', $slug)->firstOrFail();

    return Inertia::render('Document', ['document' => $document->only(['slug', 'title', 'description', 'category', 'published_at', 'reference_date'])]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.show');

Route::get('/documents/{slug}/download', function (string $slug) {
    $document = Document::query()->public()->where('slug', $slug)->with('media')->firstOrFail();
    $media = $document->media;
    abort_unless($media instanceof Media, 404);
    abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

    return Storage::disk(config('cms.media_disk'))->download(
        $media->storage_path,
        $media->original_filename,
        ['Content-Type' => $media->mime_type, 'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "default-src 'none'; sandbox"]
    );
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.download');

Route::get('/services', function () {
    return Inertia::render('Services', ['services' => Service::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary'])]);
})->name('services.index');

Route::get('/services/{slug}', function (string $slug) {
    $service = Service::query()->public()->with('department')->where('slug', $slug)->firstOrFail();

    $department = $service->department;

    return Inertia::render('Service', ['service' => $service->only(['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'seo_title', 'meta_description']), 'department' => $department instanceof Department ? $department->name : null]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('services.show');

Route::get('/departments', function () {
    $departments = Department::query()->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']);

    return Inertia::render('PublicDepartments', ['departments' => $departments]);
})->name('departments.public.index');

Route::get('/departments/{department}', function (Department $department) {
    abort_unless($department->status === 'active' && $department->public_status === 'published' && $department->public_verification_status === 'publishable' && $department->public_published_at && now()->gte($department->public_published_at), 404);

    return Inertia::render('PublicDepartment', ['department' => $department->only(['public_name', 'public_summary', 'public_description', 'responsibilities'])]);
})->whereNumber('department')->name('departments.public.show');

Route::get('/news', function () {
    return Inertia::render('EditorialList', ['type' => 'News', 'items' => EditorialItem::query()->public()->where('type', 'news')->orderByDesc('published_at')->get(['slug', 'title', 'summary', 'published_at', 'featured_media_id'])->map(fn (EditorialItem $item): array => [...$item->only(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? route('managed-media.show', $item->featured_media_id) : null])]);
})->name('news.index');
Route::get('/news/{slug}', function (string $slug) {
    $item = EditorialItem::query()->public()->where('type', 'news')->with(['documents' => fn ($query) => $query->public()])->where('slug', $slug)->firstOrFail();

    return Inertia::render('EditorialDetail', ['type' => 'News', 'item' => [...$item->only(['slug', 'title', 'summary', 'body', 'published_at']), 'image_url' => $item->featured_media_id ? route('managed-media.show', $item->featured_media_id) : null], 'documents' => $item->documents->map(fn (Document $document) => $document->only(['slug', 'title']))]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('news.show');
Route::get('/notices', function () {
    return Inertia::render('EditorialList', ['type' => 'Notices', 'items' => EditorialItem::query()->public()->where('type', 'notice')->orderByDesc('published_at')->get(['slug', 'title', 'summary', 'published_at', 'featured_media_id', 'expires_at'])->map(fn (EditorialItem $item): array => [...$item->only(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? route('managed-media.show', $item->featured_media_id) : null])]);
})->name('notices.index');
Route::get('/notices/{slug}', function (string $slug) {
    $item = EditorialItem::query()->public()->where('type', 'notice')->with(['documents' => fn ($query) => $query->public()])->where('slug', $slug)->firstOrFail();

    return Inertia::render('EditorialDetail', ['type' => 'Notice', 'item' => [...$item->only(['slug', 'title', 'summary', 'body', 'published_at', 'expires_at']), 'image_url' => $item->featured_media_id ? route('managed-media.show', $item->featured_media_id) : null], 'documents' => $item->documents->map(fn (Document $document) => $document->only(['slug', 'title']))]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('notices.show');

Route::get('/wards', function () {
    return Inertia::render('Wards', ['wards' => Ward::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'description'])]);
})->name('wards.index');
Route::get('/wards/{slug}', function (string $slug) {
    $ward = Ward::query()->public()->where('slug', $slug)->firstOrFail();

    return Inertia::render('Ward', ['ward' => $ward->only(['slug', 'name', 'description', 'boundaries_description'])]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('wards.show');

Route::get('/officials', function () {
    $officials = Official::query()->public()->with('department')->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'title', 'biography', 'department_id', 'photo_media_id']);

    return Inertia::render('Officials', ['officials' => $officials->map(fn (Official $official): array => [...$official->only(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->name : null, 'photo_url' => $official->photo_media_id ? route('managed-media.show', $official->photo_media_id) : null])]);
})->name('officials.index');
Route::get('/officials/{slug}', function (string $slug) {
    $official = Official::query()->public()->with('department')->where('slug', $slug)->firstOrFail();

    return Inertia::render('Official', ['official' => [...$official->only(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->name : null, 'photo_url' => $official->photo_media_id ? route('managed-media.show', $official->photo_media_id) : null]]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('officials.show');

Route::get('/managed-media/{media}', function (Media $media) {
    abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 404);
    $isPublishedOfficialPhoto = Official::query()->public()->where('photo_media_id', $media->id)->exists();
    $isPublishedEditorialImage = EditorialItem::query()->public()->where('featured_media_id', $media->id)->exists();
    abort_unless($isPublishedOfficialPhoto || $isPublishedEditorialImage, 404);
    abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

    return Storage::disk(config('cms.media_disk'))->response($media->storage_path, 'image', [
        'Content-Type' => $media->mime_type,
        'X-Content-Type-Options' => 'nosniff',
        'Content-Security-Policy' => "default-src 'none'; sandbox",
        'Cache-Control' => 'private, no-store',
    ], 'inline');
})->whereNumber('media')->name('managed-media.show');
