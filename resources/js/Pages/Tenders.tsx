import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
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

const statusLabels: Record<string, { labelKey: TranslationKey; class: string }> = {
    open: { labelKey: 'tenderOpen' as TranslationKey, class: 'currency-usd' },
    closed: { labelKey: 'tenderClosed' as TranslationKey, class: 'currency-zig' },
    awarded: { labelKey: 'tenderAwarded' as TranslationKey, class: 'currency-multi' },
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
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>{t('opportunities')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('tendersCrumb')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('tendersEyebrow')}</span>
                    <h1>{t('tendersTitle')}</h1>
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
                                <div className="about-stat-num">{t('statPraz')}</div>
                                <div className="about-stat-label">{t('statRegulated')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statTenderBox')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">10:00 AM</div>
                                <div className="about-stat-label">{t('statClosingTime')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statPublished')}</div>
                                <div className="about-stat-label">{t('statBidAccess')}</div>
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
                            <strong>{t('prazTitle')}</strong> {copy[locale].prazNotice} {t('prazTail')}
                        </p>
                    </div>

                    {/* TABS */}
                    <div className="rates-filter-tabs" role="tablist" aria-label={t('tenderTabs')}>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'all'}
                            className={`rates-tab-btn ${selectedTab === 'all' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('all')}
                        >
                            {t('allNotices')} ({tenders.length})
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'open'}
                            className={`rates-tab-btn ${selectedTab === 'open' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('open')}
                        >
                            {t('tenderOpen')}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'closed'}
                            className={`rates-tab-btn ${selectedTab === 'closed' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('closed')}
                        >
                            {t('underEvaluation')}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedTab === 'awarded'}
                            className={`rates-tab-btn ${selectedTab === 'awarded' ? 'active' : ''}`}
                            onClick={() => setSelectedTab('awarded')}
                        >
                            {t('awardedContracts')}
                        </button>
                    </div>

                    {filtered.length === 0 ? (
                        <EmptyState
                            title={t('noTendersTitle')}
                            text={t('noTendersDesc')}
                        />
                    ) : (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            {filtered.map((tender) => {
                                const statusInfo = statusLabels[tender.display_status] ?? {
                                    labelKey: 'tenderClosed' as TranslationKey,
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
                                                        aria-label={t('copyRef')}
                                                    >
                                                        {copiedRef === tender.reference ? t('copiedMark') : t('copyRefShort')}
                                                    </button>
                                                </div>
                                                <h3 style={{ fontSize: '1.2rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: 0 }}>
                                                    <Link href={`/${locale}/tenders/${tender.slug}`}>{tender.title}</Link>
                                                </h3>
                                            </div>
                                            <span className={`rates-bank-currency ${statusInfo.class}`}>
                                                {t(statusInfo.labelKey)}
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
                                                    <span>{t('submissionDeadline')}: <strong>{formatDate(tender.closes_at, locale)} {t('atTime')} 10:00 AM</strong></span>
                                                </span>
                                            )}
                                        </div>

                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: '12px', borderTop: '1px solid #F1F5F9' }}>
                                            <span style={{ fontSize: '0.8rem', color: '#64748B' }}>
                                                {t('tenderBoxStand')}
                                            </span>
                                            <Link href={`/${locale}/tenders/${tender.slug}`} className="vacancy-view-cta">
                                                <span>{t('viewTenderDetails')}</span>
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

