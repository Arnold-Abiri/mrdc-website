import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SourcedText } from '../Components/public/SourceButtons';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
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

const statusLabels: Record<string, { labelKey: TranslationKey; class: string }> = {
    open: { labelKey: 'tenderOpen' as TranslationKey, class: 'currency-usd' },
    closed: { labelKey: 'tenderClosed' as TranslationKey, class: 'currency-zig' },
    awarded: { labelKey: 'tenderAwarded' as TranslationKey, class: 'currency-multi' },
};

export default function Tender({ tender, department }: { tender: TenderDetail; department: string | null }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const statusInfo = statusLabels[tender.display_status] ?? {
        labelKey: 'tenderClosed' as TranslationKey,
        class: 'currency-zig',
    };

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [companyName, setCompanyName] = useState('');
    const [representativeName, setRepresentativeName] = useState('');
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');
    const handleDownloadSubmit = () => {
        // Form submits standard POST to download file
    };

    return (
        <PublicLayout>
            <Head title={`${tender.title} | Mutoko RDC Procurement`} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/tenders`}>{t('tenders')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{tender.reference}</span>
                    </nav>
                    <span className="about-accent-eyebrow">
                        {t('tenderRefEyebrow')}: {tender.reference} · {t('prazRegulated')}
                    </span>
                    <h1>{tender.title}</h1>
                    <p className="about-page-hero-subtitle">
                        {department ? `${t('respDirectorate')}: ${department}. ` : ''} {t('tenderIssuedTail')}
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
                                    <h3 id="tender-overview" style={{ margin: 0 }}>{t('tenderScopeTitle')}</h3>
                                    <span className={`rates-bank-currency ${statusInfo.class}`}>
                                        {t(statusInfo.labelKey)}
                                    </span>
                                </div>

                                <div style={{ fontSize: '0.98rem', lineHeight: 1.7, color: '#334155' }}>
                                    <SourcedText text={tender.description} />
                                </div>

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
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>{t('awardedContractor')}:</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B', fontWeight: 700 }}>{tender.awarded_to}</dd>
                                                </>
                                            )}
                                            {tender.award_amount && (
                                                <>
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>{t('awardValue')}</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B' }}>{tender.award_amount}</dd>
                                                </>
                                            )}
                                            {tender.awarded_at && (
                                                <>
                                                    <dt style={{ fontWeight: 700, color: '#047857' }}>{t('awardDate')}</dt>
                                                    <dd style={{ margin: 0, color: '#1E293B' }}>{formatDate(tender.awarded_at, locale)}</dd>
                                                </>
                                            )}
                                        </dl>
                                    </div>
                                )}
                            </article>
                        </div>

                        <aside className="about-summary-card" aria-label="Tender submission summary">
                            <h3>{t('biddingSummary')}</h3>
                            <dl>
                                <dt>{t('reference')}</dt>
                                <dd style={{ fontFamily: 'ui-monospace, monospace', fontWeight: 700 }}>{tender.reference}</dd>
                                <dt>{t('statusLabel')}</dt>
                                <dd>{t(statusInfo.labelKey)}</dd>
                                {tender.category && (
                                    <>
                                        <dt>{t('category')}</dt>
                                        <dd>{tender.category}</dd>
                                    </>
                                )}
                                {tender.opens_at && (
                                    <>
                                        <dt>{t('openingDate')}</dt>
                                        <dd>{formatDate(tender.opens_at, locale)}</dd>
                                    </>
                                )}
                                {tender.closes_at && (
                                    <>
                                        <dt>{t('closingDate')}</dt>
                                        <dd><strong>{formatDate(tender.closes_at, locale)} at 10:00 AM</strong></dd>
                                    </>
                                )}
                                <dt>{t('submissionBox')}</dt>
                                <dd>Stand 366 Mutoko Centre</dd>
                            </dl>

                            {tender.document && (
                                <div style={{ marginTop: '20px' }}>
                                    <button
                                        type="button"
                                        onClick={() => setIsModalOpen(true)}
                                        className="rates-pop-btn-primary"
                                        style={{ width: '100%', justifyContent: 'center', cursor: 'pointer' }}
                                    >
                                        <span>{t('downloadRfp')}</span>
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </div>
                            )}

                            <p style={{ marginTop: '16px', fontSize: '0.82rem', color: '#64748B', textAlign: 'center' }}>
                                {t('tenderQuestions')} <Link href={`/${locale}/contact?context=tender:${tender.slug}`} className="about-page-text-link">{t('procurementDesk')}</Link>
                            </p>
                        </aside>
                    </div>
                </div>
            </section>

            {/* PRAZ BIDDER REGISTRATION MODAL */}
            {isModalOpen && tender.document && (
                <div
                    style={{
                        position: 'fixed',
                        inset: 0,
                        backgroundColor: 'rgba(15, 23, 42, 0.75)',
                        backdropFilter: 'blur(4px)',
                        zIndex: 9999,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '16px',
                    }}
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="bidder-modal-title"
                >
                    <div
                        style={{
                            background: '#FFFFFF',
                            borderRadius: '12px',
                            maxWidth: '520px',
                            width: '100%',
                            padding: '28px',
                            boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                            position: 'relative',
                        }}
                    >
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '16px' }}>
                            <div>
                                <span style={{ fontSize: '0.78rem', fontWeight: 800, textTransform: 'uppercase', letterSpacing: '0.05em', color: '#0B8F62' }}>
                                    PRAZ Compliance Register
                                </span>
                                <h3 id="bidder-modal-title" style={{ margin: '4px 0 0', fontSize: '1.25rem', color: '#0F172A', fontWeight: 700 }}>
                                    Register to Download Specifications
                                </h3>
                            </div>
                            <button
                                type="button"
                                onClick={() => setIsModalOpen(false)}
                                style={{
                                    border: 'none',
                                    background: 'transparent',
                                    fontSize: '1.5rem',
                                    lineHeight: 1,
                                    cursor: 'pointer',
                                    color: '#94A3B8',
                                    padding: '4px 8px',
                                }}
                                aria-label="Close modal"
                            >
                                ×
                            </button>
                        </div>

                        <p style={{ fontSize: '0.88rem', color: '#64748B', lineHeight: 1.5, marginBottom: '20px' }}>
                            In accordance with the Public Procurement and Disposal of Public Assets Act, please provide your company details to receive the official tender specifications for <strong>{tender.reference}</strong>.
                        </p>

                        <form
                            method="POST"
                            action={`/${locale}/tenders/${tender.slug}/download`}
                            onSubmit={handleDownloadSubmit}
                        >
                            <input type="hidden" name="_token" value={(document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''} />

                            <div style={{ marginBottom: '14px' }}>
                                <label style={{ display: 'block', fontSize: '0.85rem', fontWeight: 600, color: '#334155', marginBottom: '6px' }}>
                                    Company / Bidder Legal Name <span style={{ color: '#EF4444' }}>*</span>
                                </label>
                                <input
                                    type="text"
                                    name="company_name"
                                    required
                                    value={companyName}
                                    onChange={(e) => setCompanyName(e.target.value)}
                                    placeholder="e.g. Acme Engineering Pvt Ltd"
                                    style={{
                                        width: '100%',
                                        padding: '10px 12px',
                                        borderRadius: '6px',
                                        border: '1px solid #CBD5E1',
                                        fontSize: '0.9rem',
                                    }}
                                />
                            </div>

                            <div style={{ marginBottom: '14px' }}>
                                <label style={{ display: 'block', fontSize: '0.85rem', fontWeight: 600, color: '#334155', marginBottom: '6px' }}>
                                    Representative Name
                                </label>
                                <input
                                    type="text"
                                    name="representative_name"
                                    value={representativeName}
                                    onChange={(e) => setRepresentativeName(e.target.value)}
                                    placeholder="Contact Person"
                                    style={{
                                        width: '100%',
                                        padding: '10px 12px',
                                        borderRadius: '6px',
                                        border: '1px solid #CBD5E1',
                                        fontSize: '0.9rem',
                                    }}
                                />
                            </div>

                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px', marginBottom: '20px' }}>
                                <div>
                                    <label style={{ display: 'block', fontSize: '0.85rem', fontWeight: 600, color: '#334155', marginBottom: '6px' }}>
                                        Email Address <span style={{ color: '#EF4444' }}>*</span>
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        placeholder="bids@company.co.zw"
                                        style={{
                                            width: '100%',
                                            padding: '10px 12px',
                                            borderRadius: '6px',
                                            border: '1px solid #CBD5E1',
                                            fontSize: '0.9rem',
                                        }}
                                    />
                                </div>
                                <div>
                                    <label style={{ display: 'block', fontSize: '0.85rem', fontWeight: 600, color: '#334155', marginBottom: '6px' }}>
                                        Phone Number
                                    </label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        value={phone}
                                        onChange={(e) => setPhone(e.target.value)}
                                        placeholder="+263 77..."
                                        style={{
                                            width: '100%',
                                            padding: '10px 12px',
                                            borderRadius: '6px',
                                            border: '1px solid #CBD5E1',
                                            fontSize: '0.9rem',
                                        }}
                                    />
                                </div>
                            </div>

                            <div style={{ display: 'flex', gap: '12px', justifyContent: 'flex-end', borderTop: '1px solid #F1F5F9', paddingTop: '16px' }}>
                                <button
                                    type="button"
                                    onClick={() => setIsModalOpen(false)}
                                    style={{
                                        padding: '9px 18px',
                                        borderRadius: '6px',
                                        border: '1px solid #E2E8F0',
                                        background: '#FFFFFF',
                                        color: '#475569',
                                        fontWeight: 600,
                                        fontSize: '0.88rem',
                                        cursor: 'pointer',
                                    }}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="rates-pop-btn-primary"
                                    style={{
                                        padding: '9px 20px',
                                        borderRadius: '6px',
                                        cursor: 'pointer',
                                    }}
                                >
                                    <span>Download RFP Document</span>
                                    <span aria-hidden="true">↓</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </PublicLayout>
    );
}

