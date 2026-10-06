import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type Tender = { slug: string; reference: string; title: string; category: string | null; closes_at: string | null; display_status: string };
export default function Tenders({ tenders }: { tenders: Tender[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Tenders and procurement</h1><p>Open council procurement opportunities. Closed tenders remain listed for transparency but are clearly marked.</p>{tenders.length === 0 ? <p>No tenders are published at this time.</p> : <ul>{tenders.map(tender => <li key={tender.slug}><Link href={`/tenders/${tender.slug}`}>{tender.title}</Link><p>{tender.reference}{tender.category ? ` — ${tender.category}` : ''} — {tender.display_status}{tender.closes_at ? `, closes ${tender.closes_at}` : ''}</p></li>)}</ul>}</div></PublicLayout>;
}
