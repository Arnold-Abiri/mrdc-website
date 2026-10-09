import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
type OpportunityDetail = { slug: string; title: string; sector: string | null; summary: string | null; description: string; location: string | null; opportunity_status: string; document: { slug: string; title: string } | null };
export default function InvestmentDetail({ opportunity }: { opportunity: OpportunityDetail }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout>
        <Head title={opportunity.title} />
        <PageHero eyebrow="INVESTMENT" title={opportunity.title} subtitle={`${opportunity.sector ? `${opportunity.sector} — ` : ''}${opportunity.opportunity_status}${opportunity.location ? ` — ${opportunity.location}` : ''}`} crumb={[{ label: t('investment'), href: `/${locale}/investment` }, { label: opportunity.title }]} />
        <section className="about-page-section"><div className="container">
            {opportunity.summary && <p className="about-page-lead">{opportunity.summary}</p>}
            <div className="about-page-copy"><p>{opportunity.description}</p></div>
            {opportunity.document && <div className="about-vision-card" style={{ marginTop: '20px' }}><div><h3>{t('supportingDocument')}</h3><p><Link className="about-page-text-link" href={`/${locale}/documents/${opportunity.document.slug}`}>{opportunity.document.title} <span aria-hidden="true">→</span></Link></p></div></div>}
            <p style={{ marginTop: '20px' }}><Link className="about-page-text-link" href={`/${locale}/contact?context=investment:${opportunity.slug}`}>{t('enquireInvestment')} <span aria-hidden="true">→</span></Link></p>
        </div></section>
        <ConnectBanner />
    </PublicLayout>;
}
