import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null; location: string | null; opportunity_status: string };
export default function Investment({ opportunities }: { opportunities: Opportunity[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Invest in Mutoko</h1><p>Investment opportunities in Mutoko district — including solar energy, mining and horticulture. Figures and availability are confirmed with council before any commitment.</p>{opportunities.length === 0 ? <p>No investment opportunities are published at this time.</p> : <ul>{opportunities.map(item => <li key={item.slug}><Link href={`/investment/${item.slug}`}>{item.title}</Link><p>{item.sector ? `${item.sector} — ` : ''}{item.opportunity_status}{item.location ? ` — ${item.location}` : ''}</p>{item.summary && <p>{item.summary}</p>}</li>)}</ul>}<p><Link href="/contact">Make an investment enquiry</Link></p></div></PublicLayout>;
}
