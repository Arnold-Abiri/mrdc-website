<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Incidents\IncidentResource;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use App\Filament\Resources\Officials\OfficialResource;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Tenders\TenderResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Enquiry;
use App\Models\Tender;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemGuide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'System Walkthrough';

    protected static \UnitEnum|string|null $navigationGroup = null;

    protected static ?string $title = 'System Walkthrough';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.system-guide';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'workflows' => self::getWorkflowCatalog(),
            'liveStatus' => $this->getLiveStatusSummary(),
            'defaultCategory' => self::getDefaultCategoryForUser(),
        ];
    }

    /**
     * Determine recommended initial workflow category for the current user.
     */
    public static function getDefaultCategoryForUser(): string
    {
        $user = auth()->user();
        if (! $user) {
            return 'all';
        }

        if ($user->hasRole('Auditor')) {
            return 'admin';
        }

        if ($user->hasRole('Department Content Owner') || $user->can('editorial.create')) {
            return 'content';
        }

        if ($user->can('tenders.view') && ! $user->can('users.view')) {
            return 'tenders';
        }

        if ($user->can('enquiries.view') && ! $user->can('users.view')) {
            return 'enquiries';
        }

        return 'all';
    }

    /**
     * Operational summary across active council workflows.
     *
     * @return array{heading: string, description: string, stats: array<string, int>, items: array<int, array<string, mixed>>}
     */
    public function getLiveStatusSummary(): array
    {
        $newEnquiries = EnquiryResource::canViewAny()
            ? Enquiry::where('status', 'new')->count()
            : 0;

        $activeTenders = TenderResource::canViewAny()
            ? Tender::where('status', 'published')
                ->whereNotIn('lifecycle_status', ['closed', 'cancelled', 'awarded'])
                ->count()
            : 0;

        $activeProjects = CouncilProjectResource::canViewAny()
            ? CouncilProject::where('status', 'published')
                ->whereNotIn('project_status', ['completed', 'cancelled'])
                ->count()
            : 0;

        $upcomingMeetings = CouncilMeetingResource::canViewAny()
            ? CouncilMeeting::whereDate('scheduled_date', '>=', today())
                ->where('meeting_status', 'scheduled')
                ->count()
            : 0;

        $items = [];

        // Latest open enquiries
        if (EnquiryResource::canViewAny()) {
            Enquiry::latest('submitted_at')
                ->limit(2)
                ->get()
                ->each(function (Enquiry $e) use (&$items) {
                    $items[] = [
                        'title' => $e->subject ?: 'Citizen Enquiry',
                        'meta' => 'From: '.($e->name ?: 'Anonymous').' • '.($e->submitted_at?->diffForHumans() ?? 'recently'),
                        'module' => 'Public Enquiries',
                        'status' => ucfirst($e->status ?? 'new'),
                        'status_color' => $e->status === 'new' ? 'amber' : ($e->status === 'resolved' ? 'emerald' : 'blue'),
                        'url' => EnquiryResource::getUrl('view', ['record' => $e]),
                    ];
                });
        }

        // Active tenders closing soon
        if (TenderResource::canViewAny()) {
            Tender::where('status', 'published')
                ->where('closes_at', '>=', now())
                ->orderBy('closes_at')
                ->limit(2)
                ->get()
                ->each(function (Tender $t) use (&$items) {
                    $items[] = [
                        'title' => $t->title,
                        'meta' => 'Ref: '.$t->reference.' • Closes: '.($t->closes_at?->format('d M Y') ?? 'N/A'),
                        'module' => 'Tenders',
                        'status' => ucfirst($t->lifecycle_status ?? 'open'),
                        'status_color' => 'emerald',
                        'url' => TenderResource::getUrl('edit', ['record' => $t]),
                    ];
                });
        }

        return [
            'heading' => 'Council Live Operational Pipeline',
            'description' => 'Current live status across critical council operational workflows requiring administrative attention or action.',
            'stats' => [
                'New Enquiries' => $newEnquiries,
                'Active Tenders' => $activeTenders,
                'Ongoing Projects' => $activeProjects,
                'Upcoming Meetings' => $upcomingMeetings,
            ],
            'items' => $items,
        ];
    }

    /**
     * Get the complete catalog of council procedures and workflows.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getWorkflowCatalog(): array
    {
        return [
            [
                'id' => 'enquiries',
                'category' => 'Citizen Services & Enquiries',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'color' => 'emerald',
                'description' => 'Receive, triage, assign, and respond to public inquiries and reported incidents.',
                'tasks' => [
                    [
                        'title' => 'Triage & Respond to Citizen Enquiries',
                        'role' => 'Public Relations / Customer Care',
                        'time' => '5 mins',
                        'keywords' => 'enquiry inquiry citizen message grievance complaint help support contact',
                        'destination' => EnquiryResource::canViewAny() ? EnquiryResource::getUrl() : null,
                        'destination_label' => 'View Public Enquiries',
                        'steps' => [
                            'Go to Enquiries > Public Enquiries in the sidebar.',
                            'Click on any enquiry with status "New" to inspect citizen details and their message.',
                            'Update the status to "Under Review" or "Assigned" and allocate to the responsible department head.',
                            'Record Council findings or reply directly using the internal notes and response form.',
                            'Once resolved, mark status as "Resolved" to close the public tracking ticket.',
                        ],
                        'tips' => 'Respond to urgent water, road, or health enquiries within 24–48 hours to maintain council service level benchmarks.',
                    ],
                    [
                        'title' => 'Log and Track Reported Incidents & Hazards',
                        'role' => 'Engineering & Works Dispatch',
                        'time' => '5 mins',
                        'keywords' => 'incident emergency hazard breakdown disaster road damage report',
                        'destination' => IncidentResource::canViewAny() ? IncidentResource::getUrl() : null,
                        'destination_label' => 'View Reported Incidents',
                        'steps' => [
                            'Open Enquiries > Incidents.',
                            'Review citizen-reported hazards (e.g. fallen trees, water burst, road collapse).',
                            'Assess severity (Low, Medium, Critical) and forward dispatch notices to Works & Engineering.',
                            'Update status to "In Progress" with photos or crew notes, and mark "Resolved" when cleared.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'tenders',
                'category' => 'Tenders, Procurement & Vacancies',
                'icon' => 'heroicon-o-briefcase',
                'color' => 'amber',
                'description' => 'Publish procurement notices, monitor compliance windows, and post employment vacancies.',
                'tasks' => [
                    [
                        'title' => 'Publish a New Public Tender',
                        'role' => 'Procurement Management Unit (PMU)',
                        'time' => '10 mins',
                        'keywords' => 'tender procurement bid rfp contract expression of interest supplier vendor',
                        'destination' => TenderResource::canCreate() ? TenderResource::getUrl('create') : null,
                        'destination_label' => '+ Create New Tender',
                        'steps' => [
                            'Navigate to Services > Tenders & Procurement.',
                            'Click "New Tender" and fill in Tender Reference Number, Title, and Category (Goods, Works, Consultancy).',
                            'Specify Closing Date and Time accurately — the portal automatically marks tenders closed once passed.',
                            'Upload standard bidding documents (SBD), RFP files, and fee requirements.',
                            'Set status to "Published" so it appears on the public website and investor portal.',
                        ],
                        'tips' => 'Always verify PRAZ compliance and mandatory statutory deadlines before publishing tender notices.',
                    ],
                    [
                        'title' => 'Check Tender Compliance & Closing Dates',
                        'role' => 'PMU / Internal Audit',
                        'time' => '3 mins',
                        'keywords' => 'tender compliance audit praz deadline closing status',
                        'destination' => class_exists(TenderCompliance::class) ? TenderCompliance::getUrl() : null,
                        'destination_label' => 'Tender Compliance Dashboard',
                        'steps' => [
                            'Open Services > Tender Compliance page.',
                            'Inspect active tenders nearing closing dates, open clarifications, or missing addenda.',
                            'Issue addenda or time extensions if required before the formal bid opening deadline.',
                        ],
                    ],
                    [
                        'title' => 'Advertise Council Job Vacancies',
                        'role' => 'Human Resources Department',
                        'time' => '8 mins',
                        'keywords' => 'job vacancy employment recruitment hiring career staff',
                        'destination' => VacancyResource::canCreate() ? VacancyResource::getUrl('create') : null,
                        'destination_label' => '+ Post New Vacancy',
                        'steps' => [
                            'Navigate to Services > Vacancies.',
                            'Click "New Vacancy" and input Job Title, Department, Grade/Level, and Responsibilities.',
                            'Set application deadline and submission instructions (email or physical office delivery).',
                            'Publish the listing so it is displayed in the Council Careers section.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'council',
                'category' => 'Council Governance, Meetings & Officials',
                'icon' => 'heroicon-o-building-office-2',
                'color' => 'blue',
                'description' => 'Manage full council and committee meetings, publish agendas/minutes, and maintain official rosters.',
                'tasks' => [
                    [
                        'title' => 'Schedule a Council / Committee Meeting',
                        'role' => 'Administration / Committee Clerk',
                        'time' => '7 mins',
                        'keywords' => 'council meeting committee schedule minutes agenda full council resolution',
                        'destination' => CouncilMeetingResource::canCreate() ? CouncilMeetingResource::getUrl('create') : null,
                        'destination_label' => '+ Schedule Council Meeting',
                        'steps' => [
                            'Go to Council > Council Meetings.',
                            'Click "New Council Meeting", choose meeting type (Full Council, Finance Committee, Works Committee, Special).',
                            'Set scheduled date, time, venue (Council Chamber or Ward Centre).',
                            'Attach the meeting Order of Proceedings / Agenda in PDF format.',
                            'After the meeting has concluded, re-open this record to upload signed Council Minutes and Resolutions.',
                        ],
                    ],
                    [
                        'title' => 'Update Councillors, Wards & Council Officials',
                        'role' => 'Administration & Executive Secretariat',
                        'time' => '5 mins',
                        'keywords' => 'councillor ward official ceo chairman chairperson management executive director',
                        'destination' => OfficialResource::canViewAny() ? OfficialResource::getUrl() : null,
                        'destination_label' => 'Manage Officials & Councillors',
                        'steps' => [
                            'Navigate to Council > Officials.',
                            'Add or update Council Chairperson, Chief Executive Officer (CEO), Heads of Departments, or Ward Councillors.',
                            'Assign each official their designated Ward, portfolio, photo, and official council contact email.',
                            'Ensure Ward details in Council > Wards remain synchronized with boundaries and development priorities.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'projects',
                'category' => 'Development Projects & Services',
                'icon' => 'heroicon-o-presentation-chart-line',
                'color' => 'indigo',
                'description' => 'Track capital infrastructure projects, clinics, schools, roads, devolution funds, and council rates.',
                'tasks' => [
                    [
                        'title' => 'Create or Update a Council Project',
                        'role' => 'Planning & Works Department',
                        'time' => '10 mins',
                        'keywords' => 'project development devolution infrastructure road clinic school water borehole contractor',
                        'destination' => CouncilProjectResource::canCreate() ? CouncilProjectResource::getUrl('create') : null,
                        'destination_label' => '+ Add Development Project',
                        'steps' => [
                            'Go to Council > Council Projects.',
                            'Click "New Council Project" and enter project name, ward, contractor, and funding source (e.g. Devolution, Zinara, Council Revenue).',
                            'Specify starting date, expected completion date, and budget allocation.',
                            'Update completion percentage (e.g. 25%, 50%, 100%) and stage (Planning, Construction, Commissioned).',
                            'Upload inspection photographs to keep council stakeholders and citizens informed.',
                        ],
                    ],
                    [
                        'title' => 'Manage Council Services & Payment Accounts',
                        'role' => 'Treasury / Finance Department',
                        'time' => '5 mins',
                        'keywords' => 'service council rates payment bank account licenses permits ecocash zipit',
                        'destination' => ServiceResource::canViewAny() ? ServiceResource::getUrl() : null,
                        'destination_label' => 'View Council Services',
                        'steps' => [
                            'Open Services > Services to update requirements for business licensing, development permits, or water connections.',
                            'Go to Services > Bank Accounts to ensure council banking details, Ecocash biller codes, and ZIPIT numbers shown to ratepayers are up to date.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'content',
                'category' => 'Website Content, News & Documents',
                'icon' => 'heroicon-o-document-text',
                'color' => 'teal',
                'description' => 'Publish press releases, speeches, downloadable bylaws, public notices, and photo galleries.',
                'tasks' => [
                    [
                        'title' => 'Publish News, Notices or Speeches',
                        'role' => 'Corporate Communications & Editors',
                        'time' => '8 mins',
                        'keywords' => 'news editorial article notice speech press release announcement publication',
                        'destination' => EditorialItemResource::canCreate() ? EditorialItemResource::getUrl('create') : null,
                        'destination_label' => '+ Draft Editorial Item',
                        'steps' => [
                            'Navigate to Content > Editorial Items.',
                            'Select "+ Create" and pick type: "News", "Public Notice", or "Speeches".',
                            'Enter title, excerpt, and full body content using the rich text editor.',
                            'Attach high-resolution featured photos and set publishing schedule.',
                            'Save as "Draft" for review or "Published" for immediate live deployment to the public site.',
                        ],
                    ],
                    [
                        'title' => 'Upload Public Documents, Bylaws & Budgets',
                        'role' => 'Legal & Finance / Secretariat',
                        'time' => '5 mins',
                        'keywords' => 'document download pdf bylaw budget financial statement annual report strategic plan',
                        'destination' => DocumentResource::canCreate() ? DocumentResource::getUrl('create') : null,
                        'destination_label' => '+ Upload Document',
                        'steps' => [
                            'Go to Content > Documents.',
                            'Click "New Document", input title, publication year, and category (e.g. Bylaws, Budgets, Strategic Plans, Tariffs).',
                            'Upload the PDF file and ensure the "Status" is set to "Published".',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'admin',
                'category' => 'System Administration & Security',
                'icon' => 'heroicon-o-shield-check',
                'color' => 'slate',
                'description' => 'User account management, role-based access control (Shield), audit trails, and system health.',
                'tasks' => [
                    [
                        'title' => 'Add Council Staff User & Assign Roles',
                        'role' => 'IT Administrator / Super Admin',
                        'time' => '4 mins',
                        'keywords' => 'user staff admin password account role permission shield security access',
                        'destination' => UserResource::canCreate() ? UserResource::getUrl('create') : null,
                        'destination_label' => '+ Add New User',
                        'steps' => [
                            'Navigate to Administration > Users.',
                            'Click "New User", enter staff name, council email, and temporary password.',
                            'Assign roles (e.g. "super_admin", "editor", "finance_officer", "enquiry_officer").',
                            'Roles control which tabs and action buttons staff can view or modify.',
                        ],
                    ],
                    [
                        'title' => 'Inspect Audit Trails & Error Logs',
                        'role' => 'IT Admin / Internal Auditor',
                        'time' => '3 mins',
                        'keywords' => 'audit log history activity security who changed error system health diagnostics',
                        'destination' => AuditReport::canAccess() ? AuditReport::getUrl() : null,
                        'destination_label' => 'View Audit Reports',
                        'steps' => [
                            'Open Reports > Audit Report or Reports > Audit Events.',
                            'Review timestamped logs of record creations, edits, and deletions for accountability.',
                            'Open Reports > System Health to verify database connectivity, cache state, and email operations.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
