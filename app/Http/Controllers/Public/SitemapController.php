<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\InvestmentOpportunity;
use App\Models\Official;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $locales = ['en', 'sn', 'nd'];
        $urls = [];
        foreach ($locales as $locale) {
            $urls[] = route('home', ['locale' => $locale]);
            foreach (['services.index', 'documents.index', 'departments.public.index', 'news.index', 'notices.index', 'wards.index', 'officials.index', 'tenders.index', 'vacancies.index', 'investment.index', 'meetings.index', 'transparency.index', 'rates.index', 'projects.index', 'tourism.index', 'contact.create', 'feedback.create'] as $name) {
                $urls[] = route($name, ['locale' => $locale]);
            }
            foreach (Page::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('pages.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Service::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('services.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Document::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('documents.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Department::query()->where('status', 'active')->where('public_status', 'published')->pluck('id') as $id) {
                $urls[] = route('departments.public.show', ['locale' => $locale, 'department' => $id]);
            }
            foreach (EditorialItem::query()->public()->where('type', 'news')->pluck('slug') as $slug) {
                $urls[] = route('news.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (EditorialItem::query()->public()->where('type', 'notice')->pluck('slug') as $slug) {
                $urls[] = route('notices.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Ward::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('wards.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Official::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('officials.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Tender::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('tenders.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (Vacancy::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('vacancies.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (InvestmentOpportunity::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('investment.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (CouncilProject::query()->public()->pluck('slug') as $slug) {
                $urls[] = route('projects.show', ['locale' => $locale, 'slug' => $slug]);
            }
            foreach (CouncilMeeting::query()->public()->pluck('id') as $id) {
                $urls[] = route('meetings.show', ['locale' => $locale, 'meeting' => $id]);
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (array_unique($urls) as $url) {
            $xml .= '<url><loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8').'</loc></url>';
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
