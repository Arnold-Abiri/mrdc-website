import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type OfficialSummary = {
    slug: string;
    name: string;
    title: string;
    biography: string | null;
    department: string | null;
    photo_url: string | null;
};

export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [filter, setFilter] = useState<'all' | 'executive' | 'secretariat'>('all');
    const [search, setSearch] = useState('');

    const filteredOfficials = useMemo(() => {
        const q = search.trim().toLowerCase();
        return officials.filter((o) => {
            const matchesQuery = !q || `${o.name} ${o.title} ${o.department ?? ''} ${o.biography ?? ''}`.toLowerCase().includes(q);
            const isExecutive = o.title.toLowerCase().includes('chairperson') || o.title.toLowerCase().includes('chief executive') || o.title.toLowerCase().includes('secretary');
            if (filter === 'executive') return matchesQuery && isExecutive;
            if (filter === 'secretariat') return matchesQuery && !isExecutive;
            return matchesQuery;
        });
    }, [officials, filter, search]);

    return (
        <PublicLayout>
            <Head title="Council Officials & Leadership" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('wardsCrumbCouncil')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('officials')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('officialsEyebrow')}</span>
                    <h1>{t('officialsTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('officialsSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Leadership statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">{t('statWardsRep')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">1</div>
                                <div className="about-stat-label">{t('statCeo')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{officials.length}</div>
                                <div className="about-stat-label">{t('statExecProfiles')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Cap 29:13</div>
                                <div className="about-stat-label">{t('statGovCap')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN ROSTER */}
            <section className="about-page-section" aria-labelledby="officials-roster">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('dirPortfolios')}</span>
                    <h2 id="officials-roster">{t('mgmtTeam')}</h2>
                    <p className="about-page-section-intro">
                        {t('officialsIntro')}
                    </p>

                    {/* SEARCH & FILTERS */}
                    <div className="dept-search-filter-wrap">
                        <div className="dept-filter-pills" role="tablist" aria-label={t('filterOfficials')}>
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'all' ? 'active' : ''}`}
                                onClick={() => setFilter('all')}
                            >
                                {t('allLeadership')} ({officials.length})
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'executive' ? 'active' : ''}`}
                                onClick={() => setFilter('executive')}
                            >
                                {t('execOffices')}
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'secretariat' ? 'active' : ''}`}
                                onClick={() => setFilter('secretariat')}
                            >
                                {t('directorateHeads')}
                            </button>
                        </div>

                        <div className="dept-search-box">
                            <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <label className="sr-only" htmlFor="official-search">{t('searchOfficialsLabel')}</label>
                            <input
                                id="official-search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder={t('searchOfficialsPlaceholder')}
                                className="dept-search-input"
                            />
                        </div>
                    </div>

                    {/* HIERARCHICAL LAYOUT: APEX CHAIRPERSON & EXECUTIVE ADMINISTRATION */}
                    {filteredOfficials.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noOfficialsMatch')} &ldquo;{search}&rdquo;.
                            </p>
                            <button
                                type="button"
                                className="dept-filter-btn"
                                style={{ marginTop: 12 }}
                                onClick={() => { setSearch(''); setFilter('all'); }}
                            >
                                {t('clearSearchFilters')}
                            </button>
                        </div>
                    ) : (
                        <>
                            {/* 1. APEX POLITICAL HEAD SHOWCASE (COUNCIL CHAIRPERSON) */}
                            {(() => {
                                const chairperson = filteredOfficials.find(o => o.title.toLowerCase().includes('chairperson'));
                                if (!chairperson) return null;
                                return (
                                    <div className="officials-apex-showcase">
                                        <article className="officials-apex-card">
                                            <div className="officials-apex-media">
                                                {chairperson.photo_url ? (
                                                    <img
                                                        src={chairperson.photo_url}
                                                        alt={`${t('portraitOf')} ${chairperson.name}`}
                                                        className="officials-apex-photo"
                                                    />
                                                ) : (
                                                    <img
                                                        src="/images/council-crest-light.webp"
                                                        alt={t('crestFallbackAlt')}
                                                        className="official-crest-fallback"
                                                    />
                                                )}
                                                <span className="officials-apex-tag">{t('govChair')}</span>
                                            </div>
                                            <div className="officials-apex-content">
                                                <span className="officials-apex-eyebrow">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                    </svg>
                                                    {t('apexPoliticalHead')}
                                                </span>
                                                <h3 className="officials-apex-name">{chairperson.name}</h3>
                                                <div className="officials-apex-title">{chairperson.title}</div>
                                                <p className="officials-apex-desc">
                                                    {t('apexChairpersonDesc')}
                                                </p>
                                                <div className="officials-apex-footer">
                                                    <Link
                                                        href={`/${locale}/council/chairperson`}
                                                        className="officials-apex-btn"
                                                    >
                                                        <span>{t('officeChairperson')}</span>
                                                        <span aria-hidden="true">→</span>
                                                    </Link>
                                                    <span className="officials-apex-stat">
                                                        <span>• 29 Wards Governed</span>
                                                        <span>• Cap 29:13 Statutory Powers</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                );
                            })()}

                            {/* 2. EXECUTIVE ADMINISTRATION & SECRETARIAT TIER */}
                            {(() => {
                                const subordinates = filteredOfficials.filter(o => !o.title.toLowerCase().includes('chairperson'));
                                if (subordinates.length === 0) return null;
                                return (
                                    <div>
                                        <div className="officials-tier-header">
                                            <h3 className="officials-tier-title">
                                                {t('execAdminSection')}
                                            </h3>
                                            <span className="officials-tier-badge">
                                                {t('execAdminLedBy')}
                                            </span>
                                        </div>

                                        <div className="officials-directory-grid">
                                            {subordinates.map((official) => (
                                                <article key={official.slug} className="official-profile-card">
                                                    <div className="official-portrait-wrapper">
                                                        {official.photo_url ? (
                                                            <img
                                                                src={official.photo_url}
                                                                alt={`${t('portraitOf')} ${official.name}`}
                                                                loading="lazy"
                                                            />
                                                        ) : (
                                                            <img
                                                                src="/images/council-crest-light.webp"
                                                                alt={t('crestFallbackAlt')}
                                                                className="official-crest-fallback"
                                                            />
                                                        )}
                                                        <span className="official-badge-role">
                                                            {official.department ?? t('execOfficeFallback')}
                                                        </span>
                                                    </div>

                                                    <div className="official-profile-body">
                                                        <h3 className="official-profile-name">{official.name}</h3>
                                                        <div className="official-profile-title">{official.title}</div>
                                                        {official.department && (
                                                            <div className="official-profile-dept">{official.department}</div>
                                                        )}
                                                        {official.biography && (
                                                            <p className="official-profile-bio">{official.biography}</p>
                                                        )}
                                                    </div>
                                                </article>
                                            ))}
                                        </div>
                                    </div>
                                );
                            })()}
                        </>
                    )}

                    {/* FOOTER STRIP */}
                    <div className="dept-portal-strip">
                        <div className="dept-portal-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            <span>{t('govWardAdmin')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/wards`} className="dept-portal-card">
                                <h4>{t('findWardCouncillor')}</h4>
                                <p>{t('findWardCouncillorDesc')}</p>
                                <span>{t('browseWardsDir')} →</span>
                            </Link>

                            <Link href={`/${locale}/departments`} className="dept-portal-card">
                                <h4>{t('councilDirectorates')}</h4>
                                <p>{t('councilDirectoratesDesc')}</p>
                                <span>{t('exploreDepartments')} →</span>
                            </Link>

                            <Link href={`/${locale}/meetings`} className="dept-portal-card">
                                <h4>{t('meetingsAgendas')}</h4>
                                <p>{t('meetingsAgendasDesc')}</p>
                                <span>{t('viewMeetingSchedule')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

