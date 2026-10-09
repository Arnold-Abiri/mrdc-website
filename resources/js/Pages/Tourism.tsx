import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
type TourismBlock = { type: string; text: string; url?: string | null };
type TourismPage = { slug: string; title: string; summary: string | null; blocks: TourismBlock[] } | null;
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null };
export default function Tourism({ page, opportunities }: { page: TourismPage; opportunities: Opportunity[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout>
        <Head title={t('tourism')} />
        <PageHero eyebrow="TOURISM" title={t('tourism')} subtitle={t('tourismDesc')} crumb={[{ label: t('tourism') }]} />
        <section className="about-page-section"><div className="container">
            {page === null ? <EmptyState title={t('tourism')} text={t('tourismPending')} /> : <article><span className="about-accent-eyebrow">DISCOVER MUTOKO</span><h2>{page.title}</h2>{page.summary && <p className="about-page-lead">{page.summary}</p>}{page.blocks.map((block, index) => block.type === 'heading' ? <h3 key={index} className="about-page-copy">{block.text}</h3> : block.type === 'cta' && block.url ? <p key={index}><Link className="about-page-text-link" href={block.url}>{block.text} <span aria-hidden="true">→</span></Link></p> : <p key={index} className="about-page-copy">{block.text}</p>)}</article>}
        </div></section>
        {opportunities.length > 0 && <section className="about-page-section about-page-section-muted"><div className="container"><span className="about-accent-eyebrow">TOURISM INVESTMENT</span><h2>{t('investment')}</h2><div className="about-economic-grid">{opportunities.map(item => <Link key={item.slug} href={`/${locale}/investment/${item.slug}`} className="about-economic-card"><div className="about-economic-body"><h3>{item.title}</h3><p>{item.sector ?? ''}{item.summary ? ` — ${item.summary}` : ''}</p><span className="about-economic-link">Explore <span aria-hidden="true">→</span></span></div></Link>)}</div></div></section>}
        <ConnectBanner />
    </PublicLayout>;
}
