import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
import { formatDate } from '../formatDate';

type FinancialDocument = {
    slug: string;
    title: string;
    description: string | null;
    category: string;
    published_at: string | null;
    reference_date: string | null;
    download_count: number;
};

const categoryLabels: Record<string, string> = {
    budget: 'Approved Council Budget',
    financial_statement: 'Audited Financial Statements',
    financial_report: 'Quarterly Financial Report',
    procurement_plan: 'Annual Procurement Plan',
    awards_register: 'Public Tender Awards Register',
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

    return (
        <PublicLayout>
            <Head title="Financial Transparency, Budgets & Audit Disclosures" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>Council</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Transparency</span>
                    </nav>
                    <span className="about-accent-eyebrow">STATUTORY FINANCIAL DISCLOSURE · PUBLIC FINANCE MANAGEMENT ACT</span>
                    <h1>Financial Transparency &amp; Open Council</h1>
                    <p className="about-page-hero-subtitle">
                        Public access to adopted annual estimates, audited balance sheets, procurement plans, and devolution grant disbursements for Mutoko Rural District Council.
                    </p>
                </div>
            </header>

            {/* STATS / KPI RIBBON */}
            <section className="about-stats-ribbon" aria-label="Financial disclosure overview">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Audited</div>
                                <div className="about-stat-label">Statutory Oversight</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Devolution</div>
                                <div className="about-stat-label">Capital Grant Tracking</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">PFMA</div>
                                <div className="about-stat-label">Treasury Compliance</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{documents.length}</div>
                                <div className="about-stat-label">Published Disclosures</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN DISCLOSURE SECTION */}
            <section className="about-page-section" aria-labelledby="transparency-docs">
                <div className="container">
                    <span className="about-accent-eyebrow">FISCAL ACCOUNTABILITY</span>
                    <h2 id="transparency-docs">Financial Disclosures &amp; Statutory Reports</h2>
                    <p className="about-page-section-intro">
                        Mutoko Rural District Council publishes its fiscal plans and expenditure registers in compliance with the Constitution of Zimbabwe and the Public Finance Management Act [Chapter 22:19].
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
                                <div className="transparency-kpi-label">Annual Council Budget</div>
                                <div className="transparency-kpi-value">Participatory Ward Consultations</div>
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
                                <div className="transparency-kpi-label">Auditor-General Submissions</div>
                                <div className="transparency-kpi-value">External Statutory Audits</div>
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
                                <div className="transparency-kpi-label">Procurement &amp; PRAZ</div>
                                <div className="transparency-kpi-value">Public Tender Awards</div>
                            </div>
                        </div>
                    </div>

                    {/* FILTER FORM */}
                    <form method="get" action={`/${locale}/transparency`} className="transparency-filter-bar">
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, flexGrow: 1, minWidth: 220 }}>
                            <label htmlFor="transparency-category" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                Document Category
                            </label>
                            <select
                                id="transparency-category"
                                name="category"
                                defaultValue={filters.category ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">All Financial Categories</option>
                                {categories.map((category) => (
                                    <option key={category} value={category}>
                                        {categoryLabels[category] ?? category}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, width: 140 }}>
                            <label htmlFor="transparency-year" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                Fiscal Year
                            </label>
                            <select
                                id="transparency-year"
                                name="year"
                                defaultValue={filters.year ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">All Years</option>
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
                                Filter Reports
                            </button>
                            {(filters.category || filters.year) && (
                                <Link
                                    href={`/${locale}/transparency`}
                                    className="dept-filter-btn"
                                    style={{ padding: '9px 14px', textDecoration: 'none' }}
                                >
                                    Reset
                                </Link>
                            )}
                        </div>
                    </form>

                    {/* DOCUMENTS LIST */}
                    {documents.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                No financial disclosure documents match the selected filters.
                            </p>
                        </div>
                    ) : (
                        <div className="dept-directory-grid">
                            {documents.map((doc) => (
                                <article key={doc.slug} className="transparency-doc-card">
                                    <div className="transparency-doc-top">
                                        <span className="transparency-file-pill">OFFICIAL PDF</span>
                                        <span style={{ fontSize: '0.78rem', color: '#64748B', fontWeight: 600 }}>
                                            {doc.reference_date ? formatDate(doc.reference_date, locale) : 'Recent'}
                                        </span>
                                    </div>

                                    <h3 className="transparency-doc-title">
                                        <Link href={`/${locale}/documents/${doc.slug}`}>{doc.title}</Link>
                                    </h3>

                                    <p className="transparency-doc-desc">
                                        {doc.description ?? 'Official council financial publication released by the Finance Department.'}
                                    </p>

                                    <div className="transparency-doc-footer">
                                        <span>
                                            {categoryLabels[doc.category] ?? doc.category}
                                        </span>
                                        <Link
                                            href={`/${locale}/documents/${doc.slug}`}
                                            className="transparency-download-cta"
                                        >
                                            <span>View &amp; Download</span>
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
                            <span>Statutory Governance &amp; Council Procurement</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/tenders`} className="dept-portal-card">
                                <h4>Open Tenders &amp; Expressions of Interest</h4>
                                <p>View active bidding opportunities for roads, water supply, and capital civil works.</p>
                                <span>Browse open tenders →</span>
                            </Link>

                            <Link href={`/${locale}/rates`} className="dept-portal-card">
                                <h4>Tariffs &amp; Ratepayer Billing</h4>
                                <p>Check current gazetted council tariffs, development levies, and payment instructions.</p>
                                <span>Check rates information →</span>
                            </Link>

                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>Audit &amp; Finance Inquiries</h4>
                                <p>Contact the Finance Directorate or Internal Audit Unit at Mutoko Centre.</p>
                                <span>Enquire with treasury →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

