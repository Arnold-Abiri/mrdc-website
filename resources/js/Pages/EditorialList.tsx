import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, EmptyState } from '../Components/public/PageHeader';
type Item = { slug: string; title: string; summary: string | null; published_at: string | null; image_url?: string | null; image_alt?: string | null; image_caption?: string | null };
export default function EditorialList({ type, items }: { type: string; items: Item[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const isNews = type === 'News';
    const title = t(isNews ? 'news' : 'notices');
    const base = `/${locale}/${type === 'News' ? 'news' : 'notices'}`;
    return (
        <PublicLayout>
            <Head title={title} />
            <PageHero eyebrow={title.toUpperCase()} title={title} subtitle={t(isNews ? 'newsDesc' : 'noticesDesc')} crumb={[{ label: title }]} />
            <section className="about-page-section" aria-labelledby="editorial-list">
                <div className="container">
                    <span className="about-accent-eyebrow">{title.toUpperCase()}</span>
                    <h2 id="editorial-list">{t(isNews ? 'latestNews' : 'latestNotices')}</h2>
                    {items.length ? (
                        <div className="about-mandate-grid">
                            {items.map(item => (
                                <div key={item.slug} className="about-mandate-card">
                                    {item.image_url && <figure><img src={item.image_url} alt={item.image_alt ?? item.title} className="editorial-featured-image" loading="lazy" />{item.image_caption && <figcaption>{item.image_caption}</figcaption>}</figure>}
                                    <h3><Link href={`${base}/${item.slug}`}>{item.title}</Link></h3>
                                    {item.summary && <p>{item.summary}</p>}
                                </div>
                            ))}
                        </div>
                    ) : <EmptyState title={t(isNews ? 'noNews' : 'noNotices')} text={t(isNews ? 'noNews' : 'noNotices')} />}
                </div>
            </section>
        </PublicLayout>
    );
}
