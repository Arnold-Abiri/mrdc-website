import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero } from '../Components/public/PageHeader';
import { SourcedText, extractSources } from '../Components/public/SourceButtons';

type RelatedDoc = {
    slug: string;
    title: string;
    category: string;
    download_count: number;
};

type DocumentItem = {
    slug: string;
    title: string;
    description: string | null;
    category: string;
    published_at: string | null;
    reference_date: string | null;
    download_count: number;
    current_version: number;
    reference_year: number | null;
    has_file: boolean;
    file_size?: string;
    mime_type?: string;
    department?: string | null;
};

function formatDate(dateStr: string | null): string {
    if (!dateStr) return 'Official Release';
    try {
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    } catch {
        return dateStr;
    }
}

export default function Document({
    document,
    related = [],
}: {
    document: DocumentItem;
    related?: RelatedDoc[];
}) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;

    const formattedCategory = document.category.replace(/_/g, ' ').toUpperCase();

    return (
        <PublicLayout>
            <Head title={`${document.title} | Council Documents`} />

            {/* Standard MRDC Hero Header */}
            <PageHero
                eyebrow={`OFFICIAL DOCUMENT · ${formattedCategory}`}
                title={document.title}
                subtitle={extractSources(document.description).body || `Official municipal document approved for public release by Mutoko Rural District Council.`}
                crumb={[
                    { label: t('documents'), href: `/${locale}/documents` },
                    { label: document.title },
                ]}
            />

            {/* Main Details Section */}
            <section className="resources-main-section" aria-labelledby="document-specs-heading">
                <div className="container">
                    <div className="resources-portal-layout">
                        {/* Main Specifications Card */}
                        <div className="resources-content-col">
                            <div className="document-specs-card">
                                <div className="specs-card-header">
                                    <div className="specs-card-title-wrap">
                                        <div className="resource-file-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                            </svg>
                                            <span className="resource-file-ext">PDF</span>
                                        </div>
                                        <div>
                                            <h2 id="document-specs-heading" className="specs-heading">
                                                {t('docSpecsTitle')}
                                            </h2>
                                            <p className="specs-subheading">
                                                {t('docSpecsDesc')}
                                            </p>
                                        </div>
                                    </div>

                                    {document.has_file ? (
                                        <a
                                            href={L(`/documents/${document.slug}/download`)}
                                            className="btn-specs-download-large"
                                            aria-label={`Download ${document.title}`}
                                        >
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                <polyline points="7 10 12 15 17 10" />
                                                <line x1="12" y1="15" x2="12" y2="3" />
                                            </svg>
                                            <span>{t('docDownloadPdf')} ({document.file_size || t('docOfficialFile')})</span>
                                        </a>
                                    ) : (
                                        <div className="file-unavailable-badge">
                                            <span>{t('docArchiveProcessing')}</span>
                                        </div>
                                    )}
                                </div>

                                {/* Metadata Grid */}
                                <div className="specs-metadata-grid">
                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaCategory')}</span>
                                        <strong className="spec-value">{formattedCategory}</strong>
                                    </div>

                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaSize')}</span>
                                        <strong className="spec-value">{document.file_size || t('docPdfDocument')}</strong>
                                    </div>

                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaYear')}</span>
                                        <strong className="spec-value">
                                            {document.reference_year ? document.reference_year : (document.reference_date?.slice(0, 4) || t('docCurrent'))}
                                        </strong>
                                    </div>

                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaPublished')}</span>
                                        <strong className="spec-value">{document.published_at ? formatDate(document.published_at) : t('docOfficialRelease')}</strong>
                                    </div>

                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaVersion')}</span>
                                        <strong className="spec-value">v{document.current_version}.0 ({t('docVersionOfficial')})</strong>
                                    </div>

                                    <div className="spec-meta-item">
                                        <span className="spec-label">{t('docMetaDownloads')}</span>
                                        <strong className="spec-value">{document.download_count} {t('downloadCount')}</strong>
                                    </div>

                                    {document.department && (
                                        <div className="spec-meta-item full-width">
                                            <span className="spec-label">{t('docMetaDept')}</span>
                                            <strong className="spec-value">{document.department}</strong>
                                        </div>
                                    )}
                                </div>

                                {document.description && (
                                    <div className="specs-synopsis">
                                        <h3 className="synopsis-title">{t('docOverviewTitle')}</h3>
                                        <div className="synopsis-text"><SourcedText text={document.description} /></div>
                                    </div>
                                )}
                            </div>

                            {/* Related Category Documents */}
                            {related.length > 0 && (
                                <div className="related-docs-card">
                                    <h3 className="related-docs-title">
                                        {t('docMoreIn')} {formattedCategory}
                                    </h3>
                                    <div className="related-docs-list">
                                        {related.map((rel) => (
                                            <Link
                                                key={rel.slug}
                                                href={L(`/documents/${rel.slug}`)}
                                                className="related-doc-row"
                                            >
                                                <div className="related-doc-left">
                                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                                    </svg>
                                                    <span className="related-doc-title">{rel.title}</span>
                                                </div>
                                                <span className="related-doc-arrow" aria-hidden="true">&rarr;</span>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right Civic Sidebar */}
                        <aside className="resources-sidebar-col" aria-label="Portal Navigation">
                            <div className="resources-sticky-wrapper">
                                <div className="sidebar-card">
                                    <div className="sidebar-card-header">
                                        <div className="sidebar-header-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                <polyline points="14 2 14 8 20 8" />
                                            </svg>
                                        </div>
                                        <h4 className="sidebar-card-title">{t('docSidebarTitle')}</h4>
                                    </div>
                                    <div className="sidebar-card-body">
                                        <p className="sidebar-card-text">
                                            {t('docSidebarDesc')}
                                        </p>
                                        <Link
                                            href={L('/documents')}
                                            className="btn-tourism-explore w-full text-center"
                                        >
                                            &larr; {t('docBackToPortal')}
                                        </Link>
                                    </div>
                                </div>

                                <div className="sidebar-card">
                                    <div className="sidebar-card-header">
                                        <div className="sidebar-header-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                                <line x1="12" y1="17" x2="12.01" y2="17" />
                                            </svg>
                                        </div>
                                        <h4 className="sidebar-card-title">{t('docEnquiriesTitle')}</h4>
                                    </div>
                                    <div className="sidebar-card-body sidebar-card-text">
                                        <p>
                                            {t('docEnquiriesDesc')}
                                        </p>
                                        <div className="sidebar-registry-box">
                                            <p className="sidebar-registry-title">{t('docRegistry')}</p>
                                            <p>registry@mutokordc.gov.zw</p>
                                            <p>+263 77 240 5683</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
