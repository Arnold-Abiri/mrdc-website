import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { EmptyState } from '../Components/public/PageHeader';
import { formatDate } from '../formatDate';

type Tender = {
    slug: string;
    reference: string;
    title: string;
    category: string | null;
    closes_at: string | null;
    display_status: string;
};

const copy = {
    en: {
        intro: 'Official competitive bidding, expressions of interest, and procurement requests issued by Mutoko Rural District Council in accordance with the Public Procurement and Disposal of Public Assets Act [Cap 22:23].',
        prazNotice: 'All prospective bidders must be registered with the Procurement Regulatory Authority of Zimbabwe (PRAZ) in the relevant category and possess valid tax clearances.',
    },
    sn: {
        intro: 'Mikana yekutenga zvinhu nemabasa yeKanzuru yeMutoko maererano nemutemo wehurumende wekutenga kwezvinhu zvevoruzhinji [Cap 22:23].',
        prazNotice: 'Vese vanoda kukwikwidza vanofanirwa kunge vakanyoreswa nePRAZ uye vaine zvitupa zvemitero zvinoshanda.',
    },
    nd: {
        intro: 'Amathuba okuthengwa kwempahla lezinkonzo zoMkhandlu weMutoko ngaphansi komthetho wokuthengwa kwempahla zikahulumende [Cap 22:23].',
        prazNotice: 'Bonke abafuna ukuthenga kumele babe babhaliswe lePRAZ futhi babe lezitifiketi zemithelo ezisemthethweni.',
    },
};

const statusLabels: Record<string, { label: string; class: string }> = {
    open: { label: 'Open for Bidding', class: 'currency-usd' },
    closed: { label: 'Under Evaluation', class: 'currency-zig' },
    awarded: { label: 'Contract Awarded', class: 'currency-multi' },
};

export default function Tenders({ tenders }: { tenders: Tender[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [selectedTab, setSelectedTab] = useState<'all' | 'open' | 'closed' | 'awarded'>('all');
    const [copiedRef, setCopiedRef] = useState<string | null>(null);

    const handleCopy = (ref: string) => {
        try {
            navigator.clipboard.writeText(ref);
            setCopiedRef(ref);
            setTimeout(() => setCopiedRef(null), 2500);
        } catch {
            // Fallback
        }
    };

    const filtered = selectedTab === 'all'
        ? tenders
        : tenders.filter((item) => item.display_status === selectedTab);

    return (
        <PublicLayout>
            <Head title="Public Tenders & Procurement | Mutoko RDC" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>Opportunities</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Tenders &amp; Procurement</span>
                    </nav>
                    <span className="about-accent-eyebrow">STATUTORY PROCUREMENT · PPDPA ACT [CAP 22:23]</span>
                    <h1>Tenders &amp; Public Bidding</h1>
                    <p className="about-page-hero-subtitle">
                        {copy[locale].intro}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Procurement stats">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">PRAZ</div>
                                <div className="about-stat-label">Regulated Procurement</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Stand 366</div>
                                <div className="about-stat-label">Tender Box Location</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">10:00 AM</div>
                                <div className="about-stat-label">Statutory Closing Time</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Published</div>
                                <div className="about-stat-label">Bidding Document Access</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN CONTENT */}
            <section className="about-page-section" aria-labelledby="tenders-list">
                <div className="container">

                    {/* PRAZ COMPLIANCE NOTICE */}
                    <div className="praz-compliance-notice">
                        <svg className="praz-compliance-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <p className="praz-compliance-text">
                            <strong>Statutory Bidding Compliance Notice:</strong> {copy[locale].prazNotice} Standard bidding documents must be submitted in sealed envelopes endorsed with the Tender Number and Name into the Council Tender Box at Mutoko RDC Main Reception, Stand 366, before the designated deadline.
                        </p>
                    </div>

                    {/* TABS */}
                    <div className="rates-filter-tabs" role="tablist" aria-label="Tender stage tabs">
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'all'}
                            className={`rates-tab-btn ${selectedTab === 'all' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('all')}
                        >
                            All Notices ({tenders.length})
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'open'}
                            className={`rates-tab-btn ${selectedTab === 'open' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('open')}
                        >
                            Open for Bidding
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'closed'}
                            className={`rates-tab-btn ${selectedTab === 'closed' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('closed')}
                        >
                            Under Evaluation
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'awarded'}
                            className={`rates-tab-btn ${selectedTab === 'awarded' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('awarded')}
                        >
                            Awarded Contracts
                        </button>
                    </div>

                    {filtered.length === 0 ? (
                        <EmptyState
                            title="No tenders found"
                            text="There are no procurement notices currently matching this filter category. Please check back later."
                        />
                    ) : (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            {filtered.map((tender) => {
                                const statusInfo = statusLabels[tender.display_status] ?? {
                                    label: tender.display_status,
                                    class: 'currency-zig',
                                };

                                return (
                                    <article key={tender.slug} className="tender-card-modern">
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '12px', marginBottom: '12px' }}>
                                            <div>
                                                <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '6px' }}>
                                                    <span style={{ fontFamily: 'ui-monospace, monospace', fontSize: '0.82rem', fontWeight: 800, color: 'var(--color-civic-navy)' }}>
                                                        {tender.reference}
                                                    </span>
                                                    <button
                                                        type="button"
                                                        className={`rates-copy-btn ${copiedRef === tender.reference ? 'copied' : ''}`}
                                                        onClick={() => handleCopy(tender.reference)}
                                                        aria-label="Copy tender reference"
                                                    >
                                                        {copiedRef === tender.reference ? '✓ Copied' : 'Copy Ref'}
                                                    </button>
                                                </div>
                                                <h3 style={{ fontSize: '1.2rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: 0 }}>
                                                    <Link href={`/${locale}/tenders/${tender.slug}`}>{tender.title}</Link>
                                                </h3>
                                            </div>
                                            <span className={`rates-bank-currency ${statusInfo.class}`}>
                                                {statusInfo.label}
                                            </span>
                                        </div>

                                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '16px', fontSize: '0.84rem', color: '#64748B', marginBottom: '14px' }}>
                                            {tender.category && (
                                                <span className="vacancy-meta-item">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                        <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                                    </svg>
                                                    <span>{tender.category}</span>
                                                </span>
                                            )}
                                            {tender.closes_at && (
                                                <span className="vacancy-meta-item">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                        <circle cx="12" cy="12" r="10" />
                                                        <polyline points="12 6 12 12 16 14" />
                                                    </svg>
                                                    <span>Submission Deadline: <strong>{formatDate(tender.closes_at, locale)} at 10:00 AM</strong></span>
                                                </span>
                                            )}
                                        </div>

                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: '12px', borderTop: '1px solid #F1F5F9' }}>
                                            <span style={{ fontSize: '0.8rem', color: '#64748B' }}>
                                                Tender Box: Stand 366 Mutoko Centre
                                            </span>
                                            <Link href={`/${locale}/tenders/${tender.slug}`} className="vacancy-view-cta">
                                                <span>View Tender Details</span>
                                                <span aria-hidden="true">→</span>
                                            </Link>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}

