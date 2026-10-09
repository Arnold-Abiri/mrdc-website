import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type FinancialDocument = { slug: string; title: string; description: string | null; category: string; published_at: string | null; reference_date: string | null; download_count: number };

export default function Transparency({ documents, categories, years, filters }: { documents: FinancialDocument[]; categories: string[]; years: number[]; filters: { category: string | null; year: string | null } }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={t('transparency')} />
            <PageHero eyebrow="OPEN GOVERNMENT" title={t('transparency')} subtitle={t('financialTransparencyDesc')} crumb={[{ label: 'Transparency' }]} />
            <section className="about-page-section" aria-labelledby="transparency-docs">
                <div className="container">
                    <span className="about-accent-eyebrow">FINANCIAL DISCLOSURE</span>
                    <h2 id="transparency-docs">Financial Documents</h2>
                    <form method="get" action="/transparency" className="about-vision-card" style={{ marginBottom: '20px' }}>
                        <div style={{ display: 'flex', gap: '12px', flexWrap: 'wrap', alignItems: 'end' }}>
                            <div>
                                <label htmlFor="transparency-category">{t('category')}</label>
                                <select id="transparency-category" name="category" defaultValue={filters.category ?? ''}>
                                    <option value="">{t('clearFilters')}</option>
                                    {categories.map((category) => <option key={category} value={category}>{category}</option>)}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="transparency-year">{t('year')}</label>
                                <select id="transparency-year" name="year" defaultValue={filters.year ?? ''}>
                                    <option value="">{t('clearFilters')}</option>
                                    {years.map((year) => <option key={year} value={year}>{year}</option>)}
                                </select>
                            </div>
                            <button type="submit">{t('filter')}</button>
                        </div>
                    </form>
                    {documents.length === 0 ? (
                        <EmptyState title="No documents" text={t('noFinancialDocs')} />
                    ) : (
                        <div className="about-focus-grid">
                            {documents.map((document) => (
                                <Link key={document.slug} href={`/${locale}/documents/${document.slug}`} className="about-focus-card">
                                    <h3>{document.title}</h3>
                                    <p>{document.category}{document.reference_date ? ` \u2014 ${document.reference_date}` : ''} \u2014 {document.download_count} {t('downloadCount')}</p>
                                    {document.description && <p>{document.description}</p>}
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
