import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';

type Result = { type: string; title: string; summary: string | null; url: string; is_review_content: boolean };

export default function Search({ query, results }: { query: string; results: Result[] }) {
    return <PublicLayout>
        <Head title="Search" />
        <div className="container coming-soon">
            <h1>Search</h1>
            <form action="/search" method="get" role="search">
                <label htmlFor="site-search">Search council pages</label>
                <input id="site-search" name="q" type="search" defaultValue={query} maxLength={100} />
                <button type="submit">Search</button>
            </form>
            {query && <p>{results.length ? `${results.length} result(s)` : 'No approved results found.'}</p>}
            <ul>{results.map(result => <li key={result.url}>
                <small>{result.type}</small> <a href={result.url}>{result.title}</a>
                {result.summary && <p>{result.summary}</p>}
                {result.is_review_content && <small>Development review content</small>}
            </li>)}</ul>
        </div>
    </PublicLayout>;
}
