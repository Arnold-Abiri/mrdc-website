import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
type Ward = { slug: string; name: string };
type LinkedDocument = { slug: string; title: string };
type Update = { update_date: string | null; title: string; summary: string | null; progress_percent: number | null };
type ProjectDetail = { slug: string; title: string; project_type: string; location: string | null; summary: string | null; description: string; starts_at: string | null; expected_completed_at: string | null; completed_at: string | null; project_status: string; progress_percent: number | null; contact_instructions: string | null; image_url: string | null };
const projectStatuses: Record<string, string> = { planned: 'planned', ongoing: 'ongoing', completed: 'completed', on_hold: 'onHold', cancelled: 'cancelled' };
export default function Project({ project, department, wards, documents, updates }: { project: ProjectDetail; department: string | null; wards: Ward[]; documents: LinkedDocument[]; updates: Update[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout>
        <Head title={project.title} />
        <PageHero eyebrow="PROJECTS" title={project.title} subtitle={`${project.project_type === 'programme' ? t('programme') : t('project')} — ${t(projectStatuses[project.project_status] as 'planned')}${project.location ? ` — ${project.location}` : ''}`} crumb={[{ label: t('projects'), href: `/${locale}/projects` }, { label: project.title }]} />
        <section className="about-page-section"><div className="container">
            {project.summary && <p className="about-page-lead">{project.summary}</p>}
            {project.image_url && <figure className="about-whoweare-figure"><img src={project.image_url} alt={project.title} className="about-whoweare-img" loading="lazy" /></figure>}
            <div className="about-mandate-grid">
                <div className="about-mandate-card"><h3>{t('timeline')}</h3><p>{t('startsAt')}: {project.starts_at ?? '—'} — {t('expectedCompletion')}: {project.expected_completed_at ?? '—'}{project.completed_at ? ` — ${t('completedAt')}: ${project.completed_at}` : ''}</p>{project.progress_percent !== null && <p>{t('progress')}: {project.progress_percent}%</p>}</div>
                <div className="about-mandate-card"><h3>{t('location')}</h3><p>{project.location ?? '—'}</p>{department && <p>{t('department')}: {department}</p>}{wards.length > 0 && <p>{t('wards')}: {wards.map(ward => ward.name).join(', ')}</p>}</div>
            </div>
            <div className="about-page-copy" style={{ marginTop: '20px' }}><p>{project.description}</p></div>
            {project.contact_instructions && <div className="about-vision-card" style={{ marginTop: '20px' }}><div><h3>{t('contactCouncil')}</h3><p>{project.contact_instructions}</p></div></div>}
        </div></section>
        <section className="about-page-section about-page-section-muted"><div className="container">
            <span className="about-accent-eyebrow">UPDATES</span>
            <h2>{t('projectUpdates')}</h2>
            {updates.length === 0 ? <EmptyState title={t('noProjectUpdates')} text={t('projectUpdates')} /> : <div className="about-economic-grid">{updates.map((update, index) => <div key={index} className="about-economic-card"><div className="about-economic-body"><h3>{update.title}</h3><p>{update.update_date}</p>{update.summary && <p>{update.summary}</p>}{update.progress_percent !== null && <p>{t('progress')}: {update.progress_percent}%</p>}</div></div>)}</div>}
            {documents.length > 0 && <><h2 style={{ marginTop: '24px' }}>{t('relatedDocuments')}</h2><div className="about-focus-grid">{documents.map(document => <Link key={document.slug} href={`/${locale}/documents/${document.slug}`} className="about-focus-card"><h3>{document.title}</h3><span className="about-economic-link">Open document <span aria-hidden="true">→</span></span></Link>)}</div></>}
            <p style={{ marginTop: '20px' }}><Link className="about-page-text-link" href={`/${locale}/contact`}>{t('contactCouncil')} <span aria-hidden="true">→</span></Link></p>
        </div></section>
        <ConnectBanner />
    </PublicLayout>;
}
