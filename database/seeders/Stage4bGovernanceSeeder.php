<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Stage 4B institutional governance & council policy pages.
 *
 * Populated with authentic statutory mandates, Vision 2030 objectives,
 * organisational leadership structure, and transparent rating/billing guidance.
 * All pages are published and publishable with complete, professional content.
 */
class Stage4bGovernanceSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'mandate',
                'title' => 'Our Mandate & Governance Framework',
                'summary' => 'The statutory mandate, legal authority, and local governance framework of Mutoko Rural District Council.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Statutory Establishment'],
                    ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council is established as a local government authority under the Rural District Councils Act [Chapter 29:13]. Amalgamated in 1995 from the former Budya, Mutoko District, and Mutoko South councils, the local authority exercises administrative and planning authority over approximately 4,092 square kilometres encompassing 29 electoral wards in Mashonaland East Province.'],
                    ['type' => 'heading', 'text' => 'Core Powers and Responsibilities'],
                    ['type' => 'paragraph', 'text' => 'In terms of the Act and related national statutes (including the Urban Councils Act where applicable to the Mutoko Town Board, the Regional, Town and Country Planning Act [Chapter 29:12], and the Public Health Act [Chapter 15:17]), Mutoko RDC is vested with statutory responsibilities to: Maintain peace, order, and good governance across the district; Plan, develop, and maintain public infrastructure, including secondary and feeder roads, culverts, bridges, and storm water drainages; Provide and manage potable water reticulation schemes, sewer reticulation, and refuse collection facilities; Administer land allocations, layout schemes, and building control inspectorates across commercial growth points and residential zones; Support primary healthcare clinics, rural health centres, and pre-tertiary educational facilities; Foster local economic development, mineral beneficiation, and commercial investment.'],
                    ['type' => 'heading', 'text' => 'Ward Governance and Community Participation'],
                    ['type' => 'paragraph', 'text' => 'Council authority operates on a participatory governance model spanning from Village Assemblies through Ward Development Committees (WARDCOs) to the Full Council. The legislative arm comprises 29 elected Ward Councillors and 12 non-elected councillors (including the 9 women\'s quota seats and Town Board representatives), providing democratic oversight over council administration.'],
                    ['type' => 'cta', 'text' => 'Contact the Council Secretariat', 'url' => '/contact'],
                ],
            ],
            [
                'slug' => 'vision-mission',
                'title' => 'Vision, Mission & Core Values',
                'summary' => 'The guiding strategic direction, long-term vision, and core corporate values of Mutoko Rural District Council.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Our Vision'],
                    ['type' => 'paragraph', 'text' => 'To be a model, prosperous, and sustainably industrialised rural local authority with socially and economically empowered communities by 2030.'],
                    ['type' => 'heading', 'text' => 'Our Mission'],
                    ['type' => 'paragraph', 'text' => 'To deliver high-standard civic services, modern public infrastructure, and transparent socio-economic development through efficient, responsive, and accountable local governance in partnership with our communities and stakeholders.'],
                    ['type' => 'heading', 'text' => 'Core Values'],
                    ['type' => 'paragraph', 'text' => 'Our daily administration, field operations, and public interactions are guided by six pillars of institutional integrity: Transparency & Accountability — ensuring open, ethical, and verifiable stewardship of public funds, assets, and decisions; Integrity & Fairness — treating every resident, investor, and visitor with equality and impartiality; Community Centricity — actively listening and consulting communities across all 29 wards in planning and resource allocation; Innovation & Continuous Improvement — leveraging modern technology and smart infrastructure to deliver agile services; Environmental Stewardship — promoting sustainable natural resources extraction, anti-litter campaigns, and green development; Excellence in Service Delivery — maintaining prompt response times, courteous communication, and reliable utilities.'],
                    ['type' => 'heading', 'text' => 'Strategic Priorities towards Vision 2030'],
                    ['type' => 'paragraph', 'text' => 'Our 5-Year Strategic Master Plan focuses on: upgrading Mutoko Centre into full town council status; expanding rural feeder road accessibility under devolution; achieving 100% clean water coverage at rural service centres; unlocking value in granite and agro-processing industries; and ensuring financial sustainability through fair property rating.'],
                    ['type' => 'cta', 'text' => 'Explore Our Development Projects', 'url' => '/projects'],
                ],
            ],
            [
                'slug' => 'organogram',
                'title' => 'Organisational Structure & Leadership',
                'summary' => 'The administrative and governance organogram of Mutoko Rural District Council.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Administrative Leadership'],
                    ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council is structured into complementary legislative and executive branches. The Full Council, chaired by the Council Chairperson, formulates policy, approves municipal by-laws, and passes annual budgets. Administrative execution is led by the Chief Executive Officer (CEO), who coordinates executive officers, technical departments, and district service centres.'],
                    ['type' => 'heading', 'text' => 'Executive Departments & Functions'],
                    ['type' => 'paragraph', 'text' => 'The council administration operates through six coordinated technical departments: 1. Office of the Chief Executive Officer: Corporate leadership, council committee secretariat, public relations, and legal compliance. 2. Engineering & Works: Civil engineering, road grading, culvert construction, piped water schemes, plant maintenance, and building inspections. 3. Finance & Revenue: Rates billing, tariff administration, financial reporting, valuation roll maintenance, and procurement. 4. Planning & Town Board Administration: Urban and rural physical planning, stand allocations, surveyed layouts, lease administration, and development control. 5. Social & Community Services: Rural health centres, school infrastructure support, environmental health, and refuse management ("Marara Mu Bin"). 6. Human Resources & Administration: Staff development, registry, customer support desks, and logistics.'],
                    ['type' => 'heading', 'text' => 'District Town Board Administration'],
                    ['type' => 'paragraph', 'text' => 'To support the rapid urbanisation of Mutoko Centre along the Harare–Nyamapanda highway, the Mutoko Town Board operates as a dedicated semi-autonomous administrative arm managing urban infrastructure, bus interchanges, high-mast solar lighting, and commercial zoning.'],
                    ['type' => 'cta', 'text' => 'Meet Council Leadership & Management', 'url' => '/officials'],
                ],
            ],
            [
                'slug' => 'rates-information',
                'title' => 'Council Rates, Tariffs & Payment Guide',
                'summary' => 'Transparent guidance on council rating categories, assessment notices, billing cycles, and approved payment methods.',
                'blocks' => [
                    ['type' => 'heading', 'text' => 'Rating Principles & Categories'],
                    ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council levies assessment rates, service charges, and development unit taxes in terms of the Rural District Councils Act [Chapter 29:13]. Revenue gathered from property rates directly finances roads gravelling, street lighting, public health centres, and clean piped water systems. Rates categories include: Residential Properties (Mutoko Centre, Jani, and growth points based on stand size and valuation); Commercial & Industrial Enterprises (retail, wholesale, fuel stations, granite extraction, and warehouses); Rural Unit Tax (communal land and agricultural plot assessments); Service Levies (water consumption tariffs, solid waste collection, and sewer reticulation).'],
                    ['type' => 'heading', 'text' => 'Billing Schedules & Account Inquiries'],
                    ['type' => 'paragraph', 'text' => 'Utility and rates statements are generated on a monthly cycle. Commercial stand holders and property owners can verify their ledger balances and obtain account statements by visiting the Revenue Hall at Stand 366 Mutoko Centre or contacting the Finance Department via info@mutokordc.co.zw.'],
                    ['type' => 'heading', 'text' => 'Approved Payment Channels'],
                    ['type' => 'paragraph', 'text' => 'Council accepts payments through official banking channels and cash office counters. Direct bank transfers can be executed to council revenue accounts: CBZ Bank (General Revenue & Rates), AFC Commercial Bank (Unit Tax & Development), ZB Bank (Capital Projects & Housing Nostro), and CABS (Business Licences). Always quote your Assessment Account Number or Stand Number as the payment reference, and present proof of payment at the civic centre.'],
                    ['type' => 'cta', 'text' => 'View Council Bank Accounts & Banking Details', 'url' => '/rates'],
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'summary' => $page['summary'],
                    'blocks' => $page['blocks'],
                    'seo_title' => $page['title'].' | Mutoko RDC',
                    'meta_description' => $page['summary'],
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                ]
            );
        }
    }
}
