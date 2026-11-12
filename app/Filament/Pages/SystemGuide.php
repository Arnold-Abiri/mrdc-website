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
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemGuide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'System Walkthrough';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'System Walkthrough';

    protected string $view = 'filament.pages.system-guide';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'workflows' => self::getWorkflowCatalog(),
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

        if ($user->can('users.view')) {
            return 'all';
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
                'description' => 'Respond to citizen enquiries and record service interruptions.',
                'tasks' => [
                    [
                        'title' => 'Respond to an enquiry',
                        'role' => 'Public Relations / Customer Care',
                        'time' => '5 mins',
                        'keywords' => 'enquiry inquiry citizen message grievance complaint help support contact',
                        'destination' => EnquiryResource::canViewAny() ? EnquiryResource::getUrl() : null,
                        'destination_label' => 'View Public Enquiries',
                        'steps' => [
                            'Open Public Enquiries and select an enquiry to read the message and its current status.',
                            'Use Route to department, then Assign staff if those actions are available to you.',
                            'Use Change status > Mark in progress when work begins. Add internal note records staff findings without sending a reply.',
                            'Use Send Update to Citizen to review and send an email containing your message, the reference number, and current status. Review any AI draft before sending.',
                            'Use Change status > Resolve when the issue is addressed, or Close when no further work is required. Only valid status changes are offered.',
                        ],
                        'tips' => 'Internal notes stay with the staff record. Use Send Update to Citizen for an email reply.',
                    ],
                    [
                        'title' => 'Record a service interruption',
                        'role' => 'Service administrator',
                        'time' => '5 mins',
                        'keywords' => 'incident interruption outage recovery monitoring service status',
                        'destination' => IncidentResource::canViewAny() ? IncidentResource::getUrl() : null,
                        'destination_label' => 'View Service Incidents',
                        'steps' => [
                            'Open Incidents to record an interruption affecting the website or its supporting services.',
                            'Enter Started at and select the source: Manual, Health check, or External monitor.',
                            'Use Open while investigating, Monitoring while checking recovery, and Resolved when the interruption has ended.',
                            'Add a brief summary of the affected service and action taken. Enter Recovered at on or after Started at when service returns, then save.',
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
                        'title' => 'Publish a tender',
                        'role' => 'Procurement Management Unit (PMU)',
                        'time' => '10 mins',
                        'keywords' => 'tender procurement bid rfp contract expression of interest supplier vendor',
                        'destination' => TenderResource::canCreate() ? TenderResource::getUrl('create') : null,
                        'destination_label' => '+ Create New Tender',
                        'steps' => [
                            'Open Tenders and create a record with the reference, title, category, and description.',
                            'Select the lifecycle status and enter opening and closing dates. The public closing status is derived from these dates.',
                            'Enter submission instructions and select the tender document. Upload the PDF through Documents first if needed.',
                            'Save the draft, review it, then use the authorised Verify and Publish actions. Ask an authorised reviewer if those actions are unavailable.',
                            'Publishing makes the approved notice available to citizens; confirm the public details and document are correct.',
                        ],
                        'tips' => 'Confirm the approved submission instructions and closing date with the procurement team before publishing.',
                    ],
                    [
                        'title' => 'Review tender deadlines',
                        'role' => 'PMU / Internal Audit',
                        'time' => '3 mins',
                        'keywords' => 'tender compliance audit praz deadline closing status',
                        'destination' => TenderCompliance::canAccess() ? TenderCompliance::getUrl() : null,
                        'destination_label' => 'Tender Compliance Dashboard',
                        'steps' => [
                            'Open Tender compliance to review the counts of open, closed, awarded, cancelled, and overdue tenders.',
                            'Open Tenders to inspect the relevant record, closing date, and lifecycle status.',
                            'Use the permitted edit or Record award action to record approved changes. Saving a date change updates how the public closing status is calculated.',
                        ],
                    ],
                    [
                        'title' => 'Publish a vacancy',
                        'role' => 'Human Resources Department',
                        'time' => '8 mins',
                        'keywords' => 'job vacancy employment recruitment hiring career staff',
                        'destination' => VacancyResource::canCreate() ? VacancyResource::getUrl('create') : null,
                        'destination_label' => '+ Post New Vacancy',
                        'steps' => [
                            'Open Vacancies and create a record with the title, grade, reference, employment type, and department as applicable.',
                            'Enter the description, responsibilities, requirements, opening and closing dates, and application instructions.',
                            'Select the vacancy document if required, then save and review the draft.',
                            'Use the authorised publishing action after review. The public listing treats past closing dates as closed.',
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
                        'title' => 'Schedule a council meeting',
                        'role' => 'Administration / Committee Clerk',
                        'time' => '7 mins',
                        'keywords' => 'council meeting committee schedule minutes agenda full council resolution',
                        'destination' => CouncilMeetingResource::canCreate() ? CouncilMeetingResource::getUrl('create') : null,
                        'destination_label' => '+ Schedule Council Meeting',
                        'steps' => [
                            'Open Council Meetings and create a record with a title and meeting type: Full council, Committee, Special, or Public hearing.',
                            'Enter the scheduled date, time, venue, department, and meeting status.',
                            'Select the agenda document. Use Documents to upload it first if necessary, then save.',
                            'After the meeting, edit the record to select the minutes document and update the meeting status.',
                            'Use the authorised Verify and Publish actions for public release. Saving a draft alone does not publish it.',
                        ],
                    ],
                    [
                        'title' => 'Update council officials',
                        'role' => 'Administration & Executive Secretariat',
                        'time' => '5 mins',
                        'keywords' => 'councillor ward official ceo chairman chairperson management executive director',
                        'destination' => OfficialResource::canViewAny() ? OfficialResource::getUrl() : null,
                        'destination_label' => 'Manage Officials & Councillors',
                        'steps' => [
                            'Open Officials and create or edit the appropriate profile.',
                            'Enter the name, Position / Role Title, category, department, biography, and display order.',
                            'Upload a portrait or choose an image from the library, and provide image alt text.',
                            'Save and review the profile. An authorised reviewer uses Verify and Publish to release it publicly; manage ward records separately in Wards.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'projects',
                'category' => 'Development Projects & Services',
                'icon' => 'heroicon-o-presentation-chart-line',
                'color' => 'indigo',
                'description' => 'Update project progress, council services, and approved payment information.',
                'tasks' => [
                    [
                        'title' => 'Update a development project',
                        'role' => 'Planning & Works Department',
                        'time' => '10 mins',
                        'keywords' => 'project development devolution infrastructure road clinic school water borehole contractor',
                        'destination' => CouncilProjectResource::canCreate() ? CouncilProjectResource::getUrl('create') : null,
                        'destination_label' => '+ Add Development Project',
                        'steps' => [
                            'Open Projects and create a record or edit an existing project.',
                            'Enter the title, project type, location, department, covered wards, summary, and description.',
                            'Select Planned, Ongoing, Completed, On Hold, or Cancelled. Record dates and a confirmed progress percentage from 0 to 100; leave progress empty when unconfirmed.',
                            'Add a cover image with alt text and select associated documents, then save.',
                            'Use the authorised Verify and Publish actions for public release. Confirm the public project information after publishing.',
                        ],
                    ],
                    [
                        'title' => 'Update services and payment information',
                        'role' => 'Treasury / Finance Department',
                        'time' => '5 mins',
                        'keywords' => 'service council rates payment bank account licenses permits ecocash zipit',
                        'destination' => ServiceResource::canViewAny() ? ServiceResource::getUrl() : null,
                        'destination_label' => 'View Council Services',
                        'steps' => [
                            'Open Services to edit the description and requirements for a council service, then save and arrange authorised review and publication.',
                            'Open Bank Accounts separately to update approved council payment information. Confirm the details before saving because they are used for public payment guidance.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'content',
                'category' => 'Website Content, News & Documents',
                'icon' => 'heroicon-o-document-text',
                'color' => 'teal',
                'description' => 'Publish news, notices, and approved public documents.',
                'tasks' => [
                    [
                        'title' => 'Publish news or a notice',
                        'role' => 'Corporate Communications & Editors',
                        'time' => '8 mins',
                        'keywords' => 'news editorial article notice speech press release announcement publication',
                        'destination' => EditorialItemResource::canCreate() ? EditorialItemResource::getUrl('create') : null,
                        'destination_label' => '+ Draft Editorial Item',
                        'steps' => [
                            'Open News and notices and create a record. Select News or Notice as the type.',
                            'Enter the title, summary, body, and classification. Add the relevant image and alt text.',
                            'Save the draft and review its content. Saving alone does not publish it.',
                            'An authorised reviewer uses Mark publishable, then Publish. If those actions are missing, ask a staff member with verification and publishing permission.',
                            'Check the public article after publishing. Use Unpublish when it should no longer be public.',
                        ],
                    ],
                    [
                        'title' => 'Upload a public document',
                        'role' => 'Legal & Finance / Secretariat',
                        'time' => '5 mins',
                        'keywords' => 'document download pdf bylaw budget financial statement annual report strategic plan',
                        'destination' => DocumentResource::canCreate() ? DocumentResource::getUrl('create') : null,
                        'destination_label' => '+ Upload Document',
                        'steps' => [
                            'Open Documents and create a record with the title, description, category, department, and reference date as appropriate.',
                            'Select Public visibility for a public download; Private documents are not intended for public release.',
                            'Upload a PDF or select an existing PDF from the library, then save and review the file.',
                            'Use the authorised Verify and Publish actions to release the approved public document. Confirm the public download works.',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'admin',
                'category' => 'System Administration & Security',
                'icon' => 'heroicon-o-shield-check',
                'color' => 'slate',
                'description' => 'Manage staff access, review administrative activity, and inspect service health.',
                'tasks' => [
                    [
                        'title' => 'Give a staff member access',
                        'role' => 'IT Administrator / Super Admin',
                        'time' => '4 mins',
                        'keywords' => 'user staff admin password account role permission shield security access',
                        'destination' => UserResource::canCreate() ? UserResource::getUrl('create') : null,
                        'destination_label' => '+ Add New User',
                        'steps' => [
                            'Open Users and create a record with the staff name, email, and department.',
                            'Saving requests an emailed password setup link. The staff member sets their own password; check any mail error before retrying.',
                            'From the Users list, use Assign role if permitted. Select an existing role appropriate to the staff member, such as Department Content Owner, Website Administrator, or Auditor.',
                            'Select Global for all permitted records, Department for the selected department, or Own for records owned by the user. Assign only the access required.',
                            'Confirm the role and scope were saved. Account creation alone does not grant a role; ask a System Administrator if Assign role is unavailable.',
                        ],
                    ],
                    [
                        'title' => 'Review audit history and system health',
                        'role' => 'IT Admin / Internal Auditor',
                        'time' => '3 mins',
                        'keywords' => 'audit log history activity security who changed error system health diagnostics',
                        'destination' => AuditReport::canAccess() ? AuditReport::getUrl() : null,
                        'destination_label' => 'View Audit Reports',
                        'steps' => [
                            'Open Audit Report or Audit Events to review recorded administrative activity, using the available filters.',
                            'Inspect who performed an action, when it occurred, and which record it affected.',
                            'If permitted, open System Health to inspect service check results. Report a failed check to the responsible administrator with the time and affected service.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
