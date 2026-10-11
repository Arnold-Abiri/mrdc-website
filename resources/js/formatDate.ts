/**
 * Format an ISO/database date string for public display.
 * Backend dates serialize as full ISO timestamps (e.g. 2026-05-15T00:00:00.000000Z);
 * rendering them raw leaks machine timestamps into the UI.
 */
export function formatDate(value: string | null | undefined, locale: string = 'en'): string {
    if (!value) {
        return '';
    }
    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    return parsed.toLocaleDateString(locale === 'en' ? 'en-GB' : `${locale}-ZW`, {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

export function formatDateLong(value: string | null | undefined, locale: string = 'en'): string {
    if (!value) {
        return '';
    }
    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    return parsed.toLocaleDateString(locale === 'en' ? 'en-GB' : `${locale}-ZW`, {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
