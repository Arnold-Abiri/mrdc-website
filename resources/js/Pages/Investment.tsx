import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { EmptyState } from '../Components/public/PageHeader';

type Opportunity = {
    slug: string;
    title: string;
    sector: string | null;
    summary: string | null;
    location: string | null;
    opportunity_status: string;
};

const copy = {
    en: {
        intro: 'Mutoko sits 143km north-east of Harare on the Harare–Nyamapanda highway, 90km from Mozambique — a strategic trade gateway offering rich mineral deposits, high solar irradiation, prime horticulture, and serviced industrial stands.',
        detail: 'The Council facilitates expedited commercial and industrial stand allocations, environmental approvals with EMA, and collaborative structuring alongside the Zimbabwe Investment and Development Agency (ZIDA).',
    },
    sn: {
        intro: 'Mutoko iri makiromita 143 kubva kuHarare mumugwagwa weHarare–Nyamapanda, makiromita 90 kubva kuMozambique. Ine mikana yakasimba muzvicherwa zvakaita segiriniti, simba rezuva, nemabasa ekurima.',
        detail: 'Kanzuru inobatsira vanoda kuisa mabhizimisi kukurumidza kuwana nzvimbo dzekushandira, zvitupa zvezvakatipoteredza, nekushanda neZIDA.',
    },
    nd: {
        intro: 'IMutoko ikumakhilomitha ayi-143 kusuka eHarare emgwaqweni weHarare–Nyamapanda, lakumakhilomitha ayi-90 ukusuka eMozambique. Kulamathuba amahle ezimayini, emandleni elanga lezokulima.',
        detail: 'UMkhandlu uyancedisa abafuna ukuvula amabhizimusi ngokuthola izindawo zokusebenzela kanye lezivumelwano zeZIDA.',
    },
};

export default function Investment({ opportunities }: { opportunities: Opportunity[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [selectedSector, setSelectedSector] = useState<string>('all');

    const sectors = ['all', ...Array.from(new Set(opportunities.map((o) => o.sector).filter(Boolean))) as string[]];

    const filtered = selectedSector === 'all'
        ? opportunities
        : opportunities.filter((o) => o.sector?.toLowerCase() === selectedSector.toLowerCase());

    return (
        <PublicLayout>
            <Head title={t('invHeadTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>{t('opportunities')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('invCrumb')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('invEyebrow')}</span>
                    <h1>{t('invTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {copy[locale].intro}
                    </p>
                </div>
            </header>

            {/* ECONOMIC PROFILE STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="District economic statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">428,916 ha</div>
                                <div className="about-stat-label">{t('statLandArea')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">143 km</div>
                                <div className="about-stat-label">{t('statFromHarare')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">90 km</div>
                                <div className="about-stat-label">{t('statToMoz')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('stat29Wards')}</div>
                                <div className="about-stat-label">{t('statAgriMining')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN CONTENT */}
            <section className="about-page-section" aria-labelledby="investment-opps">
                <div className="container">

                    {/* ZIDA & COUNCIL FACILITATION BANNER */}
                    <div className="invest-zida-banner">
                        <div className="invest-zida-info">
                            <h3>{t('zidaTitle')}</h3>
                            <p>
                                {copy[locale].detail} {t('zidaTail')}
                            </p>
                        </div>
                        <div style={{ display: 'flex', gap: '10px', flexShrink: 0 }}>
                            <Link href={`/${locale}/contact?context=investor-desk`} className="rates-pop-btn-primary">
                                <span>{t('contactInvestorDesk')}</span>
                                <span aria-hidden="true">→</span>
                            </Link>
                        </div>
                    </div>

                    {/* SECTOR FILTER TABS */}
                    <span className="about-accent-eyebrow">{t('sectorsEyebrow')}</span>
                    <h2 id="investment-opps">{t('sectorsTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('sectorsIntro')}
                    </p>

                    {sectors.length > 1 && (
                        <div className="rates-filter-tabs" role="tablist" aria-label={t('sectorFilters')}>
                            {sectors.map((sec) => (
                                <button
                                    key={sec}
                                    type="button"
                                    role="tab"
                                    aria-selected={selectedSector === sec}
                                    className={`rates-tab-btn ${selectedSector === sec ? 'active' : ''}`}
                                    onClick={() => setSelectedSector(sec)}
                                >
                                    {sec === 'all' ? t('allOpportunities') : sec}
                                </button>
                            ))}
                        </div>
                    )}

                    {filtered.length === 0 ? (
                        <EmptyState title={t('noOpportunities')} text={t('noInvestmentDesc')} />
                    ) : (
                        <div className="vacancies-modern-grid">
                            {filtered.map((item) => (
                                <article key={item.slug} className="vacancy-card-modern">
                                    <div className="vacancy-top-pills">
                                        <span className="vacancy-pill-grade">
                                            {item.sector ?? t('generalSector')}
                                        </span>
                                        <span className="vacancy-pill-type" style={{ background: '#ECFDF5', color: '#047857' }}>
                                            {item.opportunity_status}
                                        </span>
                                    </div>

                                    <h3 className="vacancy-title-modern">
                                        <Link href={`/${locale}/investment/${item.slug}`}>{item.title}</Link>
                                    </h3>

                                    {item.location && (
                                        <div className="vacancy-meta-row">
                                            <span className="vacancy-meta-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                                <span>{item.location}</span>
                                            </span>
                                        </div>
                                    )}

                                    {item.summary && (
                                        <p className="vacancy-summary-modern">{item.summary}</p>
                                    )}

                                    <div className="vacancy-card-footer-modern">
                                        <span style={{ fontSize: '0.8rem', color: '#64748B', fontWeight: 600 }}>
                                            {t('councilSupported')}
                                        </span>
                                        <Link href={`/${locale}/investment/${item.slug}`} className="vacancy-view-cta">
                                            <span>{t('exploreProposal')}</span>
                                            <span aria-hidden="true">→</span>
                                        </Link>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}

