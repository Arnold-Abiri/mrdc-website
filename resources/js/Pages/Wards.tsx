import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type WardSummary = { slug: string; name: string; description: string | null };

export default function Wards({ wards }: { wards: WardSummary[] }) {
    const locale = usePublicLocale();
    const [search, setSearch] = useState('');

    const filteredWards = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return wards;
        return wards.filter((w) => `${w.name} ${w.description ?? ''}`.toLowerCase().includes(q));
    }, [wards, search]);

    return (
        <PublicLayout>
            <Head title="Council Wards (1–29) & Electoral Boundaries" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>Council</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Wards</span>
                    </nav>
                    <span className="about-accent-eyebrow">ELECTORAL &amp; ADMINISTRATIVE BOUNDARIES · MUTOKO DISTRICT</span>
                    <h1>Council Wards Directory</h1>
                    <p className="about-page-hero-subtitle">
                        29 administrative wards representing rural communal lands, resettlement schemes, and the Mutoko Centre commercial growth point under local democratic governance.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Ward territorial statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">Electoral Wards</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">100%</div>
                                <div className="about-stat-label">WADCO Representation</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Devolution</div>
                                <div className="about-stat-label">Decentralised Budgeting</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Stand 366</div>
                                <div className="about-stat-label">Central Coordination Office</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN WARDS SECTION */}
            <section className="about-page-section" aria-labelledby="wards-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">COMMUNITY DIRECTORY</span>
                    <h2 id="wards-heading">Find Your Local Ward</h2>
                    <p className="about-page-section-intro">
                        Select your ward to see its elected councillor, public clinics, primary and secondary schools, community boreholes, and developmental priorities submitted through Ward Development Committees (WADCOs).
                    </p>

                    {/* SEARCH HEADER */}
                    <div className="wards-lookup-header">
                        <div>
                            <h3 style={{ margin: '0 0 4px', fontSize: '1.05rem', fontWeight: 800, color: 'var(--color-civic-navy)' }}>
                                Ward Filter &amp; Search
                            </h3>
                            <p style={{ margin: 0, fontSize: '0.86rem', color: '#64748B' }}>
                                Search by ward number or community keyword (e.g. &ldquo;Ward 5&rdquo;, &ldquo;Ward 12&rdquo;)
                            </p>
                        </div>

                        <div className="dept-search-box" style={{ maxWidth: 320 }}>
                            <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <label className="sr-only" htmlFor="ward-search">Search wards</label>
                            <input
                                id="ward-search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search wards (e.g. Ward 1)..."
                                className="dept-search-input"
                            />
                        </div>
                    </div>

                    {/* WARDS GRID */}
                    {filteredWards.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                No wards match your search &ldquo;{search}&rdquo;.
                            </p>
                            <button
                                type="button"
                                className="dept-filter-btn"
                                style={{ marginTop: 12 }}
                                onClick={() => setSearch('')}
                            >
                                Clear search
                            </button>
                        </div>
                    ) : (
                        <div className="wards-grid">
                            {filteredWards.map((ward) => (
                                <Link
                                    key={ward.slug}
                                    href={`/${locale}/wards/${ward.slug}`}
                                    className="ward-card-modern"
                                >
                                    <div className="ward-card-number-badge">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                                            <line x1="8" y1="2" x2="8" y2="18" />
                                            <line x1="16" y1="6" x2="16" y2="22" />
                                        </svg>
                                        <span>Mutoko District</span>
                                    </div>

                                    <h3 className="ward-card-title">{ward.name}</h3>

                                    <p className="ward-card-desc">
                                        {ward.description ?? 'Local developmental priorities, ward councillor representation, and community infrastructure.'}
                                    </p>

                                    <div className="ward-card-link">
                                        <span>Explore Ward Profile</span>
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
                            <span>Ward Participation &amp; Council Interaction</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/officials`} className="dept-portal-card">
                                <h4>Elected Ward Councillors</h4>
                                <p>Meet the political leaders representing your ward in full council sessions.</p>
                                <span>View councillor directory →</span>
                            </Link>

                            <Link href={`/${locale}/meetings`} className="dept-portal-card">
                                <h4>Full Council &amp; Ward Hearings</h4>
                                <p>Review statutory meeting dates, public budget consultations, and minutes.</p>
                                <span>Check meeting dates →</span>
                            </Link>

                            <Link href={`/${locale}/contact`} className="dept-portal-card">
                                <h4>Report a Local Community Issue</h4>
                                <p>Submit road repairs, borehole faults, or local clinic enquiries directly to council.</p>
                                <span>Contact council helpdesk →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

