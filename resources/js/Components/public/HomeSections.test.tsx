import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/react', async (importOriginal) => ({
    ...(await importOriginal<typeof import('@inertiajs/react')>()),
    usePage: () => ({ props: { locale: 'en', localeCsrfToken: 'test-token' } }),
}));
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

    it('renders the 7 TOR quick access cards with correct titles and descriptions', () => {
        render(<QuickAccess />);
        expect(screen.getByText('Our Services')).toBeInTheDocument();
        expect(screen.getByText('Water, roads, health, sanitation & more')).toBeInTheDocument();
        expect(screen.getByText('Online Services')).toBeInTheDocument();
        expect(screen.getByText('Rates, bills, fees & payments')).toBeInTheDocument();
        expect(screen.getByText('Public Notices')).toBeInTheDocument();
        expect(screen.getByText('Announcements & consultations')).toBeInTheDocument();
        expect(screen.getByText('Tenders')).toBeInTheDocument();
        expect(screen.getByText('Business opportunities')).toBeInTheDocument();
        expect(screen.getByText('Vacancies')).toBeInTheDocument();
        expect(screen.getByText('Join our team')).toBeInTheDocument();
        expect(screen.getByText('Contact Directory')).toBeInTheDocument();
        expect(screen.getByText('Reach the council & enquire')).toBeInTheDocument();
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
        expect(screen.getByText('Our Location')).toBeInTheDocument();
        expect(screen.getByText('Our Communities')).toBeInTheDocument();
        expect(screen.getByText('Our Mission')).toBeInTheDocument();
        expect(screen.getByText('Vision 2030')).toBeInTheDocument();
        expect(screen.getByAltText('Welcome to Mutoko road entrance')).toBeInTheDocument();
    });

    it('renders key services cards', () => {
        render(<KeyServicesSection />);
        expect(screen.getByText('Key Services')).toBeInTheDocument();
        expect(screen.getByText('Water Supply')).toBeInTheDocument();
        expect(screen.getByText('Roads & Works')).toBeInTheDocument();
        expect(screen.getByText('Health & Sanitation')).toBeInTheDocument();
        expect(screen.getByText('Environment & Conservation')).toBeInTheDocument();
    });

    it('shows empty states when no approved homepage CMS records exist', () => {
        render(<ManagedHomepageContent services={[]} documents={[]} departments={[]} notices={[]} />);
        expect(screen.getByText('Approved services will appear here once published.')).toBeInTheDocument();
        expect(screen.getByText('No published documents at this time.')).toBeInTheDocument();
        expect(screen.getByText('No published notices at this time.')).toBeInTheDocument();
        expect(screen.getByText('Leadership profiles are being prepared for publication.')).toBeInTheDocument();
        expect(screen.getByText('Ward profiles are being prepared for publication.')).toBeInTheDocument();
        expect(screen.getByText('Use our enquiry form and the team will respond.')).toBeInTheDocument();
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
        expect(screen.getByText('No upcoming meetings scheduled')).toBeInTheDocument();
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
        expect(document.querySelectorAll('.news-image .img-fallback')).toHaveLength(2);
        expect(screen.getByText('Fixture event')).toBeInTheDocument();
    });

    it('renders managed homepage CMS items with statistics, services, notices, and documents', () => {
        render(
            <ManagedHomepageContent
                services={[{ slug: 'roads', name: 'Roads & Works', summary: 'Road network maintenance' }]}
                documents={[{ slug: 'master-plan', title: 'Mutoko Master Plan', description: 'Development blueprint' }]}
                departments={[{ id: 1, public_name: 'Engineering', public_summary: 'Civil works' }]}
                notices={[{ slug: 'budget-consultation', title: 'Budget Consultation Notice', summary: 'Community input needed' }]}
                statistics={[{ label: 'Electoral wards', value: '29', unit: null, icon: 'wards' }]}
                officials={[{ slug: 'ceo', name: 'Council CEO', title: 'Chief Executive Officer' }]}
                wardCount={29}
                contacts={[{ office: 'Main Office', type: 'phone', value: '+263 123 456' }]}
            />
        );
        expect(screen.getByText('29')).toBeInTheDocument();
        expect(screen.getByText('Electoral wards')).toBeInTheDocument();
        expect(screen.getByText('Roads & Works')).toBeInTheDocument();
        expect(screen.getByText('Road network maintenance')).toBeInTheDocument();
        expect(screen.getByText('Budget Consultation Notice')).toBeInTheDocument();
        expect(screen.getByText('Mutoko Master Plan')).toBeInTheDocument();
        expect(screen.getByText('Council CEO')).toBeInTheDocument();
        expect(screen.getByText('Main Office:')).toBeInTheDocument();
    });
});


