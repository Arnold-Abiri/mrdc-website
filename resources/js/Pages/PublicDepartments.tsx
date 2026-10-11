import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';

type Department = { id: number; public_name: string | null; public_summary: string | null };

type DeptMetadata = {
    category: 'governance' | 'infrastructure' | 'social';
    badgeKey: TranslationKey;
    color: string;
    icon: React.ReactNode;
    focusKeys: TranslationKey[];
};

function getDepartmentMeta(name: string): DeptMetadata {
    const n = name.toLowerCase();

    if (n.includes('executive') || n.includes('ceo')) {
        return {
            category: 'governance',
            badgeKey: 'deptExecSec',
            color: '#0B2D4D',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            ),
            focusKeys: ['focusCorpLead', 'focusResolutions', 'focusLiaison', 'focusWardsCoord'],
        };
    }

    if (n.includes('financ')) {
        return {
            category: 'social',
            badgeKey: 'deptTreasury',
            color: '#0B8F62',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
            ),
            focusKeys: ['focusBilling', 'focusBudget', 'focusProcSupport', 'focusAudits'],
        };
    }

    if (n.includes('engineer') || n.includes('works') || n.includes('roads') || n.includes('water')) {
        return {
            category: 'infrastructure',
            badgeKey: 'deptTechnical',
            color: '#D97706',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                </svg>
            ),
            focusKeys: ['focusRoads', 'focusBoreholes', 'focusSewer', 'focusBuilding'],
        };
    }

    if (n.includes('plan')) {
        return {
            category: 'infrastructure',
            badgeKey: 'deptSpatial',
            color: '#0284C7',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                    <line x1="8" y1="2" x2="8" y2="18" />
                    <line x1="16" y1="6" x2="16" y2="22" />
                </svg>
            ),
            focusKeys: ['focusStands', 'focusLeases', 'focusTownPlanning', 'focusDevControl'],
        };
    }

    if (n.includes('health') || n.includes('social')) {
        return {
            category: 'social',
            badgeKey: 'deptCommunity',
            color: '#059669',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
            ),
            focusKeys: ['focusClinics', 'focusSchools', 'focusStadium', 'focusWelfare'],
        };
    }

    if (n.includes('admin') || n.includes('human')) {
        return {
            category: 'governance',
            badgeKey: 'deptCorpSec',
            color: '#475569',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            ),
            focusKeys: ['focusRegistry', 'focusHR', 'focusCare', 'focusCommittees'],
        };
    }

    if (n.includes('audit')) {
        return {
            category: 'governance',
            badgeKey: 'deptAssurance',
            color: '#7C3AED',
            icon: (
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            ),
            focusKeys: ['focusRisk', 'focusCompliance', 'focusAssets', 'focusValue'],
        };
    }

    return {
        category: 'governance',
        badgeKey: 'deptDirectorate',
        color: '#0B2D4D',
        icon: (
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M3 9h18M9 21V9" />
            </svg>
        ),
        focusKeys: ['focusDelivery', 'focusAdmin'],
    };
}

