import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
type Project = { slug: string; title: string; project_type: string; project_status: string; location: string | null; summary: string | null; progress_percent: number | null };
const projectStatuses: Record<string, string> = { planned: 'planned', ongoing: 'ongoing', completed: 'completed', on_hold: 'onHold', cancelled: 'cancelled' };
export default function Projects({ projects, filters }: { projects: Project[]; filters: { status: string | null; type: string | null; department: string | null; ward: string | null } }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout>
        <Head title={t('projects')} />
        <PageHero eyebrow="PROJECTS" title={t('projects')} subtitle={t('projectsDesc')} crumb={[{ label: t('projects') }]} />
        <section className="about-page-section"><div className="container">
            <span className="about-accent-eyebrow">FILTER PROJECTS</span>
            <h2>Find Projects &amp; Programmes</h2>
            <form method="get" action="/projects" className="about-mandate-grid" style={{ gridTemplateColumns: 'repeat(auto-fit,minmax(180px,1fr))' }}>
                <div className="about-mandate-card"><label htmlFor="projects-status"><strong>{t('projectStatus')}</strong></label><select id="projects-status" name="status" defaultValue={filters.status ?? ''}><option value="">{t('clearFilters')}</option><option value="planned">{t('planned')}</option><option value="ongoing">{t('ongoing')}</option><option value="completed">{t('completed')}</option><option value="on_hold">{t('onHold')}</option><option value="cancelled">{t('cancelled')}</option></select></div>
                <div className="about-mandate-card"><label htmlFor="projects-type"><strong>{t('projectType')}</strong></label><select id="projects-type" name="type" defaultValue={filters.type ?? ''}><option value="">{t('clearFilters')}</option><option value="project">{t('project')}</option><option value="programme">{t('programme')}</option></select><button type="submit" className="btn-ward-dir" style={{ marginTop: '10px' }}><span>{t('filter')}</span><span aria-hidden="true">→</span></button></div>
            </form>
            <div style={{ marginTop: '24px' }}>
            {projects.length === 0 ? <EmptyState title={t('noProjects')} text={t('projectsDesc')} /> : <div className="about-focus-grid">{projects.map(project => <Link key={project.slug} href={`/${locale}/projects/${project.slug}`} className="about-focus-card"><h3>{project.title}</h3><p>{t(projectStatuses[project.project_status] as 'planned')}{project.location ? ` — ${project.location}` : ''}{project.progress_percent !== null ? ` — ${project.progress_percent}%` : ''}</p>{project.summary && <p>{project.summary}</p>}<span className="about-economic-link">View project <span aria-hidden="true">→</span></span></Link>)}</div>}
            </div>
        </div></section>
        <ConnectBanner />
    </PublicLayout>;
}
