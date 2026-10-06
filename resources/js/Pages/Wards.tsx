import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type WardSummary = { slug: string; name: string; description: string | null };
export default function Wards({ wards }: { wards: WardSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('wards')}</h1>{wards.length ? <ul>{wards.map(ward => <li key={ward.slug}><Link href={`/${locale}/wards/${ward.slug}`}>{ward.name}</Link>{ward.description && <p>{ward.description}</p>}</li>)}</ul> : <p>No approved ward information is available yet.</p>}</div></PublicLayout>;
}
