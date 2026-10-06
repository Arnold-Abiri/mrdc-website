import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type DocumentItem = { slug: string; title: string; description: string | null; category: string; published_at: string | null; reference_date: string | null; download_count: number; current_version: number; reference_year: number | null };
export default function Document({ document }: { document: DocumentItem }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout><div className="mx-auto max-w-5xl px-6 py-12"><h1 className="text-3xl font-bold">{document.title}</h1><p className="mt-4">{document.description}</p><p className="mt-4">{document.category}{document.reference_year ? ` — ${t('referenceYear')}: ${document.reference_year}` : ''}</p><p className="mt-2"><small>{t('downloadFile')}: {t('version')} {document.current_version} — {document.download_count} {t('downloadCount')}</small></p><a className="mt-6 inline-block underline" href={`/${locale}/documents/${document.slug}/download`}>{t('downloadFile')}</a></div></PublicLayout>;
}
