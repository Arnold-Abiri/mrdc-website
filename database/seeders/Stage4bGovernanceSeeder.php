<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Stage 4B institutional page scaffolding.
 *
 * All pages are seeded as drafts (never published): council reviews, completes,
 * and publishes each one. No names, figures, or decisions are seeded.
 * Provenance for each draft is recorded in docs/STAGE-4-REQUIREMENTS.md.
 */
class Stage4bGovernanceSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'mandate',
                'title' => 'Our mandate',
                'summary' => 'The statutory mandate of Mutoko Rural District Council.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Statutory mandate'],
                    ['type' => 'paragraph', 'text' => 'Draft: the council secretariat to confirm the mandate statement with reference to the Rural District Councils Act before publication.'],
                    ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
                ],
            ],
            [
                'slug' => 'vision-mission',
                'title' => 'Vision and mission',
                'summary' => 'Council vision, mission, and strategic goals.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Vision'],
                    ['type' => 'paragraph', 'text' => 'Draft: council to confirm the approved vision statement before publication.'],
                    ['type' => 'heading', 'text' => 'Mission'],
                    ['type' => 'paragraph', 'text' => 'Draft: council to confirm the approved mission statement before publication.'],
                    ['type' => 'heading', 'text' => 'Strategic goals'],
                    ['type' => 'paragraph', 'text' => 'Draft: council to confirm strategic goals from the approved strategic plan before publication.'],
                ],
            ],
            [
                'slug' => 'organogram',
                'title' => 'Council organogram',
                'summary' => 'The approved organisational structure of Mutoko Rural District Council.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Organisational structure'],
                    ['type' => 'paragraph', 'text' => 'Draft: publish the council-approved organogram here as an approved document (Organogram category in the document centre). Reporting relationships are never invented; only the approved chart is shown.'],
                    ['type' => 'cta', 'text' => 'Browse council documents', 'url' => '/documents'],
                ],
            ],
            [
                'slug' => 'rates-information',
                'title' => 'Council rates',
                'summary' => 'Approved rate categories, billing periods, and payment guidance.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Rate categories'],
                    ['type' => 'paragraph', 'text' => 'Draft: council finance to confirm approved rate categories and the applicable billing period before publication. No private ratepayer account information is ever published.'],
                    ['type' => 'heading', 'text' => 'Payment guidance'],
                    ['type' => 'paragraph', 'text' => 'Draft: council to confirm where and how rates are paid. No online payment is offered until an approved integration exists.'],
                    ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
                ],
            ],
        ];

        foreach ($pages as $page) {
            if (Page::query()->where('slug', $page['slug'])->exists()) {
                continue;
            }
            Page::query()->create([...$page, 'seo_title' => $page['title'], 'meta_description' => $page['summary']]);
        }
    }
}
