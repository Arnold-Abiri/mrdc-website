// DEVELOPMENT DEMO DATA — UNVERIFIED. Replace only after council content approval; see docs/content/DEVELOPMENT-CONTENT-POLICY.md.
export type NewsPreview = {
    title: string;
    date: string;
    summary: string;
    image?: string;
    href?: string;
};

export type EventPreview = {
    day: string;
    month: string;
    title: string;
    time: string;
    location: string;
    href?: string;
};

export type ServicePreview = {
    id: string;
    title: string;
    description: string;
    image: string;
    icon: 'water' | 'roads' | 'health' | 'environment';
    href: string;
};

export type TourismPreview = {
    title: string;
    tag: string;
    image: string;
    href: string;
};

export type AboutPillar = {
    title: string;
    description: string;
    icon: 'gear' | 'shield' | 'leaf' | 'users';
};

export const newsPreviews: NewsPreview[] = [
    {
        title: 'Council Launches New Water Supply Project',
        date: '02 Nov 2024',
        summary: 'Mutoko RDC has launched a new water supply project to improve access to clean and safe water in rural communities.',
        image: '/images/home/news-water.webp',
        href: '/coming-soon?topic=news',
    },
    {
        title: 'Community Engagement Meetings Continue',
        date: '28 Oct 2024',
        summary: 'Council holds community engagement meetings across all 12 wards to discuss development priorities.',
        image: '/images/home/news-community.webp',
        href: '/coming-soon?topic=news',
    },
    {
        title: 'Road Rehabilitation Works Progress Well',
        date: '15 Oct 2024',
        summary: 'Major road rehabilitation works are ongoing to improve connectivity and support local economic development.',
        image: '/images/home/news-roads.webp',
        href: '/coming-soon?topic=news',
    },
];

export const eventPreviews: EventPreview[] = [
    {
        day: '25',
        month: 'Nov',
        title: 'Ward Community Meeting',
        location: 'Ward 5 - Chivhu River',
        time: '10:00 AM - 1:00 PM',
        href: '/coming-soon?topic=events',
    },
    {
        day: '28',
        month: 'Nov',
        title: 'Community Information Session',
        location: 'Council Chambers',
        time: '09:00 AM - 12:00 PM',
        href: '/coming-soon?topic=events',
    },
    {
        day: '05',
        month: 'Dec',
        title: 'Clean Up Campaign',
        location: 'Mutoko Town',
        time: '08:00 AM - 12:00 PM',
        href: '/coming-soon?topic=events',
    },
];

export const keyServices: ServicePreview[] = [
    {
        id: 'water',
        title: 'Water Supply',
        description: 'Provision and maintenance of clean and safe water systems.',
        image: '/images/home/service-water.webp',
        icon: 'water',
        href: '/coming-soon?topic=water',
    },
    {
        id: 'roads',
        title: 'Roads & Infrastructure',
        description: 'Construction and maintenance of rural roads and infrastructure.',
        image: '/images/home/service-roads.webp',
        icon: 'roads',
        href: '/coming-soon?topic=roads',
    },
    {
        id: 'health',
        title: 'Health & Sanitation',
        description: 'Support for health facilities and improved sanitation services.',
        image: '/images/home/service-health.webp',
        icon: 'health',
        href: '/coming-soon?topic=health',
    },
    {
        id: 'environment',
        title: 'Environmental Management',
        description: 'Conservation of natural resources and a cleaner environment.',
        image: '/images/home/service-environment.webp',
        icon: 'environment',
        href: '/coming-soon?topic=environment',
    },
];

export const tourismDestinations: TourismPreview[] = [
    {
        title: 'Nyamurora Mountains',
        tag: 'Natural Attraction',
        image: '/images/home/tourism-nyamurora.webp',
        href: '/coming-soon?topic=tourism',
    },
    {
        title: 'Cultural Heritage',
        tag: 'Rich Traditions',
        image: '/images/home/tourism-cultural.webp',
        href: '/coming-soon?topic=tourism',
    },
    {
        title: 'Scenic Landscapes',
        tag: 'Breathtaking Views',
        image: '/images/home/tourism-scenic.webp',
        href: '/coming-soon?topic=tourism',
    },
    {
        title: 'Community Experiences',
        tag: 'Local Culture',
        image: '/images/home/tourism-community.webp',
        href: '/coming-soon?topic=tourism',
    },
];

export const aboutPillars: AboutPillar[] = [
    {
        title: 'Service Delivery',
        description: 'Quality services for all communities',
        icon: 'gear',
    },
    {
        title: 'Transparency',
        description: 'Accountable and open governance',
        icon: 'shield',
    },
    {
        title: 'Sustainable Growth',
        description: 'Environmental stewardship',
        icon: 'leaf',
    },
    {
        title: 'Community Focus',
        description: 'People-centred development',
        icon: 'users',
    },
];
