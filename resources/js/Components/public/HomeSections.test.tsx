import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import {
    AboutSection,
    CtaBanner,
    DevelopmentSection,
    FeatureCallouts,
    Hero,
    KeyServicesSection,
    ManagedHomepageContent,
    NewsAndEvents,
    QuickAccess,
    TourismSection,
    ValuePillars,
} from './HomeSections';

describe('homepage sections', () => {
    it('renders hero with welcome statement and key stats', () => {
        render(<Hero />);
        expect(screen.getByRole('heading', { level: 1 })).toHaveTextContent('Mutoko Rural District Council');
        expect(screen.getByText('People. Development. Sustainable Communities.')).toBeInTheDocument();
        expect(screen.queryByText('50+')).not.toBeInTheDocument();
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

    it('renders the about us section with core pillars and welcome image', () => {
        render(<AboutSection />);
        expect(screen.getByText('A Vibrant and Prosperous Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Service Delivery')).toBeInTheDocument();
        expect(screen.getByText('Transparency')).toBeInTheDocument();
        expect(screen.getByText('Sustainable Growth')).toBeInTheDocument();
        expect(screen.getByText('Community Focus')).toBeInTheDocument();
        expect(screen.getByAltText('Welcome to Mutoko road entrance')).toBeInTheDocument();
    });

    it('renders key services cards', () => {
        render(<KeyServicesSection />);
        expect(screen.getByText('Key Services')).toBeInTheDocument();
        expect(screen.getByText('Water Supply')).toBeInTheDocument();
        expect(screen.getByText('Roads & Infrastructure')).toBeInTheDocument();
        expect(screen.getByText('Health & Sanitation')).toBeInTheDocument();
        expect(screen.getByText('Environmental Management')).toBeInTheDocument();
    });

    it('shows empty states when no approved homepage CMS records exist', () => {
        render(<ManagedHomepageContent services={[]} documents={[]} departments={[]} notices={[]} />);
        expect(screen.getByText('No approved services are available yet.')).toBeInTheDocument();
        expect(screen.getByText('No approved documents are available yet.')).toBeInTheDocument();
        expect(screen.getByText('No approved department information is available yet.')).toBeInTheDocument();
        expect(screen.getByText('No approved notices are available yet.')).toBeInTheDocument();
    });

    it('renders development banner section with headline and metrics', () => {
        render(<DevelopmentSection />);
        expect(screen.getByText('Building a Better Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Explore Development')).toBeInTheDocument();
        expect(screen.queryByText('50+')).not.toBeInTheDocument();
    });

    it('renders tourism section with featured destinations', () => {
        render(<TourismSection />);
        expect(screen.getByText('Discover Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Nyamurora Mountains')).toBeInTheDocument();
        expect(screen.getByText('Natural Attraction')).toBeInTheDocument();
        expect(screen.getByText('Cultural Heritage')).toBeInTheDocument();
        expect(screen.getByText('Scenic Landscapes')).toBeInTheDocument();
        expect(screen.getByText('Community Experiences')).toBeInTheDocument();
    });

    it('preserves the discover more mutoko at a glance section', () => {
        render(<FeatureCallouts />);
        expect(screen.getByText('Discover more')).toBeInTheDocument();
        expect(screen.getByText('Mutoko at a glance')).toBeInTheDocument();
        expect(screen.getByText('Invest in Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Explore Our Tourism')).toBeInTheDocument();
        expect(screen.getByText('Engage With Us')).toBeInTheDocument();
    });

    it('renders the partner CTA banner', () => {
        render(<CtaBanner />);
        expect(screen.getByText('Partner with Us for a Better Mutoko')).toBeInTheDocument();
        expect(screen.getByText('Get in Touch')).toBeInTheDocument();
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


