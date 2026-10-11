import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/react')>()),
    usePage: () => ({ props: { locale: 'en' } }),
}));
import { extractSources, SourcedText } from './SourceButtons';

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

describe('SourcedText markdown', () => {
    it('renders bold, italic and links', () => {
        render(<SourcedText text="Council meeting on **Friday** at *10am*. See [agenda](https://mutokordc.gov.zw/agenda)." />);
        expect(screen.getByText('Friday').tagName).toBe('STRONG');
        expect(screen.getByText('10am').tagName).toBe('EM');
        const link = screen.getByRole('link', { name: 'agenda' });
        expect(link).toHaveAttribute('href', 'https://mutokordc.gov.zw/agenda');
        expect(link).toHaveAttribute('rel', 'noopener noreferrer');
    });

    it('renders bullet lists', () => {
        const { container } = render(<SourcedText text={'Works update:\n- Roads graded\n- Boreholes drilled'} />);
        const items = container.querySelectorAll('li');
        expect(items).toHaveLength(2);
        expect(items[0]).toHaveTextContent('Roads graded');
    });

    it('does not interpret raw HTML', () => {
        const { container } = render(<SourcedText text={'<script>alert(1)</script><img src=x onerror=alert(1)>'} />);
        expect(container.querySelector('script')).toBeNull();
        expect(container.querySelector('img')).toBeNull();
        expect(container.textContent).toContain('<script>');
    });

    it('renders plain paragraphs unchanged', () => {
        render(<SourcedText text="Official council financial publication." />);
        expect(screen.getByText('Official council financial publication.')).toBeInTheDocument();
    });
});
