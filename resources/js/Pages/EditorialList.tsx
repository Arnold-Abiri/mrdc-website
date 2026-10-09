import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
type Item = { slug: string; title: string; summary: string | null; published_at: string | null; image_url?: string | null; image_alt?: string | null; image_caption?: string | null };
export default function EditorialList({ type, items }: { type: string; items: Item[] }) {
    const locale = usePublicLocale();
    const base = `/${locale}/${type === 'News' ? 'news' : 'notices'}`;
    return <PublicLayout><div className="container coming-soon"><h1>{type}</h1>{items.length ? <ul>{items.map(item => <li key={item.slug}>{item.image_url && <figure><img src={item.image_url} alt={item.image_alt ?? item.title} className="editorial-featured-image" loading="lazy" />{item.image_caption && <figcaption>{item.image_caption}</figcaption>}</figure>}<Link href={`${base}/${item.slug}`}>{item.title}</Link>{item.summary && <p>{item.summary}</p>}</li>)}</ul> : <p>No approved {type.toLowerCase()} are available yet.</p>}</div></PublicLayout>;
}
