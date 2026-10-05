import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type OfficialSummary = { slug: string; name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Council officials</h1>{officials.length ? <ul>{officials.map(official => <li key={official.slug}><Link href={`/officials/${official.slug}`}>{official.name}</Link>{official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} />}<p>{official.title}</p>{official.department && <p>{official.department}</p>}{official.biography && <p>{official.biography}</p>}</li>)}</ul> : <p>No approved official information is available yet.</p>}</div></PublicLayout>;
}
