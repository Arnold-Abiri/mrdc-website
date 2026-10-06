<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PublicEnquiryController;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        'statistics' => DistrictStatistic::query()->public()->orderBy('display_order')->get(['label', 'value', 'unit', 'icon']),
        'tenders' => Tender::query()->public()->orderBy('display_order')->limit(3)->get(['slug', 'reference', 'title'])->map(fn (Tender $tender): array => [...$tender->only(['slug', 'reference', 'title']), 'display_status' => $tender->displayStatus()]),
        'investment' => InvestmentOpportunity::query()->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'sector', 'summary']),
        'projects' => CouncilProject::query()->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'project_status', 'summary']),
        'slides' => HomepageSlide::query()->public()->orderBy('display_order')->get(['headline', 'supporting_text', 'cta_label', 'cta_url'])->map(function (HomepageSlide $slide): array {
            $media = $slide->media_id ? Media::query()->where('id', $slide->media_id)->where('status', 'active')->first() : null;

            return [...$slide->only(['headline', 'supporting_text', 'cta_label', 'cta_url']), 'image_url' => $media ? route('managed-media.show', $media->id) : null];
        }),
    ]);
})->name('home');

Route::post('/locale', function (Request $request) {
    $data = $request->validate(['locale' => ['required', 'in:en,sn']]);
    $request->session()->put('public_locale', $data['locale']);

    return back();
})->name('locale.update');

Route::get('/contact', [PublicEnquiryController::class, 'create'])->name('contact.create');
Route::post('/contact', [PublicEnquiryController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:5,1')->name('feedback.store');

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

    $tenders = $query === '' ? collect() : Tender::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('reference', 'like', '%'.$escaped.'%')->orWhere('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);
    $tenderResults = $tenders->map(fn (Tender $tender): array => [
        'type' => 'Tender', 'title' => $tender->title, 'summary' => null,
        'url' => route('tenders.show', $tender->slug), 'is_review_content' => false,
    ]);
    $vacancies = $query === '' ? collect() : Vacancy::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);
    $vacancyResults = $vacancies->map(fn (Vacancy $vacancy): array => [
        'type' => 'Vacancy', 'title' => $vacancy->title, 'summary' => null,
        'url' => route('vacancies.show', $vacancy->slug), 'is_review_content' => false,
    ]);
    $investment = $query === '' ? collect() : InvestmentOpportunity::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'summary']);
    $investmentResults = $investment->map(fn (InvestmentOpportunity $opportunity): array => [
        'type' => 'Investment', 'title' => $opportunity->title, 'summary' => $opportunity->summary,
        'url' => route('investment.show', $opportunity->slug), 'is_review_content' => false,
    ]);
    $meetings = $query === '' ? collect() : CouncilMeeting::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('venue', 'like', '%'.$escaped.'%');
    })->orderBy('scheduled_date')->limit(20)->get(['id', 'title', 'summary']);
    $meetingResults = $meetings->map(fn (CouncilMeeting $meeting): array => [
        'type' => 'Meeting', 'title' => $meeting->title, 'summary' => $meeting->summary,
        'url' => route('meetings.show', $meeting->id), 'is_review_content' => false,
    ]);
    $projects = $query === '' ? collect() : CouncilProject::query()->public()->where(function ($builder) use ($query): void {
        $escaped = addcslashes($query, '%_\\');
        $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
    })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'summary']);
    $projectResults = $projects->map(fn (CouncilProject $project): array => [
        'type' => 'Project', 'title' => $project->title, 'summary' => $project->summary,
        'url' => route('projects.show', $project->slug), 'is_review_content' => false,
    ]);

    return Inertia::render('Search', ['query' => $query, 'results' => $pageResults->concat($documentResults)->concat($serviceResults)->concat($departmentResults)->concat($editorialResults)->concat($wardResults)->concat($officialResults)->concat($tenderResults)->concat($vacancyResults)->concat($investmentResults)->concat($meetingResults)->concat($projectResults)->take(20)->values()]);
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

