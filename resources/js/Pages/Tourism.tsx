import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type TourismBlock = { type: string; text: string; url?: string | null };
type TourismPage = { slug: string; title: string; summary: string | null; blocks: TourismBlock[] } | null;
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null };

export default function Tourism({ page, opportunities }: { page?: TourismPage; opportunities: Opportunity[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={page?.title ?? t('tourHeadTitle')} />

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
                    <h1>{page?.title ?? t('tourTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {page?.summary}
                    </p>
                </div>
            </header>

            <section className="about-page-section">
                <div className="container">
                    {page ? page.blocks.map((block, index) => {
                        if (block.type === 'heading') return <h2 key={index}>{block.text}</h2>;
                        if (block.type === 'paragraph') return <div key={index} className="about-page-copy"><p>{block.text}</p></div>;
                        if (block.type === 'cta' && block.url?.startsWith('/') && !block.url.startsWith('//')) {
                            const url = /^\/(en|sn|nd)(\/|$)/.test(block.url) ? block.url : `/${locale}${block.url}`;
                            return <p key={index}><Link className="about-page-text-link" href={url}>{block.text}</Link></p>;
                        }
                        return null;
                    }) : <p role="status">Tourism information is not currently published.</p>}

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

