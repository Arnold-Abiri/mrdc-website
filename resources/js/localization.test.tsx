import { describe, expect, it } from 'vitest';
import { normalizeLocale, translate } from './localization';

describe('public interface locale fallback', () => {
    it('normalizes unknown locale values to English', () => {
        expect(normalizeLocale('en')).toBe('en');
        expect(normalizeLocale('sn')).toBe('sn');
        expect(normalizeLocale('nd')).toBe('nd');
        expect(normalizeLocale('invalid')).toBe('en');
    });

    it('uses draft Shona and Ndebele interface wording', () => {
        expect(translate('en', 'home')).toBe('Home');
        expect(translate('sn', 'home')).toBe('Peji rekutanga');
        expect(translate('nd', 'home')).toBe('Ikhaya');
        expect(translate('sn', 'accessibility')).toBe('Kuwanika');
        expect(translate('nd', 'accessibility')).toBe('Ukufinyeleleka');
    });
});
