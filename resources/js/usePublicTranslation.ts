import { usePage } from '@inertiajs/react';
import { normalizeLocale, translate } from './localization';

export function usePublicTranslation() {
    const { locale } = usePage().props as { locale?: string };
    return (key: Parameters<typeof translate>[1]) => translate(normalizeLocale(locale), key);
}
