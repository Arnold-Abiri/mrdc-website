import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type WardSummary = { slug: string; name: string; description: string | null };

export default function Wards({ wards }: { wards: WardSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [search, setSearch] = useState('');

    const filteredWards = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return wards;
        return wards.filter((w) => `${w.name} ${w.description ?? ''}`.toLowerCase().includes(q));
    }, [wards, search]);

    return (
        <PublicLayout>
            <Head title={t('wardsTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('wardsCrumbCouncil')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('wards')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('wardsEyebrow')}</span>
                    <h1>{t('wardsTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('wardsSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Ward territorial statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">{t('statElectoralWards')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">100%</div>
                                <div className="about-stat-label">{t('statWadco')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statDevolution')}</div>
                                <div className="about-stat-label">{t('statDecentralised')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statCentralOffice')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN WARDS SECTION */}
            <section className="about-page-section" aria-labelledby="wards-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('dirCommunity')}</span>
                    <h2 id="wards-heading">{t('findYourWard')}</h2>
                    <p className="about-page-section-intro">
                        {t('wardsIntro')}
                    </p>

                    {/* SEARCH HEADER */}
                    <div className="wards-lookup-header">
                        <div>
                            <h3 style={{ margin: '0 0 4px', fontSize: '1.05rem', fontWeight: 800, color: 'var(--color-civic-navy)' }}>
                                {t('wardFilterSearch')}
                            </h3>
                            <p style={{ margin: 0, fontSize: '0.86rem', color: '#64748B' }}>
                                {t('wardSearchHint')}
                            </p>
                        </div>

                        <div className="dept-search-box" style={{ maxWidth: 320 }}>
                            <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <label className="sr-only" htmlFor="ward-search">{t('wardSearchLabel')}</label>
                            <input
                                id="ward-search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder={t('wardSearchPlaceholder')}
                                className="dept-search-input"
                            />
                        </div>
                    </div>

                    {/* WARDS GRID */}
                    {filteredWards.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('wardNoMatch')} &ldquo;{search}&rdquo;.
                            </p>
                            <button
                                type="button"
                                className="dept-filter-btn"
                                style={{ marginTop: 12 }}
                                onClick={() => setSearch('')}
                            >
                                {t('wardClearSearch')}
                            </button>
                        </div>
                    ) : (
                        <div className="wards-grid">
                            {filteredWards.map((ward) => (
                                <Link
                                    key={ward.slug}
                                    href={`/${locale}/wards/${ward.slug}`}
                                    className="ward-card-modern"
                                >
                                    <div className="ward-card-number-badge">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                                            <line x1="8" y1="2" x2="8" y2="18" />
                                            <line x1="16" y1="6" x2="16" y2="22" />
                                        </svg>
                                        <span>{t('wardDistrictBadge')}</span>
                                    </div>

                                    <h3 className="ward-card-title">{ward.name}</h3>

                                    <p className="ward-card-desc">
                                        {ward.description ?? t('wardCardFallback')}
                                    </p>

                                    <div className="ward-card-link">
                                        <span>{t('wardExploreProfile')}</span>
                                        <span aria-hidden="true">→</span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* CIVIC PORTAL FOOTER */}
                    <div className="dept-portal-strip">
                        <div className="dept-portal-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <span>{t('wardPortalTitle')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/officials`} className="dept-portal-card">
                                <h4>{t('portalCouncillors')}</h4>
                                <p>{t('portalCouncillorsDesc')}</p>
                                <span>{t('portalCouncillorsLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/meetings`} className="dept-portal-card">
                                <h4>{t('portalHearings')}</h4>
                                <p>{t('portalHearingsDesc')}</p>
                                <span>{t('portalHearingsLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>{t('portalIssue')}</h4>
                                <p>{t('portalIssueDesc')}</p>
                                <span>{t('portalIssueLink')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

