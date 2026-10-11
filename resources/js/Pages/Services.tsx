import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';

type Service = { slug: string; name: string; summary: string | null };

const CORE_PORTFOLIOS: { titleKey: TranslationKey; categoryKey: TranslationKey; textKey: TranslationKey; icon: React.ReactNode }[] = [
    {
        titleKey: 'pfRoadsTitle',
        categoryKey: 'pfRoadsCat',
        textKey: 'pfRoadsText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
        ),
    },
    {
        titleKey: 'pfWaterTitle',
        categoryKey: 'pfWaterCat',
        textKey: 'pfWaterText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
            </svg>
        ),
    },
    {
        titleKey: 'pfHealthTitle',
        categoryKey: 'pfHealthCat',
        textKey: 'pfHealthText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        ),
    },
    {
        titleKey: 'pfBuildingTitle',
        categoryKey: 'pfBuildingCat',
        textKey: 'pfBuildingText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                <line x1="8" y1="2" x2="8" y2="18" />
                <line x1="16" y1="6" x2="16" y2="22" />
            </svg>
        ),
    },
    {
        titleKey: 'pfPropertyTitle',
        categoryKey: 'pfPropertyCat',
        textKey: 'pfPropertyText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M3 9h18M9 21V9" />
            </svg>
        ),
    },
    {
        titleKey: 'pfWelfareTitle',
        categoryKey: 'pfWelfareCat',
        textKey: 'pfWelfareText',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
        ),
    },
];

export default function Services({ services }: { services: Service[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [search, setSearch] = useState('');

    const filteredServices = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return services;
        return services.filter((s) => `${s.name} ${s.summary ?? ''}`.toLowerCase().includes(q));
    }, [services, search]);

    return (
        <PublicLayout>
            <Head title={t('servicesHeadTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('services')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('servicesEyebrow')}</span>
                    <h1>{t('servicesTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('servicesSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Service delivery statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">{t('statWardsServed')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">84+</div>
                                <div className="about-stat-label">{t('statSchools')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statServiceDesk')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statTransparent')}</div>
                                <div className="about-stat-label">{t('statClearFees')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* CORE PORTFOLIOS SECTION */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="portfolios-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('pfEyebrow')}</span>
                    <h2 id="portfolios-heading">{t('pfHeading')}</h2>
                    <p className="about-page-section-intro">
                        {t('pfIntro')}
                    </p>
                    <div className="services-catalog-grid">
                        {CORE_PORTFOLIOS.map((item) => (
                            <div key={item.titleKey} className="service-item-card">
                                <span className="service-item-badge">{t(item.categoryKey)}</span>
                                <h3 className="service-item-title">{t(item.titleKey)}</h3>
                                <p className="service-item-desc">{t(item.textKey)}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* APPROVED SERVICE GUIDES */}
            <section className="about-page-section" aria-labelledby="guides-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('guidesEyebrow')}</span>
                    <h2 id="guides-heading">{t('guidesHeading')}</h2>
                    <p className="about-page-section-intro">
                        {t('guidesIntro')}
                    </p>

                    {/* SEARCH BOX */}
                    <div className="dept-search-box" style={{ maxWidth: 360, margin: '20px 0 28px' }}>
                        <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <label className="sr-only" htmlFor="service-search">{t('searchServicesLabel')}</label>
                        <input
                            id="service-search"
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('searchServicesPlaceholder')}
                            className="dept-search-input"
                        />
                    </div>

                    {filteredServices.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noGuidesMatch')} &ldquo;{search}&rdquo;. {t('noGuidesDesc')}
                            </p>
                            {search && (
                                <button
                                    type="button"
                                    className="dept-filter-btn"
                                    style={{ marginTop: 12 }}
                                    onClick={() => setSearch('')}
                                >
                                    {t('wardClearSearch')}
                                </button>
                            )}
                        </div>
                    ) : (
                        <div className="services-catalog-grid">
                            {filteredServices.map((service) => (
                                <Link
                                    key={service.slug}
                                    href={`/${locale}/services/${service.slug}`}
                                    className="service-item-card"
                                >
                                    <span className="service-item-badge">{t('officialGuide')}</span>
                                    <h3 className="service-item-title">{service.name}</h3>
                                    <p className="service-item-desc">
                                        {service.summary ?? t('guideFallback')}
                                    </p>
                                    <div className="service-item-cta">
                                        <span>{t('viewProcedure')}</span>
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
                            <span>{t('helpdeskTitle')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/rates`} className="dept-portal-card">
                                <h4>{t('tariffsCard')}</h4>
                                <p>{t('tariffsCardDesc')}</p>
                                <span>{t('checkRates')} →</span>
                            </Link>

                            <Link href={`/${locale}/projects`} className="dept-portal-card">
                                <h4>{t('infraProjectsCard')}</h4>
                                <p>{t('infraProjectsCardDesc')}</p>
                                <span>{t('viewProjects')} →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>{t('feedbackCard')}</h4>
                                <p>{t('feedbackCardDesc')}</p>
                                <span>{t('sendMessageCard')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

