import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type OfficialSummary = {
    slug: string;
    name: string;
    title: string;
    biography: string | null;
    department: string | null;
    photo_url: string | null;
};

export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    const locale = usePublicLocale();
    const [filter, setFilter] = useState<'all' | 'executive' | 'secretariat'>('all');
    const [search, setSearch] = useState('');

    const filteredOfficials = useMemo(() => {
        const q = search.trim().toLowerCase();
        return officials.filter((o) => {
            const matchesQuery = !q || `${o.name} ${o.title} ${o.department ?? ''} ${o.biography ?? ''}`.toLowerCase().includes(q);
            const isExecutive = o.title.toLowerCase().includes('chairperson') || o.title.toLowerCase().includes('chief executive') || o.title.toLowerCase().includes('secretary');
            if (filter === 'executive') return matchesQuery && isExecutive;
            if (filter === 'secretariat') return matchesQuery && !isExecutive;
            return matchesQuery;
        });
    }, [officials, filter, search]);

    return (
        <PublicLayout>
            <Head title="Council Officials & Leadership" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>Council</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Officials</span>
                    </nav>
                    <span className="about-accent-eyebrow">COUNCIL LEADERSHIP &amp; MANAGEMENT · STAND 366 MUTOKO</span>
                    <h1>Elected &amp; Appointed Officials</h1>
                    <p className="about-page-hero-subtitle">
                        The political and administrative leadership entrusted with governance, policy formulation, and executive management for Mutoko Rural District Council.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Leadership statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">Wards Represented</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">1</div>
                                <div className="about-stat-label">Chief Executive Officer</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{officials.length}</div>
                                <div className="about-stat-label">Published Executive Profiles</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Cap 29:13</div>
                                <div className="about-stat-label">Statutory Governance</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN ROSTER */}
            <section className="about-page-section" aria-labelledby="officials-roster">
                <div className="container">
                    <span className="about-accent-eyebrow">DIRECTORY &amp; PORTFOLIOS</span>
                    <h2 id="officials-roster">Management &amp; Governance Team</h2>
                    <p className="about-page-section-intro">
                        Full-time directors and appointed officials overseeing technical engineering, fiscal stewardship, town planning, social services, and internal audits.
                    </p>

                    {/* SEARCH & FILTERS */}
                    <div className="dept-search-filter-wrap">
                        <div className="dept-filter-pills" role="tablist" aria-label="Filter officials">
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'all' ? 'active' : ''}`}
                                onClick={() => setFilter('all')}
                            >
                                All Leadership ({officials.length})
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'executive' ? 'active' : ''}`}
                                onClick={() => setFilter('executive')}
                            >
                                Executive Offices
                            </button>
                            <button
                                type="button"
                                className={`dept-filter-btn ${filter === 'secretariat' ? 'active' : ''}`}
                                onClick={() => setFilter('secretariat')}
                            >
                                Directorate Heads &amp; Officers
                            </button>
                        </div>

                        <div className="dept-search-box">
                            <svg className="dept-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <label className="sr-only" htmlFor="official-search">Search officials by name or title</label>
                            <input
                                id="official-search"
                                type="search"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search by name, engineer, finance..."
                                className="dept-search-input"
                            />
                        </div>
                    </div>

                    {/* GRID */}
                    {filteredOfficials.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                No officials match your search &ldquo;{search}&rdquo;.
                            </p>
                            <button
                                type="button"
                                className="dept-filter-btn"
                                style={{ marginTop: 12 }}
                                onClick={() => { setSearch(''); setFilter('all'); }}
                            >
                                Clear search filters
                            </button>
                        </div>
                    ) : (
                        <div className="officials-directory-grid">
                            {filteredOfficials.map((official) => (
                                <article key={official.slug} className="official-profile-card">
                                    <div className="official-portrait-wrapper">
                                        {official.photo_url ? (
                                            <img
                                                src={official.photo_url}
                                                alt={`Portrait of ${official.name}`}
                                                loading="lazy"
                                            />
                                        ) : (
                                            <img
                                                src="/images/council-crest-light.webp"
                                                alt="Mutoko Council Crest"
                                                className="official-crest-fallback"
                                            />
                                        )}
                                        <span className="official-badge-role">
                                            {official.department ?? 'Executive Office'}
                                        </span>
                                    </div>

                                    <div className="official-profile-body">
                                        <h3 className="official-profile-name">{official.name}</h3>
                                        <div className="official-profile-title">{official.title}</div>
                                        {official.department && (
                                            <div className="official-profile-dept">{official.department}</div>
                                        )}
                                        <p className="official-profile-bio">
                                            {official.biography ?? 'Appointed public official serving Mutoko Rural District Council under the Local Government service.'}
                                        </p>
                                        <Link
                                            href={`/${locale}/officials/${official.slug}`}
                                            className="official-profile-action"
                                        >
                                            <span>View official profile &amp; contacts</span>
                                            <span aria-hidden="true">→</span>
                                        </Link>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}

                    {/* FOOTER STRIP */}
                    <div className="dept-portal-strip">
                        <div className="dept-portal-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            <span>Council Governance &amp; Ward Administration</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/wards`} className="dept-portal-card">
                                <h4>Find Your Ward &amp; Councillor</h4>
                                <p>Explore all 29 electoral wards and their local development priorities.</p>
                                <span>Browse wards directory →</span>
                            </Link>

                            <Link href={`/${locale}/departments`} className="dept-portal-card">
                                <h4>Council Directorates</h4>
                                <p>Review department duties across Engineering, Planning, Social Services, and Finance.</p>
                                <span>Explore departments →</span>
                            </Link>

                            <Link href={`/${locale}/meetings`} className="dept-portal-card">
                                <h4>Council Meetings &amp; Agendas</h4>
                                <p>Check dates for upcoming full council meetings, committees, and public hearings.</p>
                                <span>View meeting schedule →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

