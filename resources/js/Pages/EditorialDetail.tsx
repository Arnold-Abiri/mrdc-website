import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Item = { slug: string; title: string; summary: string | null; body: string; published_at: string | null; expires_at?: string | null; image_url?: string | null };
type DocumentLink = { slug: string; title: string };
export default function EditorialDetail({ type, item, documents }: { type: string; item: Item; documents: DocumentLink[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const schema = {
        '@context': 'https://schema.org',
        '@type': type === 'News' ? 'NewsArticle' : 'Article',
        headline: item.title,
        description: item.summary ?? undefined,
        datePublished: item.published_at ?? undefined,
    };
    return <PublicLayout><SeoHead title={item.title} description={item.summary} image={item.image_url} schema={schema} /><div className="container coming-soon"><p>{type}</p><h1>{item.title}</h1>{item.summary && <p>{item.summary}</p>}{item.image_url && <img src={item.image_url} alt={item.title} className="editorial-featured-image" />}<article className="whitespace-pre-wrap">{item.body}</article>{documents.length ? <section><h2>{t('relatedDocuments')}</h2><ul>{documents.map(document => <li key={document.slug}><a href={`/${locale}/documents/${document.slug}`}>{document.title}</a></li>)}</ul></section> : null}</div></PublicLayout>;
}
