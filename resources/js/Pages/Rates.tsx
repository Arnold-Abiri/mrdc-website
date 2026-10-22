import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
import { formatDate } from '../formatDate';

type RatesBlock = { type: string; text: string; url?: string | null };
type RatesPage = { slug: string; title: string; summary: string | null; blocks: RatesBlock[] } | null;
type RateSchedule = { slug: string; title: string; category: string; reference_date: string | null };

type BankAccount = {
    id: string;
    bank: string;
    accountName: string;
    accountNumber: string;
    branch: string;
    branchCode: string;
    currency: 'ZiG' | 'USD / Nostro' | 'Multi-Currency';
    currencyClass: string;
    purposeKey: TranslationKey;
    category: 'all' | 'rates' | 'stands' | 'licenses';
};

const BANK_ACCOUNTS: BankAccount[] = [
    {
        id: 'cbz-zig',
        bank: 'CBZ Bank',
        accountName: 'Mutoko Rural District Council - General Revenue',
        accountNumber: '02923984010012',
        branch: 'Mutoko Branch',
        branchCode: '4129',
        currency: 'ZiG',
        currencyClass: 'currency-zig',
        purposeKey: 'purRates',
        category: 'rates',
    },
    {
        id: 'afc-zig',
        bank: 'AFC Commercial Bank (Agribank)',
        accountName: 'Mutoko RDC - Development & Unit Tax',
        accountNumber: '100004829104',
        branch: 'Mutoko Branch',
        branchCode: '3104',
        currency: 'ZiG',
        currencyClass: 'currency-zig',
        purposeKey: 'purUnitTax',
        category: 'rates',
    },
    {
        id: 'zb-usd',
        bank: 'ZB Bank',
        accountName: 'Mutoko RDC - Capital Projects & Housing Nostro',
        accountNumber: '4521-789320-200',
        branch: 'Mutoko Branch',
        branchCode: '5201',
        currency: 'USD / Nostro',
        currencyClass: 'currency-usd',
        purposeKey: 'purStands',
        category: 'stands',
    },
    {
        id: 'cabs-multi',
        bank: 'CABS',
        accountName: 'Mutoko RDC - Business & Commercial Licensing',
        accountNumber: '1004928172',
        branch: 'Mutoko / Corporate Branch',
        branchCode: '2401',
        currency: 'Multi-Currency',
        currencyClass: 'currency-multi',
        purposeKey: 'purLicences',
        category: 'licenses',
    },
];

const FAQS: { qKey: TranslationKey; aKey: TranslationKey }[] = [
    { qKey: 'faq1q', aKey: 'faq1a' },
    { qKey: 'faq2q', aKey: 'faq2a' },
    { qKey: 'faq3q', aKey: 'faq3a' },
    { qKey: 'faq4q', aKey: 'faq4a' },
    { qKey: 'faq5q', aKey: 'faq5a' },
];

type DynamicBankAccount = {
    id: number;
    bank_name: string;
    account_name: string;
    account_number: string;
    branch_name: string;
    branch_code: string | null;
    currency: string;
    category: string;
    purpose: string | null;
};

type DynamicFaq = {
    id: number;
    question: string;
    answer: string;
};

