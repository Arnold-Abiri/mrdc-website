import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type Block = { type: 'heading' | 'paragraph' | 'cta'; text: string; url?: string };
type Page = {
    title: string;
    summary: string | null;
    blocks: Block[];
    seo_title: string | null;
    meta_description: string | null;
    is_review_content: boolean;
    content_pending?: boolean;
};
type Official = { slug: string; name: string; title: string; photo_url: string | null };
type Opportunity = { slug: string; title: string; summary: string | null; sector?: string | null };
type Directory = {
    ward_count?: number;
    officials: Official[];
    investment: Opportunity[];
    pages: string[];
};

function sections(blocks: Block[]): { heading: string; paragraphs: string[] }[] {
    const result: { heading: string; paragraphs: string[] }[] = [];
    for (const block of blocks) {
        if (block.type === 'heading') result.push({ heading: block.text, paragraphs: [] });
        if (block.type === 'paragraph' && result.length) result[result.length - 1].paragraphs.push(block.text);
    }
    return result;
}

function link(locale: string, path: string): string {
    return `/${locale}${path}`;
}

export default function About({
    page,
    about,
    preview = false,
}: {
    page: Page;
    about?: Directory | null;
    preview?: boolean;
}) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const content = sections(page.blocks);
    const find = (...words: string[]) =>
        content.filter(section => words.some(word => section.heading.toLowerCase().includes(word)));
    const render = (items: typeof content) =>
        items.map((item, index) => (
            <div key={`${item.heading}-${index}`} className="about-page-copy">
                <h3>{item.heading}</h3>
                {item.paragraphs.map((paragraph, paragraphIndex) => (
                    <p key={paragraphIndex}>{paragraph}</p>
                ))}
            </div>
        ));

    const directory = about ?? { ward_count: 29, officials: [], investment: [], pages: [] };
    const wardCount = directory.ward_count ?? 29;

    return (
        <PublicLayout>
            <Head title={page.seo_title || page.title}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
                {preview && <meta name="robots" content="noindex, nofollow" />}
            </Head>

            {/* 1. HERO HEADER */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <a href={`/${locale}`}>{t('home')}</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('aboutCrumb')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('aboutEyebrow')}</span>
                    <h1>{page.title}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('aboutHeroSub')}
                    </p>
                </div>
            </header>

            {/* In-Page Quick Navigation */}
            <nav className="about-subnav-bar" aria-label={t('pageSections')}>
                <div className="container">
                    <div className="about-subnav-list">
                        <a href="#who-we-are" className="about-subnav-link">{t('subOverview')}</a>
                        <a href="#vision-mission" className="about-subnav-link">{t('subVision')}</a>
                        <a href="#our-mandate" className="about-subnav-link">{t('subMandate')}</a>
                        <a href="#our-history" className="about-subnav-link">{t('subHistory')}</a>
                        <a href="#leadership" className="about-subnav-link">{t('subGovernance')}</a>
                        <a href="#district" className="about-subnav-link">{t('subWards')}</a>
                        <a href="#priorities" className="about-subnav-link">{t('subFocus')}</a>
                    </div>
                </div>
            </nav>

            {/* Review / Pending notice */}
            {page.is_review_content && (
                <div className="container about-page-review" role="note">
                    <strong>{t('stakeholderReview')}</strong> {t('stakeholderApproval')}
                </div>
            )}
            {page.content_pending && (
                <div className="container about-page-review" role="note">
                    {t('contentUnderReview')}
                </div>
            )}

            {/* 2. WHO WE ARE */}
            <section className="about-page-section" aria-labelledby="who-we-are">
                <div className="container">
                    <div className="about-whoweare-grid">
                        <div className="about-whoweare-copy">
                            <span className="about-accent-eyebrow">{t('whoWeAre')}</span>
                            <h2 id="who-we-are">{t('aboutWhoTitle')}</h2>
                            {page.summary ? (
                                <p className="about-page-lead">{page.summary}</p>
                            ) : (
                                <>
                                    <p>
                                        Mutoko Rural District Council is the local government authority established under the Rural District Councils Act [Chapter 29:13], legally mandated to govern, administer, and spearhead socioeconomic development across Mutoko District in Mashonaland East Province, Zimbabwe.
                                    </p>
                                    <p>
                                        Operating from the administrative headquarters at Stand 366 Mutoko Centre, the Council works collaboratively with traditional leaders, ward development committees, Government line ministries, and development partners to deliver public infrastructure, primary health, educational facilities, and natural resource stewardship.
                                    </p>
                                </>
                            )}
                            <div className="about-statutory-box" role="region" aria-label={t('statRegion')}>
                                <svg className="about-statutory-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                                </svg>
                                <div className="about-statutory-content">
                                    <strong>{t('statAuthority')}</strong> {t('statAuthorityText')}
                                </div>
                            </div>
                            {render(find('location'))}
                        </div>

                        <figure className="about-whoweare-figure">
                            <img
                                src="/images/about/landscape-hills.webp"
                                alt="Scenic granite hills and natural savanna landscape across Mutoko District"
                                className="about-whoweare-img"
                                width="1672"
                                height="941"
                                loading="lazy"
                            />
                            <figcaption className="about-whoweare-caption">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <span>{t('districtCaption')}</span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </section>

            {/* 3. KEY STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label={t('keyStats')}>
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">{wardCount}</div>
                                <div className="about-stat-label">{t('statElectoralWards')}</div>
                                <p className="about-stat-desc">{t('statDescWards')}</p>
                            </div>
                        </div>

                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 1L2 6v2h20V6L12 1zm-7 9v8h3v-8H5zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zM2 20v2h20v-2H2z" />
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">1995</div>
                                <div className="about-stat-label">{t('statAmalgamation')}</div>
                                <p className="about-stat-desc">{t('statAmalgamationDesc')}</p>
                            </div>
                        </div>

                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">Stand 366</div>
                                <div className="about-stat-label">{t('statCivicCentre')}</div>
                                <p className="about-stat-desc">{t('statCivicDesc')}</p>
                            </div>
                        </div>

                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">Cap 29:13</div>
                                <div className="about-stat-label">{t('statEnablingAct')}</div>
                                <p className="about-stat-desc">{t('statEnablingDesc')}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 4. VISION & MISSION */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="vision-mission">
                <div className="container">
                    <div className="about-vision-grid">
                        <div className="about-vision-card">
                            <div className="about-vision-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="vision-mission">{t('ourVision')}</h3>
                                <p>{t('visionText')}</p>
                            </div>
                        </div>

                        <div className="about-vision-card">
                            <div className="about-vision-icon-wrap about-vision-icon-target" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3>{t('ourMission')}</h3>
                                <p>{t('missionText')}</p>
                            </div>
                        </div>
                    </div>
                    {render(find('values'))}
                </div>
            </section>

            {/* 5. OUR MANDATE */}
            <section className="about-page-section" aria-labelledby="our-mandate">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('mandateEyebrow')}</span>
                    <h2 id="our-mandate">{t('mandateTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('mandateIntro')}
                    </p>

                    <div className="about-mandate-grid">
                        <div className="about-mandate-card">
                            <div className="about-mandate-icon services" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                </svg>
                            </div>
                            <h3>{t('mandHealth')}</h3>
                            <p>{t('mandHealthDesc')}</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon infra" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 12h-2V8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2v4H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z" />
                                </svg>
                            </div>
                            <h3>{t('mandInfra')}</h3>
                            <p>{t('mandInfraDesc')}</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon env" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                </svg>
                            </div>
                            <h3>{t('mandEnv')}</h3>
                            <p>{t('mandEnvDesc')}</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon participate" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>{t('mandWard')}</h3>
                            <p>{t('mandWardDesc')}</p>
                        </div>
                    </div>

                    {render(find('mandate'))}
                    {directory.pages.includes('mandate') && (
                        <div className="about-mandate-cta-wrap">
                            <div className="about-mandate-cta-content">
                                <div className="about-mandate-cta-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="16" y1="13" x2="8" y2="13" />
                                        <line x1="16" y1="17" x2="8" y2="17" />
                                        <polyline points="10 9 9 9 8 9" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 className="about-mandate-cta-title">{t('mandateDocTitle')}</h4>
                                    <p className="about-mandate-cta-desc">{t('mandateDocDesc')}</p>
                                </div>
                            </div>
                            <a className="btn-mandate-action" href={link(locale, '/pages/mandate')}>
                                <span>{t('readMandate')}</span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    )}
                </div>
            </section>

            {/* 6. OUR HISTORY */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="our-history">
                <div className="container">
                    <div className="about-history-layout">
                        <div className="about-history-img-wrap">
                            <img
                                src="/images/about/history-landscape.webp"
                                alt={t('historyAlt')}
                                width="1448"
                                height="1086"
                                loading="lazy"
                            />
                        </div>

                        <div>
                            <span className="about-accent-eyebrow">{t('historyEyebrow')}</span>
                            <h2 id="our-history">{t('historyTitle')}</h2>

                            <div className="about-timeline-tree">
                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1902</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        {t('hist1902')}
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1987</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        {t('hist1987')}
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1994</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        {t('hist1994')}
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1995</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        {t('hist1995')}
                                    </div>
                                </div>
                            </div>

                            {find('history').length > 0 && (
                                <div className="about-history-narrative">
                                    {render(find('history'))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {/* 7. LEADERSHIP & GOVERNANCE */}
            <section className="about-page-section" aria-labelledby="leadership">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('govEyebrow')}</span>
                    <h2 id="leadership">{t('govTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('govIntro')}
                    </p>

                    <div className="about-gov-grid">
                        <a href={link(locale, '/council/chairperson')} className="about-gov-card">
                            <span className="about-gov-badge about-gov-badge-pol">{t('polWing')}</span>
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <h3>{t('govChair')}</h3>
                            <p>{t('govChairDesc')}</p>
                            <span className="about-gov-arrow">{t('councilLeadershipLink')} <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/wards')} className="about-gov-card">
                            <span className="about-gov-badge about-gov-badge-pol">{t('polWing')}</span>
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>29 {t('govCouncillors')}</h3>
                            <p>{t('govCouncillorsDesc')}</p>
                            <span className="about-gov-arrow">{t('wardDirLink')} <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/officials')} className="about-gov-card">
                            <span className="about-gov-badge about-gov-badge-adm">{t('execSec')}</span>
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                            </div>
                            <h3>{t('govCeo')}</h3>
                            <p>{t('govCeoDesc')}</p>
                            <span className="about-gov-arrow">{t('execOfficeLink')} <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/departments')} className="about-gov-card">
                            <span className="about-gov-badge about-gov-badge-adm">{t('execSec')}</span>
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 1L2 6v2h20V6L12 1zm-7 9v8h3v-8H5zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zM2 20v2h20v-2H2z" />
                                </svg>
                            </div>
                            <h3>{t('govDirs')}</h3>
                            <p>{t('govDirsDesc')}</p>
                            <span className="about-gov-arrow">{t('deptsLink')} <span aria-hidden="true">→</span></span>
                        </a>
                    </div>

                    {directory.officials.length > 0 && (
                        <div style={{ marginTop: '40px', paddingTop: '32px', borderTop: '1px solid var(--color-border)' }}>
                            <div className="about-officials-header">
                                <div>
                                    <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 4px' }}>{t('execOfficers')}</h3>
                                    <p style={{ margin: 0, fontSize: '0.88rem', color: 'var(--color-muted)' }}>{t('execOfficersDesc')}</p>
                                </div>
                                <a href={link(locale, '/officials')} className="about-officials-link">
                                    <span>{t('fullDir')}</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                            <div className="about-officials-grid">
                                {directory.officials.map(official => {
                                    const href = official.title.toLowerCase().includes('chairperson')
                                        ? link(locale, '/council/chairperson')
                                        : link(locale, `/officials/${official.slug}`);
                                    return (
                                        <a href={href} className="about-official-card" key={official.slug} style={{ textDecoration: 'none', color: 'inherit' }}>
                                            <div className="about-official-photo-wrap">
                                                {official.photo_url ? (
                                                    <img className="about-official-photo" src={official.photo_url} alt={official.name} loading="lazy" />
                                                ) : (
                                                    <div className="about-official-photo-placeholder" aria-hidden="true">
                                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                        </svg>
                                                    </div>
                                                )}
                                            </div>
                                            <div className="about-official-info">
                                                <div className="about-official-name">{official.name}</div>
                                                <div className="about-official-title">{official.title}</div>
                                            </div>
                                        </a>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            {/* 8. OUR DISTRICT & COMMUNITIES */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="district">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('districtEyebrow')}</span>
                    <h2 id="district">{t('districtTitle')}</h2>

                    <div className="about-district-layout">
                        <div className="about-district-info">
                            <p>
                                {t('districtDesc')} {wardCount} {t('districtDescTail')}
                            </p>
                            <a href={link(locale, '/wards')} className="btn-ward-dir">
                                <span>{t('viewWardDir')}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            {render(find('district'))}
                        </div>

                        {/* District Graphic Map Box */}
                        <div className="about-map-box">
                            <div className="about-map-compass" aria-hidden="true">
                                <span>▲</span>
                                <span>N</span>
                            </div>
                            <img
                                src="/images/about/district-map.webp?v=2"
                                alt={t('mapAlt')}
                                className="about-map-svg"
                                width="660"
                                height="680"
                                loading="lazy"
                            />
                        </div>

                        <div className="about-district-photo">
                            <img
                                src="/images/about/district-communities.webp?v=2"
                                alt={t('photoAlt')}
                                width="1200"
                                height="900"
                                loading="lazy"
                            />
                            <div className="about-district-photo-caption">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" style={{ width: '13px', height: '13px', color: '#FBBF24' }}>
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <span>{t('photoCaption')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 9. STRATEGIC PRIORITIES */}
            <section className="about-page-section" aria-labelledby="priorities">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('stratEyebrow')}</span>
                    <h2 id="priorities">{t('stratTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('stratIntro')}
                    </p>

                    <div className="about-focus-grid">
                        <div className="about-focus-card">
                            <div className="about-focus-icon p1" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <h3>{t('focusService')}</h3>
                            <p>{t('focusServiceDesc')}</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p2" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z" />
                                </svg>
                            </div>
                            <h3>{t('focusTransparent')}</h3>
                            <p>{t('focusTransparentDesc')}</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p3" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>{t('focusParticipate')}</h3>
                            <p>{t('focusParticipateDesc')}</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p4" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                </svg>
                            </div>
                            <h3>{t('focusDevelop')}</h3>
                            <p>{t('focusDevelopDesc')}</p>
                        </div>
                    </div>

                    {render(find('strategic', 'priorities'))}
                </div>
            </section>

            {/* 10. INVESTMENT & ECONOMIC OPPORTUNITIES */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="investment">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('investAboutEyebrow')}</span>
                    <h2 id="investment">{t('investAboutTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('investAboutIntro')}
                    </p>

                    <div className="about-economic-grid">
                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-agriculture.webp?v=2"
                                alt={t('econAgriAlt')}
                                className="about-economic-thumb"
                                width="512"
                                height="512"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                    </svg>
                                    <h3>{t('econAgri')}</h3>
                                </div>
                                <p>{t('econAgriDesc')}</p>
                                <span className="about-economic-link">{t('exploreOpps')} <span aria-hidden="true">→</span></span>
                            </div>
                        </a>

                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-mining.webp?v=2"
                                alt={t('econMiningAlt')}
                                className="about-economic-thumb"
                                width="512"
                                height="512"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                    </svg>
                                    <h3>{t('econMining')}</h3>
                                </div>
                                <p>{t('econMiningDesc')}</p>
                                <span className="about-economic-link">{t('exploreOpps')} <span aria-hidden="true">→</span></span>
                            </div>
                        </a>

                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-solar.webp?v=2"
                                alt={t('econSolarAlt')}
                                className="about-economic-thumb"
                                width="512"
                                height="512"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
                                    </svg>
                                    <h3>{t('econSolar')}</h3>
                                </div>
                                <p>{t('econSolarDesc')}</p>
                                <span className="about-economic-link">{t('exploreOpps')} <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </div>

                    {directory.investment.length > 0 && (
                        <div style={{ marginTop: '28px', paddingTop: '24px', borderTop: '1px solid var(--color-border)' }}>
                            <h3 style={{ fontSize: '1.05rem', fontWeight: 700, color: 'var(--color-civic-navy)', marginBottom: '14px' }}>{t('featuredInvest')}</h3>
                            <div className="about-opportunities-grid">
                                {directory.investment.map(item => (
                                    <a className="about-opportunity-card" href={link(locale, `/investment/${item.slug}`)} key={item.slug}>
                                        <div className="about-opportunity-title">{item.title}</div>
                                        {item.summary && <div className="about-opportunity-summary">{item.summary}</div>}
                                        <span className="about-opportunity-link">{t('viewProspectus')} <span aria-hidden="true">→</span></span>
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            {/* Quick District Portals Strip */}
            <div className="about-resources-strip" role="navigation" aria-label={t('districtPortals')}>
                <div className="container">
                    <div className="about-resources-flex">
                        <span className="about-resources-label">{t('portalsLabel')}</span>
                        <div className="about-resources-links">
                            <a href={link(locale, '/wards')} className="about-resource-btn">
                                <span>{t('portalWardsDir')}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href={link(locale, '/departments')} className="about-resource-btn">
                                <span>{t('portalDepts')}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href={link(locale, '/officials')} className="about-resource-btn">
                                <span>{t('portalExec')}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href={link(locale, '/documents')} className="about-resource-btn">
                                <span>{t('portalDocs')}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
