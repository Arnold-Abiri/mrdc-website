<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\InvestmentOpportunity;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Stage 4 verified Mutoko content and municipal service guides.
 *
 * Populated with extensive step-by-step procedures, required documentation,
 * applicable fee categories, and department assignments for citizen and business services.
 * All records are seeded idempotently with published and publishable status.
 */
class Stage4MutokoContentSeeder extends Seeder
{
    public function run(): void
    {
        // Services reference departments by their stable council code, never by a seeded row ID.
        $this->call(DepartmentSeeder::class);
        $this->seedAboutPage();
        $this->seedServices();
        $this->seedInvestment();
        $this->seedStatistics();
    }

    private function seedAboutPage(): void
    {
        $aboutData = [
            'slug' => 'about-mutoko',
            'title' => 'About Mutoko Rural District Council',
            'summary' => 'Comprehensive district profile, history, governance mandate, and economic potential of Mutoko Rural District Council in Mashonaland East Province, Zimbabwe.',
            'blocks' => [
                ['type' => 'heading', 'text' => 'Our Geographic Setting & Regional Position'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council presides over an expansive territory of 428,916 hectares (approximately 4,092 square kilometres) situated within the north-eastern quadrant of Mashonaland East Province. The administrative centre is positioned at Mutoko Centre along the strategic Harare–Nyamapanda international corridor, exactly 143 kilometres north-east of Harare and approximately 90 kilometres south-west of the Nyamapanda international border post with Mozambique. The district is bounded by Mudzi District to the east, Uzumba-Maramba-Pfungwe (UMP) to the north and north-west, and Murehwa District to the south-west.'],
                ['type' => 'heading', 'text' => 'Our Vision and Mission'],
                ['type' => 'paragraph', 'text' => 'Our Vision: To be a model, prosperous, and sustainably industrialised rural local authority with socially and economically empowered communities by 2030.'],
                ['type' => 'paragraph', 'text' => 'Our Mission: To deliver high-standard civic services, modern public infrastructure, and transparent socio-economic development through efficient, responsive, and accountable local governance in partnership with our communities and stakeholders.'],
                ['type' => 'heading', 'text' => 'District History & Evolution of Local Governance'],
                ['type' => 'paragraph', 'text' => 'Mutoko has a long and storied administrative tradition, having first been designated an administrative district in 1902. Following the historical inequities of the Native Reserve administration in early colonial decades, the Native Council Act of 1937 established the earliest African council structures. Following Zimbabwe\'s national independence in 1980, the district was reorganised into district councils, culminating in the historic opening of the current civic centre offices at Mutoko Centre by Prime Minister Robert Gabriel Mugabe on 4 June 1987.'],
                ['type' => 'paragraph', 'text' => 'In 1994, the surrounding area was administered across three distinct local authorities: Budya Council, Mutoko District Council, and Mutoko South Council. In 1995, in accordance with the national integration under the Rural District Councils Act [Chapter 29:13], these three entities amalgamated to form the present Mutoko Rural District Council. Today, it stands as one of Zimbabwe\'s 58 rural district authorities, spearheading devolution, community resilience, and civic modernization.'],
                ['type' => 'heading', 'text' => 'Demographics, Culture & Ward Structure'],
                ['type' => 'paragraph', 'text' => 'According to the official 2022 National Population and Housing Census, Mutoko District is home to 161,091 residents (approximately 40,000 households). The district comprises 29 administrative and electoral wards, represented by 29 elected ward councillors alongside 12 non-elected special councillors (comprising 9 women\'s quota representatives and 3 Town Board representatives). The predominantly Shona-speaking populace belongs to the esteemed VaBudya culture, renowned for customary craftsmanship, solidarity, and agrarian enterprise under traditional chieftainships.'],
                ['type' => 'heading', 'text' => 'Economic Pillars: Agriculture, Mining & Commerce'],
                ['type' => 'paragraph', 'text' => 'Mutoko represents one of Zimbabwe\'s primary economic dynamos in both commercial horticulture and mineral extraction: 1. Agriculture & Horticulture: Known as the national tomato capital, Mutoko yields massive harvests of fresh tomatoes, mangoes, butternuts, onions, groundnuts, and maize, supplying major wholesale markets in Harare, Marondera, and across the nation. 2. Black Granite & Mineral Wealth: Mutoko is globally celebrated for its high-density, export-grade black and white granite dimension stone, alongside deposits of lithium, gold, tantalite, and beryl. 3. Commercial Hub & Town Board: Mutoko Centre and Jani Growth Point provide thriving retail, financial, transport, and warehousing hubs serving international highway traffic and district communities.'],
                ['type' => 'heading', 'text' => 'Our Core Mandate & Civic Service Commitments'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council delivers comprehensive services across the district: maintenance of more than 1,200 kilometres of feeder and access roads; clean potable water provision through piped schemes and boreholes; environmental sanitation and refuse compaction under our "Marara Mu Bin" campaign; operational support to 84 primary schools, 44 secondary schools, and multiple rural health centres; and modern town planning and surveyed stand allocations.'],
                ['type' => 'cta', 'text' => 'View Council Leadership & Management', 'url' => '/officials'],
                ['type' => 'cta', 'text' => 'Contact the Council Offices', 'url' => '/contact'],
            ],
            'seo_title' => 'About Mutoko Rural District Council | History, Profile & Mandate',
            'meta_description' => 'Detailed profile, history, demographic data, economic pillars, and governance mandate of Mutoko Rural District Council.',
            'status' => 'published',
            'verification_status' => 'publishable',
            'published_at' => now(),
        ];

        Page::query()->updateOrCreate(['slug' => 'about-mutoko'], $aboutData);
    }

    /**
     * Detailed service guides with step-by-step procedures, documentation requirements, and fees.
     *
     * @return list<array{
     *     slug: string,
     *     name: string,
     *     summary: string,
     *     description: string,
     *     department_code: string,
     *     requirements: list<string>,
     *     steps: list<string>,
     *     fees_information: string
     * }>
     */
    private function serviceDefinitions(): array
    {
        return [
            [
                'slug' => 'education-services',
                'name' => 'Education Services',
                'summary' => 'Council support for 84 primary and 44 secondary schools across the district, school stand planning, and classroom infrastructure grants.',
                'description' => 'Mutoko Rural District Council partners with the Ministry of Primary and Secondary Education, School Development Committees (SDCs), and community leadership to support 84 primary and 44 secondary schools across all 29 wards. The council facilitates the surveyed allocation of school institutional stands, building plan vetting, devolution-funded classroom construction, boreholes and sanitation facilities, and district sports development tournaments.',
                'department_code' => 'DEPT-SOC',
                'requirements' => [
                    'Formal application letter from the School Development Committee (SDC) or Ministry of Primary and Secondary Education.',
                    'Approved site plan and layout diagram for institutional land allocation.',
                    'Standard classroom or staff housing architectural blueprints complying with Council Building By-laws.',
                    'Proof of community matching labour or Devolution grant allocation confirmation.',
                    'Environmental health and water availability assessment report.',
                ],
                'steps' => [
                    'Step 1: Submission of infrastructure or stand regularisation proposal to the Social Services Department at Mutoko Civic Centre.',
                    'Step 2: Joint site verification by Council Building Inspectors, Town Planning section, and District Education Officers.',
                    'Step 3: Vetting and formal approval of architectural working drawings by the Engineering Department.',
                    'Step 4: Council resolution on institutional stand allocation or procurement clearance for construction materials.',
                    'Step 5: Phased construction monitoring, structural stage inspections (foundation, lintel, roof), and final issuance of Certificate of Occupancy.',
                ],
                'fees_information' => 'Institutional land allocation and building plan vetting fees are subsidised under the Council Education Support Schedule. Statutory inspection fees apply as per approved annual tariff schedule.',
            ],
            [
                'slug' => 'environmental-protection',
                'name' => 'Environmental Protection',
                'summary' => 'Effluent treatment, sewer reticulation, environmental by-law compliance, and scheduled refuse collection under the "Marara Mu Bin" campaign.',
                'description' => 'The Environmental Health and Social Services section safeguards public hygiene and environmental sustainability across Mutoko Centre, growth points, and rural service centres. Council operates modern refuse compactor vehicles, administers the "Mutoko RDC Irikuti Marara Mu Bin" anti-litter campaign, maintains effluent stabilization ponds, monitors commercial grease traps, and conducts environmental audits in collaboration with the Environmental Management Agency (EMA).',
                'department_code' => 'DEPT-SOC',
                'requirements' => [
                    'Standard Council Refuse Service Registration Form for commercial premises, market stalls, and residential holders.',
                    'Installation of approved waste disposal bins (standard 240L plastic wheeled bin or concrete receptacle).',
                    'For food handling and commercial premises: Valid Health Inspection Certificate and grease trap installation certificate.',
                    'For industrial/mining operators: Approved Environmental Impact Assessment (EIA) certificate and effluent discharge license.',
                ],
                'steps' => [
                    'Step 1: Application for refuse collection service or sewer connection at the Social Services / Revenue office.',
                    'Step 2: Premise inspection by Council Environmental Health Technicians to confirm waste storage standards.',
                    'Step 3: Allocation of collection schedule zone and billing account opening.',
                    'Step 4: Routine scheduled collection by council refuse compactor fleet (weekly for commercial, bi-weekly for residential).',
                    'Step 5: Annual compliance review and issuance of Environmental Sanitation Clearance.',
                ],
                'fees_information' => 'Monthly refuse collection and sewer tariffs are billed according to property category (Residential, Commercial, Industrial, or Institutions) as defined in the Council Tariff Gazette.',
            ],
            [
                'slug' => 'roads-and-works',
                'name' => 'Roads and Works',
                'summary' => 'Road network maintenance, culvert construction, building inspectorate, bridge repairs, and civil infrastructure across the 29 wards.',
                'description' => 'The Engineering and Works Department designs, constructs, and maintains more than 1,200 kilometres of primary, secondary, and feeder gravel roads connecting communal areas, clinics, schools, and business centres to the Harare–Nyamapanda highway. The section operates council heavy plant (motor graders, tippers, front-end loaders), undertakes emergency road repairs under the ERRP-II programme, manages piped water extensions, and enforces structural building compliance.',
                'department_code' => 'DEPT-ENG',
                'requirements' => [
                    'For Building Plan Approval: 4 copies of complete architectural drawings drawn to standard metric scale (including floor plans, elevations, section, and drainage layout).',
                    'Proof of stand ownership, valid lease agreement, or formal council offer letter.',
                    'Structural engineering calculations for multi-storey, commercial, or public hall developments.',
                    'Payment of statutory building plan examination fee and beacon relocation fee (where applicable).',
                    'For Road Access / Culvert Permits: Site plan indicating access intersection with council road reserve.',
                ],
                'steps' => [
                    'Step 1: Lodgement of building plans and ownership documents at the Engineering Works office, Stand 366 Mutoko Centre.',
                    'Step 2: Technical review by Building Inspector, Planning Officer, and Environmental Health Inspector (7-14 working days).',
                    'Step 3: Issuance of approved stamped plans and Building Notice Book.',
                    'Step 4: Mandatory stage inspections: Trench foundation, damp-proof course (DPC), wall plate/lintel, and roof structure.',
                    'Step 5: Final plumbing/drainage test and issuance of Certificate of Completion & Occupancy.',
                ],
                'fees_information' => 'Building plan fees are calculated per square metre of gross floor area in accordance with the gazetted building tariff. Road access and heavy equipment hire rates are available upon formal quotation.',
            ],
            [
                'slug' => 'health-services',
                'name' => 'Health Services',
                'summary' => 'Rural health centres: maternal child health, immunisation, treatment of ailments, disease surveillance, and food safety inspection.',
                'description' => 'Mutoko Rural District Council operates an integrated network of rural clinics and health centres across the district, working in close liaison with the Ministry of Health and Child Care. Facilities provide primary healthcare, maternal and child healthcare, comprehensive immunisation (EPI), voluntary counselling and testing, chronic ailment management, outpatient treatment, and emergency ambulance dispatch, alongside continuous disease prevention programmes.',
                'department_code' => 'DEPT-SOC',
                'requirements' => [
                    'National Identity Card, Birth Certificate, or Road to Health Child Card for medical consultations.',
                    'Referral letter from village health workers or community clinics for specialised interventions.',
                    'For Commercial Food Handlers: Medical fitness examination certificate (Food Handlers Certificate), chest X-ray report, and stool microscopy.',
                    'For Business Premise Licencing: Compliance with public health regulations (adequate ventilation, running water, refuse disposal, approved ablutions).',
                ],
                'steps' => [
                    'Step 1: Patient registration and vital statistics recording at the clinic reception desk.',
                    'Step 2: Nursing triage and confidential clinical consultation.',
                    'Step 3: Laboratory rapid diagnostic testing (malaria, HIV, diabetes, blood pressure screening).',
                    'Step 4: Prescription dispensing and health wellness education.',
                    'Step 5: For business premises: Annual inspection by Council Health Inspector followed by issuance of Public Health Licence.',
                ],
                'fees_information' => 'Primary maternal care, child immunisations under 5 years, and vulnerable community health consultations are heavily subsidised or exempt. Commercial food handler medicals and business health certifications follow statutory rates.',
            ],
            [
                'slug' => 'business-centre-servicing',
                'name' => 'Servicing Business Centres',
                'summary' => 'Planned commercial stands, shop licenses, market vending stalls, development control, and infrastructure at growth points.',
                'description' => 'The Planning and Town Board administration coordinates physical planning, stand survey pegs, and infrastructure servicing at Mutoko Centre, Jani Growth Point, and designated rural service centres. Services encompass issuance of shop operating licenses, vendor stall allocations, business lease regularisations, public lighting, and commercial parking bays to foster an orderly, thriving business ecosystem along the transit corridor.',
                'department_code' => 'DEPT-PLAN',
                'requirements' => [
                    'Completed Business Licence Application Form.',
                    'Certified Certificate of Incorporation / CR14 (for companies) or National ID (for sole traders).',
                    'Valid lease agreement or title deed for the commercial premises.',
                    'Public Health Clearance Certificate from Council Environmental Health Department.',
                    'Fire Safety Inspection Clearance from Fire Brigade / Civil Protection Unit.',
                ],
                'steps' => [
                    'Step 1: Application lodgement with the Planning & Business Licencing section at Stand 366 Mutoko Centre.',
                    'Step 2: Notice publication in local press / council notice board for statutory objection period (where required for liquor or special trades).',
                    'Step 3: Multi-departmental premise inspection (Town Planning, Building Inspectorate, Public Health, Fire Officer).',
                    'Step 4: Deliberation and recommendation by Council Licencing Committee.',
                    'Step 5: Payment of licence fee and issuance of framed Council Business Operation Licence.',
                ],
                'fees_information' => 'Licence fees vary by category (General Dealer, Butchery, Wholesale, Hardware, Liquormart, Service Station, Light Industry). An itemised commercial fee schedule is provided by the revenue department.',
            ],
            [
                'slug' => 'property-management',
                'name' => 'Property Management',
                'summary' => 'Acquisition, leasing, development, stand allocation, title deeds processing, and property rights securitization.',
                'description' => 'The Property Management and Town Planning section administers the council estate portfolio comprising residential, commercial, industrial, and institutional stands across all 29 wards. The unit oversees transparent waiting list administration, lease-to-buy conversions, stand titling, property repossessions, ground rent billing, and lease transfer documentation in strict adherence to council resolutions.',
                'department_code' => 'DEPT-PLAN',
                'requirements' => [
                    'Registration on the Council Housing / Commercial Stand Waiting List (valid annual renewal).',
                    'Copy of National Identity Document, proof of current residential address, and passport photos.',
                    'For Stand Transfers: Cession application form signed by both assignor and assignee before a Commissioner of Oaths.',
                    'Proof of rate clearance (zero arrears on assessment rates, refuse, and ground rent).',
                    'Confirmation of required minimum structural development (slab level or completed structure) before cession approval.',
                ],
                'steps' => [
                    'Step 1: Application submission and registration on the Council Housing / Commercial Stand Database.',
                    'Step 2: Public stand advertisement or allocation offer letter issued upon verification of stand availability.',
                    'Step 3: Execution of Memorandum of Agreement of Lease with Council within 30 days of allocation.',
                    'Step 4: Submission and approval of building plans prior to commencing construction works.',
                    'Step 5: Upon full payment of purchase price and completion of minimum developments, council issues Title Deed processing clearance.',
                ],
                'fees_information' => 'Waiting list registration, stand application fees, lease preparation fees, and cession fees are payable at council revenue offices. Full details published in the Council Stand Policy manual.',
            ],
            [
                'slug' => 'recreational-facilities',
                'name' => 'Recreational Facilities',
                'summary' => 'Chikondoma Stadium bookings, community halls, public sports grounds, youth development centres, and cultural events.',
                'description' => 'Council provides, manages, and maintains sporting and social infrastructure, spearheaded by the multi-purpose Chikondoma Stadium, Ward Community Halls, public playing fields, and recreational open spaces. The facilities cater to premier league and district football matches, school athletics, community celebrations, state functions, cultural galas, and agricultural expositions.',
                'department_code' => 'DEPT-SOC',
                'requirements' => [
                    'Official booking request letter stating event date, nature of event, expected attendance, and security arrangements.',
                    'For Commercial Musical Shows / Galas: Police clearance from Zimbabwe Republic Police (ZRP Mutoko District) and censorship board clearance.',
                    'Payment of hire deposit and refundable breakage/cleaning bond.',
                    'Provision of public liability insurance or event safety protocol agreement.',
                ],
                'steps' => [
                    'Step 1: Verification of venue date availability at the Social Services Department, Civic Centre.',
                    'Step 2: Completion of Official Venue Booking Agreement Form.',
                    'Step 3: Submission of security clearances and stakeholder liaison (Red Cross / Ambulance, ZRP).',
                    'Step 4: Settlement of hiring charges and security deposit at the Revenue Cashier.',
                    'Step 5: Joint pre-event and post-event inspection of facilities, followed by refund of caution fee upon clearance.',
                ],
                'fees_information' => 'Hire tariffs are structured on an hourly or daily rate, categorized by sporting events, community/church gatherings, state functions, or commercial entertainment events.',
            ],
            [
                'slug' => 'natural-resources-conservation',
                'name' => 'Conservation of Natural Resources',
                'summary' => 'Sustainable environmental management, mining rehabilitation compliance, granite quarry monitoring, and veld fire prevention.',
                'description' => 'Mutoko District is endowed with exceptional natural wealth, particularly export-grade black granite, mineral deposits, indigenous woodlands, and fertile catchment basins. Council\'s Natural Resources unit collaborates with traditional leadership, the Forestry Commission, and EMA to enforce conservation by-laws, regulate sand and gravel abstraction, oversee post-mining land reclamation, prevent deforestation, and combat seasonal veld fires.',
                'department_code' => 'DEPT-PLAN',
                'requirements' => [
                    'For Sand Abstraction / Quarrying: Council Natural Resource Extraction Permit application.',
                    'Approved Environmental Management Plan (EMP) and Environmental Impact Assessment (EIA) certificate.',
                    'Written concurrence from the local Ward Development Committee (WARDCO) and traditional leadership.',
                    'Payment of statutory royalties and land rehabilitation deposit bond.',
                    'Compliance with designated extraction corridors avoiding riverbanks and road reserves.',
                ],
                'steps' => [
                    'Step 1: Lodging application for resource extraction or timber utilisation permit with Council Natural Resources Officer.',
                    'Step 2: Site appraisal and environmental baseline survey conducted with EMA and Ward Councillor.',
                    'Step 3: Verification of community benefit sharing plan and extraction boundaries.',
                    'Step 4: Approval by Council Environment and Natural Resources Committee.',
                    'Step 5: Issuance of Extraction Permit and scheduled quarterly environmental monitoring audits.',
                ],
                'fees_information' => 'Extraction royalties and environmental permit fees are determined on a volumetric (per cubic metre or tonnage) basis in line with the gazetted Natural Resources By-laws.',
            ],
            [
                'slug' => 'welfare-services',
                'name' => 'Welfare Services',
                'summary' => 'Social safety nets, disaster relief coordination, disability support, assistance for older persons, and community welfare referral.',
                'description' => 'The Social Welfare desk provides social safety nets and humanitarian assistance to vulnerable households, orphans, persons with disabilities, and senior citizens across Mutoko District. Operating in coordination with the Ministry of Public Service, Labour and Social Welfare and international humanitarian partners, council facilitates BEAM school fee support, food deficit mitigation programmes, assistive mobility devices, and emergency civil protection during weather disasters.',
                'department_code' => 'DEPT-SOC',
                'requirements' => [
                    'National Identity Document and verification letter from the local Village Head or Ward Councillor.',
                    'Assessment report from the Ward Social Welfare Committee or healthcare personnel.',
                    'For BEAM Educational Assistance: Recommendation by School Selection Committee confirming vulnerability status.',
                    'For Disability Support: Medical disability assessment form signed by Government Medical Officer.',
                ],
                'steps' => [
                    'Step 1: Confidential case intake and vulnerability assessment at the Social Services Desk or Ward Outreach Centre.',
                    'Step 2: Household verification and eligibility screening against national social protection criteria.',
                    'Step 3: Registration on the District Vulnerability Register and Social Welfare database.',
                    'Step 4: Enrolment into designated relief stream (BEAM, food assistance, assistive devices, or psychosocial counselling).',
                    'Step 5: Case monitoring, progress evaluation, and referral to specialized governmental or NGO support services.',
                ],
                'fees_information' => 'All public welfare assessments, community social protection registrations, and humanitarian referrals are delivered free of charge as an essential civic mandate.',
            ],
        ];
    }

    private function seedServices(): void
    {
        foreach ($this->serviceDefinitions() as $index => $definition) {
            $department = Department::query()->where('code', $definition['department_code'])->firstOrFail();
            unset($definition['department_code']);
            $definition['department_id'] = $department->id;

            Service::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    ...$definition,
                    'display_order' => $index,
                    'seo_title' => $definition['name'].' | Council Services | Mutoko RDC',
                    'meta_description' => $definition['summary'],
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),

                ]
            );
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
            InvestmentOpportunity::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),

                ]
            );
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
            DistrictStatistic::query()->updateOrCreate(
                ['label' => $item['label']],
                $item
            );
        }
    }
}
