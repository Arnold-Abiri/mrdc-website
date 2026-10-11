import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, EmptyState } from '../Components/public/PageHeader';
type Item = {
    slug: string;
    title: string;
    summary: string | null;
    published_at: string | null;
    expires_at?: string | null;
    image_url?: string | null;
    image_alt?: string | null;
    image_caption?: string | null;
};

function formatDate(value: string | null, locale: string): string {
    if (!value) return '';
    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime())
        ? ''
        : parsed.toLocaleDateString(locale === 'en' ? 'en-ZW' : `${locale}-ZW`, {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
          });
}

export default function EditorialList({ type, items }: { type: string; items: Item[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const isNews = type === 'News';
    const title = t(isNews ? 'news' : 'notices');
    const base = `/${locale}/${type === 'News' ? 'news' : 'notices'}`;

    return (
        <PublicLayout>
            <Head title={`${title} | Mutoko RDC`} />
            <PageHero eyebrow={title.toUpperCase()} title={title} subtitle={t(isNews ? 'newsDesc' : 'noticesDesc')} crumb={[{ label: title }]} />
            <section className="about-page-section" aria-labelledby="editorial-list">
                <div className="container">
                    <span className="about-accent-eyebrow">{title.toUpperCase()}</span>
                    <h2 id="editorial-list">{t(isNews ? 'latestNews' : 'latestNotices')}</h2>
                    {items.length ? (
                        <div className="editorial-grid">
                            {items.map(item => (
                                <article key={item.slug} className="editorial-card">
                                    {item.image_url ? (
                                        <figure className="editorial-card-figure">
                                            <img
                                                src={item.image_url}
                                                alt={item.image_alt ?? item.title}
                                                className="editorial-card-img"
                                                loading="lazy"
                                            />
                                        </figure>
                                    ) : null}
                                    <div className="editorial-card-body">
                                        <div className="editorial-meta-row">
                                            <span className={`editorial-badge ${isNews ? 'editorial-badge-news' : 'editorial-badge-notice'}`}>
                                                {isNews ? t('news') : t('noticeSingular')}
                                            </span>
                                            {item.published_at && (
                                                <span className="editorial-date">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                        <line x1="16" y1="2" x2="16" y2="6" />
                                                        <line x1="8" y1="2" x2="8" y2="6" />
                                                        <line x1="3" y1="10" x2="21" y2="10" />
                                                    </svg>
                                                    <time dateTime={item.published_at}>{formatDate(item.published_at, locale)}</time>
                                                </span>
                                            )}
                                        </div>
                                        <h3 className="editorial-card-title">
                                            <Link href={`${base}/${item.slug}`}>{item.title}</Link>
                                        </h3>
                                        {item.summary && <p className="editorial-card-summary">{item.summary}</p>}
                                        <div className="editorial-card-footer">
                                            <Link href={`${base}/${item.slug}`} className="editorial-read-link">
                                                <span>{t('readAnnouncementAction')}</span>
                                                <span aria-hidden="true">→</span>
                                            </Link>
                                            {item.expires_at && (
                                                <span className="editorial-expiry-badge">
                                                    {t('validUntil')}: {formatDate(item.expires_at, locale)}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    ) : (
                        <EmptyState title={t(isNews ? 'noNews' : 'noNotices')} text={t(isNews ? 'noNews' : 'noNotices')} />
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}
