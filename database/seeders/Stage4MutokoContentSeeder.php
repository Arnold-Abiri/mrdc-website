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
                ['type' => 'heading', 'text' => 'Our location'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council covers 428,916 hectares in Mashonaland East, about 143 kilometres north-east of Harare on the Harare–Nyamapanda highway and roughly 90 kilometres from the Zimbabwe–Mozambique border at Nyamapanda. The district seat at Mutoko Centre serves 29 electoral wards, complemented by 12 non-elected seats (9 women\'s quota and 3 Town Board representatives).'],
                ['type' => 'heading', 'text' => 'Our mission and vision'],
                ['type' => 'paragraph', 'text' => 'Our mission is to promote sustainable development through the provision of quality services in an efficient, effective and responsive manner. Our vision is socially and economically empowered communities by 2030.'],
                ['type' => 'heading', 'text' => 'Our history'],
                ['type' => 'paragraph', 'text' => 'Mutoko was awarded administrative district status in 1902. From the 1890s to the 1930s Native Reserves received low priority in services, until the Native Council Act of 1937 created the first African councils. At independence in 1980, African Councils were eliminated and replaced with 55 District Councils.'],
                ['type' => 'paragraph', 'text' => 'Council offices at Mutoko Centre were officially opened by Prime Minister R.G. Mugabe on 4 June 1987. In 1994 the area was served by three councils — Budya, Mutoko District, and Mutoko South — which were amalgamated in 1995 under the Rural District Councils Act (Chapter 29:13) into the present Mutoko Rural District Council, one of 58 rural district councils nationally.'],
                ['type' => 'heading', 'text' => 'Our district'],
                ['type' => 'paragraph', 'text' => 'Mutoko District covers roughly 4,092 square kilometres in Mashonaland East and had a population of 161,091 at the 2022 census (about 163,000 on council estimates). The district has 29 electoral wards. Agriculture — chiefly maize, groundnuts, tobacco, and horticulture — is the primary occupation, alongside the Jani growth point south-west of Mutoko Centre.'],
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
            ['slug' => 'education-services', 'name' => 'Education Services', 'summary' => 'Council support for 84 primary and 44 secondary schools across the district.', 'description' => 'The council supports 84 primary and 44 secondary schools in partnership with the Ministry of Primary and Secondary Education and school development committees, including classroom development and maintenance. Mutoko schools are also known for producing outstanding sporting talent that competes at provincial and national level.'],
            ['slug' => 'environmental-protection', 'name' => 'Environmental Protection', 'summary' => 'Effluent treatment, sewer reticulation, and refuse collection and disposal.', 'description' => 'The council provides effluent treatment, sewer reticulation, and refuse collection and disposal at designated centres. It promotes clean environments and works with the Environmental Management Agency on conservation and anti-litter programmes.'],
            ['slug' => 'roads-and-works', 'name' => 'Roads and Works', 'summary' => 'Road network, public infrastructure, and building inspectorate.', 'description' => 'The works section maintains the district road network, bridges, and public infrastructure across the wards, prioritising all-weather access to service centres, schools, and clinics. It also provides a building inspectorate function and maintains council water and sewer infrastructure and plant.'],
            ['slug' => 'health-services', 'name' => 'Health Services', 'summary' => 'Rural health centres: maternity, immunisation, and treatment services.', 'description' => 'The council operates rural health centres offering maternity care, treatment of ailments including STIs, counselling, immunisation, and health education. Services are delivered in partnership with the Ministry of Health and Child Care through disease-prevention and primary health-care programmes.'],
            ['slug' => 'business-centre-servicing', 'name' => 'Servicing Business Centres', 'summary' => 'Planned stands, health compliance, and maintained buildings at centres.', 'description' => 'The council plans and services stands at Mutoko Centre, the Jani growth point, and rural service centres to approved planning standards. It enforces health compliance at business premises and maintains council buildings at the centres, alongside water reticulation, sewer, and road access.'],
            ['slug' => 'property-management', 'name' => 'Property Management', 'summary' => 'Acquisition, maintenance, development, and disposal of council property.', 'description' => 'The council handles acquisition, maintenance, development, and disposal of council property, including community centres, schools, and clinics. It administers residential, commercial, and institutional stands and leases in line with approved layouts and lease terms.'],
            ['slug' => 'recreational-facilities', 'name' => 'Recreational Facilities', 'summary' => 'Chikondoma Stadium, sports grounds, halls, and cultural events.', 'description' => 'The council maintains Chikondoma Stadium, sports grounds, and community halls available for hire by clubs, schools, and community groups. These venues host community and cultural events, tournaments, and celebrations across the district.'],
            ['slug' => 'natural-resources-conservation', 'name' => 'Conservation of Natural Resources', 'summary' => 'Sustainable land use and conservation awareness.', 'description' => 'The council promotes sustainable land use, woodland management, and protection of water sources, working with traditional leaders and conservation partners. It runs awareness programmes encouraging communities to conserve natural resources for future generations.'],
            ['slug' => 'welfare-services', 'name' => 'Welfare Services', 'summary' => 'Financial, food, and counselling support for vulnerable groups.', 'description' => 'The social services section provides financial, food, and counselling support for vulnerable households, older persons, and children in difficult circumstances. It coordinates assistance with community structures and partner organisations across the wards.'],
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
            ['slug' => 'mineral-mining-mutoko', 'title' => 'Mineral mining: lithium, gold, tantalite and granite', 'sector' => 'Mining', 'summary' => 'Lithium, gold, tantalite, and black and white granite deposits.', 'description' => 'The district hosts lithium, gold, tantalite, and black and white granite deposits of interest to small and large-scale operators. All activity requires valid licences from the Ministry of Mines and Mining Development and compliance with environmental requirements.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 1],
            ['slug' => 'horticulture-mutoko', 'title' => 'Horticulture production', 'sector' => 'Agriculture', 'summary' => 'Tomatoes, maize, and vegetables on fertile soils with Harare market access.', 'description' => 'Mutoko is known for horticultural produce, including tomatoes and maize grown on fertile soils, with road access to Harare markets. Opportunities exist in expanded production, irrigation, and outgrower schemes.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 2],
            ['slug' => 'processing-plants-mutoko', 'title' => 'Fruit and vegetable processing plants', 'sector' => 'Manufacturing', 'summary' => 'Canning, freezing, drying, and packaging plants for local produce.', 'description' => 'Investors can establish canning, freezing, drying, and packaging plants to add value to Mutoko fruit and vegetables. Council facilitates site allocation and can link investors with producer groups in the district.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 3],
            ['slug' => 'residential-stands-mutoko', 'title' => 'Residential stands development', 'sector' => 'Housing', 'summary' => 'Serviced residential stands at Mutoko Centre and growth points.', 'description' => 'The council offers serviced residential stands at Mutoko Centre and other centres for individual buyers and developers. Enquiries on availability, layouts, and terms are handled through council property administration.', 'location' => 'Mutoko Centre', 'opportunity_status' => 'prospecting', 'display_order' => 4],
            ['slug' => 'granite-cutting-polishing-mutoko', 'title' => 'Granite cutting and polishing plants', 'sector' => 'Mining', 'summary' => 'Value-addition plants for Mutoko black granite.', 'description' => 'Mutoko black granite is quarried in the district and mostly exported in raw form. Cutting and polishing plants would capture more value locally, with council support on siting and liaison with mining and environmental authorities.', 'location' => 'Mutoko District', 'opportunity_status' => 'prospecting', 'display_order' => 5],
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
            ['label' => 'District area', 'value' => '428916', 'unit' => 'hectares', 'icon' => 'area', 'source_note' => 'Old council site: 428,916 hectares.', 'display_order' => 2, 'is_active' => true],
            ['label' => 'Administrative district since', 'value' => '1902', 'unit' => null, 'icon' => 'history', 'source_note' => 'Old council site: Mutoko awarded administrative district 1902; offices opened 1987.', 'display_order' => 3, 'is_active' => true],
            ['label' => 'Primary schools', 'value' => '84', 'unit' => null, 'icon' => 'education', 'source_note' => 'Old council site: 84 primary schools.', 'display_order' => 4, 'is_active' => true],
            ['label' => 'Secondary schools', 'value' => '44', 'unit' => null, 'icon' => 'education', 'source_note' => 'Old council site: 44 secondary schools.', 'display_order' => 5, 'is_active' => true],
        ];
        foreach ($items as $item) {
            if (DistrictStatistic::query()->where('label', $item['label'])->exists()) {
                continue;
            }
            DistrictStatistic::query()->create($item);
        }
    }
}
