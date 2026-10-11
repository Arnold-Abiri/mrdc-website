import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { EmptyState, PageHero } from '../Components/public/PageHeader';
import GalleryLightbox, { type GalleryImage } from '../Components/public/GalleryLightbox';
import { extractSources, SourcedText, SourceButtons } from '../Components/public/SourceButtons';

type GalleryDetail = {
    slug: string;
    title: string;
    summary: string | null;
    description: string | null;
    location: string | null;
    album_date: string | null;
    department: string | null;
    is_review_content?: boolean;
};

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }
    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime()) ? '' : parsed.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
}

export default function Gallery({ gallery, images }: { gallery: GalleryDetail; images: GalleryImage[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const [activeIndex, setActiveIndex] = useState<number | null>(null);
    const meta = [formatDate(gallery.album_date), gallery.location, gallery.department].filter(Boolean).join(' · ');

    return (
        <PublicLayout>
            <Head title={`${gallery.title} | ${t('galleries')}`} />
            <PageHero
                eyebrow={t('gallery')}
                title={gallery.title}
                subtitle={meta || undefined}
                crumb={[{ label: t('gallery'), href: `/${locale}/galleries` }, { label: gallery.title }]}
            />

            <section className="about-page-section" aria-labelledby="gallery-detail">
                <div className="container">
                    {gallery.summary && <p className="about-page-lead">{gallery.summary}</p>}
                    {gallery.description && <div className="gallery-detail-copy"><SourcedText text={gallery.description} /></div>}

                    <div className="gallery-detail-head">
                        <span className="about-accent-eyebrow">{t('album')}</span>
                        <h2 id="gallery-detail">
                            {images.length} {images.length === 1 ? t('photo') : t('photos')}
                        </h2>
                    </div>

                    {images.length === 0 ? (
                        <EmptyState title={t('noGalleries')} text={t('galleriesDesc')} />
                    ) : (
                        <ul className="gallery-grid">
                            {images.map((image, index) => (
                                <li key={image.url}>
                                    <button type="button" className="gallery-tile" onClick={() => setActiveIndex(index)} aria-label={`${t('openImage')}: ${image.alt}`}>
                                        <span className="gallery-tile-frame" style={{ aspectRatio: image.width && image.height ? `${image.width} / ${image.height}` : '3 / 2' }}>
                                            <img src={image.url} alt={image.alt} loading="lazy" />
                                        </span>
                                        <span className="gallery-tile-overlay" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <circle cx="11" cy="11" r="7" />
                                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                                <line x1="11" y1="8" x2="11" y2="14" />
                                                <line x1="8" y1="11" x2="14" y2="11" />
                                            </svg>
                                        </span>
                                        {image.caption && <span className="gallery-tile-caption">{extractSources(image.caption).body}</span>}
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}

                    <p className="gallery-detail-back">
                        <Link className="about-page-text-link" href={`/${locale}/galleries`}>
                            <span aria-hidden="true">←</span> {t('viewGallery')}
                        </Link>
                    </p>
                </div>
            </section>

            {activeIndex !== null && (
                <GalleryLightbox images={images} index={activeIndex} onClose={() => setActiveIndex(null)} onIndexChange={setActiveIndex} />
            )}
        </PublicLayout>
    );
}
