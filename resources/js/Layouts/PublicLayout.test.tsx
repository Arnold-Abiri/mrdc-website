import { fireEvent, render, screen, within } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

let mockPageState: { locale: string; localeCsrfToken: string; topbar_notices: { slug: string; title: string; is_urgent: boolean }[] } = { locale: 'en', localeCsrfToken: 'test-token', topbar_notices: [] };
let mockPageUrl = '/en';

vi.mock('@inertiajs/react', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/react')>()),
    usePage: () => ({ props: mockPageState, url: mockPageUrl }),
}));
import PublicLayout from './PublicLayout';

describe('public navigation', () => {
    it('shows the contact banner on non-home pages and omits it from home', () => {
        mockPageUrl = '/en/about';
        const page = render(<PublicLayout><h1>Content</h1></PublicLayout>);
        expect(screen.getByRole('heading', { name: 'Connect with Your Council' })).toBeInTheDocument();
        expect(screen.getByText('Questions about this page? Reach the right team through one enquiry form.')).toBeInTheDocument();
        expect(within(screen.getByRole('main')).getByRole('link', { name: 'Contact Us' })).toHaveAttribute('href', '/en/contact');

        mockPageUrl = '/en';
        page.rerender(<PublicLayout><h1>Content</h1></PublicLayout>);
        expect(screen.queryByRole('heading', { name: 'Connect with Your Council' })).not.toBeInTheDocument();
    });
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
        expect(screen.getByText(/Welcome to Mutoko Rural District Council/i)).toBeInTheDocument();
        expect(screen.queryByText('+263 71 234 5678')).not.toBeInTheDocument();
        expect(screen.queryByText('info@mutokordc.gov.zw')).not.toBeInTheDocument();
    });

    it('renders published notices in the topbar ticker when provided', () => {
        mockPageState = {
            locale: 'en',
            localeCsrfToken: 'test-token',
            topbar_notices: [
                { slug: 'water-pipe-notice', title: 'Planned Water Disruption in Ward 5', is_urgent: true },
            ],
        };
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        expect(screen.getByText('Urgent Alert')).toBeInTheDocument();
        expect(screen.getByText('Planned Water Disruption in Ward 5')).toBeInTheDocument();
    });

    it('renders Financial Transparency in the navigation and has a single search trigger', () => {
        render(<PublicLayout><h1>Content</h1></PublicLayout>);
        expect(screen.getByRole('link', { name: 'Financial Transparency' })).toBeInTheDocument();
        const searchButtons = screen.getAllByRole('button', { name: 'Search' });
        // Exactly one search trigger in the header
        expect(searchButtons).toHaveLength(1);
    });
});

