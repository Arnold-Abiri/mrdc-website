import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';
type RatesBlock = { type: string; text: string; url?: string | null };
type RatesPage = { slug: string; title: string; summary: string | null; blocks: RatesBlock[] } | null;
type RateSchedule = { slug: string; title: string; category: string; reference_date: string | null };
export default function Rates({ page, schedules }: { page: RatesPage; schedules: RateSchedule[] }) {
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('rates')}</h1><p>{t('ratesDesc')}</p>{page === null ? <p>{t('ratesPagePending')}</p> : <article><h2>{page.title}</h2>{page.summary && <p>{page.summary}</p>}{page.blocks.map((block, index) => block.type === 'heading' ? <h3 key={index}>{block.text}</h3> : block.type === 'cta' && block.url ? <p key={index}><Link href={block.url}>{block.text}</Link></p> : <p key={index}>{block.text}</p>)}</article>}<section><h2>{t('rateSchedules')}</h2>{schedules.length === 0 ? <p>{t('ratesPagePending')}</p> : <ul>{schedules.map(schedule => <li key={schedule.slug}><Link href={`/documents/${schedule.slug}`}>{schedule.title}</Link><p>{schedule.category}{schedule.reference_date ? ` — ${schedule.reference_date}` : ''}</p></li>)}</ul>}</section><p><Link href="/contact">{t('contactCouncil')}</Link></p></div></PublicLayout>;
}
