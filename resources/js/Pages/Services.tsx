import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type Service = { slug: string; name: string; summary: string | null };
export default function Services({ services }: { services: Service[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Services</h1>{services.length === 0 ? <p>No approved services are available yet.</p> : <ul>{services.map(service => <li key={service.slug}><Link href={`/services/${service.slug}`}>{service.name}</Link>{service.summary && <p>{service.summary}</p>}</li>)}</ul>}</div></PublicLayout>;
}
