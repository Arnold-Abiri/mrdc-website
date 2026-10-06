import { usePage } from '@inertiajs/react';
import { normalizeLocale, translate, type PublicLocale } from './localization';

export function usePublicTranslation() {
    const { locale } = usePage().props as { locale?: string };
    return (key: Parameters<typeof translate>[1]) => translate(normalizeLocale(locale), key);
}

export function usePublicLocale(): PublicLocale {
    const { locale } = usePage().props as { locale?: string };
    return normalizeLocale(locale);
}
