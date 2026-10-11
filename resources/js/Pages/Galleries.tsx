import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { ConnectBanner, EmptyState, PageHero } from '../Components/public/PageHeader';

type GalleryCard = {
    slug: string;
    title: string;
    summary: string | null;
    location: string | null;
    album_date: string | null;
    is_featured: boolean;
    image_count: number;
    cover_url: string | null;
    cover_alt: string | null;
};

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }
    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime()) ? '' : parsed.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
}

export default function Galleries({ galleries }: { galleries: GalleryCard[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();

    return (
        <PublicLayout>
            <Head title={`${t('galleries')} | Mutoko RDC`} />
            <PageHero eyebrow="MULTIMEDIA" title={t('galleries')} subtitle={t('galleriesDesc')} crumb={[{ label: t('gallery') }]} />

            <section className="about-page-section" aria-labelledby="gallery-list">
                <div className="container">
                    <span className="about-accent-eyebrow">PHOTO GALLERY</span>
                    <h2 id="gallery-list">From around the district</h2>

                    {galleries.length === 0 ? (
                        <EmptyState title={t('noGalleries')} text={t('galleriesDesc')} />
                    ) : (
                        <div className="gallery-albums">
                            {galleries.map(gallery => {
                                const meta = [formatDate(gallery.album_date), gallery.location].filter(Boolean).join(' · ');

                                return (
                                    <article key={gallery.slug} className={gallery.is_featured ? 'gallery-album is-featured' : 'gallery-album'}>
                                        <Link href={`/${locale}/galleries/${gallery.slug}`} className="gallery-album-media" aria-label={`${t('viewGallery')}: ${gallery.title}`}>
                                            {gallery.cover_url ? (
                                                <img src={gallery.cover_url} alt={gallery.cover_alt ?? gallery.title} loading="lazy" />
                                            ) : (
                                                <span className="gallery-album-placeholder" aria-hidden="true">
                                                    <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round">
                                                        <rect x="3" y="4" width="18" height="16" rx="2" />
                                                        <circle cx="9" cy="10" r="2" />
                                                        <path d="m21 16-5-5L5 20" />
                                                    </svg>
                                                </span>
                                            )}
                                            {gallery.is_featured && <span className="gallery-album-flag">Featured</span>}
                                            <span className="gallery-album-count">
                                                <span>{gallery.image_count}</span> {t('photos')}
                                            </span>
                                        </Link>
                                        <div className="gallery-album-body">
                                            <h3>
                                                <Link href={`/${locale}/galleries/${gallery.slug}`}>{gallery.title}</Link>
                                            </h3>
                                            {meta && <p className="gallery-album-meta">{meta}</p>}
                                            {gallery.summary && <p className="gallery-album-summary">{gallery.summary}</p>}
                                            <Link href={`/${locale}/galleries/${gallery.slug}`} className="about-economic-link">
                                                {t('viewGallery')} <span aria-hidden="true">→</span>
                                            </Link>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                    )}
                </div>
            </section>

            <ConnectBanner />
        </PublicLayout>
    );
}
