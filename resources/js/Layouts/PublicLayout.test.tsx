import { fireEvent, render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicLayout from './PublicLayout';

describe('public navigation', () => {
    it('opens on mobile and exposes its state', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        const menu = screen.getByRole('button', { name: 'Menu' });
        expect(menu).toHaveAttribute('aria-expanded', 'false');
        fireEvent.click(menu);
        expect(screen.getByRole('button', { name: 'Close menu' })).toHaveAttribute('aria-expanded', 'true');
    });

    it('renders the official council logo and contact utility details', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        const logo = screen.getByAltText('Mutoko Rural District Council Crest');
        expect(logo).toBeInTheDocument();
        expect(logo).toHaveAttribute('src', '/images/logo.png');
        expect(screen.getByText('Mutoko, Mashonaland East, Zimbabwe')).toBeInTheDocument();
        expect(screen.getByText('+263 71 234 5678')).toBeInTheDocument();
        expect(screen.getByText('info@mutokordc.gov.zw')).toBeInTheDocument();
    });
});

