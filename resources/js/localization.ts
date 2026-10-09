export type PublicLocale = 'en' | 'sn' | 'nd';

const english = {
    home: 'Home', about: 'About', council: 'Council', officials: 'Officials', wards: 'Wards',
    services: 'Services', documents: 'Documents', news: 'News', notices: 'Notices',
    search: 'Search', contact: 'Contact', skip: 'Skip to main content',
    language: 'Language', english: 'English', shona: 'Shona', ndebele: 'Ndebele',
    developmentNotice: 'Development preview — displayed council information and imagery are unverified.',
    setLanguage: 'Set language', socialPreviews: 'Social media previews', councilHome: 'Mutoko Rural District Council home',
    brandingAlt: 'Development branding mark for Mutoko Rural District Council', councilName: 'Rural District Council',
    tagline: 'Service Delivery for Sustainable Communities', closeMenu: 'Close menu', menu: 'Menu', primaryNavigation: 'Primary navigation',
    footerAbout: 'Working with our communities to deliver quality services, promote local development and build a better Mutoko.',
    quickLinks: 'Quick Links', ourServices: 'Our Services', development: 'Development', tourism: 'Tourism', media: 'Media', contactUs: 'Contact Us',
    waterSupply: 'Water Supply', roadsInfrastructure: 'Roads & Infrastructure', healthSanitation: 'Health & Sanitation',
    environmentalManagement: 'Environmental Management', developmentPlanning: 'Development Planning', communityServices: 'Community Services', opportunities: 'Opportunities',
    previewCopyright: 'Mutoko Rural District Council · Service Delivery for Sustainable Communities', privacyPolicy: 'Privacy Policy', termsOfUse: 'Terms of Use', siteMap: 'Site Map',
    searchCouncilPages: 'Search council pages', noApprovedResults: 'No approved results found.', results: 'result(s)', reviewContent: 'Development review content',
    contactCouncil: 'Contact the council', enquiryReceived: 'Your enquiry was received. Thank you.',
    enquiryHelp: 'Use this form for a general enquiry. Do not include sensitive personal information.', contactDetails: 'Council contact details',
    name: 'Name', email: 'Email', phoneOptional: 'Phone (optional)', category: 'Category', general: 'General', feedback: 'Feedback', other: 'Other',
    subject: 'Subject', message: 'Message', website: 'Website', sendEnquiry: 'Send enquiry',
    meetings: 'Council meetings', meetingSchedule: 'Full council meeting schedule', meetingScheduleDesc: 'Scheduled council meetings with published agendas and minutes.',
    noMeetings: 'No meetings are published at this time.', meetingType: 'Meeting type', scheduledDate: 'Date', time: 'Time', venue: 'Venue', meetingStatus: 'Status',
    agenda: 'Agenda', minutes: 'Minutes', agendaPending: 'The agenda has not been published yet.', minutesPending: 'Minutes have not been published yet.',
    scheduled: 'Scheduled', completed: 'Completed', postponed: 'Postponed', cancelled: 'Cancelled',
    fullCouncil: 'Full council', committee: 'Committee', special: 'Special', publicHearing: 'Public hearing',
    transparency: 'Financial transparency', financialTransparencyDesc: 'Approved council budgets, audited statements, financial reports, procurement plans, and the awarded tenders register.',
    noFinancialDocs: 'No financial publications are available yet.', rates: 'Rates', ratesDesc: 'Council-approved rate categories, billing periods, and payment guidance.',
    ratesPagePending: 'The approved rates schedule is being prepared for publication.', rateSchedules: 'Rate schedules and guidance',
    year: 'Year', department: 'Department', keyword: 'Keyword', filter: 'Filter', clearFilters: 'Clear filters',
    noDocuments: 'No documents match the selected filters.', referenceYear: 'Reporting year', downloadCount: 'downloads', downloadFile: 'Download file',
    urgentNotice: 'Important notice', version: 'Version',
    projects: 'Projects and programmes', projectsDesc: 'Council projects and development programmes across Mutoko district.',
    noProjects: 'No projects are published at this time.', projectStatus: 'Status', projectType: 'Type',
    planned: 'Planned', ongoing: 'Ongoing', onHold: 'On hold', programme: 'Programme', project: 'Project',
    location: 'Location', timeline: 'Timeline', startsAt: 'Start', expectedCompletion: 'Expected completion', completedAt: 'Completed',
    progress: 'Progress', projectUpdates: 'Project updates', noProjectUpdates: 'No public updates have been published for this project yet.',
    relatedDocuments: 'Related documents', askAboutService: 'Ask about this service', enquireInvestment: 'Enquire about this opportunity',
    feedbackPage: 'Feedback and complaints', feedbackDesc: 'Send the council your feedback or lodge a complaint. Complaints are private and never published.',
    complaint: 'Complaint', investmentEnquiryLabel: 'Investment enquiry', serviceEnquiryLabel: 'Service enquiry',
    organisation: 'Organisation (optional)', consent: 'Privacy acknowledgement', consentText: 'I understand my submission will be handled privately by council staff.',
    submitFeedback: 'Submit feedback', feedbackReceived: 'Thank you. Your reference number is', selectDepartment: 'Department (optional)',
    tourismDesc: 'Discover Mutoko district — its heritage, landscapes, and visitor opportunities.',
    tourismPending: 'Council-approved tourism information is being prepared for publication.',
    investment: 'Investment',
    award: 'Award', awardedTo: 'Awarded to', awardedAt: 'Award date', awardAmount: 'Award amount', awardRemarks: 'Award remarks',
    reference: 'Reference', employmentType: 'Employment type',
    fullTime: 'Full time', partTime: 'Part time', contract: 'Contract', temporary: 'Temporary', internship: 'Internship',
    aboutContext: 'About', viewDetails: 'View details',
    accessibility: 'Accessibility', fontSize: 'Font size', decreaseFontSize: 'Decrease font size',
    resetFontSize: 'Reset font size', increaseFontSize: 'Increase font size',
    highContrast: 'High contrast', previousSlide: 'Previous highlight', nextSlide: 'Next highlight', pauseSlides: 'Pause slide rotation', resumeSlides: 'Resume slide rotation', slideNavigation: 'Slide navigation', chooseSlide: 'Choose a slide', showSlide: 'Show slide',
    formErrorsNotice: 'Please correct the following errors',
    councilDocuments: 'Council documents', supportingDocument: 'Supporting document',
    investInMutoko: 'Invest in Mutoko', makeInvestmentEnquiry: 'Make an investment enquiry',
    councilOfficials: 'Council officials', councilDepartments: 'Council departments',
    responsibilities: 'Responsibilities', headOfDepartment: 'Head of department',
    departmentContacts: 'Department contacts', requirements: 'Requirements', steps: 'Steps', fees: 'Fees',
    tendersProcurement: 'Tenders and procurement', tenders: 'Tenders', vacancies: 'Vacancies', howToApply: 'How to apply', tenderDocument: 'Tender document',
    askAboutTender: 'Ask about this tender', vacancyAdvert: 'Vacancy advert', areaDescription: 'Area description',
    returnHomepage: 'Return to the homepage', newsSection: 'News', noticesSection: 'Notices',
} as const;

type Key = keyof typeof english;
// Add only council-approved Shona/Ndebele interface wording. Missing keys use English.
const shona: Partial<Record<Key, string>> = {};
const ndebele: Partial<Record<Key, string>> = {};

export function translate(locale: PublicLocale, key: Key): string {
    if (locale === 'sn') {
        return shona[key] ?? english[key];
    }
    if (locale === 'nd') {
        return ndebele[key] ?? english[key];
    }

    return english[key];
}

export function normalizeLocale(locale: unknown): PublicLocale {
    if (locale === 'sn' || locale === 'nd') {
        return locale;
    }

    return 'en';
}
