import { useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
import { useSiteSettings } from '../useSiteSettings';
import { EmptyState } from '../Components/public/PageHeader';
import { formatDate } from '../formatDate';

type Vacancy = {
    slug: string;
    title: string;
    grade: string | null;
    reference: string | null;
    employment_type: string | null;
    description: string;
    opens_at: string | null;
    closes_at: string | null;
    is_open: boolean;
    department: string | null;
};

const employmentLabels: Record<string, TranslationKey> = {
    full_time: 'empFullTime',
    part_time: 'empPartTime',
    contract: 'empContract',
    temporary: 'empTemporary',
    internship: 'empInternship',
};

export function daysRemaining(closesAt: string | null): number | null {
    if (!closesAt) {
        return null;
    }
    const closing = closesAt.includes('T') ? new Date(closesAt) : new Date(`${closesAt}T23:59:59`);
    if (Number.isNaN(closing.getTime())) {
        return null;
    }
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.ceil((closing.getTime() - today.getTime()) / 86400000);
}

function VacancyCardModern({ vacancy, locale }: { vacancy: Vacancy; locale: string }) {
    const t = usePublicTranslation();
    const remaining = vacancy.is_open ? daysRemaining(vacancy.closes_at) : null;
    const isUrgent = remaining !== null && remaining <= 5 && remaining >= 0;

    return (
        <article className="vacancy-card-modern">
            <div className="vacancy-top-pills">
                <div style={{ display: 'flex', gap: '6px', alignItems: 'center', flexWrap: 'wrap' }}>
                    {vacancy.grade && <span className="vacancy-pill-grade">{t('grade')} {vacancy.grade}</span>}
                    {vacancy.department && (
                        <span style={{ fontSize: '0.72rem', fontWeight: 700, color: 'var(--color-deep-green)', background: 'rgba(11, 143, 98, 0.1)', padding: '3px 8px', borderRadius: '6px' }}>
                            {vacancy.department}
                        </span>
                    )}
                </div>
                <span className="vacancy-pill-type">
                    {vacancy.employment_type && employmentLabels[vacancy.employment_type] ? t(employmentLabels[vacancy.employment_type]) : t('empFullTime')}
                </span>
            </div>

            <h3 className="vacancy-title-modern">
                <Link href={`/${locale}/vacancies/${vacancy.slug}`}>{vacancy.title}</Link>
            </h3>

            <div className="vacancy-meta-row">
                <span className="vacancy-meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span>Stand 366, Mutoko Centre</span>
                </span>
                {vacancy.reference && (
                    <span className="vacancy-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M7 7h10" />
                            <path d="M7 12h10" />
                            <path d="M7 17h10" />
                        </svg>
                        <span>{vacancy.reference}</span>
                    </span>
                )}
            </div>

            <p className="vacancy-summary-modern">
                {vacancy.description.length > 170 ? `${vacancy.description.slice(0, 170)}…` : vacancy.description}
            </p>

            <div className="vacancy-card-footer-modern">
                <span className={`vacancy-closes-chip ${isUrgent ? 'vacancy-closes-urgent' : 'text-muted'}`}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    {vacancy.is_open
                        ? vacancy.closes_at
                            ? `${t('closesOn')} ${formatDate(vacancy.closes_at, locale)}${remaining !== null ? ` (${remaining}${t('dayUnit')} ${t('daysLeft')})` : ''}`
                            : t('openForApplications')
                        : vacancy.closes_at ? `${t('closedOn')} ${formatDate(vacancy.closes_at, locale)}` : t('applicationsClosed')}
                </span>

                <Link href={`/${locale}/vacancies/${vacancy.slug}`} className="vacancy-view-cta">
                    <span>{t('viewOpening')}</span>
                    <span aria-hidden="true">→</span>
                </Link>
            </div>
        </article>
    );
}

export default function Vacancies({ vacancies }: { vacancies: Vacancy[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const settings = useSiteSettings();
    const recruitmentEmail = settings.recruitment_email?.trim() || 'recruitment@mutokordc.co.zw';

    const [query, setQuery] = useState('');
    const [department, setDepartment] = useState('all');
    const [showClosed, setShowClosed] = useState(true);

    const departments = useMemo(
        () => [...new Set(vacancies.map((vacancy) => vacancy.department).filter((name): name is string => Boolean(name)))].sort(),
        [vacancies]
    );
    const openCount = vacancies.filter((vacancy) => vacancy.is_open).length;

    const filtered = vacancies.filter((vacancy) => {
        if (!vacancy.is_open && !showClosed) {
            return false;
        }
        if (department !== 'all' && vacancy.department !== department) {
            return false;
        }
        if (query.trim() !== '' && !`${vacancy.title} ${vacancy.reference ?? ''} ${vacancy.grade ?? ''}`.toLowerCase().includes(query.trim().toLowerCase())) {
            return false;
        }
        return true;
    });

    const openVacancies = filtered.filter((vacancy) => vacancy.is_open);
    const closedVacancies = filtered.filter((vacancy) => !vacancy.is_open);

    return (
        <PublicLayout>
            <Head title="Careers & Municipal Vacancies | Mutoko RDC" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>{t('opportunities')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('careersCrumb')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('careersEyebrow')}</span>
                    <h1>{t('careersTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('careersSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Careers statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{openCount}</div>
                                <div className="about-stat-label">{t('statActiveVacancies')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{departments.length || '6'}</div>
                                <div className="about-stat-label">{t('statHiringDepts')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statStand366')}</div>
                                <div className="about-stat-label">{t('statCivicHq')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statOpen')}</div>
                                <div className="about-stat-label">{t('statStudentAttach')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN CONTENT */}
            <section className="about-page-section" aria-labelledby="vacancies-list">
                <div className="container">

                    {/* STUDENT & GRADUATE ATTACHMENT BANNER (Inspiration from Harare City) */}
                    <div className="attachment-banner">
                        <div className="attachment-banner-left">
                            <div className="attachment-banner-icon" aria-hidden="true">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                                    <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5" />
                                </svg>
                            </div>
                            <div>
                                <h3 className="attachment-banner-title">{t('attachTitle')}</h3>
                                <p className="attachment-banner-desc">
                                    {t('attachDesc')}
                                </p>
                            </div>
                        </div>
                        <a
                            href={`mailto:${recruitmentEmail}?subject=Application%20for%20Industrial%20Attachment%20Placement`}
                            className="attachment-banner-btn"
                        >
                            <span>{t('attachApply')}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    {/* HEADER CONTROL BAR WITH CONFIGURABLE APPLICATION EMAIL */}
                    <div className="vacancies-header-card">
                        <div>
                            <span className="about-accent-eyebrow">{t('jobVacanciesEyebrow')}</span>
                            <h2 id="vacancies-list" style={{ margin: 0, fontSize: '1.5rem', color: 'var(--color-civic-navy)', fontWeight: 800 }}>
                                {t('activePostings')} ({openCount})
                            </h2>
                        </div>
                        <div className="vacancies-contact-badge">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <span>{t('applicationsLabel')}: <strong><a href={`mailto:${recruitmentEmail}`} style={{ color: 'inherit', textDecoration: 'none' }}>{recruitmentEmail}</a></strong></span>
                        </div>
                    </div>

                    {vacancies.length === 0 ? (
                        <EmptyState title={t('noVacanciesTitle')} text={t('noVacanciesDesc')} />
                    ) : (
                        <>
                            {/* SEARCH & FILTER BAR */}
                            <form className="about-filter-bar" onSubmit={(event) => event.preventDefault()} aria-label={t('filterVacancies')}>
                                <label>
                                    {t('searchLabel')}
                                    <input
                                        type="search"
                                        value={query}
                                        onChange={(event) => setQuery(event.target.value)}
                                        placeholder={t('searchVacancyPlaceholder')}
                                    />
                                </label>
                                <label>
                                    {t('department')}
                                    <select value={department} onChange={(event) => setDepartment(event.target.value)}>
                                        <option value="all">{t('allDepartments')}</option>
                                        {departments.map((name) => (
                                            <option key={name} value={name}>{name}</option>
                                        ))}
                                    </select>
                                </label>
                                <label className="about-filter-check">
                                    <input
                                        type="checkbox"
                                        checked={showClosed}
                                        onChange={(event) => setShowClosed(event.target.checked)}
                                    />
                                    {t('showClosed')}
                                </label>
                            </form>

                            {filtered.length === 0 ? (
                                <EmptyState title={t('noMatchVacanciesTitle')} text={t('noMatchVacanciesDesc')} />
                            ) : (
                                <>
                                    {openVacancies.length > 0 && (
                                        <div style={{ marginBottom: '36px' }}>
                                            <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                                                {t('nowHiring')} ({openVacancies.length})
                                            </h3>
                                            <div className="vacancies-modern-grid">
                                                {openVacancies.map((vacancy) => (
                                                    <VacancyCardModern key={vacancy.slug} vacancy={vacancy} locale={locale} />
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {closedVacancies.length > 0 && (
                                        <div style={{ marginBottom: '36px' }}>
                                            <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: '#64748B', margin: '0 0 16px' }}>
                                                {t('archivedClosed')} ({closedVacancies.length})
                                            </h3>
                                            <div className="vacancies-modern-grid">
                                                {closedVacancies.map((vacancy) => (
                                                    <VacancyCardModern key={vacancy.slug} vacancy={vacancy} locale={locale} />
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </>
                            )}

                            {/* APPLICATION GUIDELINES */}
                            <div className="rates-guidance-card" style={{ marginTop: '24px' }}>
                                <h3>{t('guidelinesTitle')}</h3>
                                <p>
                                    {t('guidelinesP1')}
                                </p>
                                <p>
                                    {t('guidelinesP2a')} <strong>Chief Executive Officer, Mutoko Rural District Council, Stand 366 Mutoko Centre</strong>, {t('guidelinesP2b')} <strong><a href={`mailto:${recruitmentEmail}`} style={{ color: 'var(--color-deep-green)' }}>{recruitmentEmail}</a></strong>.
                                </p>
                                <p style={{ margin: 0, fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                    {t('guidelinesP3')}
                                </p>
                            </div>
                        </>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}

