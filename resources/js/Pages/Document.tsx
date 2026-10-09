import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';

type DocumentItem = { slug: string; title: string; description: string | null; category: string; published_at: string | null; reference_date: string | null; download_count: number; current_version: number; reference_year: number | null; has_file: boolean };
export default function Document({ document }: { document: DocumentItem }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={document.title} />
            <PageHero eyebrow="DOCUMENT" title={document.title} subtitle={document.description || `View this ${document.category.toLowerCase()} from Mutoko Rural District Council.`} crumb={[{ label: 'Documents', href: `/${locale}/documents` }, { label: document.title }]} />
            <section className="about-page-section" aria-labelledby="document-detail">
                <div className="container">
                    <div className="about-vision-card">
                        <div>
                            <h3 id="document-detail">{document.title}</h3>
                            <p>{document.description}</p>
                            <p>{document.category}{document.reference_year ? ` — ${t('referenceYear')}: ${document.reference_year}` : ''}</p>
                            <p><small>{t('downloadFile')}: {t('version')} {document.current_version} — {document.download_count} {t('downloadCount')}</small></p>
                            {document.has_file ? <p style={{ marginTop: '12px' }}><Link className="about-page-text-link" href={`/${locale}/documents/${document.slug}/download`}>{t('downloadFile')} <span aria-hidden="true">→</span></Link></p> : <span className="file-fallback mt-6" role="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" /><path d="M14 2v6h6" /></svg><span>File currently unavailable — please check back later</span></span>}
                        </div>
                    </div>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
