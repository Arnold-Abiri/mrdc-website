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
                    <span className="about-accent-eyebrow">{t('chairpersonEyebrow')}</span>
                    <h1>{chairperson.name}</h1>
                    <p className="about-page-hero-subtitle">
                        {chairperson.title} — {t('chairpersonHeroSub')}
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
                                <div className="about-stat-label">{t('chairpersonStatWards')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Cap 29:13</div>
                                <div className="about-stat-label">{t('chairpersonStatAuthority')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Full Council</div>
                                <div className="about-stat-label">{t('chairpersonStatPresiding')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Vision 2030</div>
                                <div className="about-stat-label">{t('chairpersonStatGrowth')}</div>
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
                                        {t('chairpersonBadge')}
                                    </span>
                                </div>
                                <div className="chairperson-card-info">
                                    <h2 id="chairperson-profile-heading" className="chairperson-card-name">
                                        {chairperson.name}
                                    </h2>
                                    <div className="chairperson-card-role">{chairperson.title}</div>
                                    <p className="chairperson-card-motto">
                                        {t('chairpersonMotto')}
                                    </p>
                                </div>

                                <div className="chairperson-desk-block">
                                    <h3 className="chairperson-desk-title">{t('chairpersonDeskTitle')}</h3>
                                    <dl className="chairperson-desk-meta">
                                        <div>
                                            <dt>{t('chairpersonDeskJurisdiction')}</dt>
                                            <dd>{t('chairpersonDeskJurisdictionVal')}</dd>
                                        </div>
                                        <div>
                                            <dt>{t('chairpersonDeskSeat')}</dt>
                                            <dd>{t('chairpersonDeskSeatVal')}</dd>
                                        </div>
                                        <div>
                                            <dt>{t('chairpersonDeskBody')}</dt>
                                            <dd>{t('chairpersonDeskBodyVal')}</dd>
                                        </div>
                                    </dl>
                                    <Link
                                        href={`/${locale}/contact?context=office-of-the-chairperson`}
                                        className="rates-pop-btn-primary"
                                        style={{ width: '100%', justifyContent: 'center', marginTop: '16px' }}
                                    >
                                        <span>{t('chairpersonDeskCta')}</span>
                                        <span aria-hidden="true">→</span>
                                    </Link>
                                </div>
                            </div>

                            {/* UPCOMING FULL COUNCIL SESSIONS */}
                            <div className="chairperson-sessions-card">
                                <h3>{t('chairpersonDeliberations')}</h3>
                                <p style={{ fontSize: '0.85rem', color: '#64748B', margin: '0 0 14px' }}>
                                    {t('chairpersonDeliberationsDesc')}
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
                                        {t('chairpersonNextSessionTba')}
                                    </p>
                                )}
                                <div style={{ marginTop: '14px' }}>
                                    <Link href={`/${locale}/meetings`} className="about-page-text-link" style={{ fontSize: '0.85rem' }}>
                                        {t('chairpersonViewAgendas')}
                                    </Link>
                                </div>
                            </div>
                        </aside>

                        {/* RIGHT COLUMN: EXECUTIVE STATEMENT & STATUTORY MANDATE */}
                        <div className="chairperson-profile-body">
                            {/* EXECUTIVE ADDRESS */}
                            <article className="chairperson-address-card">
                                <span className="about-accent-eyebrow">{t('chairpersonAddressEyebrow')}</span>
                                <h3 className="chairperson-section-title">{t('chairpersonAddressTitle')}</h3>
                                <div className="chairperson-address-content">
                                    <p className="lead-quote">
                                        {t('chairpersonAddressQuote')}
                                    </p>
                                    <p>
                                        {chairperson.biography}
                                    </p>
                                    <p>
                                        {t('chairpersonAddressP2')}
                                    </p>
                                    <p>
                                        {t('chairpersonAddressP3')}
                                    </p>
                                </div>
                            </article>

                            {/* CORE FOCUS PILLARS */}
                            <div className="chairperson-pillars-wrap">
                                <span className="about-accent-eyebrow">{t('chairpersonPillarsEyebrow')}</span>
                                <h3 className="chairperson-section-title">{t('chairpersonPillarsTitle')}</h3>

                                <div className="chairperson-pillars-grid">
                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                            </svg>
                                        </div>
                                        <h4>{t('chairpersonPillarHealthTitle')}</h4>
                                        <p>
                                            {t('chairpersonPillarHealthDesc')}
                                        </p>
                                    </div>

                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                            </svg>
                                        </div>
                                        <h4>{t('chairpersonPillarGraniteTitle')}</h4>
                                        <p>
                                            {t('chairpersonPillarGraniteDesc')}
                                        </p>
                                    </div>

                                    <div className="chairperson-pillar-item">
                                        <div className="chairperson-pillar-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                            </svg>
                                        </div>
                                        <h4>{t('chairpersonPillarAgriTitle')}</h4>
                                        <p>
                                            {t('chairpersonPillarAgriDesc')}
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
                                        <h4>{t('chairpersonPillarDemocracyTitle')}</h4>
                                        <p>
                                            {t('chairpersonPillarDemocracyDesc')}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* STATUTORY MANDATE CALLOUT */}
                            <div className="chairperson-statutory-card">
                                <h4>{t('chairpersonStatutoryTitle')}</h4>
                                <p>
                                    {t('chairpersonStatutoryIntro')}
                                </p>
                                <ul>
                                    <li>{t('chairpersonStatutoryR1')}</li>
                                    <li>{t('chairpersonStatutoryR2')}</li>
                                    <li>{t('chairpersonStatutoryR3')}</li>
                                    <li>{t('chairpersonStatutoryR4')}</li>
                                </ul>
                            </div>

                            {/* FOOTER DIRECTORY SHORTCUTS */}
                            <div className="chairperson-quicknav">
                                <Link href={`/${locale}/officials`} className="chairperson-navlink">
                                    <span>{t('chairpersonNavOfficials')}</span>
                                </Link>
                                <Link href={`/${locale}/wards`} className="chairperson-navlink">
                                    <span>{t('chairpersonNavWards')}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
