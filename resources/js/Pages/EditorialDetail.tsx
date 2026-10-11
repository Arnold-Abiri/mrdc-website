import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { SeoHead } from '../Seo';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero } from '../Components/public/PageHeader';

type Item = {
    slug: string;
    title: string;
    summary: string | null;
    body: string;
    published_at: string | null;
    expires_at?: string | null;
    image_url?: string | null;
    image_alt?: string | null;
    image_caption?: string | null;
};
type DocumentLink = { slug: string; title: string };

const fallback = {
    en: { news: 'Read this council news update from Mutoko Rural District Council.', notice: 'Read this official notice from Mutoko Rural District Council.' },
    sn: { news: 'Verengai nhau idzi dzeKanzuru yeMutoko Rural District.', notice: 'Verengai chiziviso ichi chepamutemo cheKanzuru yeMutoko Rural District.' },
    nd: { news: 'Bala lezi indaba zoMkhandlu weMutoko Rural District.', notice: 'Bala lesi saziso esisemthethweni soMkhandlu weMutoko Rural District.' },
};

function formatDate(value: string | null, locale: string): string {
    if (!value) return '';
    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime())
        ? ''
        : parsed.toLocaleDateString(locale === 'en' ? 'en-ZW' : `${locale}-ZW`, {
              day: 'numeric',
              month: 'long',
              year: 'numeric',
          });
}

export default function EditorialDetail({ type, item, documents }: { type: string; item: Item; documents: DocumentLink[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const isNews = type === 'News';
    const schema = {
        '@context': 'https://schema.org',
        '@type': isNews ? 'NewsArticle' : 'Article',
        headline: item.title,
        description: item.summary ?? undefined,
        datePublished: item.published_at ?? undefined,
    };
    const base = `/${locale}/${isNews ? 'news' : 'notices'}`;
    const pageUrl = typeof window !== 'undefined' ? window.location.href : '';
    const shareText = encodeURIComponent(`${item.title}\n${pageUrl}`);

    const handlePrint = () => {
        if (typeof window !== 'undefined') {
            window.print();
        }
    };

    return (
        <PublicLayout>
            <SeoHead title={item.title} description={item.summary} image={item.image_url} schema={schema} />
            <PageHero
                eyebrow={isNews ? t('news').toUpperCase() : t('notices').toUpperCase()}
                title={item.title}
                subtitle={item.summary || fallback[locale][isNews ? 'news' : 'notice']}
                crumb={[{ label: isNews ? t('news') : t('notices'), href: base }, { label: item.title }]}
            />
            <section className="about-page-section" aria-labelledby="editorial-detail">
                <div className="container" style={{ maxWidth: '860px' }}>
                    <div className="editorial-detail-meta">
                        <span className={`editorial-badge ${isNews ? 'editorial-badge-news' : 'editorial-badge-notice'}`}>
                            {isNews ? t('news') : t('noticeSingular')}
                        </span>
                        {item.published_at && (
                            <span className="editorial-date">
                                <strong>{t('publishedOn')}:</strong>&nbsp;{formatDate(item.published_at, locale)}
                            </span>
                        )}
                        {item.expires_at && (
                            <span className="editorial-expiry-badge">
                                <strong>{t('validUntil')}:</strong>&nbsp;{formatDate(item.expires_at, locale)}
                            </span>
                        )}
                    </div>

                    {item.image_url && (
                        <figure className="editorial-featured-figure">
                            <img
                                src={item.image_url}
                                alt={item.image_alt ?? item.title}
                                className="editorial-featured-image"
                            />
                            {item.image_caption && (
                                <figcaption className="editorial-featured-caption">{item.image_caption}</figcaption>
                            )}
                        </figure>
                    )}

                    <div className="about-page-copy">
                        <article className="editorial-article-body">{item.body}</article>
                    </div>

                    <div className="editorial-actions-bar">
                        <a
                            href={`https://wa.me/?text=${shareText}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="btn-editorial-action btn-whatsapp-share"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12.031 2C6.495 2 2 6.494 2 12.029c0 1.942.556 3.754 1.517 5.29L2 22l4.825-1.488A9.972 9.972 0 0 0 12.03 22c5.536 0 10.03-4.494 10.03-10.029 0-5.535-4.494-10.029-10.029-10.029zm5.82 14.346c-.244.686-1.42 1.31-1.956 1.385-.506.071-1.144.103-3.666-.94-3.223-1.332-5.284-4.606-5.443-4.82-.16-.213-1.304-1.733-1.304-3.304 0-1.572.825-2.348 1.118-2.668.293-.32.64-.4.853-.4.213 0 .426.002.613.012.199.01.466-.075.73.559.274.66.932 2.274 1.014 2.44.08.165.134.36.027.574-.107.213-.16.347-.32.534-.16.186-.337.417-.48.56-.16.16-.328.334-.141.654.187.32.83 1.368 1.78 2.213 1.22 1.087 2.25 1.424 2.57 1.584.32.16.507.134.694-.08.187-.214.8-1.014 1.014-1.36.213-.347.426-.293.72-.186.293.107 1.867.88 2.187 1.04.32.16.533.24.613.373.08.134.08.774-.164 1.46z" />
                            </svg>
                            <span>{t('shareWhatsApp')}</span>
                        </a>

                        <button
                            type="button"
                            onClick={handlePrint}
                            className="btn-editorial-action btn-print-notice"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <polyline points="6 9 6 2 18 2 18 9" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                <rect x="6" y="14" width="12" height="8" />
                            </svg>
                            <span>{t('printNotice')}</span>
                        </button>

                        <Link
                            href={base}
                            className="btn-editorial-action btn-print-notice"
                            style={{ marginLeft: 'auto' }}
                        >
                            <span aria-hidden="true">←</span>
                            <span>{isNews ? t('backToNews') : t('backToNotices')}</span>
                        </Link>
                    </div>

                    {documents.length ? (
                        <div style={{ marginTop: '32px' }}>
                            <h2 style={{ fontSize: '1.25rem', marginBottom: '16px', color: 'var(--color-civic-navy)' }}>
                                {t('relatedDocuments')}
                            </h2>
                            <div className="about-focus-grid">
                                {documents.map(document => (
                                    <div key={document.slug} className="about-focus-card">
                                        <h3>
                                            <a href={`/${locale}/documents/${document.slug}`}>{document.title}</a>
                                        </h3>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ) : null}
                </div>
            </section>
        </PublicLayout>
    );
}
