import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import '../../css/search.css';

type Result = { type: string; title: string; summary: string | null; url: string; is_review_content: boolean };

const suggestions = [
    { label: 'Council services', path: '/services' },
    { label: 'Public notices', path: '/notices' },
    { label: 'Wards', path: '/wards' },
    { label: 'Documents', path: '/documents' },
];

function SearchIcon({ size = 24 }: { size?: number }) {
    return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" /><path d="m16 16 4.6 4.6" /></svg>;
}

export default function Search({ query, results }: { query: string; results: Result[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const [selectedType, setSelectedType] = useState('All');
    const counts = results.reduce<Record<string, number>>((totals, result) => {
        totals[result.type] = (totals[result.type] ?? 0) + 1;
        return totals;
    }, {});
    const types = Object.keys(counts);
    const activeType = selectedType in counts ? selectedType : 'All';
    const visibleResults = activeType === 'All' ? results : results.filter(result => result.type === activeType);

    return <PublicLayout>
        <Head title={t('search')} />
        <div className="search-page">
            <header className={`search-hero${query ? ' search-hero-compact' : ''}`}>
                <div className="container search-hero-inner">
                    <nav className="search-breadcrumb" aria-label="Breadcrumb"><a href={`/${locale}`}>Home</a><span aria-hidden="true">/</span><span aria-current="page">{t('search')}</span></nav>
                    <span className="search-eyebrow">EXPLORE MUTOKO RDC</span>
                    <h1>What can we help you find?</h1>
                    <p>Search council services, public information, opportunities and updates in one place.</p>
                    <form action={`/${locale}/search`} method="get" role="search" className="search-form">
                        <label htmlFor="site-search" className="sr-only">{t('searchCouncilPages')}</label>
                        <span className="search-form-icon"><SearchIcon /></span>
                        <input id="site-search" name="q" type="search" defaultValue={query} maxLength={100} placeholder="Search the council website..." autoComplete="off" />
                        <button type="submit">{t('search')} <span aria-hidden="true">→</span></button>
                    </form>
                    <div className="search-suggestions"><span>Popular pages</span>{suggestions.map(item => <a key={item.path} href={`/${locale}${item.path}`}>{item.label} <span aria-hidden="true">↗</span></a>)}</div>
                </div>
            </header>

            <div className="container search-content">
                {query ? <>
                    <div className="search-results-heading"><div><span className="search-section-eyebrow">SEARCH RESULTS</span><h2>Results for “{query}”</h2><p>{results.length} {t('results')}</p></div><a href={`/${locale}/search`} className="search-clear">Start a new search <span aria-hidden="true">↗</span></a></div>
                    {results.length > 0 ? <>
                        <div className="search-filter-bar" role="group" aria-label="Filter search results by type">
                            <button type="button" className={activeType === 'All' ? 'active' : ''} aria-pressed={activeType === 'All'} onClick={() => setSelectedType('All')}>All results <span>{results.length}</span></button>
                            {types.map(type => <button key={type} type="button" className={activeType === type ? 'active' : ''} aria-pressed={activeType === type} onClick={() => setSelectedType(type)}>{type} <span>{counts[type]}</span></button>)}
                        </div>
                        <p className="search-filter-count" role="status">Showing {visibleResults.length} {activeType === 'All' ? 'results' : `${activeType.toLowerCase()} results`}</p>
                        <ul className="search-result-list">{visibleResults.map(result => <li key={result.url} className="search-result-card"><div className="search-result-main"><span className="search-result-type">{result.type}</span><h3><a href={result.url}>{result.title}</a></h3>{result.summary && <p>{result.summary}</p>}{result.is_review_content && <small className="search-review">{t('reviewContent')}</small>}</div><span className="search-result-arrow" aria-hidden="true">→</span></li>)}</ul>
                    </> : <div className="search-empty"><span className="search-empty-icon"><SearchIcon size={30} /></span><h3>{t('noApprovedResults')}</h3><p>Try a shorter or more general term, or browse one of the popular pages above.</p></div>}
                </> : <section className="search-start" aria-labelledby="search-start-heading"><span className="search-section-eyebrow">FIND YOUR WAY</span><h2 id="search-start-heading">Browse the council website</h2><p>Start with one of these commonly visited sections, or use the search bar above.</p><div className="search-start-grid">{suggestions.map((item, index) => <a href={`/${locale}${item.path}`} key={item.path}><span className="search-start-number">0{index + 1}</span><strong>{item.label}</strong><span aria-hidden="true">↗</span></a>)}</div></section>}
            </div>
        </div>
    </PublicLayout>;
}
