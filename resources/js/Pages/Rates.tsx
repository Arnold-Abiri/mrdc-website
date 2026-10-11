import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
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
    purpose: string;
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
        purpose: 'Assessment rates, monthly water consumption, and refuse collection levies.',
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
        purpose: 'Rural unit tax, communal development levies, and agricultural plot assessments.',
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
        purpose: 'Serviced residential/commercial stand purchases, lease premiums, and infrastructure capital.',
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
        purpose: 'Shop licenses, market stall fees, mining development levies, and health inspection permits.',
        category: 'licenses',
    },
];

const FAQS = [
    {
        q: 'What payment reference MUST I quote when depositing rates or water bills?',
        a: 'Always quote your Council Assessment/Account Number or your Stand Number and Ward (e.g. "STAND 452 WARD 18 - RATES"). Without an exact reference, your payment will be held in our unallocated suspense account until manual verification occurs.',
    },
    {
        q: 'How are payments calculated between Zimbabwe Gold (ZiG) and US Dollars (USD)?',
        a: 'In accordance with Finance and RBZ statutory regulations, all council tariffs are gazetted in USD baseline and can be legally discharged in ZiG at the ruling Reserve Bank of Zimbabwe (RBZ) wholesale interbank rate prevailing on the date of banking.',
    },
    {
        q: 'How do I obtain a Rates Clearance Certificate for property transfer?',
        a: 'Rates Clearance Certificates are issued under Section 138 of the RDC Act. All outstanding rates, water balances, and supplementary assessments must be paid in full up to the end of the current quarter. Submit an application alongside title deeds/allocation letters to the Revenue Office at Stand 366 Mutoko Centre.',
    },
    {
        q: 'How quickly is my bank transfer or mobile money payment credited to my account?',
        a: 'When you email your Proof of Payment (POP) to finance@mutokordc.co.zw or submit via WhatsApp to our Finance Desk (+263 77 210 0366), accounts are reconciled and updated within 24 to 48 business hours.',
    },
    {
        q: 'Does Council charge interest on overdue rate accounts?',
        a: 'Yes. In accordance with statutory council by-laws, accounts in arrears beyond 30 days attract statutory interest compounded monthly. Ratepayers experiencing financial hardship are encouraged to enter into formal debt payment plans with the Council Treasurer.',
    },
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

export default function Rates({
    page,
    schedules,
    bankAccounts = [],
}: {
    page: RatesPage;
    schedules: RateSchedule[];
    bankAccounts?: DynamicBankAccount[];
}) {
    const locale = usePublicLocale();
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
            <Head title="Council Rates, Bills & Citizen Payments" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/services`}>Services</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Rates &amp; Payments</span>
                    </nav>
                    <span className="about-accent-eyebrow">PAYMENTS &amp; CIVIC REVENUE · RDC ACT [CAP 29:13]</span>
                    <h1>Bills, Council Rates &amp; Payment Methods</h1>
                    <p className="about-page-hero-subtitle">
                        Official bank accounts, mobile money biller codes, statutory tariff schedules, and billing guidance for residents, leaseholders, and businesses across Mutoko.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Rates overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">ZiG &amp; USD</div>
                                <div className="about-stat-label">Multi-Currency Accepted</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">RBZ Pegged</div>
                                <div className="about-stat-label">Official Interbank Rates</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">48-Hour</div>
                                <div className="about-stat-label">POP Crediting Turnaround</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Stand 366</div>
                                <div className="about-stat-label">Revenue Banking Hall</div>
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
                                Official Reserve Bank of Zimbabwe Multi-Currency Notice
                            </h3>
                            <p className="rates-currency-text">
                                In full compliance with Statutory Instruments and the Reserve Bank of Zimbabwe guidelines, Mutoko Rural District Council accepts payments in <strong>Zimbabwe Gold (ZiG)</strong> and <strong>US Dollars (USD cash or Domestic Nostro)</strong>. All ZiG transactions are computed based on the prevailing RBZ official interbank exchange rate on the date payment is recorded.
                            </p>
                            <div className="rates-currency-tags">
                                <span className="rates-currency-pill">✓ ZiG Electronic &amp; POS</span>
                                <span className="rates-currency-pill">✓ USD Cash at Stand 366</span>
                                <span className="rates-currency-pill">✓ Nostro Bank Transfers</span>
                                <span className="rates-currency-pill">✓ EcoCash &amp; Mobile Biller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* BANK ACCOUNTS SECTION */}
            <section className="about-page-section" aria-labelledby="bank-details">
                <div className="container">
                    <span className="about-accent-eyebrow">DIRECT BANKING CHANNELS</span>
                    <h2 id="bank-details">Official Council Bank Accounts</h2>
                    <p className="about-page-section-intro">
                        Please deposit directly into the designated account corresponding to your transaction type. Click the copy button beside any account number for instant mobile or online banking accuracy.
                    </p>

                    {/* Filter Tabs */}
                    <div className="rates-filter-tabs" role="tablist" aria-label="Account category filters">
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'all'}
                            className={`rates-tab-btn ${selectedCategory === 'all' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('all')}
                        >
                            All Bank Accounts
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'rates'}
                            className={`rates-tab-btn ${selectedCategory === 'rates' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('rates')}
                        >
                            Rates &amp; Water (ZiG)
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'stands'}
                            className={`rates-tab-btn ${selectedCategory === 'stands' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('stands')}
                        >
                            Housing &amp; Stands (USD/Nostro)
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={selectedCategory === 'licenses'}
                            className={`rates-tab-btn ${selectedCategory === 'licenses' ? 'active' : ''}`}
                            onClick={() => setSelectedCategory('licenses')}
                        >
                            Business &amp; Trade Licences
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
                                        <span className="rates-bank-label">Account Name</span>
                                        <span style={{ fontSize: '0.9rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                            {acc.accountName}
                                        </span>
                                    </div>

                                    <div className="rates-bank-row">
                                        <span className="rates-bank-label">Account Number</span>
                                        <div className="rates-bank-val">
                                            <span>{acc.accountNumber}</span>
                                            <button
                                                type="button"
                                                className={`rates-copy-btn ${copiedId === acc.id ? 'copied' : ''}`}
                                                onClick={() => handleCopy(acc.id, acc.accountNumber)}
                                                aria-label={`Copy account number for ${acc.bank}`}
                                            >
                                                {copiedId === acc.id ? '✓ Copied' : 'Copy'}
                                            </button>
                                        </div>
                                    </div>

                                    <div className="rates-bank-row">
                                        <span className="rates-bank-label">Branch &amp; Code</span>
                                        <span style={{ fontSize: '0.88rem', fontWeight: 600, color: '#334155' }}>
                                            {acc.branch} (Code: {acc.branchCode})
                                        </span>
                                    </div>
                                </div>

                                <div className="rates-bank-footer">
                                    <strong>Purpose:</strong> {acc.purpose}
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
                                <span>EcoCash Biller Code Payment</span>
                            </h3>
                            <ol className="rates-mobile-steps">
                                <li>Dial <strong>*151#</strong> and enter your EcoCash PIN.</li>
                                <li>Select <strong>Make Payment</strong> → <strong>Pay Bill</strong>.</li>
                                <li>Enter Council Biller Code: <strong>94812</strong>.</li>
                                <li>Enter Amount to pay (ZiG or USD equivalent).</li>
                                <li>Enter Reference: <strong>Stand &amp; Ward No</strong> (e.g. <em>ST366-W18</em>).</li>
                            </ol>
                            <div className="rates-mobile-badge">
                                <span>Biller Code: <strong>94812</strong></span>
                                <button
                                    type="button"
                                    className={`rates-copy-btn ${copiedId === 'ecocash' ? 'copied' : ''}`}
                                    onClick={() => handleCopy('ecocash', '94812')}
                                >
                                    {copiedId === 'ecocash' ? '✓ Copied' : 'Copy Code'}
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
                                <span>NetOne OneMoney Merchant Code</span>
                            </h3>
                            <ol className="rates-mobile-steps">
                                <li>Dial <strong>*111#</strong> on your NetOne line.</li>
                                <li>Select <strong>Pay Merchant</strong>.</li>
                                <li>Enter Council Merchant Code: <strong>48920</strong>.</li>
                                <li>Enter Reference: <strong>Stand Number &amp; Ratepayer Surname</strong>.</li>
                                <li>Confirm transaction details with your OneMoney PIN.</li>
                            </ol>
                            <div className="rates-mobile-badge">
                                <span>Merchant Code: <strong>48920</strong></span>
                                <button
                                    type="button"
                                    className={`rates-copy-btn ${copiedId === 'onemoney' ? 'copied' : ''}`}
                                    onClick={() => handleCopy('onemoney', '48920')}
                                >
                                    {copiedId === 'onemoney' ? '✓ Copied' : 'Copy Code'}
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* 4-STEP PAYMENT CREDITING GUIDE */}
                    <span className="about-accent-eyebrow">STEP-BY-STEP INSTRUCTIONS</span>
                    <h3 style={{ fontSize: '1.4rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                        How to Ensure Your Account is Credited Quickly
                    </h3>
                    <div className="rates-steps-grid">
                        <div className="rates-step-card">
                            <div className="rates-step-num">1</div>
                            <h4 className="rates-step-title">Quote Reference</h4>
                            <p className="rates-step-text">
                                Ensure your transfer details explicitly quote your <strong>Stand Number, Ward Number, and Name</strong> as the payment narrative.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">2</div>
                            <h4 className="rates-step-title">Execute Transfer</h4>
                            <p className="rates-step-text">
                                Deposit funds via Online Banking, ZIPIT, RTGS, Mobile Money, or in person at Stand 366 Mutoko Centre.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">3</div>
                            <h4 className="rates-step-title">Submit Proof (POP)</h4>
                            <p className="rates-step-text">
                                Forward your stamped bank confirmation or EcoCash confirmation SMS immediately to the Council Treasury.
                            </p>
                        </div>
                        <div className="rates-step-card">
                            <div className="rates-step-num">4</div>
                            <h4 className="rates-step-title">Receipt &amp; Clearance</h4>
                            <p className="rates-step-text">
                                Receive your official printed council receipt within 24 to 48 hours and an updated monthly statement.
                            </p>
                        </div>
                    </div>

                    {/* PROOF OF PAYMENT (POP) SUBMISSION BANNER */}
                    <div className="rates-pop-banner">
                        <div className="rates-pop-info">
                            <h3>Submit Proof of Payment (POP) Directly to Finance</h3>
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
                                <span>WhatsApp Finance POP</span>
                            </a>
                            <a href="mailto:finance@mutokordc.co.zw?subject=Proof%20of%20Payment%20-%20Rates%20Deposit" className="rates-pop-btn-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                <span>Email POP to Finance</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* BILLING FAQS */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="billing-faqs">
                <div className="container">
                    <span className="about-accent-eyebrow">RATEPAYER CLARIFICATIONS</span>
                    <h2 id="billing-faqs">Frequently Asked Billing Questions</h2>
                    <p className="about-page-section-intro">
                        Clear answers on assessment valuations, payment grace periods, interest charges, and rates clearance certification.
                    </p>

                    <div className="rates-faq-list" role="region" aria-label="Billing FAQs Accordion">
                        {FAQS.map((faq, index) => {
                            const isOpen = openFaq === index;
                            return (
                                <div key={index} className={`rates-faq-item ${isOpen ? 'open' : ''}`}>
                                    <button
                                        type="button"
                                        className="rates-faq-trigger"
                                        aria-expanded={isOpen}
                                        onClick={() => setOpenFaq(isOpen ? null : index)}
                                    >
                                        <span>{faq.q}</span>
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
                                            <p style={{ margin: 0 }}>{faq.a}</p>
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>

                    {/* GAZETTED SCHEDULES */}
                    <span className="about-accent-eyebrow">STATUTORY DOWNLOADS</span>
                    <h3 style={{ fontSize: '1.4rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                        Gazetted Tariff Schedules &amp; Bylaws
                    </h3>
                    <p style={{ color: '#64748B', fontSize: '0.92rem', marginBottom: 24 }}>
                        Download official approved tariff schedules, unit tax rates, business license classifications, and ministerial approvals.
                    </p>

                    {schedules.length === 0 ? (
                        <div style={{ padding: '36px 24px', textAlign: 'center', background: '#FFFFFF', borderRadius: 12, border: '1px dashed var(--color-border)', marginBottom: 32 }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                Current tariff schedules are being gazetted for publication. For rate assessments or account balances, visit Stand 366 Mutoko Centre.
                            </p>
                        </div>
                    ) : (
                        <div className="dept-directory-grid" style={{ marginBottom: 36 }}>
                            {schedules.map((schedule) => (
                                <article key={schedule.slug} className="transparency-doc-card">
                                    <div className="transparency-doc-top">
                                        <span className="transparency-file-pill">OFFICIAL PDF</span>
                                        <span style={{ fontSize: '0.78rem', color: '#64748B', fontWeight: 600 }}>
                                            {schedule.reference_date ? formatDate(schedule.reference_date, locale) : 'Recent'}
                                        </span>
                                    </div>
                                    <h4 className="transparency-doc-title">
                                        <Link href={`/${locale}/documents/${schedule.slug}`}>{schedule.title}</Link>
                                    </h4>
                                    <p className="transparency-doc-desc">
                                        Gazetted tariff schedule and schedule of municipal rates approved by the Minister of Local Government and Public Works.
                                    </p>
                                    <div className="transparency-doc-footer">
                                        <span>{schedule.category}</span>
                                        <Link href={`/${locale}/documents/${schedule.slug}`} className="transparency-download-cta">
                                            <span>Download PDF</span>
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
                            <span>Revenue Assistance &amp; Account Inquiries</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>Revenue Office &amp; Banking Hall</h4>
                                <p>Pay in person via POS, cash, or RTGS/ZiG transfer at Stand 366 Mutoko Centre.</p>
                                <span>Get physical office directions →</span>
                            </Link>

                            <Link href={`/${locale}/transparency`} className="dept-portal-card">
                                <h4>Annual Council Budget</h4>
                                <p>Inspect how rate revenues are invested in capital infrastructure and social services.</p>
                                <span>View budget disclosures →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>Billing Queries &amp; Clearances</h4>
                                <p>Submit a rate dispute, apply for a rate clearance certificate, or request balance verification.</p>
                                <span>Lodge billing enquiry →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}


