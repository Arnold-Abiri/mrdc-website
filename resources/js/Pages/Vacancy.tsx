import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { SourcedText } from '../Components/public/SourceButtons';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { useSiteSettings } from '../useSiteSettings';
import { PageHero } from '../Components/public/PageHeader';
import { daysRemaining } from './Vacancies';
import { formatDate } from '../formatDate';

type VacancyDetail = { slug: string; title: string; grade: string | null; reference: string | null; employment_type: string | null; description: string; responsibilities: string | null; requirements: string | null; opens_at: string | null; closes_at: string | null; is_open: boolean; application_instructions: string | null; document: { slug: string; title: string } | null };
type RelatedVacancy = { slug: string; title: string; grade: string | null; closes_at: string | null; is_open: boolean; department: string | null };
const employmentTypes: Record<string, string> = { full_time: 'fullTime', part_time: 'partTime', contract: 'contract', temporary: 'temporary', internship: 'internship' };

function toListItems(text: string): string[] {
    return text.split('\n').map((line) => line.replace(/^[•\-*)\d.\s]+/, '').trim()).filter((line) => line !== '');
}

export default function Vacancy({ vacancy, department, related = [] }: { vacancy: VacancyDetail; department: string | null; related?: RelatedVacancy[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const settings = useSiteSettings();
    const recruitmentEmail = settings.recruitment_email?.trim() || 'recruitment@mutokordc.co.zw';

    const remaining = vacancy.is_open ? daysRemaining(vacancy.closes_at) : null;
    const responsibilities = vacancy.responsibilities ? toListItems(vacancy.responsibilities) : [];
    const requirements = vacancy.requirements ? toListItems(vacancy.requirements) : [];
    const pageUrl = `https://www.mutokordc.co.zw/${locale}/vacancies/${vacancy.slug}`;
    const applyHref = `mailto:${recruitmentEmail}?subject=${encodeURIComponent(`Application for ${vacancy.title}${vacancy.reference ? ` - ${vacancy.reference}` : ''}`)}`;

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
            <PageHero eyebrow="CAREERS" title={vacancy.title} subtitle={`${vacancy.grade ? `${vacancy.grade} — ` : ''}${vacancy.is_open ? t('openForApplications') : t('applicationsClosed')}`} crumb={[{ label: t('vacancies'), href: `/${locale}/vacancies` }, { label: vacancy.title }]} />
            <section className="about-page-section" aria-labelledby="vacancy-detail">
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Back"><Link href={`/${locale}/vacancies`}>← {t('vacancies')}</Link></nav>
                    <p className="about-card-badges" aria-label="Advert details">
                        <span className={`about-badge ${vacancy.is_open ? 'about-badge-open' : 'about-badge-closed'}`}>{vacancy.is_open ? t('openStatus') : t('closedStatus')}</span>
                        {vacancy.grade && <span className="about-badge">{vacancy.grade}</span>}
                        {vacancy.employment_type && employmentTypes[vacancy.employment_type] && <span className="about-badge">{t(employmentTypes[vacancy.employment_type] as 'contract')}</span>}
                        {department && <span className="about-badge">{department}</span>}
                        {vacancy.reference && <span className="about-badge">{vacancy.reference}</span>}
                    </p>
                    {vacancy.closes_at && (
                        <p className={`about-closing-banner ${vacancy.is_open ? '' : 'about-closing-banner-closed'}`} role="status">
                            {vacancy.is_open
                                ? remaining !== null ? `Closing date: ${formatDate(vacancy.closes_at, locale)} — ${remaining} day${remaining === 1 ? '' : 's'} remaining to apply` : `Closing date: ${formatDate(vacancy.closes_at, locale)}`
                                : `Applications closed on ${formatDate(vacancy.closes_at, locale)}`}
                        </p>
                    )}
                    <div className="about-detail-layout">
                        <div className="about-detail-main">
                            <div className="about-mandate-card">
                                <h3 id="vacancy-detail">{t('positionOverview')}</h3>
                                <SourcedText text={vacancy.description} />
                                {department && <p>{t('department')}: {department}</p>}
                                {vacancy.opens_at && <p>{t('opensOn')}: {formatDate(vacancy.opens_at, locale)}</p>}
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
                                    {vacancy.is_open && <p><a className="btn-connect-gold" href={applyHref}>{t('applyViaEmail')} →</a></p>}
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
                            <h3>{t('applicationSummary')}</h3>
                            <dl>
                                {department && <><dt>{t('department')}</dt><dd>{department}</dd></>}
                                {vacancy.employment_type && employmentTypes[vacancy.employment_type] && <><dt>{t('employmentType')}</dt><dd>{t(employmentTypes[vacancy.employment_type] as 'contract')}</dd></>}
                                {vacancy.grade && <><dt>{t('grade')}</dt><dd>{vacancy.grade}</dd></>}
                                {vacancy.reference && <><dt>{t('reference')}</dt><dd>{vacancy.reference}</dd></>}
                                {vacancy.closes_at && <><dt>{t('closingDate')}</dt><dd>{formatDate(vacancy.closes_at, locale)}</dd></>}
                                <dt>{t('email')}</dt><dd><a href={applyHref}>{recruitmentEmail}</a></dd>
                            </dl>
                            {vacancy.is_open ? (
                                <p><a className="btn-connect-gold" href={applyHref}>{t('applyNow')} →</a> <button type="button" className="about-print-btn" onClick={() => window.print()}>{t('printSavePdf')}</button></p>
                            ) : (
                                <p>{t('advertClosedBrowse')}</p>
                            )}
                            <p className="about-share-row">{t('shareLabel')}:
                                <a href={`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(pageUrl)}`} target="_blank" rel="noreferrer">LinkedIn</a>
                                <a href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(vacancy.title)}&url=${encodeURIComponent(pageUrl)}`} target="_blank" rel="noreferrer">X</a>
                                <a href={`https://api.whatsapp.com/send?text=${encodeURIComponent(`${vacancy.title}: ${pageUrl}`)}`} target="_blank" rel="noreferrer">WhatsApp</a>
                            </p>
                        </aside>
                    </div>
                    {related.length > 0 && (
                        <section className="about-related-section" aria-labelledby="other-openings">
                            <span className="about-accent-eyebrow">{t('careersEyebrowShort')}</span>
                            <h2 id="other-openings">{t('otherOpenings')}</h2>
                            <div className="about-focus-grid about-focus-grid-3">
                                {related.map((item) => (
                                    <Link key={item.slug} href={`/${locale}/vacancies/${item.slug}`} className="about-focus-card">
                                        <h3>{item.title}</h3>
                                        <p>{item.department ? `${item.department} — ` : ''}{item.is_open ? t('openStatus') : t('closedStatus')}{item.closes_at ? `, closes ${formatDate(item.closes_at, locale)}` : ''}</p>
                                    </Link>
                                ))}
                            </div>
                        </section>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}
