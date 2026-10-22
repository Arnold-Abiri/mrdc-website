<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Stage 5 tourism and heritage pages.
 *
 * Enriched with detailed historical, cultural, and geographic attractions
 * across Mutoko District (Tere Ruins, Ruchera Cave Paintings, Mutoko Granite Kopjes,
 * Mudzi River scenic points, and cultural festivities).
 */
class Stage5TourismSeeder extends Seeder
{
    public function run(): void
    {
        $page = [
            'slug' => 'tourism-mutoko',
            'title' => 'Tourism & Cultural Heritage in Mutoko',
            'summary' => 'Explore the majestic granite kopjes, ancient dry-stone monuments, San rock art caves, and authentic cultural heritage of Mutoko District.',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Welcome to Mutoko: The Granite Heartland'],
                ['type' => 'paragraph', 'text' => 'Situated 143 kilometres north-east of Harare along the scenic international highway towards Mozambique, Mutoko District is one of Zimbabwe\'s most visually captivating landscapes. Known worldwide for its dramatic granite inselbergs (kopjes), balancing rocks, and rich cultural traditions of the VaBudya people, Mutoko offers a unique fusion of historical discovery, eco-tourism, and authentic rural hospitality.'],
                ['type' => 'heading', 'text' => 'Major Cultural & Heritage Attractions'],
                ['type' => 'paragraph', 'text' => 'Mutoko is home to remarkable archaeological and natural monuments that celebrate southern Africa\'s deep past: 1. Tere National Monument (Tere Ruins): Located east of Mutoko town, this ancient dry-stone walled citadel mirrors the Great Zimbabwe architectural tradition. Constructed without mortar upon commanding granite elevations, Tere features intricate stone banding, defensive terraces, and ancient iron-smelting platforms. 2. Ruchera Cave Rock Art: Sheltered within granite overhangs near Mutoko Centre, Ruchera Cave boasts exceptionally preserved late Stone Age San hunter-gatherer rock paintings, depicting antelopes, hunters, geometric symbols, and ancestral rituals dating back thousands of years. 3. Mutoko Balancing Rocks & Granite Domes: Spectacular geological formations and granite domes rise sharply from the savannah floor, providing breath-taking viewpoints for photography, hiking, and sunrise vantage points.'],
                ['type' => 'heading', 'text' => 'Local Culture, Horticulture & Community Life'],
                ['type' => 'paragraph', 'text' => 'Beyond its archaeological sites, Mutoko is celebrated as Zimbabwe\'s agricultural powerhouse for horticulture and tomatoes. Visitors can tour vibrant farming communities in Jani, attend traditional music gatherings and the annual Mutoko District Agricultural Show & Business Expo, or explore local stone-sculpting and basketry crafts produced by talented community artisans.'],
                ['type' => 'heading', 'text' => 'Eco-Tourism & Hospitality Opportunities'],
                ['type' => 'paragraph', 'text' => 'As part of our Vision 2030 development plan, Mutoko Rural District Council actively facilitates sustainable eco-tourism, cultural lodges, conference facilities, and outdoor recreation ventures. We welcome investors and tourism operators interested in establishing safari lodges, hiking trails, and heritage interpretation centres.'],
                ['type' => 'cta', 'text' => 'Explore Investment & Stand Opportunities', 'url' => '/investment'],
                ['type' => 'cta', 'text' => 'View District Photo Galleries', 'url' => '/galleries'],
            ],
            'seo_title' => 'Tourism & Cultural Heritage in Mutoko | Mutoko RDC',
            'meta_description' => 'Explore Tere Ruins, Ruchera Cave Rock Art, granite landscapes, and cultural heritage in Mutoko District.',
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => now(),

        ];

        Page::query()->updateOrCreate(
            ['slug' => $page['slug']],
            $page
        );
    }
}
