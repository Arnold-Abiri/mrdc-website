import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
type Item = { slug: string; title: string; summary: string | null; body: string; published_at: string | null; expires_at?: string | null; image_url?: string | null; image_alt?: string | null; image_caption?: string | null };
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
    const base = `/${locale}/${type === 'News' ? 'news' : 'notices'}`;
    return (
        <PublicLayout>
            <SeoHead title={item.title} description={item.summary} image={item.image_url} schema={schema} />
            <PageHero eyebrow={type.toUpperCase()} title={item.title} subtitle={item.summary || (type === 'News' ? 'Read this council news update from Mutoko Rural District Council.' : 'Read this official notice from Mutoko Rural District Council.')} crumb={[{ label: type, href: base }, { label: item.title }]} />
            <section className="about-page-section" aria-labelledby="editorial-detail">
                <div className="container">
                    {item.image_url && <figure><img src={item.image_url} alt={item.image_alt ?? item.title} className="editorial-featured-image" />{item.image_caption && <figcaption>{item.image_caption}</figcaption>}</figure>}
                    <div className="about-page-copy"><article className="whitespace-pre-wrap">{item.body}</article></div>
                    {documents.length ? (
                        <div style={{ marginTop: '24px' }}>
                            <h2>{t('relatedDocuments')}</h2>
                            <div className="about-focus-grid">
                                {documents.map(document => (
                                    <div key={document.slug} className="about-focus-card"><h3><a href={`/${locale}/documents/${document.slug}`}>{document.title}</a></h3></div>
                                ))}
                            </div>
                        </div>
                    ) : null}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
