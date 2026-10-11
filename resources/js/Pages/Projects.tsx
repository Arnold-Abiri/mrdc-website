import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Project = {
    slug: string;
    title: string;
    project_type: string;
    project_status: string;
    location: string | null;
    summary: string | null;
    progress_percent: number | null;
};

const statusLabels: Record<string, { label: string; class: string }> = {
    planned: { label: 'Planned / Tender Stage', class: 'project-status-planned' },
    ongoing: { label: 'Active Implementation', class: 'project-status-ongoing' },
    completed: { label: 'Commissioned & Completed', class: 'project-status-completed' },
    on_hold: { label: 'On Hold / Reprioritised', class: 'project-status-on_hold' },
    cancelled: { label: 'Cancelled', class: 'project-status-on_hold' },
};

export default function Projects({
    projects,
    filters,
}: {
    projects: Project[];
    filters: { status: string | null; type: string | null; department: string | null; ward: string | null };
}) {
    const locale = usePublicLocale();

    return (
        <PublicLayout>
            <Head title="Council Capital Projects & Infrastructure Tracking" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/services`}>Services</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Projects</span>
                    </nav>
                    <span className="about-accent-eyebrow">CAPITAL CIVIL WORKS · DEVOLUTION &amp; PSIP FUNDING</span>
                    <h1>District Capital Projects &amp; Infrastructure</h1>
                    <p className="about-page-hero-subtitle">
                        Tracking construction of clinics, road gravelling, piped water schemes, classroom blocks, and marketplace upgrades across Mutoko District.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Projects overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Devolution</div>
                                <div className="about-stat-label">Intergovernmental Grants</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">29 Wards</div>
                                <div className="about-stat-label">Equitable Distribution</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{projects.length}</div>
                                <div className="about-stat-label">Active &amp; Tracked Projects</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">WADCO</div>
                                <div className="about-stat-label">Community Prioritised</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN PROJECTS LIST */}
            <section className="about-page-section" aria-labelledby="projects-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">INFRASTRUCTURE REPOSITORY</span>
                    <h2 id="projects-heading">Capital Development Tracker</h2>
                    <p className="about-page-section-intro">
                        Council implements capital works through internally generated revenues, Zimbabwe National Water Authority (ZINWA), ERRP-II road rehabilitation, and devolution funding.
                    </p>

                    {/* FILTER TOOLBAR */}
                    <form method="get" action={`/${locale}/projects`} className="transparency-filter-bar">
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, flexGrow: 1, minWidth: 200 }}>
                            <label htmlFor="projects-status" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                Project Status
                            </label>
                            <select
                                id="projects-status"
                                name="status"
                                defaultValue={filters.status ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">All Statuses</option>
                                <option value="ongoing">Active Implementation</option>
                                <option value="completed">Commissioned &amp; Completed</option>
                                <option value="planned">Planned / Procurement Stage</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, minWidth: 180 }}>
                            <label htmlFor="projects-type" style={{ fontSize: '0.82rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                Programme Scope
                            </label>
                            <select
                                id="projects-type"
                                name="type"
                                defaultValue={filters.type ?? ''}
                                style={{ padding: '9px 12px', borderRadius: 8, border: '1px solid var(--color-border)', fontSize: '0.88rem' }}
                            >
                                <option value="">All Scopes</option>
                                <option value="project">Capital Project</option>
                                <option value="programme">District Programme</option>
                            </select>
                        </div>

                        <div style={{ display: 'flex', gap: 8, alignSelf: 'flex-end' }}>
                            <button
                                type="submit"
                                className="btn-mandate-action"
                                style={{ padding: '9px 18px', fontSize: '0.86rem' }}
                            >
                                Filter Projects
                            </button>
                            {(filters.status || filters.type) && (
                                <Link
                                    href={`/${locale}/projects`}
                                    className="dept-filter-btn"
                                    style={{ padding: '9px 14px', textDecoration: 'none' }}
                                >
                                    Reset
                                </Link>
                            )}
                        </div>
                    </form>

                    {/* PROJECTS GRID */}
                    {projects.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                No capital projects match the selected criteria.
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
                                                {statusConfig.label}
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
                                            {project.summary ?? 'Statutory public sector civil works project implemented under Council Engineering & Works.'}
                                        </p>

                                        {project.progress_percent !== null && (
                                            <div className="project-progress-wrapper">
                                                <div className="project-progress-labels">
                                                    <span>Civil Progress</span>
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
                                            <span>View Project Timeline &amp; Costing</span>
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
                            <span>Procurement &amp; Development Oversight</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/tenders`} className="dept-portal-card">
                                <h4>Open Tenders &amp; Bidding</h4>
                                <p>Apply for municipal civil construction and material supply contracts.</p>
                                <span>View tender opportunities →</span>
                            </Link>

                            <Link href={`/${locale}/transparency`} className="dept-portal-card">
                                <h4>Devolution Fund Disclosures</h4>
                                <p>Review financial grants received and audited expenditure accounts.</p>
                                <span>Inspect financial reports →</span>
                            </Link>

                            <Link href={`/${locale}/feedback`} className="dept-portal-card">
                                <h4>Project Feedback &amp; Suggestions</h4>
                                <p>Report on project execution quality or submit ward development proposals.</p>
                                <span>Submit project feedback →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

