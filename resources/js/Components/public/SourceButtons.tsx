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
    let body = text;
    for (const pattern of SOURCE_PATTERNS) {
        pattern.lastIndex = 0;
        body = body.replace(pattern, (_match, url: string) => {
            sources.push(url.replace(/[.,;]+$/, ''));
            return '';
        });
    }
    body = body
        .replace(/\(\s*\)/g, '')
        .replace(/\s{2,}/g, ' ')
        .replace(/\s+[.,;]/g, '')
        .trim();

    return { body, sources: [...new Set(sources)] };
}

/**
 * Render a paragraph of text with any `Source: <url>` references pulled out
 * into new-tab source buttons below it.
 */
export function SourcedText({ text, className }: { text: string | null | undefined; className?: string }) {
    const { body, sources } = extractSources(text);

    return (
        <>
            <p className={className}>{body}</p>
            <SourceButtons sources={sources} />
        </>
    );
}
function hostname(url: string): string {
    try {
        return new URL(url).hostname.replace(/^www\./, '');
    } catch {
        return url;
    }
}

export function SourceButtons({ sources }: { sources: string[] }) {
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
                    <span>View Source: {hostname(url)}</span>
                    <span aria-hidden="true">↗</span>
                </a>
            ))}
        </span>
    );
}
