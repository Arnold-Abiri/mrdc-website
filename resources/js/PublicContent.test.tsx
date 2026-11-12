import { render, screen } from '@testing-library/react';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ children, href }: { children: ReactNode; href: string }) => <a href={href}>{children}</a>,
}));
vi.mock('./Layouts/PublicLayout', () => ({ default: ({ children }: { children: ReactNode }) => <main>{children}</main> }));
vi.mock('./usePublicTranslation', () => ({
    usePublicLocale: () => 'en',
    usePublicTranslation: () => (key: string) => key,
}));

import Tourism from './Pages/Tourism';
import Meetings from './Pages/Meetings';
import About from './Pages/About';

describe('published public content', () => {
    it('renders tourism CMS blocks instead of hardcoded attractions and travel guidance', () => {
        const view = render(<Tourism page={{ slug: 'tourism-mutoko', title: 'Approved Tourism Guide', summary: 'Approved summary', blocks: [
            { type: 'heading', text: 'Approved attraction' },
            { type: 'paragraph', text: 'Council approved attraction details.' },
            { type: 'cta', text: 'Ask the council', url: '/contact' },
        ] }} opportunities={[]} />);
        expect(screen.getByRole('heading', { name: 'Approved Tourism Guide' })).toBeInTheDocument();
        expect(screen.getByText('Council approved attraction details.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Ask the council' })).toHaveAttribute('href', '/en/contact');
        expect(screen.queryByText('attrRuinsTitle')).not.toBeInTheDocument();
        expect(screen.queryByText('guideGetHereText')).not.toBeInTheDocument();
        view.rerender(<Tourism page={null} opportunities={[]} />);
        expect(screen.queryByText('Council approved attraction details.')).not.toBeInTheDocument();
        expect(screen.getByRole('status')).toHaveTextContent('not currently published');
    });

    it('does not invent meeting times or venues when they are missing', () => {
        const meeting = { id: 1, title: 'Council Meeting', meeting_type: 'full_council', scheduled_date: null,
            scheduled_time: null, venue: null, meeting_status: 'scheduled' };
        const view = render(<Meetings meetings={[meeting]} />);
        expect(screen.getByText('Time not published')).toBeInTheDocument();
        expect(screen.getByText('Venue not published')).toBeInTheDocument();
        expect(screen.queryByText('10:00 AM')).not.toBeInTheDocument();
        expect(screen.queryByText('Council Chambers, Stand 366 Mutoko Centre')).not.toBeInTheDocument();
        view.rerender(<Meetings meetings={[{ ...meeting, scheduled_time: '14:30', venue: 'Approved Hall' }]} />);
        expect(screen.getByText('14:30')).toBeInTheDocument();
        expect(screen.getByText('Approved Hall')).toBeInTheDocument();
    });

    it('does not insert a ward count when the directory is missing', () => {
        const { container } = render(<About page={{ title: 'About', summary: null, blocks: [],
            seo_title: null, meta_description: null, is_review_content: false }} />);
        const statistics = Array.from(container.querySelectorAll('.about-stat-num')).map((element) => element.textContent);
        expect(statistics).not.toContain('29');
        expect(statistics).toContain('0');
    });
});
