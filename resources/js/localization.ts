export type PublicLocale = 'en' | 'sn';

const english = {
    home: 'Home', about: 'About', council: 'Council', officials: 'Officials', wards: 'Wards',
    services: 'Services', documents: 'Documents', news: 'News', notices: 'Notices',
    search: 'Search', contact: 'Contact', skip: 'Skip to main content',
    language: 'Language', english: 'English', shona: 'Shona',
    developmentNotice: 'Development preview — displayed council information and imagery are unverified.',
    setLanguage: 'Set language', socialPreviews: 'Social media previews', councilHome: 'Mutoko Rural District Council home',
    brandingAlt: 'Development branding mark for Mutoko Rural District Council', councilName: 'Rural District Council',
    tagline: 'Service Delivery for Sustainable Communities', closeMenu: 'Close menu', menu: 'Menu', primaryNavigation: 'Primary navigation',
    footerAbout: 'Working with our communities to deliver quality services, promote local development and build a better Mutoko.',
    quickLinks: 'Quick Links', ourServices: 'Our Services', development: 'Development', tourism: 'Tourism', media: 'Media', contactUs: 'Contact Us',
    waterSupply: 'Water Supply', roadsInfrastructure: 'Roads & Infrastructure', healthSanitation: 'Health & Sanitation',
    environmentalManagement: 'Environmental Management', developmentPlanning: 'Development Planning', communityServices: 'Community Services',
    previewCopyright: 'Development preview — content pending council approval.', privacyPolicy: 'Privacy Policy', termsOfUse: 'Terms of Use', siteMap: 'Site Map',
    searchCouncilPages: 'Search council pages', noApprovedResults: 'No approved results found.', results: 'result(s)', reviewContent: 'Development review content',
    contactCouncil: 'Contact the council', enquiryReceived: 'Your enquiry was received. Thank you.',
    enquiryHelp: 'Use this form for a general enquiry. Do not include sensitive personal information.', contactDetails: 'Council contact details',
    name: 'Name', email: 'Email', phoneOptional: 'Phone (optional)', category: 'Category', general: 'General', feedback: 'Feedback', other: 'Other',
    subject: 'Subject', message: 'Message', website: 'Website', sendEnquiry: 'Send enquiry',
} as const;

type Key = keyof typeof english;
// Add only council-approved Shona interface wording. Missing keys use English.
const shona: Partial<Record<Key, string>> = {};

export function translate(locale: PublicLocale, key: Key): string {
    return (locale === 'sn' ? shona[key] : undefined) ?? english[key];
}

export function normalizeLocale(locale: unknown): PublicLocale {
    return locale === 'sn' ? 'sn' : 'en';
}
