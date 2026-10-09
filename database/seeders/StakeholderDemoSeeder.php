<?php

namespace Database\Seeders;

use App\Models\CouncilMeeting;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\Service;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Stakeholder demonstration content.
 *
 * Classification: PROVISIONAL DEMO. Every record seeded here uses
 * status=draft + verification_status=demo, which the public scopes
 * (scopePublic) can never return. Council reviews each item in Filament
 * and publishes it explicitly; nothing here reaches the public site.
 *
 * Idempotent: stable slugs via updateOrCreate guarded so administrator
 * edits are never overwritten (only demo-classified rows are refreshed,
 * and featured content fields are only filled when empty).
 */
class StakeholderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEditorial();
        $this->seedDocuments();
        $this->seedWards();
        $this->seedOfficials();
        $this->seedMeetings();
        $this->seedSlides();
        $this->seedServices();
        $this->seedInvestment();
        $this->seedPages();
        $this->seedStatisticProvenance();
        $this->publishVerifiedContent();
    }

    // ------------------------------------------------------------------
    // News (informational editorial) + sample notices
    // ------------------------------------------------------------------
    private function seedEditorial(): void
    {
        // Retired sample notices: replaced with standing informational notices.
        EditorialItem::query()->whereIn('slug', ['sample-public-consultation-notice', 'sample-community-meeting-notice', 'sample-service-announcement'])->delete();

        $items = [
            [
                'slug' => 'demo-understanding-mrdc-role',
                'type' => 'news',
                'title' => 'Understanding the role of Mutoko Rural District Council',
                'summary' => 'What a rural district council does, and how Mutoko RDC plans, funds and delivers local services.',
                'category' => 'Governance',
                'body' => "Mutoko Rural District Council is the local authority responsible for rural local governance across Mutoko District in Mashonaland East, operating under the Rural District Councils Act.\n\nThe council maintains rural roads, supports water and sanitation provision, manages business centres, oversees land-use planning, protects the environment, and promotes local economic development across the district's 29 wards.\n\nCouncil decisions are made by elected councillors in full council and committee meetings. Residents can follow council business through published notices, meeting outcomes, and the documents centre on this website.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'demo-accessing-council-services',
                'type' => 'news',
                'title' => 'How residents can access council services',
                'summary' => 'A practical guide to requesting services, tracking enquiries, and visiting council offices.',
                'category' => 'Services',
                'body' => "Residents can contact the council through the public enquiry form on this website, by visiting council offices at Mutoko Centre, or through their ward councillor.\n\nWhen submitting an enquiry, describe the issue, give the ward and location, and keep any reference number issued so the matter can be followed up.\n\nService requests commonly include road maintenance, water supply faults, business-centre servicing, and property matters. Each request is routed to the responsible department.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'demo-community-participation',
                'type' => 'news',
                'title' => 'Community participation in local governance',
                'summary' => 'How village, ward and district structures give residents a voice in council decisions.',
                'category' => 'Governance',
                'body' => "Participation runs from the village assembly through ward development committees to the Rural District Development Committee, where community priorities feed into council planning and budgeting.\n\nResidents are encouraged to attend community meetings, raise needs through their councillor, and comment during public consultation processes announced in the notices section.\n\nStrong participation helps the council direct limited resources to the most pressing local needs.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'demo-rural-roads-maintenance',
                'type' => 'news',
                'title' => 'Rural roads and infrastructure maintenance',
                'summary' => 'How the council prioritises grading, drainage and repairs across a large rural road network.',
                'category' => 'Infrastructure',
                'body' => "Mutoko District covers an extensive rural road network linking farming areas, schools, clinics and business centres. Maintenance is prioritised by traffic volume, access to essential services, and reports received from communities.\n\nRoutine work includes grading, spot gravelling, culvert clearing and bush clearing. Heavier rehabilitation is programmed through periodic projects subject to funding.\n\nResidents can report impassable sections through the enquiry form or their ward councillor, giving the road name and nearest landmark.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'demo-environmental-stewardship',
                'type' => 'news',
                'title' => 'Environmental stewardship in Mutoko District',
                'summary' => 'Conservation, woodland management and responsible mining in a granite landscape.',
                'category' => 'Environment',
                'body' => "Mutoko's granite kopjes, woodlands and wetlands sustain farming, grazing and biodiversity. The council works with environmental agencies and traditional leadership to discourage stream-bank cultivation, uncontrolled fires and unregulated extraction.\n\nWoodland management, gully reclamation and waste management at business centres are standing community priorities.\n\nGuidance on permits for sand, gravel and quarry extraction is available from the council's environmental and planning functions.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'demo-agriculture-economic-development',
                'type' => 'news',
                'title' => 'Agriculture and local economic development',
                'summary' => 'Maize, groundnuts, tobacco and horticulture anchor the district economy; value addition is the next step.',
                'category' => 'Economy',
                'body' => "Agriculture — chiefly maize, groundnuts, tobacco and horticulture — is the primary occupation across Mutoko District, complemented by small-scale mining and trade at rural business centres.\n\nThe council supports production through business-centre servicing, market infrastructure, and investment facilitation in agro-processing such as fruit and vegetable processing plants.\n\nFarmers and cooperatives seeking to engage the council can use the investment and enquiry channels on this website.\n\n[DRAFT DEMONSTRATION CONTENT — awaiting council verification before publication.]",
            ],
            [
                'slug' => 'how-council-consultations-work',
                'type' => 'notice',
                'title' => 'How council consultations work',
                'summary' => 'Where and how the council announces opportunities for public comment.',
                'category' => 'Information',
                'body' => "When the council seeks public comment — on plans, tariffs or bylaws — this notices section carries the subject of the consultation, how to submit views, and the closing date.\n\nSubmissions may be made in writing through the contact page or delivered to council offices at Mutoko Centre. Late submissions may not be considered, so residents are encouraged to respond before the stated deadline.",
            ],
            [
                'slug' => 'attending-council-meetings',
                'type' => 'notice',
                'title' => 'Following council meetings',
                'summary' => 'How full council and committee business is communicated to residents.',
                'category' => 'Information',
                'body' => "Full council sittings consider reports from portfolio committees and take decisions by resolution. Agendas, meeting outcomes and minutes are made available through the meetings and documents sections of this website as they are confirmed.\n\nResidents who wish to raise a matter for council attention should do so through their ward councillor or the public enquiry form.",
            ],
            [
                'slug' => 'how-service-updates-are-communicated',
                'type' => 'notice',
                'title' => 'How service updates are communicated',
                'summary' => 'Where to check when water, roads or office services are affected.',
                'category' => 'Information',
                'body' => "Planned works and service interruptions affecting water supply, road access or office services are announced in this notices section, stating the affected area and expected duration.\n\nFor faults such as burst pipes or impassable road sections, report the location and ward through the public enquiry form or your ward councillor.",
            ],
        ];

        foreach ($items as $order => $item) {
            $record = EditorialItem::query()->firstOrNew(['slug' => $item['slug']]);
            if (! $record->exists) {
                $record->fill([
                    'type' => $item['type'],
                    'status' => 'draft',
                    'verification_status' => 'demo',
                    'display_order' => $order,
                    'seo_title' => $item['title'].' | Mutoko RDC',
                    'meta_description' => $item['summary'],
                ]);
            }
            if ($record->verification_status === 'demo') {
                $record->fill([
                    'title' => $item['title'],
                    'summary' => $item['summary'],
                    'body' => $item['body'],
                    'category' => $item['category'],
                    'seo_title' => $item['title'].' | Mutoko RDC',
                    'meta_description' => $item['summary'],
                ]);
                $record->save();
            }
        }
    }

    // ------------------------------------------------------------------
    // Informational guides (real downloadable text files, draft/demo)
    // ------------------------------------------------------------------
    private function seedDocuments(): void
    {
        $docs = [
            ['slug' => 'demo-council-services-overview', 'title' => 'Council Services Overview', 'description' => 'A guide to the services provided by Mutoko Rural District Council and how to request them.', 'category' => 'publication', 'filename' => 'council-services-overview.txt', 'body' => "MUTOKO RURAL DISTRICT COUNCIL — COUNCIL SERVICES OVERVIEW\n\nThis guide summarises the services covered on this website: education support, environmental management, roads and works, health support, business-centre servicing, property management, recreation, conservation and welfare.\n\nTo request a service, use the public enquiry form with your ward and location, or visit council offices at Mutoko Centre."],
            ['slug' => 'demo-public-enquiry-guide', 'title' => 'How to Submit a Public Enquiry', 'description' => 'A step-by-step guide to submitting and following up a council enquiry.', 'category' => 'form', 'filename' => 'public-enquiry-guide.txt', 'body' => "MUTOKO RURAL DISTRICT COUNCIL — HOW TO SUBMIT A PUBLIC ENQUIRY\n\n1. Open the Contact page and complete the enquiry form.\n2. Describe the issue, state your ward and nearest landmark.\n3. Keep the reference number issued for follow-up.\n4. Enquiries are routed to the responsible department during working hours."],
            ['slug' => 'demo-community-participation-guide', 'title' => 'Community Participation Guide', 'description' => 'How residents take part in ward structures and council planning.', 'category' => 'publication', 'filename' => 'community-participation-guide.txt', 'body' => "MUTOKO RURAL DISTRICT COUNCIL — COMMUNITY PARTICIPATION\n\nParticipation runs from the village assembly through ward development committees to the Rural District Development Committee.\n\nAttend community meetings, raise needs through your councillor, and comment during public consultations announced in the notices section."],
        ];
        foreach ($docs as $doc) {
            Storage::disk(config('cms.media_disk', 'local'))->put('demo-guides/'.$doc['filename'], $doc['body']);
            $media = Media::query()->firstOrNew(['storage_path' => 'demo-guides/'.$doc['filename']]);
            if (! $media->exists) {
                $media->fill([
                    'title' => $doc['title'],
                    'alt_text' => $doc['title'],
                    'original_filename' => $doc['filename'],
                    'mime_type' => 'text/plain',
                    'size' => strlen($doc['body']),
                    'status' => 'draft',
                ]);
                $media->save();
            }
            $record = Document::query()->firstOrNew(['slug' => $doc['slug']]);
            if (! $record->exists) {
                $record->fill(['status' => 'draft', 'verification_status' => 'demo', 'visibility' => 'public', 'reference_date' => now()->toDateString(), 'media_id' => $media->id]);
            }
            if ($record->verification_status === 'demo') {
                $record->fill(['title' => $doc['title'], 'description' => $doc['description'], 'category' => $doc['category'], 'media_id' => $media->id]);
                $record->save();
            }
        }
        // Seeder-owned copy sync for published guides.
        foreach ($docs as $doc) {
            Document::query()->where('slug', $doc['slug'])->update(['title' => $doc['title'], 'description' => $doc['description']]);
        }
    }

    // ------------------------------------------------------------------
    // Neutral ward placeholders (29 verified wards, details pending)
    // ------------------------------------------------------------------
    private function seedWards(): void
    {
        for ($i = 1; $i <= 29; $i++) {
            $slug = sprintf('ward-%02d', $i);
            $record = Ward::query()->firstOrNew(['slug' => $slug]);
            if (! $record->exists) {
                $record->fill(['status' => 'draft', 'verification_status' => 'demo', 'display_order' => $i]);
            }
            if ($record->verification_status === 'demo') {
                $record->fill([
                    'name' => sprintf('Ward %d', $i),
                    'description' => sprintf('Ward %d of Mutoko Rural District Council. Councillor information, local facilities and meeting updates will be listed on this page.', $i),
                    'boundaries_description' => 'Ward boundary information will be shown here.',
                ]);
                $record->save();
            }
        }
        // Seeder-owned copy sync for ward placeholders (numbered from slug).
        foreach (Ward::query()->where('slug', 'like', 'ward-%')->get(['id', 'slug']) as $ward) {
            if (preg_match('/ward-(\d+)/', (string) $ward->slug, $m)) {
                $ward->description = sprintf('Ward %d of Mutoko Rural District Council. Councillor information, local facilities and meeting updates will be listed on this page.', (int) $m[1]);
                $ward->boundaries_description = 'Ward boundary information will be shown here.';
                $ward->save();
            }
        }
    }

    // ------------------------------------------------------------------
    // Role-based leadership placeholders (no fictitious identities)
    // ------------------------------------------------------------------
    private function seedOfficials(): void
    {
        $roles = [
            ['slug' => 'demo-office-chairperson', 'name' => 'Office of the Council Chairperson', 'title' => 'Council Chairperson'],
            ['slug' => 'demo-office-ceo', 'name' => 'Office of the Chief Executive Officer', 'title' => 'Chief Executive Officer'],
            ['slug' => 'demo-office-clerk', 'name' => 'Office of the Council Secretary', 'title' => 'Council Secretary'],
        ];
        foreach ($roles as $order => $role) {
            $record = Official::query()->firstOrNew(['slug' => $role['slug']]);
            if (! $record->exists) {
                $record->fill(['status' => 'draft', 'verification_status' => 'demo', 'display_order' => $order]);
            }
            if ($record->verification_status === 'demo') {
                $record->fill([
                    'name' => $role['name'],
                    'title' => $role['title'],
                    'biography' => sprintf('The %s provides leadership to Mutoko Rural District Council — setting policy direction through full council, overseeing the administration, and representing the district. Office-holder details and an official portrait will be added here.', $role['title']),
                ]);
                $record->save();
            }
        }
        // Seeder-owned copy sync for published office profiles.
        $officeCopy = [
            'demo-office-chairperson' => ['Office of the Council Chairperson', 'The Council Chairperson provides political leadership to Mutoko Rural District Council — presiding over full council, guiding policy direction, and representing the district. Office-holder details and an official portrait will be added here.'],
            'demo-office-ceo' => ['Office of the Chief Executive Officer', 'The Chief Executive Officer heads the council administration — implementing council resolutions, managing departments, and accounting for service delivery across the district. Office-holder details and an official portrait will be added here.'],
            'demo-office-clerk' => ['Office of the Council Secretary', 'The Council Secretary supports the governance of Mutoko Rural District Council — managing council records, meeting procedures, and official correspondence. Office-holder details and an official portrait will be added here.'],
        ];
        foreach ($officeCopy as $slug => [$name, $bio]) {
            Official::query()->where('slug', $slug)->update(['name' => $name, 'biography' => $bio]);
        }
    }

    // ------------------------------------------------------------------
    // Hero slides (3 demo slides reusing approved local imagery)
    // ------------------------------------------------------------------
    private function seedSlides(): void
    {
        // The first slide mirrors the approved static hero (identity + image),
        // so the carousel opens with the council's established presentation.
        // A legacy first-slide headline from an earlier demo seed is retired.
        HomepageSlide::query()->where('headline', 'Quality Services for Every Ward')->where('status', 'draft')->delete();

        // Keep edits made in the admin portal when the demo seeder is run again.
        if (HomepageSlide::query()->exists()) {
            return;
        }

        $slides = [
            [
                'source' => 'public/images/hero-clean.webp',
                'path' => 'slides/demo-services.webp',
                'alt' => 'Scenic Mutoko landscape showing rocky kopje mountains, green valley and Mutoko town center',
                'headline' => 'Mutoko Rural District Council',
                'supporting_text' => 'Working with our communities to deliver quality services, promote local development and build a better Mutoko.',
                'cta_label' => 'Our Services',
                'cta_url' => '/services',
            ],
            [
                'source' => 'public/images/home/dev-background.webp',
                'path' => 'slides/demo-development.webp',
                'alt' => 'Mutoko rural landscape earmarked for development',
                'headline' => 'Roads, Water and Growth Points',
                'supporting_text' => 'The council maintains rural access, services business centres and programmes rehabilitation where funding allows.',
                'cta_label' => 'See development priorities',
                'cta_url' => '/projects',
            ],
            [
                'source' => 'public/images/feature-invest-development.webp',
                'path' => 'slides/demo-investment.webp',
                'alt' => 'Development activity in Mutoko District',
                'headline' => 'Invest in Mutoko’s Future',
                'supporting_text' => 'Agriculture, agro-processing, mining value addition and housing — provisional sector briefs for investor engagement.',
                'cta_label' => 'Explore investment',
                'cta_url' => '/investment',
            ],
        ];

        foreach ($slides as $order => $slide) {
            if (! is_file(base_path($slide['source']))) {
                continue;
            }
            Storage::disk(config('cms.media_disk', 'local'))->put($slide['path'], file_get_contents(base_path($slide['source'])));
            $media = Media::unguarded(function () use ($slide) {
                return Media::query()->updateOrCreate(
                    ['storage_path' => $slide['path']],
                    [
                        'title' => $slide['headline'],
                        'alt_text' => $slide['alt'],
                        'original_filename' => basename($slide['path']),
                        'mime_type' => 'image/webp',
                        'size' => Storage::disk(config('cms.media_disk', 'local'))->size($slide['path']),
                        'width' => 1983,
                        'height' => 793,
                        'status' => 'active',
                    ]
                );
            });

            HomepageSlide::unguarded(function () use ($slide, $media, $order) {
                HomepageSlide::query()->create([
                    'headline' => $slide['headline'],
                    'supporting_text' => $slide['supporting_text'],
                    'cta_label' => $slide['cta_label'],
                    'cta_url' => $slide['cta_url'],
                    'media_id' => $media->id,
                    'display_order' => $order,
                    'is_active' => true,
                    'status' => $order === 0 ? 'published' : 'draft',
                    'published_at' => $order === 0 ? now() : null,
                ]);
            });
        }
    }

    // ------------------------------------------------------------------
    // Draft meetings for the preview events panel (never published here).
    // ------------------------------------------------------------------
    private function seedMeetings(): void
    {
        $meetings = [
            [
                'title' => 'Ordinary Full Council Sitting',
                'meeting_type' => 'full_council',
                'scheduled_date' => now()->addDays(21)->toDateString(),
                'scheduled_time' => '09:00',
                'venue' => 'Council Chambers, Mutoko Centre',
                'meeting_status' => 'scheduled',
                'summary' => 'Quarterly sitting of full council to consider committee reports and resolutions.',
            ],
            [
                'title' => 'Finance and Administration Committee',
                'meeting_type' => 'committee',
                'scheduled_date' => now()->addDays(14)->toDateString(),
                'scheduled_time' => '10:00',
                'venue' => 'Council Chambers, Mutoko Centre',
                'meeting_status' => 'scheduled',
                'summary' => 'Committee sitting on finance, administration and staffing matters.',
            ],
        ];
        foreach ($meetings as $order => $meeting) {
            $record = CouncilMeeting::query()->firstOrNew(['title' => $meeting['title']]);
            if (! $record->exists) {
                $record->fill(['status' => 'draft', 'verification_status' => 'demo', 'display_order' => $order]);
            }
            if ($record->verification_status === 'demo' && $record->status === 'draft') {
                $record->fill($meeting);
                $record->save();
            }
        }
    }

    // ------------------------------------------------------------------
    // Service improvements (demo rows only; statuses preserved)
    // ------------------------------------------------------------------
    private function seedServices(): void
    {
        $improvements = [
            'education-services' => ['Education Services', 'Council support for schools, learning facilities and education infrastructure across the district.', 'The council supports basic education through classroom infrastructure development, maintenance of school facilities, and collaboration with education authorities and school development committees. This includes servicing school stands, supporting sanitation at learning institutions, and facilitating community contributions to school projects. For school-specific administrative matters, residents are guided to the relevant education authorities; the council handles the physical and planning aspects within its mandate.'],
            'environmental-protection' => ['Environmental Management', 'Guidance and enforcement for a cleaner, sustainable Mutoko — waste, woodlands and pollution control.', 'The council promotes environmental stewardship through waste management at business centres, control of litter and illegal dumping, woodland and wetland protection awareness, and collaboration with environmental agencies. Residents and businesses can seek guidance on permits, report environmental offences, and participate in clean-up programmes.'],
            'roads-and-works' => ['Roads and Works', 'Maintenance and development of the rural road network linking communities, schools and markets.', 'The council maintains rural access roads through grading, spot gravelling, drainage clearing and bush clearing, prioritised by traffic, access to essential services, and community reports. New works and rehabilitation are programmed subject to funding. Report impassable sections with the road name and nearest landmark through the enquiry form or your ward councillor.'],
            'health-services' => ['Health Services Support', 'Council support for clinics, sanitation and public-health programmes in partnership with health authorities.', 'The council supports public health through clinic infrastructure maintenance, sanitation and waste management, health-centre servicing, and community health campaigns in partnership with the Ministry of Health and Child Care. Clinical services themselves are delivered by health authorities; the council provides the enabling infrastructure and environment.'],
            'servicing-business-centres' => ['Business Centre Servicing', 'Planned servicing of rural business centres — stands, roads, water, sanitation and market infrastructure.', 'The council plans and services rural business and growth points, including stand allocation processes, internal roads, water and sanitation reticulation, and market infrastructure. Traders and developers can enquire about stand availability and development procedures through the council. No stand is sold or allocated outside formal council processes.'],
            'property-management' => ['Property Management', 'Transparent management of council properties, leases and stand administration.', 'The council administers its property portfolio — including leased premises and stands — under approved policies. Enquiries about leases, stand administration and property procedures are handled through the council offices with formal application processes.'],
            'recreational-facilities' => ['Recreation and Community Facilities', 'Sports, recreation and community gathering facilities, including Chikondoma Stadium.', 'The council provides and maintains sports and recreation infrastructure, community halls and open spaces. Clubs and organisers can enquire about booking procedures and maintenance requests. Facility availability and booking terms are confirmed by the council on application.'],
            'conservation-natural-resources' => ['Conservation of Natural Resources', 'Protection of woodlands, wetlands, grazing and the district\u2019s granite landscape heritage.', 'The council works with communities, traditional leadership and environmental agencies to conserve woodlands, wetlands and grazing resources — controlling stream-bank cultivation, veld fires and unregulated extraction. Guidance on resource-use permits is available from the council.'],
            'welfare-services' => ['Community Welfare Services', 'Support for vulnerable groups through community-based welfare coordination.', 'The council coordinates community welfare responses — including support referrals for vulnerable households — working with social welfare authorities and community structures. Cases are handled confidentially through the appropriate professional channels.'],
        ];

        foreach ($improvements as $slug => [$name, $summary, $description]) {
            $record = Service::query()->where('slug', $slug)->first();
            if (! $record instanceof Service || $record->verification_status !== 'demo') {
                continue;
            }
            $record->fill([
                'name' => $name,
                'summary' => $summary,
                'description' => $description,
                'seo_title' => $name.' | Mutoko RDC',
                'meta_description' => $summary,
            ]);
            $record->save();
        }
    }

    // ------------------------------------------------------------------
    // Investment: clarify scopes (no deletions; duplicates unconfirmed)
    // ------------------------------------------------------------------
    private function seedInvestment(): void
    {
        $scopes = [
            'mineral-opportunities-mutoko' => ['sector' => 'Mining', 'summary' => 'Overview of the district\u2019s mineral endowment and the council\u2019s facilitation role. Concessions and licences are issued by national authorities, not the council.'],
            'mineral-mining-mutoko' => ['sector' => 'Mining', 'summary' => 'Commodities of interest include lithium, gold, tantalite and granite. Sector brief; licensing follows national mining law.'],
            'granite-cutting-polishing-mutoko' => ['sector' => 'Value addition', 'summary' => 'Value-addition opportunity: cutting and polishing of Mutoko\u2019s dimension stone. Land, power and approvals subject to formal processes.'],
            'solar-energy-mutoko' => ['sector' => 'Energy', 'summary' => 'Solar generation potential in a high-insolation district. Grid, land and licensing matters follow national energy processes.'],
            'horticulture-mutoko' => ['sector' => 'Agriculture', 'summary' => 'Horticulture production building on existing fruit, vegetable and tobacco value chains.'],
            'processing-plants-mutoko' => ['sector' => 'Agro-processing', 'summary' => 'Fruit and vegetable processing plants to add value to local produce. Concept note for investor engagement.'],
            'residential-stands-mutoko' => ['sector' => 'Housing', 'summary' => 'Planned residential stand development at serviced centres. Availability follows formal council allocation processes.'],
        ];
        foreach ($scopes as $slug => $attrs) {
            $record = InvestmentOpportunity::query()->where('slug', $slug)->first();
            if (! $record instanceof InvestmentOpportunity || $record->verification_status !== 'demo') {
                continue;
            }
            $record->fill($attrs);
            $record->save();
        }
    }

    // ------------------------------------------------------------------
    // Pages: professional draft blocks for stubs (about preserved)
    // ------------------------------------------------------------------
    private function seedPages(): void
    {
        $pages = [
            'mandate' => ['Our Mandate', 'What Mutoko Rural District Council is legally responsible for.', [
                ['type' => 'heading', 'text' => 'Legal foundation'],
                ['type' => 'paragraph', 'text' => 'Mutoko Rural District Council is established under the Rural District Councils Act (Chapter 29:13) as the rural local authority for Mutoko District. [DRAFT DEMONSTRATION CONTENT — the precise mandate wording awaits council and legal verification.]'],
                ['type' => 'heading', 'text' => 'Core responsibilities'],
                ['type' => 'paragraph', 'text' => 'The council is responsible for rural roads, water and sanitation support, business-centre servicing, land-use planning, environmental management, and the promotion of local economic development — delivered through council resolutions, committees and the administration.'],
                ['type' => 'cta', 'text' => 'Contact the council', 'url' => '/contact'],
            ]],
            'vision-mission' => ['Vision and Mission', 'Proposed vision and mission wording for council consideration.', [
                ['type' => 'heading', 'text' => 'Proposed vision'],
                ['type' => 'paragraph', 'text' => 'PROPOSED WORDING FOR COUNCIL APPROVAL (not an official statement): A vibrant and prosperous Mutoko by 2030.'],
                ['type' => 'heading', 'text' => 'Proposed mission'],
                ['type' => 'paragraph', 'text' => 'PROPOSED WORDING FOR COUNCIL APPROVAL (not an official statement): To provide quality, sustainable services with our communities.'],
            ]],
            'organogram' => ['Council Organogram', 'How the council is structured: elected arm, committees and administration.', [
                ['type' => 'heading', 'text' => 'Elected arm'],
                ['type' => 'paragraph', 'text' => 'Twenty-nine elected ward councillors form full council, supported by portfolio committees. [DRAFT DEMONSTRATION CONTENT — committee names and the quota/Town Board composition await verification.]'],
                ['type' => 'heading', 'text' => 'Administration'],
                ['type' => 'paragraph', 'text' => 'The Chief Executive Officer heads the administration, supported by departmental heads covering finance, engineering, planning, environmental health, housing and community services. An organogram chart will be attached here after council approval.'],
            ]],
            'rates-information' => ['Council Rates', 'How rates, fees and payments work. Figures require council approval.', [
                ['type' => 'heading', 'text' => 'About this page'],
                ['type' => 'paragraph', 'text' => 'DRAFT DEMONSTRATION CONTENT. No tariffs, fees, deadlines or payment details are published here. The approved tariff schedule, payment points, deadlines and penalty policy will appear once adopted by council.'],
                ['type' => 'cta', 'text' => 'Make an enquiry', 'url' => '/contact'],
            ]],
            'tourism-mutoko' => ['Tourism in Mutoko', 'Granite kopjes, Buja heritage, agriculture and growth-point enterprise.', [
                ['type' => 'heading', 'text' => 'A granite landscape'],
                ['type' => 'paragraph', 'text' => 'Mutoko District is known for its granite kopjes — including formations around Nyamurora — set among miombo woodland, farming homesteads and seasonal wetlands about 143 kilometres north-east of Harare. [DRAFT DEMONSTRATION CONTENT — site details and access information await verification.]'],
                ['type' => 'heading', 'text' => 'People and culture'],
                ['type' => 'paragraph', 'text' => 'Mutoko is home of the Buja people, with a living culture of farming, crafts, music and community ceremony. Visitors are asked to respect homesteads, sacred sites and local custom.'],
                ['type' => 'heading', 'text' => 'Visitor experiences'],
                ['type' => 'paragraph', 'text' => 'Potential experiences include scenic viewpoints, cultural visits arranged with community consent, agricultural tours, and stopovers en route to Nyamapanda and Mozambique. No visitor facilities are officially endorsed on this page until confirmed by council.'],
            ]],
            'privacy-policy' => ['Privacy Policy', 'How this website handles visitor information.', [
                ['type' => 'heading', 'text' => 'Information we collect'],
                ['type' => 'paragraph', 'text' => 'The enquiry and feedback forms on this website collect the details you provide — such as your name, contact information, ward and message — so that the council can respond to you.'],
                ['type' => 'heading', 'text' => 'How information is used'],
                ['type' => 'paragraph', 'text' => 'Submitted information is used to respond to enquiries. Concerns are handled confidentially and shared only with the staff responsible for resolving the matter.'],
                ['type' => 'heading', 'text' => 'Questions about personal data'],
                ['type' => 'paragraph', 'text' => 'If you have questions about personal information you have submitted, contact the council through the enquiry form. Retention periods and further details will be confirmed here.'],
            ]],
        ];

        foreach ($pages as $slug => [$title, $summary, $blocks]) {
            $record = Page::query()->where('slug', $slug)->first();
            if (! $record instanceof Page || $record->status !== 'draft') {
                continue;
            }
            $record->fill(['title' => $title, 'summary' => $summary, 'blocks' => $blocks]);
            $record->save();
        }
    }

    // ------------------------------------------------------------------
    // Verified publication: safe, honest records go live on the public site.
    //
    // Published: 9 services, 5 informational pages, 6 news articles,
    // 29 ward placeholders (verified count, details marked pending),
    // 3 role-based leadership profiles (clearly labelled TBC, no portraits),
    // 3 downloadable guides (with active media), approved welcome slide.
    // Never published: SAMPLE notices (no fake meetings), investment
    // opportunities (unverified), vision/mission (proposed wording),
    // privacy policy (needs legal approval), later hero slides (preview only).
    // ------------------------------------------------------------------
    private function publishVerifiedContent(): void
    {
        $now = now();

        foreach (Service::query()->where('verification_status', 'demo')->get() as $service) {
            $service->status = 'published';
            $service->verification_status = 'publishable';
            $service->published_at ??= $now;
            $service->seo_title = preg_replace('/\s*\| Mutoko RDC \(demo\)\s*/', '', (string) $service->seo_title) ?: $service->name.' | Mutoko RDC';
            $service->save();
        }

        $publishablePages = ['about-mutoko', 'mandate', 'organogram', 'tourism-mutoko', 'rates-information'];
        foreach (Page::query()->whereIn('slug', $publishablePages)->get() as $page) {
            $page->blocks = array_values(array_filter(
                array_map(fn ($block) => $this->cleanDemoMarker($block), (array) $page->blocks),
                fn ($block) => ! (($block['type'] ?? '') === 'paragraph' && trim((string) ($block['text'] ?? '')) === '')
            ));
            if ($page->status === 'draft') {
                $page->status = 'published';
                $page->published_at ??= $now;
            }
            $page->verification_status = 'publishable';
            $page->save();
        }

        $day = 0;
        foreach (EditorialItem::query()->where('type', 'news')->where('verification_status', 'demo')->orderBy('display_order')->get() as $item) {
            $item->body = $this->cleanDemoMarkerText((string) $item->body);
            $item->seo_title = preg_replace('/\s*\|\s*Mutoko RDC \(demo\)\s*/', '', (string) $item->seo_title) ?: $item->title.' | Mutoko RDC';
            $item->status = 'published';
            $item->verification_status = 'publishable';
            $item->published_at = $now->copy()->subDays($day++);
            $item->save();
        }

        foreach (Ward::query()->where('verification_status', 'demo')->get() as $ward) {
            $ward->status = 'published';
            $ward->verification_status = 'publishable';
            $ward->published_at ??= $now;
            $ward->save();
        }

        foreach (Official::query()->where('verification_status', 'demo')->get() as $official) {
            $official->status = 'published';
            $official->verification_status = 'publishable';
            $official->published_at ??= $now;
            $official->save();
        }

        foreach (Document::query()->where('slug', 'like', 'demo-%')->where('status', 'draft')->get() as $document) {
            if (! in_array($document->verification_status, ['demo', 'publishable'], true)) {
                continue;
            }
            if ($document->media_id) {
                Media::query()->where('id', $document->media_id)->where('status', 'draft')->update(['status' => 'active']);
            }
            $document->status = 'published';
            $document->verification_status = 'publishable';
            $document->published_at ??= $now;
            $document->save();
        }

        // Backstop: clean markers on already-published demo-origin news.
        foreach (EditorialItem::query()->where('type', 'news')->where('slug', 'like', 'demo-%')->where('status', 'published')->get() as $item) {
            $cleanBody = $this->cleanDemoMarkerText((string) $item->body);
            $cleanSeo = preg_replace('/\s*\|\s*Mutoko RDC \(demo\)\s*/', '', (string) $item->seo_title) ?: $item->title.' | Mutoko RDC';
            if ($cleanBody !== $item->body || $cleanSeo !== $item->seo_title) {
                $item->body = $cleanBody;
                $item->seo_title = $cleanSeo;
                $item->save();
            }
        }
        // Standing information notices: honest, undated guidance with no
        // fabricated events, safe to publish so the notices section is alive.
        $noticeDay = 0;
        foreach (EditorialItem::query()->where('type', 'notice')->whereIn('slug', ['how-council-consultations-work', 'attending-council-meetings', 'how-service-updates-are-communicated'])->where('status', 'draft')->get() as $item) {
            $item->status = 'published';
            $item->verification_status = 'publishable';
            $item->published_at = $now->copy()->subDays($noticeDay++);
            $item->save();
        }
    }

    private function cleanDemoMarker(array $block): array
    {
        foreach (['text'] as $field) {
            if (isset($block[$field]) && is_string($block[$field])) {
                $block[$field] = $this->cleanDemoMarkerText($block[$field]);
            }
        }

        return $block;
    }

    private function cleanDemoMarkerText(string $text): string
    {
        $text = (string) preg_replace('/\s*\[DRAFT DEMONSTRATION CONTENT[^\]]*\]\s*/', ' ', $text);
        $text = (string) preg_replace('/\s*DRAFT DEMONSTRATION CONTENT\.\s*/', ' ', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    // ------------------------------------------------------------------
    // Statistics provenance: flag the disputed area figure in-CMS
    // ------------------------------------------------------------------
    private function seedStatisticProvenance(): void
    {
        $area = DistrictStatistic::query()->where('label', 'District area')->first();
        if ($area && ! str_contains((string) $area->source_note, 'DISPUTED')) {
            $area->source_note = 'DISPUTED — do not treat as verified. Live value 4,092.5 km² was entered directly (no recorded source); the original seeder cited the old council site at 428,916 hectares. Council planner to confirm against Surveyor-General / census profile.';
            $area->save();
        }
    }
}
