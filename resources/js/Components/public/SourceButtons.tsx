import React from 'react';
import { usePublicTranslation } from '../../usePublicTranslation';

const SOURCE_PATTERNS = [
    // `Source: <url>` — direct form.
    /Source:\s*(https?:\/\/[^\s)]+)/gi,
    // `Source: <label> (<url>)` — e.g. `Source: Wikimedia Commons (https://…)`.
    /Source:\s*[^()\n]*?\(\s*(https?:\/\/[^\s)]+)\s*\)/gi,
    // Bare parenthesised URLs — e.g. licence links `(https://…)` with no `Source:` prefix.
    /\(\s*(https?:\/\/[^\s)]+)\s*\)/g,
];

/**
 * Split trailing `Source: <url>` references out of a body of text.
 * Returns the readable body plus the extracted source URLs.
 */
export function extractSources(text: string | null | undefined): { body: string; sources: string[] } {
    if (!text) {
        return { body: '', sources: [] };
    }
    const sources: string[] = [];
    // Protect Markdown links so their URLs are not mistaken for `Source:` references.
    const mdLinks: string[] = [];
    let body = text.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, (match) => {
        mdLinks.push(match);
        return `@@MDLINK-${mdLinks.length - 1}@@`;
    });
    for (const pattern of SOURCE_PATTERNS) {
        pattern.lastIndex = 0;
        body = body.replace(pattern, (_match, url: string) => {
            sources.push(url.replace(/[.,;]+$/, ''));
            return '';
        });
    }
    body = body
        .replace(/\(\s*\)/g, '')
        .replace(/[^\S\n]{2,}/g, ' ')
        .replace(/ +[.,;]/g, '')
        .split('\n')
        .map((line) => line.trim())
        .join('\n')
        .trim();
    body = body.replace(/@@MDLINK-(\d+)@@/g, (_match, index: string) => mdLinks[Number(index)] ?? '');

    return { body, sources: [...new Set(sources)] };
}

/**
 * Render a paragraph of text with any `Source: <url>` references pulled out
 * into new-tab source buttons below it. Basic Markdown (bold, italic,
 * links, lists, headings, paragraphs) is rendered; all other HTML is escaped.
 */
export function SourcedText({ text, className }: { text: string | null | undefined; className?: string }) {
    const { body, sources } = extractSources(text);

    return (
        <>
            <div className={className}>{renderMarkdownLite(body)}</div>
            <SourceButtons sources={sources} />
        </>
    );
}

/** Render a safe subset of Markdown as React nodes. Raw HTML is never
 * interpreted: text is only ever rendered as React string children. */
function renderMarkdownLite(source: string): React.ReactNode {

    const renderInline = (value: string, keyPrefix: string): React.ReactNode[] => {
        const nodes: React.ReactNode[] = [];
        const pattern = /(\[([^\]]+)\]\((https?:\/\/[^\s)]+)\))|(\*\*([^*]+)\*\*)|(\*([^*]+)\*)/g;
        let last = 0;
        let match: RegExpExecArray | null;
        let key = 0;
        const pushText = (raw: string): void => {
            if (raw) {
                nodes.push(
                    <React.Fragment key={`${keyPrefix}-${key++}`}>
                        {raw.split('\n').reduce<React.ReactNode[]>((acc, line, i) => (i === 0 ? [...acc, line] : [...acc, <br key={i} />, line]), [])}
                    </React.Fragment>,
                );
            }
        };
        while ((match = pattern.exec(value)) !== null) {
            pushText(value.slice(last, match.index));
            last = match.index + match[0].length;
            if (match[1]) {
                nodes.push(
                    <a key={`${keyPrefix}-${key++}`} href={match[3]} target="_blank" rel="noopener noreferrer">
                        {match[2]}
                    </a>,
                );
            } else if (match[4]) {
                nodes.push(<strong key={`${keyPrefix}-${key++}`}>{match[5]}</strong>);
            } else if (match[6]) {
                nodes.push(<em key={`${keyPrefix}-${key++}`}>{match[7]}</em>);
            }
        }
        pushText(value.slice(last));
        return nodes;
    };

    // No manual HTML escaping: all text is rendered as React string children,
    // which React escapes automatically, so raw HTML from editors is inert.
    const lines = source.split('\n');
    const blocks: React.ReactNode[] = [];
    let listItems: string[] = [];
    const flushList = (): void => {
        if (listItems.length > 0) {
            const items = listItems;
            listItems = [];
            blocks.push(
                <ul key={`ul-${blocks.length}`}>
                    {items.map((item, i) => (
                        <li key={i}>{renderInline(item, `li-${blocks.length}-${i}`)}</li>
                    ))}
                </ul>,
            );
        }
    };
    lines.forEach((line) => {
        const bullet = line.match(/^\s*[-*]\s+(.*)$/);
        const ordered = line.match(/^\s*\d+[.)]\s+(.*)$/);
        const heading = line.match(/^(#{1,4})\s+(.*)$/);
        if (bullet) {
            listItems.push(bullet[1]);
        } else if (ordered) {
            listItems.push(ordered[1]);
        } else if (heading) {
            flushList();
            const level = heading[1].length;
            const Tag = `h${Math.min(level + 1, 4)}` as 'h2' | 'h3' | 'h4';
            blocks.push(<Tag key={`h-${blocks.length}`}>{renderInline(heading[2], `h-${blocks.length}`)}</Tag>);
        } else if (line.trim() === '') {
            flushList();
        } else {
            flushList();
            blocks.push(<p key={`p-${blocks.length}`}>{renderInline(line, `p-${blocks.length}`)}</p>);
        }
    });
    flushList();
    return blocks.length > 0 ? blocks : null;
}
function hostname(url: string): string {
    try {
        return new URL(url).hostname.replace(/^www\./, '');
    } catch {
        return url;
    }
}

export function SourceButtons({ sources }: { sources: string[] }) {
    const t = usePublicTranslation();
    const unique = [...new Set(sources)];

    if (unique.length === 0) {
        return null;
    }

    return (
        <span className="source-buttons" style={{ display: 'inline-flex', flexWrap: 'wrap', gap: '8px', marginTop: '8px' }}>
            {unique.map((url) => (
                <a
                    key={url}
                    href={url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="source-btn"
                    style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: '6px',
                        padding: '6px 14px',
                        borderRadius: '999px',
                        background: 'var(--color-civic-navy, #1e3a5f)',
                        color: '#fff',
                        fontSize: '0.78rem',
                        fontWeight: 700,
                        textDecoration: 'none',
                    }}
                >
                    <span>{t('viewSource')}: {hostname(url)}</span>
                    <span aria-hidden="true">↗</span>
                </a>
            ))}
        </span>
    );
}
