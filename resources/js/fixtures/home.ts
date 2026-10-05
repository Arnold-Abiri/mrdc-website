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

export const newsPreviews: NewsPreview[] = [];

export const eventPreviews: EventPreview[] = [];

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
