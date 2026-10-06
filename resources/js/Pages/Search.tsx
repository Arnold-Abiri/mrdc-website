import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type Result = { type: string; title: string; summary: string | null; url: string; is_review_content: boolean };

export default function Search({ query, results }: { query: string; results: Result[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout>
        <Head title={t('search')} />
        <div className="container coming-soon">
            <h1>{t('search')}</h1>
            <form action={`/${locale}/search`} method="get" role="search">
                <label htmlFor="site-search">{t('searchCouncilPages')}</label>
                <input id="site-search" name="q" type="search" defaultValue={query} maxLength={100} />
                <button type="submit">{t('search')}</button>
            </form>
            {query && <p>{results.length ? `${results.length} ${t('results')}` : t('noApprovedResults')}</p>}
            <ul>{results.map(result => <li key={result.url}>
                <small>{result.type}</small> <a href={result.url}>{result.title}</a>
                {result.summary && <p>{result.summary}</p>}
                {result.is_review_content && <small>{t('reviewContent')}</small>}
            </li>)}</ul>
        </div>
    </PublicLayout>;
}