Route::get('/documents', function (Request $request) {
    $category = $request->query('category');
    $year = $request->query('year');
    $departmentId = $request->query('department');
    $keyword = trim((string) $request->query('q', ''));
    abort_unless($category === null || in_array($category, Document::CATEGORIES, true), 422);
    abort_unless($year === null || (is_numeric($year) && (int) $year >= 1990 && (int) $year <= (int) now()->format('Y') + 1), 422);

    $documents = Document::query()->public()
        ->when($category, fn ($query) => $query->where('category', $category))
        ->when($year, fn ($query) => $query->whereYear('reference_date', (int) $year))
        ->when($departmentId, fn ($query) => $query->where('department_id', (int) $departmentId))
        ->when($keyword !== '', function ($query) use ($keyword): void {
            $escaped = addcslashes($keyword, '%_\\');
            $query->where(fn ($builder) => $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%'));
        })
        ->orderByDesc('published_at')
        ->get(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count']);
    $years = Document::query()->public()->whereNotNull('reference_date')->orderByDesc('reference_date')->pluck('reference_date')->map(fn ($date): int => (int) substr((string) $date, 0, 4))->unique()->values();

    return Inertia::render('Documents', ['documents' => $documents, 'categories' => Document::CATEGORIES, 'years' => $years, 'filters' => ['category' => $category, 'year' => $year, 'department' => $departmentId, 'q' => $keyword]]);
})->name('documents.index');

Route::get('/documents/{slug}', function (string $slug) {
    $document = Document::query()->public()->where('slug', $slug)->firstOrFail();

    return Inertia::render('Document', ['document' => [...$document->only(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count', 'current_version']), 'reference_year' => $document->referenceYear()]]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.show');

Route::get('/documents/{slug}/download', function (string $slug) {
    $document = Document::query()->public()->where('slug', $slug)->with('media')->firstOrFail();
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
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.download');

Route::get('/services', function () {
    return Inertia::render('Services', ['services' => Service::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary'])]);
})->name('services.index');

Route::get('/services/{slug}', function (string $slug) {
    $service = Service::query()->public()->with('department')->where('slug', $slug)->firstOrFail();

    $department = $service->department;

    return Inertia::render('Service', ['service' => $service->only(['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'seo_title', 'meta_description']), 'department' => $department instanceof Department ? $department->name : null, 'documents' => $service->department_id ? Document::query()->public()->where('department_id', $service->department_id)->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category']) : []]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('services.show');

Route::get('/departments', function () {
    $departments = Department::query()->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']);

    return Inertia::render('PublicDepartments', ['departments' => $departments]);
})->name('departments.public.index');

Route::get('/departments/{department}', function (Department $department) {
    abort_unless($department->status === 'active' && $department->public_status === 'published' && $department->public_verification_status === 'publishable' && $department->public_published_at && now()->gte($department->public_published_at), 404);
    $head = Official::query()->public()->where('department_id', $department->id)->where('is_department_head', true)->orderBy('display_order')->first(['slug', 'name', 'title']);
    $officials = Official::query()->public()->where('department_id', $department->id)->where('is_department_head', false)->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'title']);
    $services = Service::query()->public()->where('department_id', $department->id)->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary']);
    $contacts = PublicContact::query()->public()->where('department_id', $department->id)->orderBy('display_order')->get(['office', 'type', 'value']);
    $documents = Document::query()->public()->where('department_id', $department->id)->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category']);
    $news = EditorialItem::query()->public()->where('type', 'news')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['slug', 'title']);
    $notices = EditorialItem::query()->public()->where('type', 'notice')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['slug', 'title']);

    return Inertia::render('PublicDepartment', ['department' => $department->only(['public_name', 'public_summary', 'public_description', 'responsibilities']), 'head' => $head, 'officials' => $officials, 'services' => $services, 'contacts' => $contacts, 'documents' => $documents, 'news' => $news, 'notices' => $notices]);
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

Route::get('/tenders', function () {
    return Inertia::render('Tenders', ['tenders' => Tender::query()->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'reference', 'title', 'category', 'closes_at'])->map(fn (Tender $tender): array => [...$tender->only(['slug', 'reference', 'title', 'category', 'closes_at']), 'display_status' => $tender->displayStatus()])]);
})->name('tenders.index');
Route::get('/tenders/{slug}', function (string $slug) {
    $tender = Tender::query()->public()->with(['document', 'department'])->where('slug', $slug)->firstOrFail();
    $document = $tender->document;

    return Inertia::render('Tender', ['tender' => [...$tender->only(['slug', 'reference', 'title', 'category', 'description', 'opens_at', 'closes_at', 'contact_instructions', 'award_status', 'awarded_to', 'awarded_at', 'award_amount', 'award_reference', 'award_remarks']), 'display_status' => $tender->displayStatus(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->only(['slug', 'title']) : null], 'department' => $tender->department instanceof Department ? $tender->department->name : null]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('tenders.show');

Route::get('/vacancies', function () {
    return Inertia::render('Vacancies', ['vacancies' => Vacancy::query()->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'grade', 'closes_at'])->map(fn (Vacancy $vacancy): array => [...$vacancy->only(['slug', 'title', 'grade', 'closes_at']), 'is_open' => $vacancy->isOpen()])]);
})->name('vacancies.index');
Route::get('/vacancies/{slug}', function (string $slug) {
    $vacancy = Vacancy::query()->public()->with(['document', 'department'])->where('slug', $slug)->firstOrFail();
    $document = $vacancy->document;

    return Inertia::render('Vacancy', ['vacancy' => [...$vacancy->only(['slug', 'title', 'grade', 'reference', 'employment_type', 'description', 'responsibilities', 'requirements', 'opens_at', 'closes_at', 'application_instructions']), 'is_open' => $vacancy->isOpen(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->only(['slug', 'title']) : null], 'department' => $vacancy->department instanceof Department ? $vacancy->department->name : null]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('vacancies.show');

Route::get('/investment', function () {
    return Inertia::render('Investment', ['opportunities' => InvestmentOpportunity::query()->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'sector', 'summary', 'location', 'opportunity_status'])]);
})->name('investment.index');
Route::get('/investment/{slug}', function (string $slug) {
    $opportunity = InvestmentOpportunity::query()->public()->with('document')->where('slug', $slug)->firstOrFail();
    $document = $opportunity->document;

    return Inertia::render('InvestmentDetail', ['opportunity' => [...$opportunity->only(['slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status']), 'document' => $document instanceof Document && $document->status === 'published' ? $document->only(['slug', 'title']) : null]]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('investment.show');

Route::get('/meetings', function () {
    return Inertia::render('Meetings', ['meetings' => CouncilMeeting::query()->public()->orderBy('scheduled_date')->orderBy('display_order')->get(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status'])]);
})->name('meetings.index');
Route::get('/meetings/{meeting}', function (CouncilMeeting $meeting) {
    abort_unless($meeting->status === 'published' && $meeting->verification_status === 'publishable' && $meeting->published_at && now()->gte($meeting->published_at), 404);
    $meeting->load(['agenda', 'minutes']);
    $agenda = $meeting->agenda;
    $minutes = $meeting->minutes;

    return Inertia::render('Meeting', ['meeting' => [...$meeting->only(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary']), 'agenda' => $agenda instanceof Document && $agenda->status === 'published' && $agenda->visibility === 'public' ? $agenda->only(['slug', 'title']) : null, 'minutes' => $minutes instanceof Document && $minutes->status === 'published' && $minutes->visibility === 'public' ? $minutes->only(['slug', 'title']) : null]]);
})->whereNumber('meeting')->name('meetings.show');

Route::get('/transparency', function (Request $request) {
    $category = $request->query('category');
    $year = $request->query('year');
    abort_unless($category === null || in_array($category, Document::FINANCIAL_CATEGORIES, true), 422);
    abort_unless($year === null || (is_numeric($year) && (int) $year >= 1990 && (int) $year <= (int) now()->format('Y') + 1), 422);
    $documents = Document::query()->public()->whereIn('category', Document::FINANCIAL_CATEGORIES)
        ->when($category, fn ($query) => $query->where('category', $category))
        ->when($year, fn ($query) => $query->whereYear('reference_date', (int) $year))
        ->orderByDesc('published_at')->get(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count']);
    $years = Document::query()->public()->whereIn('category', Document::FINANCIAL_CATEGORIES)->whereNotNull('reference_date')->orderByDesc('reference_date')->pluck('reference_date')->map(fn ($date): int => (int) substr((string) $date, 0, 4))->unique()->values();

    return Inertia::render('Transparency', ['documents' => $documents, 'categories' => Document::FINANCIAL_CATEGORIES, 'years' => $years, 'filters' => ['category' => $category, 'year' => $year]]);
})->name('transparency.index');

Route::get('/rates', function () {
    $page = Page::query()->public()->where('slug', 'rates-information')->first();
    $schedules = Document::query()->public()->whereIn('category', ['bylaw', 'report', 'plan'])->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category', 'reference_date']);

    return Inertia::render('Rates', ['page' => $page ? $page->only(['slug', 'title', 'summary', 'blocks']) : null, 'schedules' => $schedules]);
})->name('rates.index');

Route::get('/projects', function (Request $request) {
    $status = $request->query('status');
    $type = $request->query('type');
    $departmentId = $request->query('department');
    $wardId = $request->query('ward');
    abort_unless($status === null || in_array($status, ['planned', 'ongoing', 'completed', 'on_hold', 'cancelled'], true), 422);
    abort_unless($type === null || in_array($type, ['project', 'programme'], true), 422);

    $projects = CouncilProject::query()->public()
        ->when($status, fn ($query) => $query->where('project_status', $status))
        ->when($type, fn ($query) => $query->where('project_type', $type))
        ->when($departmentId, fn ($query) => $query->where('department_id', (int) $departmentId))
        ->when($wardId, fn ($query) => $query->whereHas('wards', fn ($wards) => $wards->where('wards.id', (int) $wardId)))
        ->orderBy('display_order')->orderBy('title')
        ->get(['slug', 'title', 'project_type', 'project_status', 'location', 'summary', 'progress_percent']);

    return Inertia::render('Projects', ['projects' => $projects, 'filters' => ['status' => $status, 'type' => $type, 'department' => $departmentId, 'ward' => $wardId]]);
})->name('projects.index');
Route::get('/projects/{slug}', function (string $slug) {
    $project = CouncilProject::query()->public()->with(['department', 'wards', 'featuredMedia', 'documents' => fn ($query) => $query->public(), 'updates' => fn ($query) => $query->public()->orderByDesc('update_date')])->where('slug', $slug)->firstOrFail();
    $department = $project->department;

    return Inertia::render('Project', ['project' => [...$project->only(['slug', 'title', 'project_type', 'location', 'summary', 'description', 'starts_at', 'expected_completed_at', 'completed_at', 'project_status', 'progress_percent', 'contact_instructions']), 'image_url' => $project->featured_media_id ? route('managed-media.show', $project->featured_media_id) : null], 'department' => $department instanceof Department ? $department->name : null, 'wards' => $project->wards->map(fn (Ward $ward) => $ward->only(['slug', 'name'])), 'documents' => $project->documents->map(fn (Document $document) => $document->only(['slug', 'title'])), 'updates' => $project->updates->map(fn ($update) => $update->only(['update_date', 'title', 'summary', 'progress_percent']))]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('projects.show');

Route::get('/tourism', function () {
    $page = Page::query()->public()->where('slug', 'tourism-mutoko')->first();
    $opportunities = InvestmentOpportunity::query()->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'sector', 'summary']);

    return Inertia::render('Tourism', ['page' => $page ? $page->only(['slug', 'title', 'summary', 'blocks']) : null, 'opportunities' => $opportunities]);
})->name('tourism.index');

Route::redirect('/about', '/pages/about-mutoko')->name('about');
Route::redirect('/downloads', '/documents')->name('downloads');

Route::get('/managed-media/{media}', function (Media $media) {
    abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 404);
    $isPublishedOfficialPhoto = Official::query()->public()->where('photo_media_id', $media->id)->exists();
    $isPublishedEditorialImage = EditorialItem::query()->public()->where('featured_media_id', $media->id)->exists();
    $isPublishedSlideImage = HomepageSlide::query()->public()->where('media_id', $media->id)->exists();
    $isPublishedProjectImage = CouncilProject::query()->public()->where('featured_media_id', $media->id)->exists();
    abort_unless($isPublishedOfficialPhoto || $isPublishedEditorialImage || $isPublishedSlideImage || $isPublishedProjectImage, 404);
    abort_unless(Storage::disk(config('cms.media_disk'))->exists($media->storage_path), 404);

    return Storage::disk(config('cms.media_disk'))->response($media->storage_path, 'image', [
        'Content-Type' => $media->mime_type,
        'X-Content-Type-Options' => 'nosniff',
        'Content-Security-Policy' => "default-src 'none'; sandbox",
        'Cache-Control' => 'private, no-store',
    ], 'inline');
})->whereNumber('media')->name('managed-media.show');
