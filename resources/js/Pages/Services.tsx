import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Service = { slug: string; name: string; summary: string | null };
const STATIC_SERVICES = [
    { title: 'Education', text: 'Support for 84 primary and 44 secondary schools: classrooms, teacher housing and facilities.' },
    { title: 'Environment & Conservation', text: 'Wetland and woodland protection, anti-litter and community clean-up campaigns.' },
    { title: 'Roads & Works', text: 'Grading and maintenance of the district road network, bridges, drifts and public buildings.' },
    { title: 'Health', text: 'Support for rural health centres, outreach and sanitation programmes in every ward.' },
    { title: 'Business Centres & Property', text: 'Serviced stands, markets, leases and development control at Mutoko Centre and growth points.' },
    { title: 'Recreation & Welfare', text: 'Chikondoma Stadium, halls, sport and social welfare support for vulnerable households.' },
];
export default function Services({ services }: { services: Service[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container max-w-5xl coming-soon"><h1>{t('services')}</h1><p>Mutoko Rural District Council delivers everyday services across 29 wards — from classrooms and clinics to roads, water points, markets and Chikondoma Stadium. Contact the council on Stand 366 Mutoko Centre, P Box 130 Mutoko, Zimbabwe, or +263 771 592 888.</p><section aria-label="Service areas"><ul>{STATIC_SERVICES.map(s => <li key={s.title}><strong>{s.title}</strong><p>{s.text}</p></li>)}</ul></section>{services.length === 0 ? <p>No approved services are available yet.</p> : <ul>{services.map(service => <li key={service.slug}><Link href={`/${locale}/services/${service.slug}`}>{service.name}</Link>{service.summary && <p>{service.summary}</p>}</li>)}</ul>}</div></PublicLayout>;
}
