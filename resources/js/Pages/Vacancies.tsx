import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Vacancy = { slug: string; title: string; grade: string | null; closes_at: string | null; is_open: boolean };
export default function Vacancies({ vacancies }: { vacancies: Vacancy[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('vacancies')}</h1><p>Current employment opportunities with Mutoko Rural District Council. Expired vacancies are shown as closed.</p>{vacancies.length === 0 ? <p>No vacancies are published at this time.</p> : <ul>{vacancies.map(vacancy => <li key={vacancy.slug}><Link href={`/${locale}/vacancies/${vacancy.slug}`}>{vacancy.title}</Link><p>{vacancy.grade ? `${vacancy.grade} — ` : ''}{vacancy.is_open ? 'Open' : 'Closed'}{vacancy.closes_at ? `, closes ${vacancy.closes_at}` : ''}</p></li>)}</ul>}</div></PublicLayout>;
}
