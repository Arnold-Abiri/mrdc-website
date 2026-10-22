import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import '../../css/search.css';

type Result = { type: string; title: string; summary: string | null; url: string; is_review_content: boolean };

const QUICK_ACTIONS = [
    { label: 'payRates', path: '/rates', highlight: true },
    { label: 'publicNotices', path: '/notices' },
    { label: 'councilServices', path: '/services' },
    { label: 'bylawsDocuments', path: '/documents' },
    { label: 'wardsCouncillors', path: '/wards' },
    { label: 'reportIssue', path: '/feedback' },
 ] as const;

const SEARCH_COPY = {
    en: {
        payRates: 'Pay Council Rates', publicNotices: 'Public Notices', councilServices: 'Council Services',
        bylawsDocuments: 'By-laws & Documents', wardsCouncillors: 'Wards & Councillors', reportIssue: 'Report an Issue',
        directory: 'Municipal directory · Mutoko RDC', searchResults: 'Search results for', findPrompt: 'What can we help you find?',
        found: 'Official records matching your query:', explore: 'Explore council services, rates, notices, documents, and wards.',
        quickShortcuts: 'Quick civic shortcuts', searchPlaceholder: 'Search services, rates, wards, by-laws, licences, and notices...',
        clearQuery: 'Clear query', filterCategory: 'Filter results by category', allResults: 'All Results',
        matchedRecords: 'Matched Directory Records', results: 'Results', showing: 'Showing', of: 'of',
        accessResource: 'Access Resource', noMatch: 'No exact match. Try a broader keyword or one of the links below.',
        ratesPayment: 'View Rates & Payment Details', browseServices: 'Browse All Council Services',
        directAccess: 'Direct access', browseCore: 'Browse Council Services & Information',
        chooseDomain: 'Select a department or service to find what you need.',
        ratesBills: 'Rates, Tariffs & Bill Payments', ratesBillsDesc: 'Council rates, tariffs, and payment channels.',
        servicesGuide: 'Municipal Services Guide', servicesGuideDesc: 'Roads, clinics, refuse collection, permits, and market stands.',
        documentsBylaws: 'Official Documents & By-laws', documentsBylawsDesc: 'Council policies, by-laws, reports, and budgets.',
        noticesAnnouncements: 'Public Notices & Announcements', noticesAnnouncementsDesc: 'Meetings, consultations, tenders, and alerts.',
        citizenDesk: 'Citizen desk', urgentHelp: 'Need urgent help?', urgentHelpDesc: 'Contact the council or report a service fault.',
        switchboard: 'Council Switchboard', enquiries: 'Official Enquiries', headquarters: 'Headquarters',
        submitFeedback: 'Submit Feedback or Report Fault', requested: 'Frequently Requested',
        paymentAccounts: 'Rates & Payment Accounts', bylawsBudgets: 'Council By-laws & Budgets',
        findCouncillor: 'Find Your Ward Councillor', procurement: 'Procurement & Tenders',
        investment: 'Investment & Growth Points', hot: 'Hot',
    },
    sn: {
        payRates: 'Bhadharai mitero yeKanzuru', publicNotices: 'Zviziviso zveveruzhinji', councilServices: 'Mabasa eKanzuru',
        bylawsDocuments: 'Mitemo nezvinyorwa', wardsCouncillors: 'Mawadhi nemakanzura', reportIssue: 'Taura dambudziko',
        directory: 'Runyorwa rwemabasa · Mutoko RDC', searchResults: 'Zvawanikwa pakutsvaga', findPrompt: 'Tingakubatsirai kuwana chii?',
        found: 'Zvinyorwa zveKanzuru zvinoenderana nezvamatsvaga:', explore: 'Ongororai mabasa, mitero, zviziviso, zvinyorwa nemawadhi eKanzuru.',
        quickShortcuts: 'Zvinongedzo zvinokurumidza', searchPlaceholder: 'Tsvaga mabasa, mitero, mawadhi, mitemo, marezinesi nezviziviso...',
        clearQuery: 'Bvisa zvatsvaga', filterCategory: 'Sefa zvawanikwa nechikamu', allResults: 'Zvose zvawanikwa',
        matchedRecords: 'Zvinyorwa zvinoenderana', results: 'Zvawanikwa', showing: 'Kuratidza', of: 'kubva pa',
        accessResource: 'Vhura mashoko', noMatch: 'Hapana zvanyatsoenderana. Edzai rimwe shoko kana zvinongedzo zviri pazasi.',
        ratesPayment: 'Ona mitero nemabhadhariro', browseServices: 'Ona mabasa ose eKanzuru',
        directAccess: 'Kuwana zvakananga', browseCore: 'Ona mabasa nemashoko eKanzuru',
        chooseDomain: 'Sarudzai dhipatimendi kana basa kuti muwane zvamunoda.',
        ratesBills: 'Mitero nemabhadhariro', ratesBillsDesc: 'Mitero yeKanzuru nenzira dzekubhadhara.',
        servicesGuide: 'Gwara remabasa eKanzuru', servicesGuideDesc: 'Migwagwa, makiriniki, marara, mvumo nemisika.',
        documentsBylaws: 'Zvinyorwa nemitemo yeKanzuru', documentsBylawsDesc: 'Mitemo, mishumo nemabhajeti eKanzuru.',
        noticesAnnouncements: 'Zviziviso zveveruzhinji', noticesAnnouncementsDesc: 'Misangano, kubvunzana, matenda neziviso.',
        citizenDesk: 'Rubatsiro rwevagari', urgentHelp: 'Munoda rubatsiro rwechimbichimbi?', urgentHelpDesc: 'Taura neKanzuru kana kuzivisa dambudziko rebasa.',
        switchboard: 'Nhare huru yeKanzuru', enquiries: 'Mibvunzo yepamutemo', headquarters: 'Hofisi huru',
        submitFeedback: 'Tumira mhinduro kana dambudziko', requested: 'Zvinowanzotsvakwa',
        paymentAccounts: 'Mitero nemaakaundi ekubhadhara', bylawsBudgets: 'Mitemo nemabhajeti eKanzuru',
        findCouncillor: 'Tsvaga kanzura wewadhi yenyu', procurement: 'Kutenga nematenda',
        investment: 'Kudyara mari nenzvimbo dzebudiriro', hot: 'Chinokosha',
    },
    nd: {
        payRates: 'Khokha imithelo yoMkhandlu', publicNotices: 'Izaziso zomphakathi', councilServices: 'Imisebenzi yoMkhandlu',
        bylawsDocuments: 'Imithetho lemibhalo', wardsCouncillors: 'Amawadi lamakhansila', reportIssue: 'Bika udaba',
        directory: 'Uhlu lwemisebenzi · Mutoko RDC', searchResults: 'Okutholakeleyo ekusesheni', findPrompt: 'Singakusiza ukuthola ini?',
        found: 'Imibhalo yoMkhandlu ehambelana lokusesha kwakho:', explore: 'Hlola imisebenzi, imithelo, izaziso, imibhalo lamawadi oMkhandlu.',
        quickShortcuts: 'Izixhumanisi ezisheshayo', searchPlaceholder: 'Sesha imisebenzi, imithelo, amawadi, imithetho, amalayisensi lezaziso...',
        clearQuery: 'Susa okuseshiweyo', filterCategory: 'Hlunga okutholakeleyo ngesigaba', allResults: 'Konke okutholakeleyo',
        matchedRecords: 'Imibhalo ehambelanayo', results: 'Okutholakeleyo', showing: 'Ukubonisa', of: 'ku',
        accessResource: 'Vula ulwazi', noMatch: 'Akukho okuhambelana ngqo. Zama elinye ibala kumbe izixhumanisi ezingezansi.',
        ratesPayment: 'Bona imithelo lokukhokha', browseServices: 'Bona yonke imisebenzi yoMkhandlu',
        directAccess: 'Ukufinyelela ngqo', browseCore: 'Bona imisebenzi lolwazi loMkhandlu',
        chooseDomain: 'Khetha umnyango kumbe umsebenzi ukuze uthole okudingayo.',
        ratesBills: 'Imithelo lokukhokha', ratesBillsDesc: 'Imithelo yoMkhandlu lezindlela zokukhokha.',
        servicesGuide: 'Umhlahlandlela wemisebenzi yoMkhandlu', servicesGuideDesc: 'Imigwaqo, amakliniki, udoti, izimvumo lezimakethe.',
        documentsBylaws: 'Imibhalo lemithetho yoMkhandlu', documentsBylawsDesc: 'Imithetho, imibiko lamabhajethi oMkhandlu.',
        noticesAnnouncements: 'Izaziso zomphakathi', noticesAnnouncementsDesc: 'Imihlangano, ukubonisana, amathenda lezaziso.',
        citizenDesk: 'Usizo lwezakhamizi', urgentHelp: 'Udinga usizo oluphuthumayo?', urgentHelpDesc: 'Xhumana loMkhandlu kumbe ubike udaba lomsebenzi.',
        switchboard: 'Ucingo olukhulu loMkhandlu', enquiries: 'Imibuzo esemthethweni', headquarters: 'Ihhovisi elikhulu',
        submitFeedback: 'Thumela impendulo kumbe ubike udaba', requested: 'Okuvame ukucelwa',
        paymentAccounts: 'Imithelo lama-akhawunti okukhokha', bylawsBudgets: 'Imithetho lamabhajethi oMkhandlu',
        findCouncillor: 'Thola ikhansila yewadi yakho', procurement: 'Ukuthenga lamathenda',
        investment: 'Ukutshala imali lendawo zentuthuko', hot: 'Okuqakathekileyo',
    },
} as const;

