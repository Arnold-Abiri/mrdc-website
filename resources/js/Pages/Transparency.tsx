import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
import { formatDate } from '../formatDate';
import { SourcedText } from '../Components/public/SourceButtons';

type FinancialDocument = {
    slug: string;
    title: string;
    description: string | null;
    category: string;
    published_at: string | null;
    reference_date: string | null;
    download_count: number;
};

const categoryLabels: Record<string, TranslationKey> = {
    budget: 'finBudget',
    financial_statement: 'finStatement',
    financial_report: 'finReport',
    procurement_plan: 'finProcPlan',
    awards_register: 'finAwards',
};

export default function Transparency({
    documents,
    categories,
    years,
    filters,
}: {
    documents: FinancialDocument[];
    categories: string[];
    years: number[];
    filters: { category: string | null; year: string | null };
}) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={t('finHeadTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('wardsCrumbCouncil')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('transparency')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('finEyebrow')}</span>
                    <h1>{t('finTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('finSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS / KPI RIBBON */}
            <section className="about-stats-ribbon" aria-label="Financial disclosure overview">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statAudited')}</div>
                                <div className="about-stat-label">{t('statOversight')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statDevolution')}</div>
                                <div className="about-stat-label">{t('statCapitalGrant')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statPfma')}</div>
                                <div className="about-stat-label">{t('statTreasury')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{documents.length}</div>
                                <div className="about-stat-label">{t('statPublishedDisc')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN DISCLOSURE SECTION */}
            <section className="about-page-section" aria-labelledby="transparency-docs">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('fiscalEyebrow')}</span>
                    <h2 id="transparency-docs">{t('fiscalTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('fiscalIntro')}
                    </p>

                    {/* PILLARS STRIP */}
                    <div className="transparency-kpi-grid">
                        <div className="transparency-kpi-card">
                            <div className="transparency-kpi-icon" aria-hidden="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                            <div>
                                <div className="transparency-kpi-label">{t('kpiBudget')}</div>
                                <div className="transparency-kpi-value">{t('kpiBudgetDesc')}</div>
                            </div>
                        </div>

                        <div className="transparency-kpi-card">
                            <div className="transparency-kpi-icon" aria-hidden="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                            </div>
                            <div>
                                <div className="transparency-kpi-label">{t('kpiAudit')}</div>
                                <div className="transparency-kpi-value">{t('kpiAuditDesc')}</div>
                            </div>
                        </div>

                        <div className="transparency-kpi-card">
                            <div className="transparency-kpi-icon" aria-hidden="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                </svg>
                            </div>
                            <div>
                                <div className="transparency-kpi-label">{t('kpiPraz')}</div>
                                <div className="transparency-kpi-value">{t('kpiPrazDesc')}</div>
                            </div>
                        </div>
                    </div>

                    {/* FILTER FORM */}
                    <form method="get" action={`/${locale}/transparency`} className="transparency-filter-bar">
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, flexGrow: 1, minWidth: 220 }}>
                            <label htmlFor="transparency-category" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                {t('docCategory')}
                            </label>
                            <select
                                id="transparency-category"
                                name="category"
                                defaultValue={filters.category ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">{t('allFinCats')}</option>
                                {categories.map((category) => (
                                    <option key={category} value={category}>
                                        {categoryLabels[category] ? t(categoryLabels[category]) : category}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, width: 140 }}>
                            <label htmlFor="transparency-year" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                {t('fiscalYear')}
                            </label>
                            <select
                                id="transparency-year"
                                name="year"
                                defaultValue={filters.year ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">{t('allYears')}</option>
                                {years.map((year) => (
                                    <option key={year} value={year}>{year}</option>
                                ))}
                            </select>
                        </div>

                        <div style={{ display: 'flex', gap: 8, alignSelf: 'flex-end' }}>
                            <button
                                type="submit"
                                className="btn-mandate-action"
                                style={{ padding: '9px 18px', fontSize: '0.86rem' }}
                            >
                                {t('filterReports')}
                            </button>
                            {(filters.category || filters.year) && (
                                <Link
                                    href={`/${locale}/transparency`}
                                    className="dept-filter-btn"
                                    style={{ padding: '9px 14px', textDecoration: 'none' }}
                                >
                                    {t('resetLink')}
                                </Link>
                            )}
                        </div>
                    </form>

                    {/* DOCUMENTS LIST */}
                    {documents.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noFinMatch')}
                            </p>
                        </div>
                    ) : (
                        <div className="dept-directory-grid">
                            {documents.map((doc) => (
                                <article key={doc.slug} className="transparency-doc-card">
                                    <div className="transparency-doc-top">
                                        <span className="transparency-file-pill">{t('schedOfficialPdf')}</span>
                                        <span style={{ fontSize: '0.78rem', color: '#64748B', fontWeight: 600 }}>
                                            {doc.reference_date ? formatDate(doc.reference_date, locale) : t('docRecent')}
                                        </span>
                                    </div>

                                    <h3 className="transparency-doc-title">
                                        <Link href={`/${locale}/documents/${doc.slug}`}>{doc.title}</Link>
                                    </h3>

                                    <div className="transparency-doc-desc">
                                        <SourcedText text={doc.description ?? t('finFallback')} />
                                    </div>

                                    <div className="transparency-doc-footer">
                                        <span>
                                            {categoryLabels[doc.category] ? t(categoryLabels[doc.category]) : doc.category}
                                        </span>
                                        <Link
                                            href={`/${locale}/documents/${doc.slug}`}
                                            className="transparency-download-cta"
                                        >
                                            <span>{t('viewDownload')}</span>
                                            <span aria-hidden="true">→</span>
                                        </Link>
                                    </div>
                                </article>
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
                            <span>{t('govProcure')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/tenders`} className="dept-portal-card">
                                <h4>{t('openTendersCard')}</h4>
                                <p>{t('openTendersCardDesc')}</p>
                                <span>{t('browseOpenTenders')} →</span>
                            </Link>

                            <Link href={`/${locale}/rates`} className="dept-portal-card">
                                <h4>{t('tariffsCard')}</h4>
                                <p>{t('tariffsBillingCardDesc2')}</p>
                                <span>{t('checkRatesInfo')} →</span>
                            </Link>

                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>{t('auditCard')}</h4>
                                <p>{t('auditCardDesc')}</p>
                                <span>{t('enquireTreasury')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

