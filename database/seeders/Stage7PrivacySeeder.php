<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Privacy-policy scaffolding for council/legal review (Stage 7).
 *
 * Draft only: describes what the system collects, why, retention, and
 * cookie/session behavior in factual terms. Final legal wording is a
 * council/legal responsibility.
 */
class Stage7PrivacySeeder extends Seeder
{
    public function run(): void
    {
        if (Page::query()->where('slug', 'privacy-policy')->exists()) {
            return;
        }
        Page::query()->create([
            'slug' => 'privacy-policy',
            'title' => 'Privacy policy',
            'summary' => 'How the council website handles personal and analytics information (pending legal review).',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Information this website collects'],
                ['type' => 'paragraph', 'text' => 'Draft for legal review: enquiries, feedback, and complaints submitted through this website are stored privately and accessed only by authorized council staff for service delivery. They are never published.'],
                ['type' => 'heading', 'text' => 'Website analytics'],
                ['type' => 'paragraph', 'text' => 'Draft for legal review: the website records aggregate, first-party usage metrics (pages viewed, document downloads, content views, referral category, selected website language). No names, email addresses, or personal profiles are collected for analytics, and no third-party advertising cookies are used. Raw analytics events are retained for a configurable period (default 365 days) and then pruned; audit logs follow separate governance retention.'],
                ['type' => 'heading', 'text' => 'Cookies and sessions'],
                ['type' => 'paragraph', 'text' => 'Draft for legal review: the website uses strictly necessary session storage for security and remembers language and accessibility display preferences on the visitor device. No account is required for public pages.'],
                ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
            ],
            'seo_title' => 'Privacy policy',
            'meta_description' => 'Privacy information for the Mutoko RDC website (pending legal review).',
        ]);
    }
}
