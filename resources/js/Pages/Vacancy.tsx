import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
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
    return <PublicLayout><SeoHead title={vacancy.title} description={vacancy.description} schema={vacancy.is_open ? schema : null} /><div className="container coming-soon"><p><Link href={`/${locale}/vacancies`}>{t('vacancies')}</Link></p><h1>{vacancy.title}</h1><p>{vacancy.grade ? `${vacancy.grade} — ` : ''}{vacancy.is_open ? 'Open for applications' : 'Applications closed'}</p>{vacancy.reference && <p>{t('reference')}: {vacancy.reference}</p>}{vacancy.employment_type && employmentTypes[vacancy.employment_type] && <p>{t('employmentType')}: {t(employmentTypes[vacancy.employment_type] as 'contract')}</p>}{vacancy.opens_at && <p>Opens: {vacancy.opens_at}</p>}{vacancy.closes_at && <p>Closes: {vacancy.closes_at}</p>}<p>{vacancy.description}</p>{vacancy.responsibilities && <section><h2>{t('responsibilities')}</h2><p>{vacancy.responsibilities}</p></section>}{vacancy.requirements && <section><h2>{t('requirements')}</h2><p>{vacancy.requirements}</p></section>}{department && <p>Department: {department}</p>}{vacancy.application_instructions && <section><h2>{t('howToApply')}</h2><p>{vacancy.application_instructions}</p></section>}{vacancy.document && <section><h2>{t('vacancyAdvert')}</h2><p><Link href={`/${locale}/documents/${vacancy.document.slug}`}>{vacancy.document.title}</Link></p></section>}</div></PublicLayout>;
}
