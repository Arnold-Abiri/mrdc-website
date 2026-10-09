import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';

type VacancyDetail = { slug: string; title: string; grade: string | null; reference: string | null; employment_type: string | null; description: string; responsibilities: string | null; requirements: string | null; opens_at: string | null; closes_at: string | null; is_open: boolean; application_instructions: string | null; document: { slug: string; title: string } | null };
const employmentTypes: Record<string, string> = { full_time: 'fullTime', part_time: 'partTime', contract: 'contract', temporary: 'temporary', internship: 'internship' };

export default function Vacancy({ vacancy, department }: { vacancy: VacancyDetail; department: string | null }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
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
            <PageHero eyebrow="CAREERS" title={vacancy.title} subtitle={`${vacancy.grade ? `${vacancy.grade} \u2014 ` : ''}${vacancy.is_open ? 'Open for applications' : 'Applications closed'}`} crumb={[{ label: t('vacancies'), href: `/${locale}/vacancies` }, { label: vacancy.title }]} />
            <section className="about-page-section" aria-labelledby="vacancy-detail">
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Back"><Link href={`/${locale}/vacancies`}>\u2190 {t('vacancies')}</Link></nav>
                    <div className="about-mandate-grid">
                        <div className="about-mandate-card">
                            <h3 id="vacancy-detail">About this role</h3>
                            {vacancy.reference && <p>{t('reference')}: {vacancy.reference}</p>}
                            {vacancy.employment_type && employmentTypes[vacancy.employment_type] && <p>{t('employmentType')}: {t(employmentTypes[vacancy.employment_type] as 'contract')}</p>}
                            {vacancy.opens_at && <p>Opens: {vacancy.opens_at}</p>}
                            {vacancy.closes_at && <p>Closes: {vacancy.closes_at}</p>}
                            <p>{vacancy.description}</p>
                            {department && <p>Department: {department}</p>}
                        </div>
                        {vacancy.responsibilities && (
                            <div className="about-mandate-card">
                                <h3>{t('responsibilities')}</h3>
                                <p>{vacancy.responsibilities}</p>
                            </div>
                        )}
                        {vacancy.requirements && (
                            <div className="about-mandate-card">
                                <h3>{t('requirements')}</h3>
                                <p>{vacancy.requirements}</p>
                            </div>
                        )}
                        {vacancy.application_instructions && (
                            <div className="about-mandate-card">
                                <h3>{t('howToApply')}</h3>
                                <p>{vacancy.application_instructions}</p>
                            </div>
                        )}
                        {vacancy.document && (
                            <div className="about-mandate-card">
                                <h3>{t('vacancyAdvert')}</h3>
                                <p><Link href={`/${locale}/documents/${vacancy.document.slug}`}>{vacancy.document.title}</Link></p>
                            </div>
                        )}
                    </div>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
