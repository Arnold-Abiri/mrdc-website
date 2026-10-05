import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type WardSummary = { slug: string; name: string; description: string | null };
export default function Wards({ wards }: { wards: WardSummary[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Wards</h1>{wards.length ? <ul>{wards.map(ward => <li key={ward.slug}><Link href={`/wards/${ward.slug}`}>{ward.name}</Link>{ward.description && <p>{ward.description}</p>}</li>)}</ul> : <p>No approved ward information is available yet.</p>}</div></PublicLayout>;
}
