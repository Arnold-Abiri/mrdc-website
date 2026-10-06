import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Service = { slug: string; name: string; summary: string | null };
export default function Services({ services }: { services: Service[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('services')}</h1>{services.length === 0 ? <p>No approved services are available yet.</p> : <ul>{services.map(service => <li key={service.slug}><Link href={`/${locale}/services/${service.slug}`}>{service.name}</Link>{service.summary && <p>{service.summary}</p>}</li>)}</ul>}</div></PublicLayout>;
}