export default function Rates({
    schedules,
    bankAccounts = [],
    billingFaqs = [],
}: {
    page: RatesPage;
    schedules: RateSchedule[];
    bankAccounts?: DynamicBankAccount[];
    billingFaqs?: DynamicFaq[];
}) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [selectedCategory, setSelectedCategory] = useState<'all' | 'rates' | 'stands' | 'licenses'>('all');
    const [copiedId, setCopiedId] = useState<string | null>(null);
    const [openFaq, setOpenFaq] = useState<number | null>(0);

    const handleCopy = (id: string, text: string) => {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                setCopiedId(id);
                setTimeout(() => setCopiedId(null), 2500);
            }).catch(() => {
                fallbackCopy(id, text);
            });
        } else {
            fallbackCopy(id, text);
        }
    };

    const fallbackCopy = (id: string, text: string) => {
        try {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            if (successful) {
                setCopiedId(id);
                setTimeout(() => setCopiedId(null), 2500);
            }
        } catch (err) {
            console.error('Fallback copy failed', err);
        }
    };

    // Map dynamic accounts from database if available; otherwise use default fallback accounts
    const accountsList = bankAccounts && bankAccounts.length > 0
        ? bankAccounts.map((acc) => {
            let currencyClass = 'currency-multi';
            if (acc.currency === 'ZiG') currencyClass = 'currency-zig';
            else if (acc.currency.includes('USD')) currencyClass = 'currency-usd';

            return {
                id: `db-${acc.id}`,
                bank: acc.bank_name,
                accountName: acc.account_name,
                accountNumber: acc.account_number,
                branch: acc.branch_name,
                branchCode: acc.branch_code ?? 'Mutoko',
                currency: acc.currency,
                currencyClass,
                purpose: acc.purpose ?? 'Council revenue and rates assessment.',
                category: acc.category,
            };
        })
        : BANK_ACCOUNTS;

    const filteredAccounts = selectedCategory === 'all'
        ? accountsList
        : accountsList.filter((acc) => acc.category === selectedCategory);


    return (
        <PublicLayout>
            <Head title={t('ratesTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/services`}>{t('services')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('rates')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('ratesEyebrow')}</span>
                    <h1>{t('ratesTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('ratesSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Rates overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statMultiCurr')}</div>
                                <div className="about-stat-label">{t('statMultiCurrLabel')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statRbZ')}</div>
                                <div className="about-stat-label">{t('statInterbank')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('stat48h')}</div>
                                <div className="about-stat-label">{t('statPopTurn')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statRevHall')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MULTI-CURRENCY RBZ POLICY ADVISORY */}
            <section className="about-page-section" style={{ paddingBottom: '16px' }} aria-labelledby="rates-policy">
                <div className="container">
                    <div className="rates-currency-alert">
                        <div className="rates-currency-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                        </div>
                        <div className="rates-currency-body">
                            <h3 id="rates-policy" className="rates-currency-title">
                                {t('rbzTitle')}
                            </h3>
                            <p className="rates-currency-text">
                                In full compliance with Statutory Instruments and the Reserve Bank of Zimbabwe guidelines, Mutoko Rural District Council accepts payments in <strong>Zimbabwe Gold (ZiG)</strong> and <strong>US Dollars (USD cash or Domestic Nostro)</strong>. All ZiG transactions are computed based on the prevailing RBZ official interbank exchange rate on the date payment is recorded.
                            </p>
                            <div className="rates-currency-tags">
                                <span className="rates-currency-pill">✓ {t('pillZigPos')}</span>
                                <span className="rates-currency-pill">✓ {t('pillUsdCash')}</span>
                                <span className="rates-currency-pill">✓ {t('pillNostro')}</span>
                                <span className="rates-currency-pill">✓ {t('pillEco')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* BANK ACCOUNTS SECTION */}
            <section className="about-page-section" aria-labelledby="bank-details">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('bankEyebrow')}</span>
                    <h2 id="bank-details">{t('bankTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('bankIntro')}
                    </p>

                    {/* Filter Tabs */}
                    <div className="rates-filter-tabs" role="tablist" aria-label={t('bankTitle')}>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'all'}
                            className={`rates-tab-btn ${selectedCategory === 'all' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('all')}
                        >
                            {t('tabAllAccounts')}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'rates'}
                            className={`rates-tab-btn ${selectedCategory === 'rates' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('rates')}
                        >
                            {t('tabRatesWater')}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'stands'}
                            className={`rates-tab-btn ${selectedCategory === 'stands' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('stands')}
                        >
                            {t('tabStands')}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'licenses'}
                            className={`rates-tab-btn ${selectedCategory === 'licenses' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('licenses')}
                        >
                            {t('tabLicences')}
                        </button>
                    </div>

                    {/* Bank Grid */}
                    <div className="rates-bank-grid">
                        {filteredAccounts.map((acc) => (
                            <article key={acc.id} className="rates-bank-card">
                                <div className="rates-bank-header">
                                    <h3 className="rates-bank-title">{acc.bank}</h3>
                                    <span className={`rates-bank-currency ${acc.currencyClass}`}>{acc.currency}</span>
                                </div>

                                <div className="rates-bank-details">
                                    <div className="rates-bank-row">
                                        <span className="rates-bank-label">{t('accName')}</span>
                                        <span style={{ fontSize: '0.9rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                            {acc.accountName}
                                        </span>
                                    </div>

                                    <div className="rates-bank-row">
                                        <span className="rates-bank-label">{t('accNumber')}</span>
                                        <div className="rates-bank-val">
                                            <span>{acc.accountNumber}</span>
                                            <button
                                                type="button"
                                                className={`rates-copy-btn ${copiedId === acc.id ? 'copied' : ''}`}
                                                onClick={() => handleCopy(acc.id, acc.accountNumber)}
                                                aria-label={`${t('copyAccFor')} ${acc.bank}`}
                                            >
                                                {copiedId === acc.id ? t('copiedTick') : t('copyVerb')}
                                            </button>
                                        </div>
                                    </div>

                                    <div className="rates-bank-row">
                                        <span className="rates-bank-label">{t('accBranchCode')}</span>
                                        <span style={{ fontSize: '0.88rem', fontWeight: 600, color: '#334155' }}>
                                            {acc.branch} ({t('accCode')}: {acc.branchCode})
                                        </span>
                                    </div>
                                </div>

                                <div className="rates-bank-footer">
                                    <strong>{t('accPurpose')}:</strong> {'purposeKey' in acc ? t(acc.purposeKey) : acc.purpose}
                                </div>
                            </article>
                        ))}
                    </div>

                    {/* MOBILE MONEY & USSD INSTRUCTIONS */}
                    <div className="rates-mobile-box">
                        <div className="rates-mobile-card">
                            <h3 className="rates-mobile-title">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                                    <line x1="12" y1="18" x2="12.01" y2="18" />
                                </svg>
                                <span>{t('ecoTitle')}</span>
                            </h3>
                            <ol className="rates-mobile-steps">
                                <li>{t('ecoDial')} <strong>*151#</strong> {t('ecoEnterPin')}</li>
                                <li>{t('ecoSelect')} <strong>{t('ecoMakePay')}</strong> → <strong>{t('ecoPayBill')}</strong>.</li>
                                <li>{t('ecoBillerCode')} <strong>94812</strong>.</li>
                                <li>{t('ecoAmount')}</li>
                                <li>{t('ecoRef')} <strong>{t('ecoStandWard')}</strong> (e.g. <em>ST366-W18</em>).</li>
                            </ol>
                            <div className="rates-mobile-badge">
                                <span>{t('ecoBiller')} <strong>94812</strong></span>
                                <button
                                    type="button"
                                    className={`rates-copy-btn ${copiedId === 'ecocash' ? 'copied' : ''}`}
                                    onClick={() => handleCopy('ecocash', '94812')}
                                >
                                    {copiedId === 'ecocash' ? t('copiedTick') : t('copyCode')}
                                </button>
                            </div>
                        </div>

                        <div className="rates-mobile-card">
                            <h3 className="rates-mobile-title">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                                <span>{t('oneTitle')}</span>
                            </h3>
                            <ol className="rates-mobile-steps">
                                <li>{t('ecoDial')} <strong>*111#</strong> {t('oneLine')}</li>
                                <li>{t('ecoSelect')} <strong>{t('onePayMerchant')}</strong>.</li>
                                <li>{t('oneMerchantCode')} <strong>48920</strong>.</li>
                                <li>{t('ecoRef')} <strong>{t('oneStandSurname')}</strong>.</li>
                                <li>{t('oneConfirm')}</li>
                            </ol>
                            <div className="rates-mobile-badge">
                                <span>{t('oneMerchant')} <strong>48920</strong></span>
                                <button
                                    type="button"
                                    className={`rates-copy-btn ${copiedId === 'onemoney' ? 'copied' : ''}`}
                                    onClick={() => handleCopy('onemoney', '48920')}
                                >
                                    {copiedId === 'onemoney' ? t('copiedTick') : t('copyCode')}
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* 4-STEP PAYMENT CREDITING GUIDE */}
                    <span className="about-accent-eyebrow">{t('stepsEyebrow')}</span>
                    <h3 style={{ fontSize: '1.4rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                        {t('stepsTitle')}
                    </h3>
                    <div className="rates-steps-grid">
                        <div className="rates-step-card">
                            <div className="rates-step-num">1</div>
                            <h4 className="rates-step-title">{t('step1t')}</h4>
                            <p className="rates-step-text">
                                Ensure your transfer details explicitly quote your <strong>Stand Number, Ward Number, and Name</strong> as the payment narrative.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">2</div>
                            <h4 className="rates-step-title">{t('step2t')}</h4>
                            <p className="rates-step-text">
                                Deposit funds via Online Banking, ZIPIT, RTGS, Mobile Money, or in person at Stand 366 Mutoko Centre.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">3</div>
                            <h4 className="rates-step-title">{t('step3t')}</h4>
                            <p className="rates-step-text">
                                Forward your stamped bank confirmation or EcoCash confirmation SMS immediately to the Council Treasury.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">4</div>
                            <h4 className="rates-step-title">{t('step4t')}</h4>
                            <p className="rates-step-text">
                                Receive your official printed council receipt within 24 to 48 hours and an updated monthly statement.
                            </p>
                        </div>
                    </div>

                    {/* PROOF OF PAYMENT (POP) SUBMISSION BANNER */}
                    <div className="rates-pop-banner">
                        <div className="rates-pop-info">
                            <h3>{t('popTitle')}</h3>
                            <p>
                                Made an electronic transfer or ZIPIT deposit? Send your bank statement or payment voucher directly to our Revenue Desk with your Stand Number for instant reconciliation and receipt generation.
                            </p>
                        </div>
                        <div className="rates-pop-actions">
                            <a
                                href="https://wa.me/263772100366?text=Hello%20Mutoko%20RDC%20Finance,%20I%20have%20made%20a%20rates%20payment.%20Here%20is%20my%20Proof%20of%20Payment."
                                target="_blank"
                                rel="noreferrer"
                                className="rates-pop-btn-primary"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                </svg>
                                <span>{t('popWhatsapp')}</span>
                            </a>
                            <a href="mailto:finance@mutokordc.co.zw?subject=Proof%20of%20Payment%20-%20Rates%20Deposit" className="rates-pop-btn-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                <span>{t('popEmail')}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* BILLING FAQS */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="billing-faqs">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('faqEyebrow')}</span>
                    <h2 id="billing-faqs">{t('faqTitle')}</h2>
                    <p className="about-page-section-intro">
                        Clear answers on assessment valuations, payment grace periods, interest charges, and rates clearance certification.
                    </p>

                    <div className="rates-faq-list" role="region" aria-label={t('faqRegion')}>
                        {(billingFaqs.length > 0 ? billingFaqs : FAQS.map((f, i) => ({ id: i, question: t(f.qKey), answer: t(f.aKey) }))).map((faq, index) => {
                            const isOpen = openFaq === index;
                            return (
                                <div key={faq.id} className={`rates-faq-item ${isOpen ? 'open' : ''}`}>
                                    <button
                                        type="button"
                                        className="rates-faq-trigger"
                                        aria-expanded={isOpen}
                                        onClick={() => setOpenFaq(isOpen ? null : index)}
                                    >
                                        <span>{faq.question}</span>
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            style={{
                                                transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)',
                                                transition: 'transform 0.2s ease',
                                                flexShrink: 0,
                                                marginLeft: 12,
                                            }}
                                            aria-hidden="true"
                                        >
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </button>
                                    {isOpen && (
                                        <div className="rates-faq-content">
                                            <p style={{ margin: 0 }}>{faq.answer}</p>
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>

                    <div style={{ marginTop: 20, marginBottom: 40, textAlign: 'center' }}>
                        <Link
                            href={`/${locale}/faqs?category=billing`}
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: 8,
                                color: 'var(--color-council-emerald, #0B8F62)',
                                fontWeight: 700,
                                textDecoration: 'none',
                                fontSize: '0.95rem',
                            }}
                        >
                            View all Council FAQs &rarr;
                        </Link>
                    </div>

                    {/* GAZETTED SCHEDULES */}
                    <span className="about-accent-eyebrow">{t('schedEyebrow')}</span>
                    <h3 style={{ fontSize: '1.4rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                        {t('schedTitle')}
                    </h3>
                    <p style={{ color: '#64748B', fontSize: '0.92rem', marginBottom: 24 }}>
                        {t('schedDesc')}
                    </p>

                    {schedules.length === 0 ? (
                        <div style={{ padding: '36px 24px', textAlign: 'center', background: '#FFFFFF', borderRadius: 12, border: '1px dashed var(--color-border)', marginBottom: 32 }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('schedPending')}
                            </p>
                        </div>
                    ) : (
                        <div className="dept-directory-grid" style={{ marginBottom: 36 }}>
                            {schedules.map((schedule) => (
                                <article key={schedule.slug} className="transparency-doc-card">
                                    <div className="transparency-doc-top">
                                        <span className="transparency-file-pill">{t('schedOfficialPdf')}</span>
                                        <span style={{ fontSize: '0.78rem', color: '#64748B', fontWeight: 600 }}>
                                            {schedule.reference_date ? formatDate(schedule.reference_date, locale) : t('docRecent')}
                                        </span>
                                    </div>
                                    <h4 className="transparency-doc-title">
                                        <Link href={`/${locale}/documents/${schedule.slug}`}>{schedule.title}</Link>
                                    </h4>
                                    <p className="transparency-doc-desc">
                                        {t('schedFallback')}
                                    </p>
                                    <div className="transparency-doc-footer">
                                        <span>{schedule.category}</span>
                                        <Link href={`/${locale}/documents/${schedule.slug}`} className="transparency-download-cta">
                                            <span>{t('schedDownloadPdf')}</span>
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
                            <span>{t('ratesPortalTitle')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>{t('ratesRevCard')}</h4>
                                <p>{t('ratesRevCardDesc')}</p>
                                <span>{t('ratesRevLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/transparency`} className="dept-portal-card">
                                <h4>{t('ratesBudgetCard')}</h4>
                                <p>{t('ratesBudgetCardDesc')}</p>
                                <span>{t('ratesBudgetLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>{t('ratesBillingCard')}</h4>
                                <p>{t('ratesBillingCardDesc')}</p>
                                <span>{t('ratesBillingLink')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}


