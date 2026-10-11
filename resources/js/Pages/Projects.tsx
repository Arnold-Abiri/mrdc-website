import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';

type Project = {
    slug: string;
    title: string;
    project_type: string;
    project_status: string;
    location: string | null;
    summary: string | null;
    progress_percent: number | null;
};

const statusLabels: Record<string, { labelKey: TranslationKey; class: string }> = {
    planned: { labelKey: 'statusPlanned' as TranslationKey, class: 'project-status-planned' },
    ongoing: { labelKey: 'statusOngoing' as TranslationKey, class: 'project-status-ongoing' },
    completed: { labelKey: 'statusCompleted' as TranslationKey, class: 'project-status-completed' },
    on_hold: { labelKey: 'statusOnHold' as TranslationKey, class: 'project-status-on_hold' },
    cancelled: { labelKey: 'statusCancelled' as TranslationKey, class: 'project-status-on_hold' },
};

export default function Projects({
    projects,
    filters,
}: {
    projects: Project[];
    filters: { status: string | null; type: string | null; department: string | null; ward: string | null };
}) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();

    return (
        <PublicLayout>
            <Head title={t('projHeadTitle')} />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/services`}>{t('services')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('projects')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('projEyebrow')}</span>
                    <h1>{t('projTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('projSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Projects overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statDevolution')}</div>
                                <div className="about-stat-label">{t('statIntergov')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('stat29Wards')}</div>
                                <div className="about-stat-label">{t('statEquitable')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{projects.length}</div>
                                <div className="about-stat-label">{t('statTracked')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statWadcoShort')}</div>
                                <div className="about-stat-label">{t('statCommunityPrior')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN PROJECTS LIST */}
            <section className="about-page-section" aria-labelledby="projects-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('repoEyebrow')}</span>
                    <h2 id="projects-heading">{t('repoTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('repoIntro')}
                    </p>

                    {/* FILTER TOOLBAR */}
                    <form method="get" action={`/${locale}/projects`} className="transparency-filter-bar">
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, flexGrow: 1, minWidth: 200 }}>
                            <label htmlFor="projects-status" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                {t('filterStatus')}
                            </label>
                            <select
                                id="projects-status"
                                name="status"
                                defaultValue={filters.status ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">{t('filterAllStatuses')}</option>
                                <option value="ongoing">{t('statusOngoing')}</option>
                                <option value="completed">{t('filterCommissioned')}</option>
                                <option value="planned">{t('filterPlanned')}</option>
                                <option value="on_hold">{t('filterOnHold')}</option>
                            </select>
                        </div>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, minWidth: 180 }}>
                            <label htmlFor="projects-type" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                {t('filterScope')}
                            </label>
                            <select
                                id="projects-type"
                                name="type"
                                defaultValue={filters.type ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">{t('filterAllScopes')}</option>
                                <option value="project">{t('filterCapital')}</option>
                                <option value="programme">{t('filterProgramme')}</option>
                            </select>
                        </div>

                        <div style={{ display: 'flex', gap: 8, alignSelf: 'flex-end' }}>
                            <button
                                type="submit"
                                className="btn-mandate-action"
                                style={{ padding: '9px 18px', fontSize: '0.86rem' }}
                            >
                                {t('filterProjectsBtn')}
                            </button>
                            {(filters.status || filters.type) && (
                                <Link
                                    href={`/${locale}/projects`}
                                    className="dept-filter-btn"
                                    style={{ padding: '9px 14px', textDecoration: 'none' }}
                                >
                                    {t('resetLink')}
                                </Link>
                            )}
                        </div>
                    </form>

                    {/* PROJECTS GRID */}
                    {projects.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noProjectsMatch')}
                            </p>
                        </div>
                    ) : (
                        <div className="dept-directory-grid">
                            {projects.map((project) => {
                                const statusConfig = statusLabels[project.project_status] ?? {
                                    label: project.project_status,
                                    class: 'project-status-planned',
                                };
                                const progress = project.progress_percent !== null ? project.progress_percent : 0;
                                return (
                                    <Link
                                        key={project.slug}
                                        href={`/${locale}/projects/${project.slug}`}
                                        className="project-card-modern"
                                    >
                                        <div className="project-card-top">
                                            <span className={`project-status-pill ${statusConfig.class}`}>
                                                {t(statusConfig.labelKey)}
                                            </span>
                                            {project.location && (
                                                <span className="project-card-location">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                        <circle cx="12" cy="10" r="3" />
                                                    </svg>
                                                    <span>{project.location}</span>
                                                </span>
                                            )}
                                        </div>

                                        <h3 className="project-card-title">{project.title}</h3>

                                        <p className="project-card-summary">
                                            {project.summary ?? t('projSummaryFallback')}
                                        </p>

                                        {project.progress_percent !== null && (
                                            <div className="project-progress-wrapper">
                                                <div className="project-progress-labels">
                                                    <span>{t('civilProgress')}</span>
                                                    <span>{progress}%</span>
                                                </div>
                                                <div className="project-progress-bar-bg">
                                                    <div
                                                        className="project-progress-bar-fill"
                                                        style={{ width: `${Math.min(100, Math.max(0, progress))}%` }}
                                                    />
                                                </div>
                                            </div>
                                        )}

                                        <div className="dept-card-footer">
                                            <span>{t('viewTimeline')}</span>
                                            <span aria-hidden="true">→</span>
                                        </div>
                                    </Link>
                                );
                            })}
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
                            <span>{t('portalProcurement')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/tenders`} className="dept-portal-card">
                                <h4>{t('portalTenders')}</h4>
                                <p>{t('portalTendersDesc')}</p>
                                <span>{t('portalTendersLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/transparency`} className="dept-portal-card">
                                <h4>{t('portalDevolution')}</h4>
                                <p>{t('portalDevolutionDesc')}</p>
                                <span>{t('portalDevolutionLink')} →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>{t('portalProjFeedback')}</h4>
                                <p>{t('portalProjFeedbackDesc')}</p>
                                <span>{t('portalProjFeedbackLink')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

