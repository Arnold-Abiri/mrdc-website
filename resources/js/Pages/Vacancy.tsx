import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
import { daysRemaining } from './Vacancies';

type VacancyDetail = { slug: string; title: string; grade: string | null; reference: string | null; employment_type: string | null; description: string; responsibilities: string | null; requirements: string | null; opens_at: string | null; closes_at: string | null; is_open: boolean; application_instructions: string | null; document: { slug: string; title: string } | null };
type RelatedVacancy = { slug: string; title: string; grade: string | null; closes_at: string | null; is_open: boolean; department: string | null };
const employmentTypes: Record<string, string> = { full_time: 'fullTime', part_time: 'partTime', contract: 'contract', temporary: 'temporary', internship: 'internship' };
const RECRUITMENT_EMAIL = 'recruitment@mutokordc.co.zw';

function toListItems(text: string): string[] {
    return text.split('\n').map((line) => line.replace(/^[•\-*)\d.\s]+/, '').trim()).filter((line) => line !== '');
}

export default function Vacancy({ vacancy, department, related = [] }: { vacancy: VacancyDetail; department: string | null; related?: RelatedVacancy[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const remaining = vacancy.is_open ? daysRemaining(vacancy.closes_at) : null;
    const responsibilities = vacancy.responsibilities ? toListItems(vacancy.responsibilities) : [];
    const requirements = vacancy.requirements ? toListItems(vacancy.requirements) : [];
    const pageUrl = `https://www.mutokordc.co.zw/${locale}/vacancies/${vacancy.slug}`;
    const applyHref = `mailto:${RECRUITMENT_EMAIL}?subject=${encodeURIComponent(`Application for ${vacancy.title}${vacancy.reference ? ` - ${vacancy.reference}` : ''}`)}`;
    const schema = {
        '@context': 'https://schema.org',
        '@type': 'JobPosting',
        title: vacancy.title,
        description: vacancy.description,
        employmentType: vacancy.employment_type ?? undefined,
        validThrough: vacancy.closes_at ?? undefined,
    };
    return (
        <PublicLayout>
            <SeoHead title={vacancy.title} description={vacancy.description} schema={vacancy.is_open ? schema : null} />
            <PageHero eyebrow="CAREERS" title={vacancy.title} subtitle={`${vacancy.grade ? `${vacancy.grade} — ` : ''}${vacancy.is_open ? 'Open for applications' : 'Applications closed'}`} crumb={[{ label: t('vacancies'), href: `/${locale}/vacancies` }, { label: vacancy.title }]} />
            <section className="about-page-section" aria-labelledby="vacancy-detail">
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Back"><Link href={`/${locale}/vacancies`}>← {t('vacancies')}</Link></nav>
                    <p className="about-card-badges" aria-label="Advert details">
                        <span className={`about-badge ${vacancy.is_open ? 'about-badge-open' : 'about-badge-closed'}`}>{vacancy.is_open ? 'Open' : 'Closed'}</span>
                        {vacancy.grade && <span className="about-badge">{vacancy.grade}</span>}
                        {vacancy.employment_type && employmentTypes[vacancy.employment_type] && <span className="about-badge">{t(employmentTypes[vacancy.employment_type] as 'contract')}</span>}
                        {department && <span className="about-badge">{department}</span>}
                        {vacancy.reference && <span className="about-badge">{vacancy.reference}</span>}
                    </p>
                    {vacancy.closes_at && (
                        <p className={`about-closing-banner ${vacancy.is_open ? '' : 'about-closing-banner-closed'}`} role="status">
                            {vacancy.is_open
                                ? remaining !== null ? `Closing date: ${vacancy.closes_at} — ${remaining} day${remaining === 1 ? '' : 's'} remaining to apply` : `Closing date: ${vacancy.closes_at}`
                                : `Applications closed on ${vacancy.closes_at}`}
                        </p>
                    )}
                    <div className="about-detail-layout">
                        <div className="about-mandate-grid">
                            <div className="about-mandate-card">
                                <h3 id="vacancy-detail">Position overview</h3>
                                <p>{vacancy.description}</p>
                                {department && <p>Department: {department}</p>}
                                {vacancy.opens_at && <p>Opens: {vacancy.opens_at}</p>}
                            </div>
                            {responsibilities.length > 0 && (
                                <div className="about-mandate-card">
                                    <h3>{t('responsibilities')}</h3>
                                    <ul className="about-list">{responsibilities.map((item, index) => <li key={index}>{item}</li>)}</ul>
                                </div>
                            )}
                            {requirements.length > 0 && (
                                <div className="about-mandate-card">
                                    <h3>{t('requirements')}</h3>
                                    <ul className="about-list">{requirements.map((item, index) => <li key={index}>{item}</li>)}</ul>
                                </div>
                            )}
                            {vacancy.application_instructions && (
                                <div className="about-mandate-card">
                                    <h3>{t('howToApply')}</h3>
                                    <p style={{ whiteSpace: 'pre-line' }}>{vacancy.application_instructions}</p>
                                    {vacancy.is_open && <p><a className="btn-connect-gold" href={applyHref}>Apply via email →</a></p>}
                                </div>
                            )}
                            {vacancy.document && (
                                <div className="about-mandate-card">
                                    <h3>{t('vacancyAdvert')}</h3>
                                    <p><Link href={`/${locale}/documents/${vacancy.document.slug}`}>{vacancy.document.title}</Link></p>
                                </div>
                            )}
                        </div>
                        <aside className="about-summary-card" aria-label="Application summary">
                            <h3>Application summary</h3>
                            <dl>
                                {department && <><dt>Department</dt><dd>{department}</dd></>}
                                {vacancy.employment_type && employmentTypes[vacancy.employment_type] && <><dt>{t('employmentType')}</dt><dd>{t(employmentTypes[vacancy.employment_type] as 'contract')}</dd></>}
                                {vacancy.grade && <><dt>Grade</dt><dd>{vacancy.grade}</dd></>}
                                {vacancy.reference && <><dt>{t('reference')}</dt><dd>{vacancy.reference}</dd></>}
                                {vacancy.closes_at && <><dt>Closing date</dt><dd>{vacancy.closes_at}</dd></>}
                                <dt>Email</dt><dd><a href={applyHref}>{RECRUITMENT_EMAIL}</a></dd>
                            </dl>
                            {vacancy.is_open ? (
                                <p><a className="btn-connect-gold" href={applyHref}>Apply now →</a> <button type="button" className="about-print-btn" onClick={() => window.print()}>Print / Save PDF</button></p>
                            ) : (
                                <p>This advert is closed. Browse other openings below.</p>
                            )}
                            <p className="about-share-row">Share:
                                <a href={`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(pageUrl)}`} target="_blank" rel="noreferrer">LinkedIn</a>
                                <a href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(vacancy.title)}&url=${encodeURIComponent(pageUrl)}`} target="_blank" rel="noreferrer">X</a>
                                <a href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${vacancy.title}: ${pageUrl}`)}`} target="_blank" rel="noreferrer">WhatsApp</a>
                            </p>
                        </aside>
                    </div>
                    {related.length > 0 && (
                        <>
                            <h3>Other openings</h3>
                            <div className="about-focus-grid">
                                {related.map((item) => (
                                    <Link key={item.slug} href={`/${locale}/vacancies/${item.slug}`} className="about-focus-card">
                                        <h3>{item.title}</h3>
                                        <p>{item.department ? `${item.department} — ` : ''}{item.is_open ? 'Open' : 'Closed'}{item.closes_at ? `, closes ${item.closes_at}` : ''}</p>
                                    </Link>
                                ))}
                            </div>
                        </>
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
