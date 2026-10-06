import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
type TourismBlock = { type: string; text: string; url?: string | null };
type TourismPage = { slug: string; title: string; summary: string | null; blocks: TourismBlock[] } | null;
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null };
export default function Tourism({ page, opportunities }: { page: TourismPage; opportunities: Opportunity[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout><div className="container coming-soon"><h1>{t('tourism')}</h1><p>{t('tourismDesc')}</p>{page === null ? <p>{t('tourismPending')}</p> : <article><h2>{page.title}</h2>{page.summary && <p>{page.summary}</p>}{page.blocks.map((block, index) => block.type === 'heading' ? <h3 key={index}>{block.text}</h3> : block.type === 'cta' && block.url ? <p key={index}><Link href={block.url}>{block.text}</Link></p> : <p key={index}>{block.text}</p>)}</article>}{opportunities.length > 0 && <section><h2>{t('investment')}</h2><ul>{opportunities.map(item => <li key={item.slug}><Link href={`/${locale}/investment/${item.slug}`}>{item.title}</Link>{item.sector ? ` — ${item.sector}` : ''}</li>)}</ul></section>}<p><Link href={`/${locale}/contact`}>{t('contactCouncil')}</Link></p></div></PublicLayout>;
}
