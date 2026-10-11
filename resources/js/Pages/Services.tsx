import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Service = { slug: string; name: string; summary: string | null };

const CORE_PORTFOLIOS = [
    {
        title: 'Roads & Works Maintenance',
        category: 'Infrastructure',
        text: 'Routine grading, storm drainage clearance, culvert installations, drifts, and civil engineering infrastructure across 29 wards.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
        ),
    },
    {
        title: 'Water & Reticulation Sanitation',
        category: 'Public Health',
        text: 'Drilling and servicing communal boreholes, solarized piped water schemes, and septic/sewer maintenance at growth points.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
            </svg>
        ),
    },
    {
        title: 'Primary Health & Clinics',
        category: 'Social Services',
        text: 'Administration of rural health centres, maternal health wards, immunization outreach, and public hygiene inspectorate.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        ),
    },
    {
        title: 'Building Inspectorate & Plans',
        category: 'Planning & Lands',
        text: 'Architectural vetting, building plan approvals, site stage inspections, and issuance of certificates of occupancy.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                <line x1="8" y1="2" x2="8" y2="18" />
                <line x1="16" y1="6" x2="16" y2="22" />
            </svg>
        ),
    },
    {
        title: 'Property Leases & Stands',
        category: 'Commercial',
        text: 'Allocation and management of residential, commercial, industrial stands, and flea market trading bays at Mutoko Centre.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M3 9h18M9 21V9" />
            </svg>
        ),
    },
    {
        title: 'Social Welfare & Education',
        category: 'Community Support',
        text: 'Grants-in-aid to 84 primary and 44 secondary schools, Chikondoma Stadium management, community halls, and welfare relief.',
        icon: (
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
        ),
    },
];

export default function Services({ services }: { services: Service[] }) {
    const locale = usePublicLocale();
    const [search, setSearch] = useState('');

    const filteredServices = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return services;
        return services.filter((s) => `${s.name} ${s.summary ?? ''}`.toLowerCase().includes(q));
    }, [services, search]);

    return (
        <PublicLayout>
            <Head title="Council Municipal Services & Citizen Procedures" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Services</span>
                    </nav>
                    <span className="about-accent-eyebrow">STATUTORY MUNICIPAL SERVICES · RDC ACT [CAP 29:13]</span>
                    <h1>Public Services &amp; Citizen Procedures</h1>
                    <p className="about-page-hero-subtitle">
                        Comprehensive service guides, application requirements, and procedures for residents, businesses, and investors across Mutoko District.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Service delivery statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">Wards Served</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">84+</div>
                                <div className="about-stat-label">Primary Schools Supported</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Stand 366</div>
                                <div className="about-stat-label">Central Service Desk</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Transparent</div>
                                <div className="about-stat-label">Clear Fees &amp; Steps</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* CORE PORTFOLIOS SECTION */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="portfolios-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">COUNCIL DEVOLVED MANDATES</span>
                    <h2 id="portfolios-heading">Core Municipal Responsibilities</h2>
                    <p className="about-page-section-intro">
                        Under the Rural District Councils Act [Chapter 29:13], council coordinates essential social infrastructure, public hygiene, and economic spatial development.
                    </p>
                    <div className="services-catalog-grid">
                        {CORE_PORTFOLIOS.map((item) => (
                            <div key={item.title} className="service-item-card">
                                <span className="service-item-badge">{item.category}</span>
                                <h3 className="service-item-title">{item.title}</h3>
                                <p className="service-item-desc">{item.text}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* APPROVED SERVICE GUIDES */}
            <section className="about-page-section" aria-labelledby="guides-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">CITIZEN &amp; BUSINESS DIRECTORY</span>
                    <h2 id="guides-heading">Approved Service Guides</h2>
                    <p className="about-page-section-intro">
                        Step-by-step instructions on applying for plan approvals, business licenses, stand leases, water connections, and certificate issuances.
                    </p>

                    {/* SEARCH BOX */}
                    <div className="dept-search-box" style={{ maxWidth: 360, margin: '20px 0 28px' }}>
                        <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <label className="sr-only" htmlFor="service-search">Search municipal services</label>
                        <input
                            id="service-search"
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search services (e.g. plans, water, license)..."
                            className="dept-search-input"
                        />
                    </div>

                    {filteredServices.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                No service guides match &ldquo;{search}&rdquo;. For direct inquiries, contact council customer care at Stand 366 Mutoko Centre.
                            </p>
                            {search && (
                                <button
                                    type="button"
                                    className="dept-filter-btn"
                                    style={{ marginTop: 12 }}
                                    onClick={() => setSearch('')}
                                >
                                    Clear search
                                </button>
                            )}
                        </div>
                    ) : (
                        <div className="services-catalog-grid">
                            {filteredServices.map((service) => (
                                <Link
                                    key={service.slug}
                                    href={`/${locale}/services/${service.slug}`}
                                    className="service-item-card"
                                >
                                    <span className="service-item-badge">Official Guide</span>
                                    <h3 className="service-item-title">{service.name}</h3>
                                    <p className="service-item-desc">
                                        {service.summary ?? 'Check required documentation, applicable fees, approval processing times, and department contacts.'}
                                    </p>
                                    <div className="service-item-cta">
                                        <span>View step-by-step procedure</span>
                                        <span aria-hidden="true">→</span>
                                    </div>
                                </Link>
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
                            <span>Council Helpdesk &amp; Resident Assistance</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/rates`} className="dept-portal-card">
                                <h4>Tariffs &amp; Ratepayer Billing</h4>
                                <p>Check current gazetted council tariffs, development levies, and payment instructions.</p>
                                <span>Check rates information →</span>
                            </Link>

                            <Link href={`/${locale}/projects`} className="dept-portal-card">
                                <h4>District Infrastructure Projects</h4>
                                <p>Track ongoing and planned capital civil works funded under the Devolution Fund.</p>
                                <span>View capital projects →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>Submit Feedback or Enquiries</h4>
                                <p>Report a municipal issue, lodge a compliment, or request direct council assistance.</p>
                                <span>Send message to council →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

