import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
import { formatDate } from '../formatDate';

type TenderDetail = {
    slug: string;
    reference: string;
    title: string;
    category: string | null;
    description: string;
    opens_at: string | null;
    closes_at: string | null;
    display_status: string;
    contact_instructions: string | null;
    award_status: string;
    awarded_to: string | null;
    awarded_at: string | null;
    award_amount: string | null;
    award_reference: string | null;
    award_remarks: string | null;
    document: { slug: string; title: string } | null;
};

const statusLabels: Record<string, { label: string; class: string }> = {
    open: { label: 'Open for Bidding', class: 'currency-usd' },
    closed: { label: 'Under Evaluation', class: 'currency-zig' },
    awarded: { label: 'Contract Awarded', class: 'currency-multi' },
};

export default function Tender({ tender, department }: { tender: TenderDetail; department: string | null }) {
    const locale = usePublicLocale();
    const statusInfo = statusLabels[tender.display_status] ?? {
        label: tender.display_status,
        class: 'currency-zig',
    };

    return (
        <PublicLayout>
            <Head title={`${tender.title} | Mutoko RDC Procurement`} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/tenders`}>Tenders</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{tender.reference}</span>
                    </nav>
                    <span className="about-accent-eyebrow">
                        TENDER REF: {tender.reference} · PRAZ REGULATED
                    </span>
                    <h1>{tender.title}</h1>
                    <p className="about-page-hero-subtitle">
                        {department ? `Responsible Directorate: ${department}. ` : ''}Public competitive tender issued by Mutoko Rural District Council under the PPDPA Act.
                    </p>
                </div>
            </header>

            {/* MAIN CONTENT */}
            <section className="about-page-section" aria-labelledby="tender-overview">
                <div className="container">
                    <div className="about-detail-layout">
                        <div className="about-detail-main">
                            <article className="rates-guidance-card">
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '14px' }}>
                                    <h3 id="tender-overview" style={{ margin: 0 }}>Tender Scope &amp; Specifications</h3>
                                    <span className={`rates-bank-currency ${statusInfo.class}`}>
                                        {statusInfo.label}
                                    </span>
                                </div>

                                <p style={{ fontSize: '0.98rem', lineHeight: 1.7, color: '#334155', whiteSpace: 'pre-line' }}>
                                    {tender.description}
                                </p>

                                {tender.contact_instructions && (
                                    <div style={{ marginTop: '24px', padding: '18px 20px', background: '#F8FAFC', border: '1px solid #E2E8F0', borderLeft: '4px solid var(--color-civic-navy)', borderRadius: '10px' }}>
                                        <h4 style={{ margin: '0 0 8px', fontSize: '1rem', fontWeight: 800, color: 'var(--color-civic-navy)' }}>
                                            Submission Guidelines &amp; Enquiries
                                        </h4>
                                        <p style={{ margin: 0, fontSize: '0.9rem', color: '#475569', lineHeight: 1.6, whiteSpace: 'pre-line' }}>
                                            {tender.contact_instructions}
                                        </p>
                                    </div>
                                )}

                                {tender.award_status === 'awarded' && (
                                    <div style={{ marginTop: '24px', padding: '18px 20px', background: '#ECFDF5', border: '1px solid #A7F3D0', borderRadius: '10px' }}>
                                        <h4 style={{ margin: '0 0 10px', fontSize: '1.05rem', fontWeight: 800, color: '#065F46' }}>
                                            Contract Award Disclosure
                                        </h4>
                                        <dl style={{ display: 'grid', gridTemplateColumns: '140px 1fr', gap: '8px', fontSize: '0.9rem', margin: 0 }}>
                                            {tender.awarded_to && (
                                                <>
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>Awarded Contractor:</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B', fontWeight: 700 }}>{tender.awarded_to}</dd>
                                                </>
                                            )}
                                            {tender.award_amount && (
                                                <>
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>Award Value:</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B' }}>{tender.award_amount}</dd>
                                                </>
                                            )}
                                            {tender.awarded_at && (
                                                <>
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>Date of Award:</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B' }}>{formatDate(tender.awarded_at, locale)}</dd>
                                                </>
                                            )}
                                        </dl>
                                    </div>
                                )}
                            </article>
                        </div>

                        <aside className="about-summary-card" aria-label="Tender submission summary">
                            <h3>Bidding Summary</h3>
                            <dl>
                                <dt>Reference</dt>
                                <dd style={{ fontFamily: 'ui-monospace, monospace', fontWeight: 700 }}>{tender.reference}</dd>
                                <dt>Status</dt>
                                <dd>{statusInfo.label}</dd>
                                {tender.category && (
                                    <>
                                        <dt>Category</dt>
                                        <dd>{tender.category}</dd>
                                    </>
                                )}
                                {tender.opens_at && (
                                    <>
                                        <dt>Opening Date</dt>
                                        <dd>{formatDate(tender.opens_at, locale)}</dd>
                                    </>
                                )}
                                {tender.closes_at && (
                                    <>
                                        <dt>Closing Date</dt>
                                        <dd><strong>{formatDate(tender.closes_at, locale)} at 10:00 AM</strong></dd>
                                    </>
                                )}
                                <dt>Submission Box</dt>
                                <dd>Stand 366 Mutoko Centre</dd>
                            </dl>

                            {tender.document && (
                                <div style={{ marginTop: '20px' }}>
                                    <Link
                                        href={`/${locale}/documents/${tender.document.slug}`}
                                        className="rates-pop-btn-primary"
                                        style={{ width: '100%', justifyContent: 'center' }}
                                    >
                                        <span>Download Bidding RFP (PDF)</span>
                                        <span aria-hidden="true">→</span>
                                    </Link>
                                </div>
                            )}

                            <p style={{ marginTop: '16px', fontSize: '0.82rem', color: '#64748B', textAlign: 'center' }}>
                                Questions regarding this tender? <Link href={`/${locale}/contact?context=tender:${tender.slug}`} className="about-page-text-link">Contact Procurement Desk</Link>
                            </p>
                        </aside>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

