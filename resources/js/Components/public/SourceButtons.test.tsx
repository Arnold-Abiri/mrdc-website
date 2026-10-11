import { describe, expect, it } from 'vitest';
import { extractSources } from './SourceButtons';

describe('extractSources', () => {
    it('extracts a direct Source: url', () => {
        const { body, sources } = extractSources(
            'Official Auditor-General report for 2023. The Auditor-General issued a qualified opinion. Source: https://auditorgeneral.gov.zw/wp-content/uploads/2025/09/LOCAL-AUTHORITIES-2024.pdf',
        );
        expect(sources).toEqual(['https://auditorgeneral.gov.zw/wp-content/uploads/2025/09/LOCAL-AUTHORITIES-2024.pdf']);
        expect(body).not.toContain('http');
        expect(body).toContain('qualified opinion');
    });

    it('extracts a labelled Source: label (url) reference', () => {
        const { body, sources } = extractSources(
            'Photographed on 22 November 2020 by Robert Stewart Burrett. Source: Wikimedia Commons (https://commons.wikimedia.org/wiki/File:Tere_Ruin_Mutoko.jpg). Licensed under CC BY-SA 4.0.',
        );
        expect(sources).toEqual(['https://commons.wikimedia.org/wiki/File:Tere_Ruin_Mutoko.jpg']);
        expect(body).not.toContain('http');
        expect(body).not.toContain('()');
    });

    it('extracts a bare parenthesised licence url', () => {
        const { body, sources } = extractSources(
            'Licensed under Creative Commons Attribution-ShareAlike 4.0 (https://creativecommons.org/licenses/by-sa/4.0/). No image edits have been made.',
        );
        expect(sources).toEqual(['https://creativecommons.org/licenses/by-sa/4.0/']);
        expect(body).not.toContain('http');
    });

    it('returns empty sources for plain copy', () => {
        expect(extractSources('Official council financial publication.')).toEqual({
            body: 'Official council financial publication.',
            sources: [],
        });
    });
});
