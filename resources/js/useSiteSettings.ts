import { usePage } from '@inertiajs/react';

export type SiteSettings = Record<string, string | null | undefined>;

export function useSiteSettings(): SiteSettings {
    const { site_settings } = usePage<{ site_settings?: SiteSettings }>().props;

    return site_settings ?? {};
}

export function whatsappLinkFor(raw: string | null | undefined): string | null {
    if (!raw || raw.trim() === '') {
        return null;
    }
    const digits = raw.replace(/\D+/g, '');
    if (!digits) {
        return null;
    }
    const normalized = digits.startsWith('0') && digits.length >= 9 ? `263${digits.slice(1)}` : digits;

    return `https://wa.me/${normalized}`;
}
