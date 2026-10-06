<?php

namespace Database\Seeders;

use App\Models\DistrictStatistic;
use App\Models\InvestmentOpportunity;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Stage 4 verified Mutoko content.
 *
 * Provenance: VERIFIED_MUTOKO facts are drawn from public sources recorded in
 * docs/STAGE-4-IMPLEMENTATION.md (district profile, census, project brief).
 * Everything is seeded as draft/demo (never published): council reviews and
 * publishes each item. The seeder is idempotent and never overwrites records
 * an administrator has edited. No tenders, vacancies, fees, payments, official
 * names, or councillors are seeded — those are COUNCIL_MANAGED only.
 */
class Stage4MutokoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAboutPage();
        $this->seedServices();
        $this->seedInvestment();
        $this->seedStatistics();
    }

    private function seedAboutPage(): void
    {
        if (Page::query()->where('slug', 'about-mutoko')->exists()) {
            return;
        }
        Page::query()->create([
            'slug' => 'about-mutoko',
            'title' => 'About Mutoko Rural District Council',
            'summary' => 'The history, district profile, and mandate of Mutoko Rural District Council in Mashonaland East.',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Our history'],
                ['type' => 'paragraph', 'text' => 'Mutoko was established as an administrative station in 1911 and is named after the local chief, Mutoko. The town lies about 143 kilometres north-east of Harare on the highway towards Nyamapanda, at an altitude of around 1,250 metres.'],
                ['type' => 'paragraph', 'text' => 'Mutoko is the administrative and commercial hub of Mutoko District, home of the Buja people. The settlement was designated a growth point in the early 1980s. Earlier council structures were amalgamated into the present Mutoko Rural District Council under the Rural District Councils Act. Council offices serve the district from Mutoko Centre.'],
                ['type' => 'heading', 'text' => 'Our district'],
                ['type' => 'paragraph', 'text' => 'Mutoko District covers roughly 4,092 square kilometres in Mashonaland East and had a population of 161,091 at the 2022 census. The district has 29 electoral wards. Agriculture — chiefly maize, groundnuts, tobacco, and horticulture — is the primary occupation, alongside the Jani growth point south-west of Mutoko Centre.'],
                ['type' => 'heading', 'text' => 'Our mandate'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council delivers local services — water, roads, health, education support, environmental management, and business-centre servicing — and promotes local development across the district.'],
                ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
            ],
            'seo_title' => 'About Mutoko Rural District Council',
            'meta_description' => 'History, district profile, and mandate of Mutoko Rural District Council, Mashonaland East.',
        ]);
    }

    /**
     * @return list<array{slug: string, name: string, summary: string, description: string}>
     */
    private function serviceDefinitions(): array
    {
        return [
            ['slug' => 'education-services', 'name' => 'Education Services', 'summary' => 'Council support for schools and learning facilities across the district.', 'description' => 'The council supports primary and secondary education infrastructure in partnership with the Ministry of Primary and Secondary Education and school development committees, including classroom development and maintenance at council schools.'],
            ['slug' => 'environmental-protection', 'name' => 'Environmental Protection', 'summary' => 'Conservation, waste management, and environmental health.', 'description' => 'The council manages refuse collection at designated centres, promotes clean environments, and works with the Environmental Management Agency on conservation and anti-litter programmes.'],
            ['slug' => 'roads-and-works', 'name' => 'Roads and Works', 'summary' => 'Maintenance of district and feeder roads and public works.', 'description' => 'The works section maintains council roads, bridges, and public infrastructure across the wards, prioritising all-weather access to service centres, schools, and clinics.'],
            ['slug' => 'health-services', 'name' => 'Health Services', 'summary' => 'Council clinics and primary health-care support.', 'description' => 'The council operates rural health facilities and supports immunisation, maternal health, and disease-prevention programmes in partnership with the Ministry of Health and Child Care.'],
            ['slug' => 'business-centre-servicing', 'name' => 'Servicing Business Centres', 'summary' => 'Planned stands, water, and sanitation at growth points and centres.', 'description' => 'The council plans and services stands at Mutoko Centre, the Jani growth point, and rural service centres, including water reticulation, sewer, and road access.'],
            ['slug' => 'property-management', 'name' => 'Property Management', 'summary' => 'Council properties, leases, and stand administration.', 'description' => 'The council administers residential, commercial, and institutional stands, leases, and council-owned properties in line with approved layouts and lease terms.'],
            ['slug' => 'recreational-facilities', 'name' => 'Recreational Facilities', 'summary' => 'Sports grounds, halls, and community facilities.', 'description' => 'The council maintains stadiums, sports grounds, and community halls available for hire by clubs, schools, and community groups.'],
            ['slug' => 'natural-resources-conservation', 'name' => 'Conservation of Natural Resources', 'summary' => 'Sustainable use of land, water, and woodland resources.', 'description' => 'The council promotes sustainable land use, woodland management, and protection of water sources, working with traditional leaders and conservation partners.'],
            ['slug' => 'welfare-services', 'name' => 'Welfare Services', 'summary' => 'Support for vulnerable groups and community welfare.', 'description' => 'The social services section coordinates assistance for vulnerable households, older persons, and children in difficult circumstances, with community and partner support.'],
        ];
    }

    private function seedServices(): void
    {
        foreach ($this->serviceDefinitions() as $index => $definition) {
            if (Service::query()->where('slug', $definition['slug'])->exists()) {
                continue;
            }
            Service::query()->create([...$definition, 'display_order' => $index]);
        }
    }

    private function seedInvestment(): void
    {
        $items = [
            ['slug' => 'solar-energy-mutoko', 'title' => 'Solar energy generation', 'sector' => 'Energy', 'summary' => 'Grid-connected and off-grid solar opportunities in a high-irradiation district.', 'description' => 'Mutoko district offers strong solar irradiation and available land suitable for generation projects. Interested developers confirm site, grid, and licensing requirements directly with council and the relevant authorities.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 0],
            ['slug' => 'mineral-opportunities-mutoko', 'title' => 'Mineral and mining opportunities', 'sector' => 'Mining', 'summary' => 'Licensed mineral exploration and value-addition prospects.', 'description' => 'The district hosts mineral occurrences of interest to small and large-scale operators. All activity requires valid licences from the Ministry of Mines and Mining Development and compliance with environmental requirements.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 1],
            ['slug' => 'horticulture-mutoko', 'title' => 'Horticulture and agro-processing', 'sector' => 'Agriculture', 'summary' => 'Tomato, mango, and vegetable value chains with Harare market access.', 'description' => 'Mutoko is known for horticultural produce, including tomatoes and mangoes, with road access to Harare markets. Opportunities exist in production, cold-chain, and agro-processing ventures.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 2],
        ];
        foreach ($items as $item) {
            if (InvestmentOpportunity::query()->where('slug', $item['slug'])->exists()) {
                continue;
            }
            InvestmentOpportunity::query()->create($item);
        }
    }

    private function seedStatistics(): void
    {
        $items = [
            ['label' => 'Electoral wards', 'value' => '29', 'unit' => null, 'icon' => 'wards', 'source_note' => 'Project brief: district has 29 electoral wards.', 'display_order' => 0, 'is_active' => true],
            ['label' => 'District population (2022 census)', 'value' => '161091', 'unit' => null, 'icon' => 'population', 'source_note' => '2022 census via public district profile.', 'display_order' => 1, 'is_active' => true],
            ['label' => 'District area', 'value' => '4092.5', 'unit' => 'km²', 'icon' => 'area', 'source_note' => 'Public district profile (4,092.5 sq km).', 'display_order' => 2, 'is_active' => true],
            ['label' => 'Administrative station founded', 'value' => '1911', 'unit' => null, 'icon' => 'history', 'source_note' => 'Public history: administrative station 1911.', 'display_order' => 3, 'is_active' => true],
        ];
        foreach ($items as $item) {
            if (DistrictStatistic::query()->where('label', $item['label'])->exists()) {
                continue;
            }
            DistrictStatistic::query()->create($item);
        }
    }
}
