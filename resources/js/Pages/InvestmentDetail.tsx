import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { SourcedText } from '../Components/public/SourceButtons';

type OpportunityDetail = {
    slug: string;
    title: string;
    sector: string | null;
    summary: string | null;
    description: string;
    location: string | null;
    opportunity_status: string;
    document: { slug: string; title: string } | null;
};

export default function InvestmentDetail({ opportunity }: { opportunity: OpportunityDetail }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={`${opportunity.title} | Investment Opportunity`} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>{t('investment')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{opportunity.title}</span>
                    </nav>
                    <span className="about-accent-eyebrow">
                        {opportunity.sector ? `${opportunity.sector.toUpperCase()} · ` : ''}MUTOKO RDC
                    </span>
                    <h1>{opportunity.title}</h1>
                    <p className="about-page-hero-subtitle">
                        {opportunity.summary ?? 'Strategic economic development and private sector partnership opportunity in Mutoko District.'}
                    </p>
                </div>
            </header>

            {/* MAIN CONTENT */}
            <section className="about-page-section" aria-labelledby="opportunity-detail">
                <div className="container">
                    <div className="about-detail-layout">
                        <div className="about-detail-main">
                            <article className="rates-guidance-card">
                                <h3 id="opportunity-detail">{t('investOverview')}</h3>
                                <div style={{ fontSize: '0.98rem', lineHeight: 1.7, color: '#334155' }}>
                                    <SourcedText text={opportunity.description} />
                                </div>

                                {opportunity.document && (
                                    <div style={{ marginTop: '24px', padding: '16px 20px', background: '#F8FAFC', border: '1px solid #E2E8F0', borderRadius: '10px' }}>
                                        <h4 style={{ margin: '0 0 6px', fontSize: '0.95rem', fontWeight: 800, color: 'var(--color-civic-navy)' }}>
                                            {t('supportingProspectus')}
                                        </h4>
                                        <p style={{ margin: 0, fontSize: '0.88rem' }}>
                                            <Link href={`/${locale}/documents/${opportunity.document.slug}`} className="about-page-text-link">
                                                {t('downloadAction')} {opportunity.document.title} <span aria-hidden="true">→</span>
                                            </Link>
                                        </p>
                                    </div>
                                )}
                            </article>
                        </div>

                        <aside className="about-summary-card" aria-label={t('oppParams')}>
                            <h3>{t('investmentSummary')}</h3>
                            <dl>
                                {opportunity.sector && (
                                    <>
                                        <dt>{t('sector')}</dt>
                                        <dd>{opportunity.sector}</dd>
                                    </>
                                )}
                                <dt>{t('statusLabel')}</dt>
                                <dd style={{ textTransform: 'capitalize' }}>{opportunity.opportunity_status}</dd>
                                {opportunity.location && (
                                    <>
                                        <dt>{t('locationLabel')}</dt>
                                        <dd>{opportunity.location}</dd>
                                    </>
                                )}
                                <dt>{t('jurisdiction')}</dt>
                                <dd>Mutoko RDC (Stand 366)</dd>
                            </dl>

                            <p style={{ marginTop: '20px' }}>
                                <Link
                                    href={`/${locale}/contact?context=investment:${opportunity.slug}`}
                                    className="rates-pop-btn-primary"
                                    style={{ width: '100%', justifyContent: 'center' }}
                                >
                                    <span>{t('submitEoi')}</span>
                                    <span aria-hidden="true">→</span>
                                </Link>
                            </p>
                        </aside>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

