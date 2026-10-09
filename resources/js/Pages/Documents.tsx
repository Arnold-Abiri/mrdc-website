import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type DocumentItem = { slug: string; title: string; description: string | null; category: string; published_at: string | null; reference_date: string | null; download_count: number };
const RESOURCE_LINKS = ['Constitution of Zimbabwe', 'Local Government Laws Amendment Act', 'Rural Land Act', 'Stock Theft Prevention Act', 'Traditional Leaders Act', 'Mutoko Master Plan — public exhibition notice'];
export default function Documents({ documents, categories, years, filters }: { documents: DocumentItem[]; categories: string[]; years: number[]; filters: { category: string | null; year: string | null; department: string | null; q: string } }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={t('councilDocuments')} />
            <PageHero eyebrow="DOCUMENTS" title={t('councilDocuments')} subtitle="Council plans, budgets, minutes and statutory instruments. Key references include the Constitution, local government and land legislation, and the Mutoko Master Plan currently on public exhibition for comment at Stand 366 Mutoko Centre." crumb={[{ label: 'Documents' }]} />
            <section className="about-page-section" aria-labelledby="documents-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">KEY RESOURCES</span>
                    <h2 id="documents-heading">Key resources</h2>
                    <div className="about-focus-grid">
                        {RESOURCE_LINKS.map(r => (
                            <div key={r} className="about-focus-card"><h3>{r}</h3></div>
                        ))}
                    </div>
                </div>
            </section>
            <section className="about-page-section about-page-section-muted" aria-label="Filter documents">
                <div className="container">
                    <div className="about-vision-card">
                        <div>
                            <h3>{t('filter')}</h3>
                            <form method="get" action={`/${locale}/documents`} className="mt-6 flex flex-wrap gap-4">
                                <label>{t('keyword')}<input type="search" name="q" defaultValue={filters.q} maxLength={100} /></label>
                                <label>{t('category')}<select name="category" defaultValue={filters.category ?? ''}><option value="">{t('clearFilters')}</option>{categories.map(category => <option key={category} value={category}>{category}</option>)}</select></label>
                                <label>{t('year')}<select name="year" defaultValue={filters.year ?? ''}><option value="">{t('clearFilters')}</option>{years.map(year => <option key={year} value={year}>{year}</option>)}</select></label>
                                <button type="submit">{t('filter')}</button>
                            </form>
                        </div>
                    </div>
                    <div style={{ marginTop: '24px' }}>
                        {documents.length === 0 ? <EmptyState title={t('noDocuments')} text={t('noDocuments')} /> : (
                            <div className="about-mandate-grid">
                                {documents.map(document => (
                                    <div key={document.slug} className="about-mandate-card">
                                        <h3><Link href={`/${locale}/documents/${document.slug}`}>{document.title}</Link></h3>
                                        <p>{document.description}</p>
                                        <p><small>{document.category}{document.reference_date ? ` — ${t('referenceYear')}: ${document.reference_date.slice(0, 4)}` : ''} — {document.download_count} {t('downloadCount')}</small></p>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
