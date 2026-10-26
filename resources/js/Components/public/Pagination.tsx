import { Link } from '@inertiajs/react';
import { usePublicTranslation } from '../../usePublicTranslation';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type PaginatedData<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
};

function cleanLabel(raw: string): string {
    return raw
        .replace(/&laquo;|&lsaquo;|«|‹/g, '')
        .replace(/&raquo;|&rsaquo;|»|›/g, '')
        .replace(/Previous/i, '')
        .replace(/Next/i, '')
        .trim();
}

export function Pagination({
    links,
    className = '',
}: {
    links: PaginationLink[];
    className?: string;
}) {
    const t = usePublicTranslation();

    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <nav
            className={`pagination-nav ${className}`.trim()}
            aria-label="Pagination Navigation"
            role="navigation"
        >
            <ul className="pagination-list">
                {links.map((link, index) => {
                    const isPrev = index === 0;
                    const isNext = index === links.length - 1;
                    const cleaned = cleanLabel(link.label);
                    const isEllipsis = !isPrev && !isNext && (cleaned === '...' || link.label.includes('...'));

                    if (isEllipsis) {
                        return (
                            <li key={index} className="pagination-item">
                                <span className="pagination-ellipsis" aria-hidden="true">
                                    …
                                </span>
                            </li>
                        );
                    }

                    if (!link.url) {
                        return (
                            <li key={index} className="pagination-item">
                                <span
                                    className={`pagination-link disabled ${isPrev ? 'prev' : ''} ${isNext ? 'next' : ''}`}
                                    aria-disabled="true"
                                >
                                    {isPrev && (
                                        <>
                                            <span aria-hidden="true">←</span>
                                            <span>{t('previousSlide') || 'Previous'}</span>
                                        </>
                                    )}
                                    {isNext && (
                                        <>
                                            <span>{t('nextSlide') || 'Next'}</span>
                                            <span aria-hidden="true">→</span>
                                        </>
                                    )}
                                    {!isPrev && !isNext && (cleaned || link.label)}
                                </span>
                            </li>
                        );
                    }

                    return (
                        <li key={index} className="pagination-item">
                            <Link
                                href={link.url}
                                preserveScroll
                                preserveState
                                className={`pagination-link ${link.active ? 'active' : ''} ${isPrev ? 'prev' : ''} ${isNext ? 'next' : ''}`}
                                aria-current={link.active ? 'page' : undefined}
                                aria-label={
                                    isPrev
                                        ? 'Go to previous page'
                                        : isNext
                                          ? 'Go to next page'
                                          : `Go to page ${cleaned || link.label}`
                                }
                            >
                                {isPrev && (
                                    <>
                                        <span aria-hidden="true">←</span>
                                        <span>{t('previousSlide') || 'Previous'}</span>
                                    </>
                                )}
                                {isNext && (
                                    <>
                                        <span>{t('nextSlide') || 'Next'}</span>
                                        <span aria-hidden="true">→</span>
                                    </>
                                )}
                                {!isPrev && !isNext && (cleaned || link.label)}
                            </Link>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}

export default Pagination;
