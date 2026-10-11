import { useEffect, useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';
import { PageHero } from '../../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../../usePublicTranslation';

type FaqItem = {
    id: number;
    question: string;
    answer: string;
    category: string;
    category_label: string;
    department?: string | null;
    is_featured: boolean;
};

interface FaqsProps {
    faqs: FaqItem[];
    categories: Record<string, string>;
    initialCategory?: string | null;
}

export default function FaqsIndex({ faqs = [], categories = {}, initialCategory = null }: FaqsProps) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [selectedCategory, setSelectedCategory] = useState<string>(initialCategory || 'all');
    const [searchQuery, setSearchQuery] = useState<string>('');
    const [openFaqId, setOpenFaqId] = useState<number | null>(null);

    // Read initial anchor hash if present
    useEffect(() => {
        if (typeof window !== 'undefined' && window.location.hash) {
            const match = window.location.hash.match(/#faq-(\d+)/);
            if (match && match[1]) {
                const id = parseInt(match[1], 10);
                setOpenFaqId(id);
                // Also scroll to it gently
                setTimeout(() => {
                    const el = document.getElementById(`faq-${id}`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 300);
            }
        }
    }, []);

    const toggleFaq = (id: number) => {
        const nextId = openFaqId === id ? null : id;
        setOpenFaqId(nextId);
        if (typeof window !== 'undefined') {
            if (nextId) {
                history.replaceState(null, '', `#faq-${nextId}`);
            } else {
                history.replaceState(null, '', window.location.pathname + window.location.search);
            }
        }
    };

    const filteredFaqs = useMemo(() => {
        const q = searchQuery.toLowerCase().trim();
        return faqs.filter((faq) => {
            const matchesCategory = selectedCategory === 'all' || faq.category === selectedCategory;
            const matchesQuery =
                !q ||
                faq.question.toLowerCase().includes(q) ||
                faq.answer.toLowerCase().includes(q) ||
                faq.category_label.toLowerCase().includes(q);
            return matchesCategory && matchesQuery;
        });
    }, [faqs, selectedCategory, searchQuery]);

    // Google FAQPage Schema.org structured data
    const faqSchema = useMemo(() => {
        return {
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: faqs.slice(0, 25).map((faq) => ({
                '@type': 'Question',
                name: faq.question,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: faq.answer,
                },
            })),
        };
    }, [faqs]);

    return (
        <PublicLayout>
            <Head>
                <title>Frequently Asked Questions | Mutoko Rural District Council</title>
                <meta
                    name="description"
                    content="Find authoritative answers to common questions about council rates, business licences, housing stands, water, and local governance in Mutoko."
                />
                <script type="application/ld+json">
                    {JSON.stringify(faqSchema)}
                </script>
            </Head>

            <PageHero
                eyebrow="CITIZEN KNOWLEDGE BASE"
                title="Frequently Asked Questions"
                subtitle="Clear, verified answers to common questions regarding council services, billing, permits, and municipal governance."
                crumb={[
                    { label: 'FAQs' },
                ]}
            />

            <section className="about-page-section" style={{ paddingTop: '2rem', paddingBottom: '5rem' }}>
                <div className="container" style={{ maxWidth: '960px', margin: '0 auto' }}>
                    {/* Live search input */}
                    <div style={{ marginBottom: '2rem' }}>
                        <div
                            style={{
                                display: 'flex',
                                alignItems: 'center',
                                background: '#FFFFFF',
                                border: '2px solid #E2E8F0',
                                borderRadius: '12px',
                                padding: '10px 18px',
                                boxShadow: '0 4px 12px rgba(0,0,0,0.03)',
                                transition: 'border-color 0.2s ease',
                            }}
                        >
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#64748B"
                                strokeWidth="2.2"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                style={{ marginRight: 12 }}
                            >
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Search questions by keyword (e.g. rates, clearance, licence, water, stand)..."
                                style={{
                                    border: 'none',
                                    outline: 'none',
                                    width: '100%',
                                    fontSize: '1.05rem',
                                    color: '#0F172A',
                                }}
                            />
                            {searchQuery && (
                                <button
                                    type="button"
                                    onClick={() => setSearchQuery('')}
                                    style={{
                                        border: 'none',
                                        background: 'transparent',
                                        cursor: 'pointer',
                                        color: '#94A3B8',
                                        fontSize: '1.2rem',
                                        padding: '0 4px',
                                    }}
                                    aria-label="Clear search"
                                >
                                    &times;
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Category Filter Pills */}
                    <div
                        style={{
                            display: 'flex',
                            gap: '8px',
                            flexWrap: 'wrap',
                            marginBottom: '2.5rem',
                            borderBottom: '1px solid #E2E8F0',
                            paddingBottom: '1.25rem',
                        }}
                    >
                        <button
                            type="button"
                            onClick={() => setSelectedCategory('all')}
                            style={{
                                padding: '8px 18px',
                                borderRadius: '24px',
                                border: '1px solid',
                                borderColor: selectedCategory === 'all' ? 'var(--color-council-emerald, #0B8F62)' : '#CBD5E1',
                                background: selectedCategory === 'all' ? 'var(--color-council-emerald, #0B8F62)' : '#FFFFFF',
                                color: selectedCategory === 'all' ? '#FFFFFF' : '#334155',
                                fontWeight: 600,
                                fontSize: '0.9rem',
                                cursor: 'pointer',
                                transition: 'all 0.15s ease',
                            }}
                        >
                            All Questions ({faqs.length})
                        </button>
                        {Object.entries(categories).map(([catKey, catLabel]) => {
                            const count = faqs.filter((f) => f.category === catKey).length;
                            const isActive = selectedCategory === catKey;
                            return (
                                <button
                                    key={catKey}
                                    type="button"
                                    onClick={() => setSelectedCategory(catKey)}
                                    style={{
                                        padding: '8px 18px',
                                        borderRadius: '24px',
                                        border: '1px solid',
                                        borderColor: isActive ? 'var(--color-council-emerald, #0B8F62)' : '#CBD5E1',
                                        background: isActive ? 'var(--color-council-emerald, #0B8F62)' : '#FFFFFF',
                                        color: isActive ? '#FFFFFF' : '#334155',
                                        fontWeight: 600,
                                        fontSize: '0.9rem',
                                        cursor: 'pointer',
                                        transition: 'all 0.15s ease',
                                    }}
                                >
                                    {catLabel} ({count})
                                </button>
                            );
                        })}
                    </div>

                    {/* FAQ Items Accordion */}
                    {filteredFaqs.length === 0 ? (
                        <div
                            style={{
                                textAlign: 'center',
                                padding: '4rem 2rem',
                                background: '#F8FAFC',
                                borderRadius: '12px',
                                border: '1px dashed #CBD5E1',
                            }}
                        >
                            <svg
                                width="40"
                                height="40"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#94A3B8"
                                strokeWidth="1.5"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                style={{ margin: '0 auto 12px' }}
                            >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <h3 style={{ fontSize: '1.2rem', color: '#1E293B', marginBottom: '8px' }}>
                                No questions found matching your search
                            </h3>
                            <p style={{ color: '#64748B', fontSize: '0.95rem', maxWidth: '420px', margin: '0 auto 20px' }}>
                                Try adjusting your keywords or clearing the category filter to find what you are looking for.
                            </p>
                            <button
                                type="button"
                                onClick={() => {
                                    setSearchQuery('');
                                    setSelectedCategory('all');
                                }}
                                style={{
                                    background: 'var(--color-council-emerald, #0B8F62)',
                                    color: '#FFFFFF',
                                    border: 'none',
                                    padding: '8px 20px',
                                    borderRadius: '6px',
                                    fontWeight: 600,
                                    cursor: 'pointer',
                                }}
                            >
                                Reset Filters
                            </button>
                        </div>
                    ) : (
                        <div className="rates-faq-list" role="region" aria-label="FAQ Knowledge Base">
                            {filteredFaqs.map((faq) => {
                                const isOpen = openFaqId === faq.id;
                                return (
                                    <div
                                        key={faq.id}
                                        id={`faq-${faq.id}`}
                                        className={`rates-faq-item ${isOpen ? 'open' : ''}`}
                                        style={{
                                            scrollMarginTop: '120px',
                                            transition: 'border-color 0.2s ease, box-shadow 0.2s ease',
                                            boxShadow: isOpen ? '0 4px 16px rgba(0,0,0,0.06)' : 'none',
                                        }}
                                    >
                                        <button
                                            type="button"
                                            className="rates-faq-trigger"
                                            aria-expanded={isOpen}
                                            onClick={() => toggleFaq(faq.id)}
                                            style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                                width: '100%',
                                                textAlign: 'left',
                                            }}
                                        >
                                            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', flex: 1 }}>
                                                <span
                                                    style={{
                                                        fontSize: '0.75rem',
                                                        textTransform: 'uppercase',
                                                        letterSpacing: '0.05em',
                                                        padding: '3px 8px',
                                                        borderRadius: '4px',
                                                        background: '#F1F5F9',
                                                        color: '#475569',
                                                        fontWeight: 700,
                                                        whiteSpace: 'nowrap',
                                                    }}
                                                >
                                                    {faq.category_label}
                                                </span>
                                                <span style={{ fontWeight: 700, color: '#0F172A', fontSize: '1.05rem' }}>
                                                    {faq.question}
                                                </span>
                                            </div>
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
                                                    marginLeft: 16,
                                                }}
                                                aria-hidden="true"
                                            >
                                                <polyline points="6 9 12 15 18 9" />
                                            </svg>
                                        </button>
                                        {isOpen && (
                                            <div className="rates-faq-content" style={{ padding: '16px 20px 22px' }}>
                                                <p
                                                    style={{
                                                        margin: 0,
                                                        color: '#334155',
                                                        fontSize: '0.98rem',
                                                        lineHeight: '1.7',
                                                        whiteSpace: 'pre-line',
                                                    }}
                                                >
                                                    {faq.answer}
                                                </p>
                                                {faq.department && (
                                                    <div style={{ marginTop: '14px', fontSize: '0.82rem', color: '#64748B' }}>
                                                        Administering Department: <strong>{faq.department}</strong>
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    )}

                    {/* Constituent Helpdesk Fallback Card */}
                    <div
                        style={{
                            marginTop: '4rem',
                            padding: '2.5rem',
                            background: 'linear-gradient(135deg, #063726 0%, #0B8F62 100%)',
                            borderRadius: '16px',
                            color: '#FFFFFF',
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            textAlign: 'center',
                            boxShadow: '0 12px 28px rgba(11, 143, 98, 0.15)',
                        }}
                    >
                        <h3 style={{ fontSize: '1.5rem', fontWeight: 800, margin: '0 0 10px', color: '#FFFFFF' }}>
                            Didn't find what you were looking for?
                        </h3>
                        <p style={{ maxWidth: '580px', color: '#E2E8F0', fontSize: '1rem', lineHeight: '1.6', marginBottom: '24px' }}>
                            Our public relations desk and department officers are on hand to provide dedicated guidance and clarify statutory procedures.
                        </p>
                        <div style={{ display: 'flex', gap: '14px', flexWrap: 'wrap', justifyContent: 'center' }}>
                            <Link
                                href={`/${locale}/contact`}
                                style={{
                                    background: '#FFFFFF',
                                    color: '#063726',
                                    fontWeight: 700,
                                    padding: '12px 24px',
                                    borderRadius: '8px',
                                    textDecoration: 'none',
                                    fontSize: '0.95rem',
                                    boxShadow: '0 2px 6px rgba(0,0,0,0.1)',
                                }}
                            >
                                Submit Public Enquiry
                            </Link>
                            <Link
                                href={`/${locale}/feedback`}
                                style={{
                                    background: 'rgba(255, 255, 255, 0.15)',
                                    color: '#FFFFFF',
                                    border: '1px solid rgba(255,255,255,0.4)',
                                    fontWeight: 600,
                                    padding: '12px 24px',
                                    borderRadius: '8px',
                                    textDecoration: 'none',
                                    fontSize: '0.95rem',
                                }}
                            >
                                Citizen Feedback
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
