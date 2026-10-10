import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import '../../css/search.css';

type Result = { type: string; title: string; summary: string | null; url: string; is_review_content: boolean };

const QUICK_ACTIONS = [
    { label: 'Pay Council Rates', path: '/rates', highlight: true },
    { label: 'Public Notices', path: '/notices' },
    { label: 'Council Services', path: '/services' },
    { label: 'By-laws & Documents', path: '/documents' },
    { label: 'Wards & Councillors', path: '/wards' },
    { label: 'Report an Issue', path: '/feedback' },
];

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
                            <a href={`/${locale}`}>Home</a>
                            <span aria-hidden="true">/</span>
                            <span aria-current="page">{t('search')}</span>
                        </nav>
                        <span className="about-accent-eyebrow">MUNICIPAL DIRECTORY &bull; MUTOKO RDC</span>
                        <h1>
                            {query ? `Search Results for “${query}”` : 'What can we help you find?'}
                        </h1>
                        <p className="about-page-hero-subtitle">
                            {query
                                ? `Found ${results.length} official record${results.length === 1 ? '' : 's'} matching your query across Mutoko Rural District Council.`
                                : 'Explore council services, verified rates schedules, public notices, official documents, and administrative wards.'}
                        </p>

                        {/* Quick action shortcuts strip */}
                        <div className="services-quick-strip" aria-label="Quick civic shortcuts">
                            {QUICK_ACTIONS.map(qa => (
                                <a
                                    key={qa.path}
                                    href={`/${locale}${qa.path}`}
                                    className={`quick-action-pill ${qa.highlight ? 'highlight' : ''}`}
                                >
                                    <span>{qa.label}</span>
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
                                placeholder="Search by service name, rates, ward, by-laws, licences, notices..."
                                autoComplete="off"
                            />
                            {query && (
                                <a href={`/${locale}/search`} className="services-search-clear-btn" title="Clear query">
                                    &times;
                                </a>
                            )}
                            <button type="submit" className="services-search-submit">
                                {t('search')}
                            </button>
                        </form>

                        {/* Category filter pills if results exist */}
                        {results.length > 0 && (
                            <div className="services-category-filters" role="group" aria-label="Filter results by category">
                                <button
                                    type="button"
                                    className={`services-filter-btn ${activeType === 'All' ? 'active' : ''}`}
                                    aria-pressed={activeType === 'All'}
                                    onClick={() => setSelectedType('All')}
                                >
                                    <span>All Results</span>
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
                                        <span>{type}</span>
                                        <span className="filter-count">{counts[type]}</span>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* 2-Column Municipal Results & Directory Layout */}
                    <div className="services-layout-grid">
                        {/* Primary Results Column */}
                        <main className="services-results-column">
                            {query ? (
                                <>
                                    <div className="services-results-meta">
                                        <h2 className="services-results-meta-title">
                                            {activeType === 'All' ? 'Matched Directory Records' : `${activeType} Results`}
                                        </h2>
                                        <span className="services-results-count-badge">
                                            Showing {visibleResults.length} of {results.length}
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
                                                                {result.type}
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
                                                            <span>Access Resource</span>
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
                                                We couldn’t find an exact match for “<strong>{query}</strong>”. Try a broader keyword, check your spelling, or select one of the civic shortcuts in the directory.
                                            </p>
                                            <div className="empty-state-actions">
                                                <a href={`/${locale}/rates`} className="btn-empty-primary">
                                                    View Rates &amp; Payment Details
                                                </a>
                                                <a href={`/${locale}/services`} className="btn-empty-secondary">
                                                    Browse All Council Services
                                                </a>
                                            </div>
                                        </div>
                                    )}
                                </>
                            ) : (
                                /* When query is blank: show categorized civic directory */
                                <div className="services-initial-overview">
                                    <div className="services-section-heading">
                                        <span className="eyebrow-accent">DIRECT ACCESS</span>
                                        <h2>Browse Core Council Services &amp; Information</h2>
                                        <p>Select a municipal department or service domain to find what you need.</p>
                                    </div>

                                    <div className="services-start-grid">
                                        <a href={`/${locale}/rates`} className="start-guide-card highlight-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="service" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>Rates, Tariffs &amp; Bill Payments</h4>
                                                <p>Council billing rates, approved tariff schedules, banking accounts &amp; EcoCash channels.</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/services`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="service" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>Municipal Services Guide</h4>
                                                <p>Roads, clinic healthcare, refuse collection, planning permits &amp; market stands.</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/documents`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="document" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>Official Documents &amp; By-laws</h4>
                                                <p>Download audited statements, council policies, by-laws, and annual budgets.</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>

                                        <a href={`/${locale}/notices`} className="start-guide-card">
                                            <div className="start-guide-icon">
                                                <TypeIcon type="notice" />
                                            </div>
                                            <div className="start-guide-info">
                                                <h4>Public Notices &amp; Announcements</h4>
                                                <p>Stay updated on stakeholder meetings, public consultations, tenders, and health alerts.</p>
                                            </div>
                                            <span className="start-guide-arrow">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            )}
                        </main>

                        {/* Secondary Sidebar Column */}
                        <aside className="services-sidebar-column">
                            {/* Harare-Style Sticky Assistance Card */}
                            <div className="sidebar-card assistance-card">
                                <div className="assistance-header">
                                    <span className="assistance-badge">CITIZEN DESK</span>
                                    <h3>Need Urgent Help?</h3>
                                    <p>Contact Mutoko RDC administration or report an infrastructure or billing fault directly.</p>
                                </div>
                                <div className="assistance-body">
                                    <div className="assistance-item">
                                        <span className="assistance-icon">📞</span>
                                        <div>
                                            <strong>Council Switchboard</strong>
                                            <a href="tel:+263771592888">+263 771 592 888</a>
                                        </div>
                                    </div>
                                    <div className="assistance-item">
                                        <span className="assistance-icon">✉️</span>
                                        <div>
                                            <strong>Official Enquiries</strong>
                                            <a href="mailto:info@mutokordc.co.zw">info@mutokordc.co.zw</a>
                                        </div>
                                    </div>
                                    <div className="assistance-item">
                                        <span className="assistance-icon">📍</span>
                                        <div>
                                            <strong>Headquarters</strong>
                                            <span>Stand 366 Mutoko Growth Point, Zimbabwe</span>
                                        </div>
                                    </div>
                                    <a href={`/${locale}/feedback`} className="sidebar-cta-btn">
                                        Submit Feedback or Report Fault &rarr;
                                    </a>
                                </div>
                            </div>

                            {/* Key Civic Links Card */}
                            <div className="sidebar-card quicklinks-card">
                                <h3>Frequently Requested</h3>
                                <ul className="sidebar-links-list">
                                    <li>
                                        <a href={`/${locale}/rates`} className="sidebar-link-item">
                                            <span>Rates &amp; Payment Accounts</span>
                                            <span className="link-tag">Hot</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/documents`} className="sidebar-link-item">
                                            <span>Council By-laws &amp; Budgets</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/wards`} className="sidebar-link-item">
                                            <span>Find Your Ward Councillor</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/tenders`} className="sidebar-link-item">
                                            <span>Procurement &amp; Tenders</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href={`/${locale}/investment`} className="sidebar-link-item">
                                            <span>Investment &amp; Growth Points</span>
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

