import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';
type OpportunityDetail = { slug: string; title: string; sector: string | null; summary: string | null; description: string; location: string | null; opportunity_status: string; document: { slug: string; title: string } | null };
export default function InvestmentDetail({ opportunity }: { opportunity: OpportunityDetail }) {
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><p><Link href="/investment">Investment</Link></p><h1>{opportunity.title}</h1><p>{opportunity.sector ? `${opportunity.sector} — ` : ''}{opportunity.opportunity_status}{opportunity.location ? ` — ${opportunity.location}` : ''}</p>{opportunity.summary && <p>{opportunity.summary}</p>}<p>{opportunity.description}</p>{opportunity.document && <section><h2>Supporting document</h2><p><Link href={`/documents/${opportunity.document.slug}`}>{opportunity.document.title}</Link></p></section>}<p><Link href={`/contact?context=investment:${opportunity.slug}`}>{t('enquireInvestment')}</Link></p></div></PublicLayout>;
}
