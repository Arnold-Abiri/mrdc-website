import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type OfficialSummary = { slug: string; name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('councilOfficials')}</h1>{officials.length ? <ul>{officials.map(official => <li key={official.slug}><Link href={`/${locale}/officials/${official.slug}`}>{official.name}</Link>{official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} />}<p>{official.title}</p>{official.department && <p>{official.department}</p>}{official.biography && <p>{official.biography}</p>}</li>)}</ul> : <p>No approved official information is available yet.</p>}</div></PublicLayout>;
}
