<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Stage 5 tourism page scaffolding (Option A: CMS-based).
 *
 * Seeded as a draft: council reviews, completes, and publishes. Facts below
 * are limited to well-established public Mutoko features; the page links to
 * the investment module for tourism opportunities and to contact for guidance.
 */
class Stage5TourismSeeder extends Seeder
{
    public function run(): void
    {
        if (Page::query()->where('slug', 'tourism-mutoko')->exists()) {
            return;
        }
        Page::query()->create([
            'slug' => 'tourism-mutoko',
            'title' => 'Tourism in Mutoko',
            'summary' => 'Heritage, landscapes, and visitor opportunities in Mutoko district.',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Discover Mutoko'],
                ['type' => 'paragraph', 'text' => 'Mutoko district in Mashonaland East is known for its granite kopje landscapes, heritage sites including the Mutoko ruins west of the town, and agricultural scenery along the Harare–Nyamapanda highway.'],
                ['type' => 'heading', 'text' => 'Heritage and nature'],
                ['type' => 'paragraph', 'text' => 'Draft: council to confirm the approved list of attractions, access guidance, and any entry requirements before publication. Do not publish unverified sites.'],
                ['type' => 'heading', 'text' => 'Tourism opportunities'],
                ['type' => 'paragraph', 'text' => 'Hospitality, conferencing, and eco-tourism ventures are welcome. See current investment opportunities or contact the council to discuss proposals.'],
                ['type' => 'cta', 'text' => 'Explore investment', 'url' => '/investment'],
                ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
            ],
            'seo_title' => 'Tourism in Mutoko',
            'meta_description' => 'Heritage, landscapes, and visitor opportunities in Mutoko district.',
        ]);
    }
}
