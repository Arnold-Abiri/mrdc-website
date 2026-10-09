import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null; location: string | null; opportunity_status: string };
export default function Investment({ opportunities }: { opportunities: Opportunity[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container max-w-5xl coming-soon"><h1>{t('investInMutoko')}</h1><p>Mutoko sits 143km north-east of Harare on the Harare–Nyamapanda highway, 90km from Mozambique — strong in horticulture, solar energy, mining and agro-processing. Serviced commercial and industrial stands are available at Mutoko Centre and growth points. Call +263 771 592 888 or visit Stand 366 Mutoko Centre to discuss proposals.</p><p>Investment opportunities in Mutoko district — including solar energy, mining and horticulture. Figures and availability are confirmed with council before any commitment.</p>{opportunities.length === 0 ? <p>No investment opportunities are published at this time.</p> : <ul>{opportunities.map(item => <li key={item.slug}><Link href={`/${locale}/investment/${item.slug}`}>{item.title}</Link><p>{item.sector ? `${item.sector} — ` : ''}{item.opportunity_status}{item.location ? ` — ${item.location}` : ''}</p>{item.summary && <p>{item.summary}</p>}</li>)}</ul>}<p><Link href={`/${locale}/contact`}>{t('makeInvestmentEnquiry')}</Link></p></div></PublicLayout>;
}
