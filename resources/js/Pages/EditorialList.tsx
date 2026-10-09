import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
type Item = { slug: string; title: string; summary: string | null; published_at: string | null; image_url?: string | null; image_alt?: string | null; image_caption?: string | null };
export default function EditorialList({ type, items }: { type: string; items: Item[] }) {
    const locale = usePublicLocale();
    const base = `/${locale}/${type === 'News' ? 'news' : 'notices'}`;
    return (
        <PublicLayout>
            <Head title={type} />
            <PageHero eyebrow={type.toUpperCase()} title={type} subtitle={type === 'News' ? 'Read the latest council news and updates from across Mutoko.' : 'Find official council notices and public announcements.'} crumb={[{ label: type }]} />
            <section className="about-page-section" aria-labelledby="editorial-list">
                <div className="container">
                    <span className="about-accent-eyebrow">{type.toUpperCase()}</span>
                    <h2 id="editorial-list">Latest {type.toLowerCase()}</h2>
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
                    ) : <EmptyState title={`No ${type.toLowerCase()} yet`} text={`No approved ${type.toLowerCase()} are available yet.`} />}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
