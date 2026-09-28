import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { Hero, NewsAndEvents, QuickAccess, ValuePillars } from './HomeSections';

describe('homepage sections', () => {
    it('renders hero with welcome statement and key stats', () => {
        render(<Hero />);
        expect(screen.getByRole('heading', { level: 1 })).toHaveTextContent('Mutoko Rural District Council');
        expect(screen.getByText('People. Development. Sustainable Communities.')).toBeInTheDocument();
        expect(screen.getByText('12')).toBeInTheDocument();
        expect(screen.getByText('Wards')).toBeInTheDocument();
        expect(screen.getByText('50+')).toBeInTheDocument();
        expect(screen.getByText('Community Projects')).toBeInTheDocument();
        expect(screen.getByText('3')).toBeInTheDocument();
        expect(screen.getByText('Growth Points')).toBeInTheDocument();
        expect(screen.getByText('1')).toBeInTheDocument();
        expect(screen.getByText('Shared Vision')).toBeInTheDocument();
    });

    it('renders the 6 quick access cards with correct titles and descriptions', () => {
        render(<QuickAccess />);
        expect(screen.getByText('Our Council')).toBeInTheDocument();
        expect(screen.getByText('Leadership & governance structures')).toBeInTheDocument();
        expect(screen.getByText('Our Services')).toBeInTheDocument();
        expect(screen.getByText('Water, roads, health, sanitation & more')).toBeInTheDocument();
        expect(screen.getByText('Development')).toBeInTheDocument();
        expect(screen.getByText('Projects & investment opportunities')).toBeInTheDocument();
        expect(screen.getByText('Tourism')).toBeInTheDocument();
        expect(screen.getByText("Explore Mutoko's natural beauty")).toBeInTheDocument();
        expect(screen.getByText('Tenders')).toBeInTheDocument();
        expect(screen.getByText('Business opportunities')).toBeInTheDocument();
        expect(screen.getByText('Vacancies')).toBeInTheDocument();
        expect(screen.getByText('Join our team')).toBeInTheDocument();
    });

    it('renders the 3 strategic value pillars', () => {
        render(<ValuePillars />);
        expect(screen.getByText('Sustainable Development')).toBeInTheDocument();
        expect(screen.getByText('A cleaner, greener and more resilient Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Community Empowerment')).toBeInTheDocument();
        expect(screen.getByText('People at the heart of progress')).toBeInTheDocument();
        expect(screen.getByText('Shared Prosperity')).toBeInTheDocument();
        expect(screen.getByText('Opportunities for a better tomorrow')).toBeInTheDocument();
    });

    it('explains when no approved news or events exist', () => {
        render(<NewsAndEvents news={[]} events={[]} />);
        expect(screen.getByText('No news published yet')).toBeInTheDocument();
        expect(screen.getByText('No upcoming events published')).toBeInTheDocument();
    });

    it('renders short and long titles without requiring images', () => {
        const longTitle = 'A long council update title that should wrap without losing its meaning on narrow screens';
        render(
            <NewsAndEvents
                news={[
                    { title: 'Update', date: 'Date pending', summary: 'Short summary' },
                    { title: longTitle, date: 'Date pending', summary: 'Long title summary' },
                ]}
                events={[{ day: '01', month: 'JAN', title: 'Fixture event', time: 'Time pending', location: 'Location pending' }]}
            />
        );
        expect(screen.getByText(longTitle)).toBeInTheDocument();
        expect(screen.getAllByText('Image pending approval')).toHaveLength(2);
        expect(screen.getByText('Fixture event')).toBeInTheDocument();
    });
});