export default function PublicDepartments({ departments }: { departments: Department[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [query, setQuery] = useState('');
    const [categoryFilter, setCategoryFilter] = useState<'all' | 'governance' | 'infrastructure' | 'social'>('all');

    const filtered = useMemo(() => {
        const q = query.trim().toLowerCase();
        return departments.filter((d) => {
            const meta = getDepartmentMeta(d.public_name ?? '');
            const matchesCategory = categoryFilter === 'all' || meta.category === categoryFilter;
            const matchesQuery = !q || `${d.public_name ?? ''} ${d.public_summary ?? ''} ${meta.focusKeys.join(' ')}`.toLowerCase().includes(q);
            return matchesCategory && matchesQuery;
        });
    }, [departments, query, categoryFilter]);

    return (
        <PublicLayout>
            <Head title={t('deptsTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('councilDepartments')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('deptsEyebrow')}</span>
                    <h1>{t('deptsTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('deptsSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Directorate administrative coverage">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{departments.length}</div>
                                <div className="about-stat-label">{t('statExecDirs')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">{t('statWardsAdmin')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statCivicCentre')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">100%</div>
                                <div className="about-stat-label">{t('statCoverage')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* DIRECTORY SECTION */}
            <section className="about-page-section" aria-labelledby="dept-roster">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('execPortfolios')}</span>
                    <h2 id="dept-roster">{t('dirDirectory')}</h2>
                    <p className="about-page-section-intro">
                        {t('deptsIntro')}
                    </p>

                    {/* SEARCH & FILTERS */}
                    <div className="dept-search-filter-wrap">
                        <div className="dept-filter-pills" role="tablist" aria-label={t('filterDepts')}>
                            <button
                                type="button"
                                className={`dept-filter-btn ${categoryFilter === 'all' ? 'active' : ''}`}
                                onClick={() => setCategoryFilter('all')}
                            >
                                {t('allDirectorates')} ({departments.length})
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${categoryFilter === 'governance' ? 'active' : ''}`}
                                onClick={() => setCategoryFilter('governance')}
                            >
                                {t('adminGov')}
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${categoryFilter === 'infrastructure' ? 'active' : ''}`}
                                onClick={() => setCategoryFilter('infrastructure')}
                            >
                                {t('techWorks')}
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${categoryFilter === 'social' ? 'active' : ''}`}
                                onClick={() => setCategoryFilter('social')}
                            >
                                {t('socialFinance')}
                            </button>
                        </div>

                        <div className="dept-search-box">
                            <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <label className="sr-only" htmlFor="dept-search">{t('searchDeptsLabel')}</label>
                            <input
                                id="dept-search"
                                type="search"
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                                placeholder={t('searchDeptsPlaceholder')}
                                className="dept-search-input"
                            />
                        </div>
                    </div>

                    {/* RESULTS COUNT */}
                    {query.trim() && (
                        <p style={{ margin: '-16px 0 20px', fontSize: '0.85rem', color: '#64748B' }}>
                            {t('showingOf')} {filtered.length} {t('ofDirectorates')} {departments.length} {t('directoratesUnit')} {t('forQuery')} &ldquo;{query}&rdquo;
                        </p>
                    )}

                    {/* CARDS GRID */}
                    {filtered.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noDeptsMatch')} &ldquo;{query}&rdquo;.
                            </p>
                            <button
                                type="button"
                                className="dept-filter-btn"
                                style={{ marginTop: 12 }}
                                onClick={() => { setQuery(''); setCategoryFilter('all'); }}
                            >
                                {t('clearSearchFilters')}
                            </button>
                        </div>
                    ) : (
                        <div className="dept-directory-grid">
                            {filtered.map((d) => {
                                const meta = getDepartmentMeta(d.public_name ?? '');
                                return (
                                    <Link key={d.id} href={`/${locale}/departments/${d.id}`} className="dept-card">
                                        <div className="dept-card-top">
                                            <div className="dept-card-icon" style={{ backgroundColor: meta.color }} aria-hidden="true">
                                                {meta.icon}
                                            </div>
                                            <span className="dept-card-badge">{t(meta.badgeKey)}</span>
                                        </div>

                                        <h3 className="dept-card-title">{d.public_name ?? `${t('deptFallbackName')} ${d.id}`}</h3>
                                        <p className="dept-card-summary">
                                            {d.public_summary ?? t('deptFallbackSummary')}
                                        </p>

                                        {meta.focusKeys.length > 0 && (
                                            <div className="dept-card-tags">
                                                {meta.focusKeys.map((tag, idx) => (
                                                    <span key={idx} className="dept-tag">{t(tag)}</span>
                                                ))}
                                            </div>
                                        )}

                                        <div className="dept-card-footer">
                                            <span>{t('exploreDirServices')}</span>
                                            <span aria-hidden="true">→</span>
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    )}

                    {/* CIVIC ACTION PORTAL STRIP */}
                    <div className="dept-portal-strip">
                        <div className="dept-portal-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <span>{t('helpdeskTitle')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/services`} className="dept-portal-card">
                                <h4>{t('allServicesCard')}</h4>
                                <p>{t('allServicesCardDesc')}</p>
                                <span>{t('browseCatalog')} →</span>
                            </Link>

                            <Link href={`/${locale}/officials`} className="dept-portal-card">
                                <h4>{t('officialsCard')}</h4>
                                <p>{t('officialsCardDesc')}</p>
                                <span>{t('viewLeadership')} →</span>
                            </Link>

                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>{t('civicEnquiries')}</h4>
                                <p>{t('civicEnquiriesDesc')}</p>
                                <span>{t('getInTouchCouncil')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

