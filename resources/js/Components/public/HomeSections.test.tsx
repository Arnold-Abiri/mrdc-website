import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { NewsAndEvents } from './HomeSections';

describe('homepage previews', () => {
    it('explains when no approved news or events exist', () => {
        render(<NewsAndEvents news={[]} events={[]} />);
        expect(screen.getByText('No news published yet')).toBeInTheDocument();
        expect(screen.getByText('No upcoming events published')).toBeInTheDocument();
    });

    it('renders short and long titles without requiring images', () => {
        const longTitle = 'A long council update title that should wrap without losing its meaning on narrow screens';
        render(<NewsAndEvents news={[
            { title: 'Update', date: 'Date pending', summary: 'Short summary' },
            { title: longTitle, date: 'Date pending', summary: 'Long title summary' },
        ]} events={[{ day: '01', month: 'JAN', title: 'Fixture event', time: 'Time pending', location: 'Location pending' }]} />);
        expect(screen.getByText(longTitle)).toBeInTheDocument();
        expect(screen.getAllByText('Image pending approval')).toHaveLength(2);
        expect(screen.getByText('Fixture event')).toBeInTheDocument();
    });
});
