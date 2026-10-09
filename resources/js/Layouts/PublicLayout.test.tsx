import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/react')>()),
    usePage: () => ({ props: { locale: 'en', localeCsrfToken: 'test-token' } }),
}));
import PublicLayout from './PublicLayout';

describe('public navigation', () => {
    it('opens on mobile and exposes its state', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        const menu = screen.getByRole('button', { name: 'Menu' });
        expect(menu).toHaveAttribute('aria-expanded', 'false');
        fireEvent.click(menu);
        expect(screen.getByRole('button', { name: 'Close menu' })).toHaveAttribute('aria-expanded', 'true');
    });

    it('renders council branding and contact utility details', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        const logo = screen.getByAltText('Mutoko Rural District Council crest');
        expect(logo).toBeInTheDocument();
        expect(logo).toHaveAttribute('src', '/images/council-crest-light.webp');
        expect(screen.getByText('Mutoko, Mashonaland East, Zimbabwe')).toBeInTheDocument();
        expect(screen.queryByText('+263 71 234 5678')).not.toBeInTheDocument();
        expect(screen.queryByText('info@mutokordc.gov.zw')).not.toBeInTheDocument();
    });
});