function SearchIcon({ size = 20 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
    );
}

function ArrowRightIcon({ size = 18 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12 5 19 12 12 19" />
        </svg>
    );
}

function TypeIcon({ type }: { type: string }) {
    const norm = type.toLowerCase();
    if (norm === 'service') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
            </svg>
        );
    }
    if (norm === 'document') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
        );
    }
    if (norm === 'notice' || norm === 'news') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
        );
    }
    if (norm === 'ward' || norm === 'official') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
        );
    }
    if (norm === 'tender' || norm === 'vacancy' || norm === 'investment') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <line x1="8" y1="21" x2="16" y2="21" />
                <line x1="12" y1="17" x2="12" y2="21" />
            </svg>
        );
    }
    if (norm === 'gallery') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <circle cx="9" cy="10" r="2" />
                <path d="m21 16-5-5L5 20" />
            </svg>
        );
    }
    return (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="16" x2="12" y2="12" />
            <line x1="12" y1="8" x2="12.01" y2="8" />
        </svg>
    );
}

export default function Search({ query, results }: { query: string; results: Result[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const copy = SEARCH_COPY[locale];
    const typeLabel = (type: string): string => {
        const keys: Record<string, Parameters<typeof t>[0]> = { Page: 'about', Document: 'documents', Service: 'services', Department: 'department', News: 'news', Notice: 'notices', Gallery: 'gallery', Ward: 'wards', Official: 'officials', Tender: 'tenders', Vacancy: 'vacancies', Investment: 'investment', Meeting: 'meetings', Project: 'projects' };
        return keys[type] ? t(keys[type]) : type;
    };
    const [selectedType, setSelectedType] = useState('All');

    const counts = results.reduce<Record<string, number>>((totals, result) => {
        totals[result.type] = (totals[result.type] ?? 0) + 1;
        return totals;
    }, {});
    const types = Object.keys(counts);
    const activeType = selectedType in counts ? selectedType : 'All';
    const visibleResults = activeType === 'All' ? results : results.filter(result => result.type === activeType);

    return (
        <PublicLayout>
            <Head title={`${query ? `${query} - ` : ''}${t('search')} | Mutoko RDC`} />
            <div className="services-page-wrap">
                {/* Standard Page Hero Banner (matching Contact Us, About Us, Services, etc.) */}
                <header className="about-page-hero search-hero">
                    <div className="about-page-hero-overlay" aria-hidden="true" />
                    <div className="container">
                        <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                            <a href={`/${locale}`}>{t('home')}</a>
                            <span aria-hidden="true">/</span>
                            <span aria-current="page">{t('search')}</span>
                        </nav>
                        <span className="about-accent-eyebrow">{copy.directory}</span>
                        <h1>
                            {query ? `${copy.searchResults} “${query}”` : copy.findPrompt}
                        </h1>
                        <p className="about-page-hero-subtitle">
                            {query
                                ? `${copy.found} ${results.length}`
                                : copy.explore}
                        </p>

                        {/* Quick action shortcuts strip */}
                        <div className="services-quick-strip" aria-label={copy.quickShortcuts}>
                            {QUICK_ACTIONS.map(qa => (
                                <a
                                    key={qa.path}
                                    href={`/${locale}${qa.path}`}
                                    className={`quick-action-pill ${'highlight' in qa && qa.highlight ? 'highlight' : ''}`}
                                >
                                    <span>{copy[qa.label]}</span>
                                    <span className="pill-arrow" aria-hidden="true">&rarr;</span>
                                </a>
                            ))}
                        </div>
                    </div>
                </header>

                <div className="container services-body-container">
                    {/* Search Controls Card */}
                    <div className="services-search-card">
                        <form action={`/${locale}/search`} method="get" role="search" className="services-search-form">
                            <label htmlFor="site-search" className="sr-only">{t('searchCouncilPages')}</label>
                            <span className="services-search-input-icon">
                                <SearchIcon size={22} />
                            </span>
                            <input
                                id="site-search"
                                name="q"
                                type="search"
                                defaultValue={query}
                                maxLength={100}
                                placeholder={copy.searchPlaceholder}
                                autoComplete="off"
                            />
                            {query && (
                                <a href={`/${locale}/search`} className="services-search-clear-btn" title={copy.clearQuery}>
                                    &times;
                                </a>
                            )}
                            <button type="submit" className="services-search-submit">
                                {t('search')}
                            </button>
                        </form>

                        {/* Category filter pills if results exist */}
                        {results.length > 0 && (
                            <div className="services-category-filters" role="group" aria-label={copy.filterCategory}>
                                <button
                                    type="button"
                                    className={`services-filter-btn ${activeType === 'All' ? 'active' : ''}`}
                                    aria-pressed={activeType === 'All'}
                                    onClick={() => setSelectedType('All')}
                                >
                                    <span>{copy.allResults}</span>
                                    <span className="filter-count">{results.length}</span>
                                </button>
                                {types.map(type => (
                                    <button
                                        key={type}
                                        type="button"
                                        className={`services-filter-btn ${activeType === type ? 'active' : ''}`}
                                        aria-pressed={activeType === type}
                                        onClick={() => setSelectedType(type)}
                                    >
                                        <span>{typeLabel(type)}</span>
                                        <span className="filter-count">{counts[type]}</span>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* 2-Column Municipal Results & Directory Layout */}
                    <div className="services-layout-grid">
                        {/* Primary Results Column */}
                        <section className="services-results-column">
                            {query ? (
                                <>
                                    <div className="services-results-meta">
                                        <h2 className="services-results-meta-title">
                                            {activeType === 'All' ? copy.matchedRecords : `${typeLabel(activeType)} ${copy.results}`}
                                        </h2>
                                        <span className="services-results-count-badge">
                                            {copy.showing} {visibleResults.length} {copy.of} {results.length}
                                        </span>
                                    </div>

                                    {visibleResults.length > 0 ? (
                                        <div className="services-cards-grid">
                                            {visibleResults.map((result) => (
                                                <article key={result.url} className="services-item-card">
                                                    <div className="services-item-top">
                                                        <div className={`services-icon-avatar type-${result.type.toLowerCase()}`}>
                                                            <TypeIcon type={result.type} />
                                                        </div>
                                                        <div className="services-item-badges">
                                                            <span className="services-category-badge">
                                                                {typeLabel(result.type)}
                                                            </span>
                                                            {result.is_review_content && (
                                                                <span className="services-review-badge">
                                                                    {t('reviewContent')}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    <h3 className="services-item-title">
                                                        <a href={result.url}>{result.title}</a>
                                                    </h3>

                                                    {result.summary && (
                                                        <p className="services-item-description">
                                                            {result.summary}
                                                        </p>
                                                    )}

                                                    <div className="services-item-footer">
                                                        <a href={result.url} className="services-explore-link">
                                                            <span>{copy.accessResource}</span>
                                                            <ArrowRightIcon size={16} />
                                                        </a>
                                                    </div>
                                                </article>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="services-empty-state">
                                            <div className="empty-state-icon">
                                                <SearchIcon size={36} />
                                            </div>
                                            <h3>{t('noApprovedResults')}</h3>
                                            <p>
                                                {copy.noMatch} <strong>{query}</strong>
                                            </p>
                                            <div className="empty-state-actions">
                                                <a href={`/${locale}/rates`} className="btn-empty-primary">
                                                    {copy.ratesPayment}
                                                </a>
                                                <a href={`/${locale}/services`} className="btn-empty-secondary">
                                                    {copy.browseServices}
                                                </a>
                                            </div>
                                        </div>
                                    )}
                                </>
                            ) : (
                                /* When query is blank: show categorized civic directory */
                                <div className="services-initial-overview">
                                    <div className="services-section-heading">
                                        <span className="eyebrow-accent">{copy.directAccess}</span>
                                        <h2>{copy.browseCore}</h2>
                                        <p>{copy.chooseDomain}</p>
                                    </div>

                                    <div className="services-start-grid">
                                        <a href={`/${locale}/rates`} className="start-guide-card highlight-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="service" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>{copy.ratesBills}</h4>
                                                <p>{copy.ratesBillsDesc}</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/services`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="service" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>{copy.servicesGuide}</h4>
                                                <p>{copy.servicesGuideDesc}</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/documents`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="document" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>{copy.documentsBylaws}</h4>
                                                <p>{copy.documentsBylawsDesc}</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/notices`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="notice" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>{copy.noticesAnnouncements}</h4>
                                                <p>{copy.noticesAnnouncementsDesc}</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            )}
                        </section>

                        {/* Secondary Sidebar Column */}
                        <aside className="services-sidebar-column">
                            {/* Harare-Style Sticky Assistance Card */}
                            <div className="sidebar-card assistance-card">
                                <div className="assistance-header">
                                    <span className="assistance-badge">{copy.citizenDesk}</span>
                                    <h3>{copy.urgentHelp}</h3>
                                    <p>{copy.urgentHelpDesc}</p>
                                </div>
                                <div className="assistance-body">
                                    <div className="assistance-item">
                                        <span className="assistance-icon">📞</span>
                                        <div>
                                            <strong>{copy.switchboard}</strong>
                                            <a href="tel:+263771592888">+263 771 592 888</a>
                                        </div>
                                    </div>
                                    <div className="assistance-item">
                                        <span className="assistance-icon">✉️</span>
                                        <div>
                                            <strong>{copy.enquiries}</strong>
                                            <a href="mailto:info@mutokordc.co.zw">info@mutokordc.co.zw</a>
                                        </div>
                                    </div>
                                    <div className="assistance-item">
                                        <span className="assistance-icon">📍</span>
                                        <div>
                                            <strong>{copy.headquarters}</strong>
                                            <span>Stand 366 Mutoko Growth Point, Zimbabwe</span>
                                        </div>
                                    </div>
                                    <a href={`/${locale}/feedback`} className="sidebar-cta-btn">
                                        {copy.submitFeedback} &rarr;
                                    </a>
                                </div>
                            </div>

                            {/* Key Civic Links Card */}
                            <div className="sidebar-card quicklinks-card">
                                <h3>{copy.requested}</h3>
                                <ul className="sidebar-links-list">
                                    <li>
                                        <a href={`/${locale}/rates`} className="sidebar-link-item">
                                            <span>{copy.paymentAccounts}</span>
                                            <span className="link-tag">{copy.hot}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/documents`} className="sidebar-link-item">
                                            <span>{copy.bylawsBudgets}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/wards`} className="sidebar-link-item">
                                            <span>{copy.findCouncillor}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/tenders`} className="sidebar-link-item">
                                            <span>{copy.procurement}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/investment`} className="sidebar-link-item">
                                            <span>{copy.investment}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}

