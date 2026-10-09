<?php

use App\Http\Controllers\AuditExportController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MonthlyMetricsExportController;
use App\Http\Controllers\Public\DepartmentController;
use App\Http\Controllers\Public\DocumentController;
use App\Http\Controllers\Public\EditorialController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InvestmentController;
use App\Http\Controllers\Public\LegacyRedirectController;
use App\Http\Controllers\Public\LocaleController;
use App\Http\Controllers\Public\MediaController;
use App\Http\Controllers\Public\MeetingController;
use App\Http\Controllers\Public\OfficialController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PreviewController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\RatesController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\TenderController;
use App\Http\Controllers\Public\TourismController;
use App\Http\Controllers\Public\TransparencyController;
use App\Http\Controllers\Public\UtilityController;
use App\Http\Controllers\Public\VacancyController;
use App\Http\Controllers\Public\WardController;
use App\Http\Controllers\PublicEnquiryController;
use App\Http\Middleware\RecordAnalytics;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'root'])->name('root');

Route::get('/{locale}', [HomeController::class, 'index'])->where(['locale' => 'en|sn|nd'])->name('home');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::prefix('{locale}')->where(['locale' => 'en|sn|nd'])->middleware(RecordAnalytics::class)->group(function () {
    Route::get('/contact', [PublicEnquiryController::class, 'create'])->name('contact.create');
    Route::post('/contact', [PublicEnquiryController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:5,1')->name('feedback.store');

    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/coming-soon', [UtilityController::class, 'comingSoon'])->name('coming-soon');
}); // end locale group

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::prefix('{locale}')->where(['locale' => 'en|sn|nd'])->middleware(RecordAnalytics::class)->group(function () {
    Route::get('/pages/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('pages.show');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{slug}', [DocumentController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.show');
    Route::get('/documents/{slug}/download', [DocumentController::class, 'download'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('documents.download');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}', [ServiceController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('services.show');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.public.index');
    Route::get('/departments/{department}', [DepartmentController::class, 'show'])->whereNumber('department')->name('departments.public.show');

    Route::get('/news', [EditorialController::class, 'newsIndex'])->name('news.index');
    Route::get('/news/{slug}', [EditorialController::class, 'newsShow'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('news.show');
    Route::get('/notices', [EditorialController::class, 'noticesIndex'])->name('notices.index');
    Route::get('/notices/{slug}', [EditorialController::class, 'noticesShow'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('notices.show');

    Route::get('/wards', [WardController::class, 'index'])->name('wards.index');
    Route::get('/wards/{slug}', [WardController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('wards.show');

    Route::get('/officials', [OfficialController::class, 'index'])->name('officials.index');
    Route::get('/officials/{slug}', [OfficialController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('officials.show');

    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
    Route::get('/tenders/{slug}', [TenderController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('tenders.show');

    Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::get('/vacancies/{slug}', [VacancyController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('vacancies.show');

    Route::get('/investment', [InvestmentController::class, 'index'])->name('investment.index');
    Route::get('/investment/{slug}', [InvestmentController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('investment.show');

    Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])->whereNumber('meeting')->name('meetings.show');

    Route::get('/transparency', [TransparencyController::class, 'index'])->name('transparency.index');
    Route::get('/rates', [RatesController::class, 'index'])->name('rates.index');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{slug}', [ProjectController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('projects.show');

    Route::get('/tourism', [TourismController::class, 'index'])->name('tourism.index');

    Route::get('/about', [UtilityController::class, 'about'])->name('about');
    Route::get('/downloads', [UtilityController::class, 'downloads'])->name('downloads');

    Route::get('/managed-media/{media}', [MediaController::class, 'show'])->whereNumber('media')->name('managed-media.show');
}); // end locale group

// Stakeholder preview: signed URLs only, never linked publicly or in sitemap.
Route::middleware(['signed', 'noindex'])->prefix('preview')->group(function () {
    Route::get('/{locale}', [PreviewController::class, 'home'])->where(['locale' => 'en|sn|nd'])->name('preview.home');
});
Route::middleware(['noindex'])->prefix('preview')->group(function () {
    // Unlocked by visiting a valid signed preview URL (session flag set there).
    Route::get('/{locale}/pages/{slug}', [PreviewController::class, 'page'])->where(['locale' => 'en|sn|nd', 'slug' => '[a-z0-9]+(?:-[a-z0-9]+)*'])->name('preview.page');
    Route::get('/{locale}/{type}/{slug}', [PreviewController::class, 'detail'])->where(['locale' => 'en|sn|nd', 'type' => 'services|news|notices|documents|wards|officials|investment|meetings', 'slug' => '[a-z0-9]+(?:-[a-z0-9]+)*'])->name('preview.detail');
});

Route::middleware('auth')->prefix('admin/reports')->group(function () {
    Route::get('/audit-export', AuditExportController::class)->name('admin.audit-export');
    Route::get('/monthly-export', MonthlyMetricsExportController::class)->name('admin.monthly-export');
});

Route::get('/{legacy}', [LegacyRedirectController::class, 'show'])->where('legacy', '(services|documents|pages|news|notices|wards|officials|departments|tenders|vacancies|investment|projects|meetings|tourism|transparency|rates|contact|feedback|search|coming-soon|about|downloads)(/.*)?')->name('legacy-redirect');
