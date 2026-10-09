import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type DocumentItem = { slug: string; title: string; description: string | null; category: string; published_at: string | null; reference_date: string | null; download_count: number; current_version: number; reference_year: number | null; has_file: boolean };
export default function Document({ document }: { document: DocumentItem }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout><div className="mx-auto max-w-5xl px-6 py-12"><h1 className="text-3xl font-bold">{document.title}</h1><p className="mt-4">{document.description}</p><p className="mt-4">{document.category}{document.reference_year ? ` — ${t('referenceYear')}: ${document.reference_year}` : ''}</p><p className="mt-2"><small>{t('downloadFile')}: {t('version')} {document.current_version} — {document.download_count} {t('downloadCount')}</small></p>{document.has_file ? <a className="mt-6 inline-block underline" href={`/${locale}/documents/${document.slug}/download`}>{t('downloadFile')}</a> : <span className="file-fallback mt-6" role="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" /><path d="M14 2v6h6" /></svg><span>File currently unavailable — please check back later</span></span>}</div></PublicLayout>;
}
