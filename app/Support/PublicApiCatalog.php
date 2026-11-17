<?php

namespace App\Support;

use App\Models\BankAccount;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\EditorialItem;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\InvestmentOpportunity;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Builder;

class PublicApiCatalog
{
    /** @return array<string, array{model: class-string, key: string, tag: string, fields: list<string>}> */
    public static function all(): array
    {
        return [
            'bank-accounts' => ['model' => BankAccount::class, 'key' => 'id', 'tag' => 'Bank Accounts', 'fields' => ['id', 'payment_type', 'code_label', 'instructions', 'bank_name', 'account_name', 'account_number', 'branch_name', 'branch_code', 'currency', 'category', 'purpose']],
            'departments' => ['model' => Department::class, 'key' => 'id', 'tag' => 'Departments', 'fields' => ['id', 'public_name', 'public_summary', 'public_description', 'responsibilities']],
            'news' => ['model' => EditorialItem::class, 'key' => 'slug', 'tag' => 'News', 'fields' => ['id', 'slug', 'title', 'summary', 'body', 'category', 'published_at']],
            'notices' => ['model' => EditorialItem::class, 'key' => 'slug', 'tag' => 'Notices', 'fields' => ['id', 'slug', 'title', 'summary', 'body', 'category', 'published_at', 'expires_at']],
            'faqs' => ['model' => Faq::class, 'key' => 'id', 'tag' => 'FAQs', 'fields' => ['id', 'question', 'answer', 'category', 'is_featured']],
            'galleries' => ['model' => Gallery::class, 'key' => 'slug', 'tag' => 'Gallery', 'fields' => ['id', 'slug', 'title', 'summary', 'description', 'location', 'album_date', 'is_featured']],
            'investments' => ['model' => InvestmentOpportunity::class, 'key' => 'slug', 'tag' => 'Investments', 'fields' => ['id', 'slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status']],
            'meetings' => ['model' => CouncilMeeting::class, 'key' => 'id', 'tag' => 'Meetings', 'fields' => ['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary']],
            'projects' => ['model' => CouncilProject::class, 'key' => 'slug', 'tag' => 'Projects', 'fields' => ['id', 'slug', 'title', 'project_type', 'location', 'summary', 'description', 'starts_at', 'expected_completed_at', 'completed_at', 'project_status', 'progress_percent', 'contact_instructions']],
            'tenders' => ['model' => Tender::class, 'key' => 'slug', 'tag' => 'Tenders', 'fields' => ['id', 'slug', 'reference', 'title', 'category', 'description', 'opens_at', 'closes_at', 'lifecycle_status', 'contact_instructions']],
            'vacancies' => ['model' => Vacancy::class, 'key' => 'slug', 'tag' => 'Vacancies', 'fields' => ['id', 'slug', 'title', 'reference', 'grade', 'employment_type', 'description', 'responsibilities', 'requirements', 'opens_at', 'closes_at', 'application_instructions']],
            'wards' => ['model' => Ward::class, 'key' => 'slug', 'tag' => 'Wards', 'fields' => ['id', 'slug', 'name', 'description', 'boundaries_description', 'map_url']],
        ];
    }

    public static function query(string $resource): Builder
    {
        $definition = self::all()[$resource];
        $query = $definition['model']::query()->with('translations');
        if ($resource === 'departments') {
            return $query->where('status', 'active')->where('public_status', 'published')
                ->where('public_verification_status', 'publishable')
                ->whereNotNull('public_published_at')->where('public_published_at', '<=', now());
        }
        $query->public();
        if ($resource === 'galleries') {
            $query->with(['media' => fn ($media) => $media->where('status', 'active')->with('translations')]);
        }
        if (in_array($resource, ['news', 'notices'], true)) {
            $query->where('type', $resource === 'news' ? 'news' : 'notice');
        }

        return $query;
    }

    /** @return array<string, mixed> */
    public static function schema(string $field, string $resource): array
    {
        if ($field === 'id' || $field === 'progress_percent') {
            return ['type' => 'integer', 'nullable' => $field !== 'id'];
        }
        if ($field === 'is_featured') {
            return ['type' => 'boolean'];
        }
        if ($field === 'responsibilities' && $resource === 'departments') {
            return ['type' => 'array', 'items' => ['type' => 'string'], 'nullable' => true];
        }
        $schema = ['type' => 'string', 'nullable' => true];
        if (in_array($field, ['published_at', 'expires_at', 'album_date', 'scheduled_date', 'starts_at', 'expected_completed_at', 'completed_at', 'opens_at', 'closes_at'], true)) {
            $schema['format'] = 'date-time';
        }

        return $schema;
    }
}
