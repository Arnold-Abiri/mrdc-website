import { describe, expect, it } from 'vitest';
import { normalizeLocale, translate } from './localization';

describe('public interface locale fallback', () => {
    it('normalizes unknown locale values to English', () => {
        expect(normalizeLocale('en')).toBe('en');
        expect(normalizeLocale('sn')).toBe('sn');
        expect(normalizeLocale('nd')).toBe('nd');
        expect(normalizeLocale('invalid')).toBe('en');
    });

    it('falls back to English until approved Shona wording is supplied', () => {
        expect(translate('en', 'home')).toBe('Home');
        expect(translate('sn', 'home')).toBe('Home');
        expect(translate('nd', 'home')).toBe('Home');
    });
});
