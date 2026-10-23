import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type ChairpersonData = {
    slug: string;
    name: string;
    title: string;
    biography: string | null;
    photo_url: string | null;
};

type UpcomingMeeting = {
    id: number;
    title: string;
    scheduled_date: string | null;
    scheduled_time: string | null;
    venue: string | null;
};

export default function Chairperson({
    chairperson,
    upcoming_meetings = [],
}: {
    chairperson: ChairpersonData;
    upcoming_meetings?: UpcomingMeeting[];
}) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={`${chairperson.name} — ${chairperson.title} | Mutoko RDC`} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('wardsCrumbCouncil')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/officials`}>{t('officials')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{chairperson.title}</span>
                    </nav>
                    <span className="about-accent-eyebrow">MUTOKO RDC · POLITICAL WING &amp; FULL COUNCIL</span>
                    <h1>{chairperson.name}</h1>
                    <p className="about-page-hero-subtitle">
                        {chairperson.title} — Presiding Head of Council &amp; Champion of Devolution in Mutoko District
                    </p>
                </div>
            </header>

            {/* EXECUTIVE STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Leadership scope">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29</div>
                                <div className="about-stat-label">Electoral Wards Governed</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Cap 29:13</div>
                                <div className="about-stat-label">Statutory Legal Authority</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Full Council</div>
                                <div className="about-stat-label">Presiding Authority</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Vision 2030</div>
                                <div className="about-stat-label">Devolved District Growth</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN CONTENT SECTION */}
            <section className="about-page-section" aria-labelledby="chairperson-profile-heading">
                <div className="container">
                    <div className="chairperson-portal-layout">
                        {/* LEFT COLUMN: HERO CARD & PROTOCOL */}
                        <aside className="chairperson-profile-sidebar">
                            <div className="chairperson-portrait-card">
                                <div className="chairperson-portrait-frame">
                                    {chairperson.photo_url ? (
                                        <img
                                            src={chairperson.photo_url}
                                            alt={`Official portrait of ${chairperson.name}`}
                                            className="chairperson-portrait-img"
                                        />
                                    ) : (
                                        <img
                                            src="/images/council-crest-light.webp"
                                            alt={t('crestFallbackAlt')}
                                            className="official-crest-fallback"
                                        />
                                    )}
                                    <span className="chairperson-dignity-badge">
                                        Elected Council Chairperson
                                    </span>
                                </div>
                                <div className="chairperson-card-info">
                                    <h2 id="chairperson-profile-heading" className="chairperson-card-name">
                                        {chairperson.name}
                                    </h2>
                                    <div className="chairperson-card-role">{chairperson.title}</div>
                                    <p className="chairperson-card-motto">
                                        &ldquo;Committed to transparent leadership, community participation, and people-driven devolution across every ward in Mutoko.&rdquo;
                                    </p>
                                </div>

                                <div className="chairperson-desk-block">
                                    <h3 className="chairperson-desk-title">Office of the Chairperson</h3>
                                    <dl className="chairperson-desk-meta">
                                        <div>
                                            <dt>Jurisdiction</dt>
                                            <dd>Mutoko Rural District Council</dd>
                                        </div>
                                        <div>
                                            <dt>Seat of Office</dt>
                                            <dd>Stand 366, Mutoko Centre, Mashonaland East</dd>
                                        </div>
                                        <div>
                                            <dt>Executive Body</dt>
                                            <dd>Full Council &amp; Standing Committees</dd>
                                        </div>
                                    </dl>
                                    <Link
                                        href={`/${locale}/contact?context=office-of-the-chairperson`}
                                        className="rates-pop-btn-primary"
                                        style={{ width: '100%', justifyContent: 'center', marginTop: '16px' }}
                                    >
                                        <span>Request Courtesy Call / Official Desk</span>
                                        <span aria-hidden="true">→</span>
                                    </Link>
                                </div>
                            </div>

                            {/* UPCOMING FULL COUNCIL SESSIONS */}
                            <div className="chairperson-sessions-card">
                                <h3>Full Council Deliberations</h3>
                                <p style={{ fontSize: '0.85rem', color: '#64748B', margin: '0 0 14px' }}>
                                    Full Council sessions presided over by the Council Chairperson are open to public observation in terms of the RDC Act.
                                </p>
                                {upcoming_meetings.length > 0 ? (
                                    <ul className="chairperson-meeting-list">
                                        {upcoming_meetings.map((m) => (
                                            <li key={m.id} className="chairperson-meeting-item">
                                                <div className="chairperson-meeting-date">
                                                    {m.scheduled_date ? new Date(m.scheduled_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }) : 'TBA'}
                                                </div>
                                                <div>
                                                    <h4 className="chairperson-meeting-title">{m.title}</h4>
                                                    <span className="chairperson-meeting-venue">{m.venue ?? 'Council Boardroom'}</span>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p style={{ fontSize: '0.86rem', fontStyle: 'italic', color: '#94A3B8', margin: 0 }}>
                                        Next session dates will be officially published once confirmed by Secretariat.
                                    </p>
                                )}
                                <div style={{ marginTop: '14px' }}>
                                    <Link href={`/${locale}/meetings`} className="about-page-text-link" style={{ fontSize: '0.85rem' }}>
                                        View Full Meetings &amp; Agendas Calendar →
                                    </Link>
                                </div>
                            </div>
                        </aside>

                        {/* RIGHT COLUMN: EXECUTIVE STATEMENT & STATUTORY MANDATE */}
                        <div className="chairperson-profile-body">
                            {/* EXECUTIVE ADDRESS */}
                            <article className="chairperson-address-card">
                                <span className="about-accent-eyebrow">CHAIRPERSON'S ADDRESS</span>
                                <h3 className="chairperson-section-title">Message from Alderman Trust Kachidza</h3>
                                <div className="chairperson-address-content">
                                    <p className="lead-quote">
                                        &ldquo;Our vision is an empowered, resilient, and economically thriving Mutoko District where quality healthcare, clean water, sound road networks, and modern schools are accessible to every resident.&rdquo;
                                    </p>
                                    <p>
                                        {chairperson.biography}
                                    </p>
                                    <p>
                                        As the Council Chairperson, it is my honour to lead our esteemed body of 29 elected Ward Councillors in working shoulder-to-shoulder with our Traditional Chiefs, Village Heads, Ward Development Committees (WADCOs), and our dedicated Executive Management team led by the Chief Executive Officer.
                                    </p>
                                    <p>
                                        Through the Second Republic's devolution framework, Mutoko Rural District Council has transitioned from mere administrative presence into an active developmental catalyst. We are putting public resources where they matter most: delivering primary health care to rural communities, upgrading bridges and agricultural feeder roads, expanding access to piped water, and ensuring our rich mineral resources—particularly black granite—directly benefit the communities from which they are extracted.
                                    </p>
                                </div>
                            </article>

                            {/* CORE FOCUS PILLARS */}
                            <div className="chairperson-pillars-wrap">
                                <span className="about-accent-eyebrow">STRATEGIC PILLARS</span>
                                <h3 className="chairperson-section-title">Devolution &amp; Community Transformation Priorities</h3>

                                <div className="chairperson-pillars-grid">
                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                            </svg>
                                        </div>
                                        <h4>Primary Health Delivery</h4>
                                        <p>
                                            Spearheading modern clinics across rural wards—exemplified by the commissioning of <strong>Chisambiro Clinic (Ward 18)</strong>—drastically cutting walking distances for mothers, children, and the elderly.
                                        </p>
                                    </div>

                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                            </svg>
                                        </div>
                                        <h4>Granite Value Addition &amp; Mining</h4>
                                        <p>
                                            Advocating for local cutting, polishing, and corporate social investment by mining conglomerates to ensure Mutoko's black granite leaves a lasting infrastructure legacy.
                                        </p>
                                    </div>

                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                            </svg>
                                        </div>
                                        <h4>Horticultural Hub &amp; Food Security</h4>
                                        <p>
                                            Strengthening Mutoko's status as Zimbabwe's horticulture capital through cold-chain storage partnerships, tomato agro-processing facilities, and irrigation development.
                                        </p>
                                    </div>

                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                        </div>
                                        <h4>Ward Democracy &amp; Devolution</h4>
                                        <p>
                                            Ensuring participatory governance through rigorous annual budget consultations across all 29 wards so every dollar allocated reflects citizen-voiced priorities.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* STATUTORY MANDATE CALLOUT */}
                            <div className="chairperson-statutory-card">
                                <h4>Statutory Role of the Council Chairperson</h4>
                                <p>
                                    Under <strong>Section 47 of the Rural District Councils Act [Chapter 29:13]</strong>, the Council Chairperson is elected by the Council from among the Councillors. The Chairperson:
                                </p>
                                <ul>
                                    <li>Presides over all Full Council meetings and ensures lawful conduct of municipal business.</li>
                                    <li>Signs and validates council resolutions, statutory by-laws, and master planning schemes.</li>
                                    <li>Serves as the civic and ceremonial representative of Mutoko District at national and international forums.</li>
                                    <li>Exercises leadership over the political wing in formulating policies for implementation by the Chief Executive Officer.</li>
                                </ul>
                            </div>

                            {/* FOOTER DIRECTORY SHORTCUTS */}
                            <div className="chairperson-quicknav">
                                <Link href={`/${locale}/officials`} className="chairperson-navlink">
                                    <span>← View All Council Officials &amp; Management</span>
                                </Link>
                                <Link href={`/${locale}/wards`} className="chairperson-navlink">
                                    <span>Explore All 29 Electoral Wards &amp; Councillors →</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
