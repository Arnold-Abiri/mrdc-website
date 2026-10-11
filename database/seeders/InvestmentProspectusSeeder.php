<?php

namespace Database\Seeders;

use App\Models\InvestmentOpportunity;
use Illuminate\Database\Seeder;

class InvestmentProspectusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $source = 'https://zimembassybeijing.org.cn/wp-content/uploads/2024/05/Provincial-Investment-Opportunities-.pdf';
        $opportunities = [
            [
                'slug' => 'mutoko-weighbridge-prospectus',
                'title' => 'Mutoko weighbridge proposal',
                'sector' => 'Mining and Transport',
                'location' => 'Nyamapanda Highway and Nyadire Road intersection',
                'summary' => 'Council proposal for a pit-mounted vehicle weighbridge.',
                'description' => 'Mutoko RDC proposed a pit-mounted weighbridge at the Nyamapanda Highway and Nyadire Road intersection in the provincial investment prospectus. The prospectus described the project as being at planning stage. Confirm current availability and terms with council. Source: '.$source,
            ],
            [
                'slug' => 'mutoko-black-granite-plant-prospectus',
                'title' => 'Black granite cutting and polishing plant',
                'sector' => 'Mining',
                'location' => 'Ward 11, Nyadire Road',
                'summary' => 'Council proposal for local cutting and polishing of black granite.',
                'description' => 'The provincial investment prospectus identifies a Mutoko RDC proposal for a black granite cutting and polishing plant in Ward 11. It described the project as being at planning stage. Confirm current availability and terms with council. Source: '.$source,
            ],
            [
                'slug' => 'mutoko-fruit-vegetable-plant-prospectus',
                'title' => 'Fruit and vegetable processing plant',
                'sector' => 'Agriculture and Processing',
                'location' => 'Mutoko Centre, Ward 20',
                'summary' => 'Council proposal to process produce from Mutoko horticulture.',
                'description' => 'The provincial investment prospectus identifies a Mutoko RDC proposal for a fruit and vegetable processing plant at Mutoko Centre in Ward 20. It described the project as being at planning stage. Confirm current availability and terms with council. Source: '.$source,
            ],
            [
                'slug' => 'mutoko-lithium-processing-plant-prospectus',
                'title' => 'Lithium processing plant',
                'sector' => 'Mining',
                'location' => 'Ward 11, near Mutoko Centre',
                'summary' => 'Council proposal for local processing of lithium.',
                'description' => 'The provincial investment prospectus identifies a Mutoko RDC proposal for a lithium processing plant in Ward 11, near Mutoko Centre. Confirm current availability, mineral rights and terms with council and the relevant authorities. Source: '.$source,
            ],
            [
                'slug' => 'mutoko-heritage-tourism-prospectus',
                'title' => 'Mutemwa and Mutoko Ruins tourism facilities',
                'sector' => 'Tourism',
                'location' => 'Ward 22, near Mutoko Centre',
                'summary' => 'Council proposal for visitor facilities at Mutemwa and Mutoko Ruins.',
                'description' => 'The provincial investment prospectus identifies a Mutoko RDC proposal to improve visitor facilities at Mutemwa and Mutoko Ruins. Confirm current availability, heritage permissions and terms with council and the relevant authorities. Source: '.$source,
            ],
        ];

        foreach ($opportunities as $displayOrder => $opportunity) {
            if (InvestmentOpportunity::query()->where('slug', $opportunity['slug'])->exists()) {
                continue;
            }

            InvestmentOpportunity::unguarded(fn (): InvestmentOpportunity => InvestmentOpportunity::query()->create([
                ...$opportunity,
                'opportunity_status' => 'prospecting',
                'display_order' => $displayOrder,
                'status' => 'published',
                'verification_status' => 'publishable',
                'verified_at' => now(),
                'published_at' => now(),
            ]));
        }
    }
}
