import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';

type TourismBlock = { type: string; text: string; url?: string | null };
type TourismPage = { slug: string; title: string; summary: string | null; blocks: TourismBlock[] } | null;
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null };

const ATTRACTIONS: { titleKey: TranslationKey; tagKey: TranslationKey; descKey: TranslationKey; locationKey: TranslationKey; image: string; imageAltKey: TranslationKey }[] = [
    {
        titleKey: 'attrRuinsTitle',
        tagKey: 'attrRuinsTag',
        descKey: 'attrRuinsDesc',
        locationKey: 'attrRuinsLoc',
        image: '/images/tourism/mutoko-ruins.webp',
        imageAltKey: 'attrRuinsAlt',
    },
    {
        titleKey: 'attrRucheraTitle',
        tagKey: 'attrRucheraTag',
        descKey: 'attrRucheraDesc',
        locationKey: 'attrRucheraLoc',
        image: '/images/tourism/ruchera-cave-art.webp',
        imageAltKey: 'attrRucheraAlt',
    },
    {
        titleKey: 'attrGraniteTitle',
        tagKey: 'attrGraniteTag',
        descKey: 'attrGraniteDesc',
        locationKey: 'attrGraniteLoc',
        image: '/images/tourism/balancing-rocks.webp',
        imageAltKey: 'attrGraniteAlt',
    },
    {
        titleKey: 'attrNyadireTitle',
        tagKey: 'attrNyadireTag',
        descKey: 'attrNyadireDesc',
        locationKey: 'attrNyadireLoc',
        image: '/images/about/landscape-hills.webp',
        imageAltKey: 'attrNyadireAlt',
    },
];

export default function Tourism({ page, opportunities }: { page: TourismPage; opportunities: Opportunity[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={t('tourHeadTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>{t('opportunities')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('tourCrumb')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('tourEyebrow')}</span>
                    <h1>{t('tourTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('tourSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Tourism overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statRuins')}</div>
                                <div className="about-stat-label">{t('statHeritageMonument')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statRuchera')}</div>
                                <div className="about-stat-label">{t('statCavePaintings')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('stat143')}</div>
                                <div className="about-stat-label">{t('stat2Hour')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statGranite')}</div>
                                <div className="about-stat-label">{t('statKopjes')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN ATTRACTIONS SECTION */}
            <section className="about-page-section" aria-labelledby="heritage-attractions">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('poiEyebrow')}</span>
                    <h2 id="heritage-attractions">{t('poiTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('poiIntro')}
                    </p>

                    <div className="tourism-attractions-grid">
                        {ATTRACTIONS.map((attraction, index) => (
                            <article key={index} className="tourism-card">
                                <div className="tourism-attraction-media">
                                    <img src={attraction.image} alt={t(attraction.imageAltKey)} loading="lazy" decoding="async" />
                                </div>
                                <div className="tourism-card-body">
                                    <span className="tourism-card-tag">{t(attraction.tagKey)}</span>
                                    <h3 className="tourism-card-title">{t(attraction.titleKey)}</h3>
                                    <div className="vacancy-meta-row" style={{ marginBottom: '10px' }}>
                                        <span className="vacancy-meta-item">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span>{t(attraction.locationKey)}</span>
                                        </span>
                                    </div>
                                    <p className="tourism-card-desc">{t(attraction.descKey)}</p>
                                </div>
                            </article>
                        ))}
                    </div>

                    {/* TRAVEL LOGISTICS GUIDE */}
                    <div className="rates-guidance-card">
                        <h3>{t('guideTitle')}</h3>
                        <p>
                            <strong>{t('guideGetHere')}</strong> {t('guideGetHereText')}
                        </p>
                        <p>
                            <strong>{t('guideAccomm')}</strong> {t('guideAccommText')}
                        </p>
                        <p style={{ margin: 0, fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                            {t('guideTours')}
                        </p>
                    </div>

                    {/* CMS EDITORIAL OR INVESTMENT OPPORTUNITIES */}
                    {opportunities.length > 0 && (
                        <div style={{ marginTop: '36px' }}>
                            <span className="about-accent-eyebrow">{t('investHosp')}</span>
                            <h3 style={{ fontSize: '1.35rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                                {t('investHospTitle')}
                            </h3>
                            <p style={{ color: '#64748B', fontSize: '0.92rem', marginBottom: 20 }}>
                                {t('investHospDesc')}
                            </p>

                            <div className="vacancies-modern-grid">
                                {opportunities.map((item) => (
                                    <article key={item.slug} className="vacancy-card-modern">
                                        <div className="vacancy-top-pills">
                                            <span className="vacancy-pill-grade">
                                                {item.sector ?? t('tourSectorFallback')}
                                            </span>
                                        </div>
                                        <h4 className="vacancy-title-modern">
                                            <Link href={`/${locale}/investment/${item.slug}`}>{item.title}</Link>
                                        </h4>
                                        {item.summary && (
                                            <p className="vacancy-summary-modern">{item.summary}</p>
                                        )}
                                        <div className="vacancy-card-footer-modern">
                                            <Link href={`/${locale}/investment/${item.slug}`} className="vacancy-view-cta">
                                                <span>{t('viewInvestScope')}</span>
                                                <span aria-hidden="true">→</span>
                                            </Link>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}

