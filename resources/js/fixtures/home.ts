// DEVELOPMENT DEMO DATA — UNVERIFIED. Replace only after council content approval; see docs/content/DEVELOPMENT-CONTENT-POLICY.md.
export type NewsPreview = {
    title: string;
    date: string;
    summary: string;
    image?: string;
    imageAlt?: string;
    imageCaption?: string;
    href?: string;
};

export type EventPreview = {
    day: string;
    month: string;
    title: string;
    time: string;
    location: string;
    href?: string;
    hasAgenda?: boolean;
    hasMinutes?: boolean;
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
        id: 'education',
        title: 'Education',
        description: 'Supporting 84 primary and 44 secondary schools with classroom blocks, teacher housing and learning facilities.',
        image: '/images/home/service-roads.webp',
        icon: 'roads',
        href: '/coming-soon?topic=education',
    },
    {
        id: 'environment',
        title: 'Environment & Conservation',
        description: 'Protecting wetlands, woodlands and grazing lands through community-led conservation and clean-up programmes.',
        image: '/images/home/service-environment.webp',
        icon: 'environment',
        href: '/coming-soon?topic=environment',
    },
    {
        id: 'roads',
        title: 'Roads & Works',
        description: 'Grading, gravelling and maintaining the district road network plus bridges, drifts and public infrastructure.',
        image: '/images/home/service-roads.webp',
        icon: 'roads',
        href: '/coming-soon?topic=roads',
    },
    {
        id: 'health',
        title: 'Health & Sanitation',
        description: 'Supporting rural health centres, outreach services and improved sanitation across all 29 wards.',
        image: '/images/home/service-health.webp',
        icon: 'health',
        href: '/coming-soon?topic=health',
    },
    {
        id: 'water',
        title: 'Water Supply',
        description: 'Provision and maintenance of boreholes, piped schemes and clean, safe water points for households.',
        image: '/images/home/service-water.webp',
        icon: 'water',
        href: '/coming-soon?topic=water',
    },
    {
        id: 'business',
        title: 'Business Centres & Markets',
        description: 'Servicing stands, markets and growth points at Mutoko Centre and rural service centres for local traders.',
        image: '/images/home/service-roads.webp',
        icon: 'roads',
        href: '/coming-soon?topic=business',
    },
    {
        id: 'property',
        title: 'Property & Planning',
        description: 'Stand allocation, leases, development control and the Mutoko Master Plan public exhibition process.',
        image: '/images/home/service-water.webp',
        icon: 'water',
        href: '/coming-soon?topic=property',
    },
    {
        id: 'recreation',
        title: 'Recreation & Welfare',
        description: 'Managing Chikondoma Stadium, community halls, sports and social welfare programmes for vulnerable groups.',
        image: '/images/home/service-health.webp',
        icon: 'health',
        href: '/coming-soon?topic=recreation',
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
        title: 'Our Location',
        description: 'Located 143km north-east of Harare on the Harare–Nyamapanda highway, 90km from the Mozambique border.',
        icon: 'gear',
    },
    {
        title: 'Our Communities',
        description: 'Serving 29 wards plus a women\u2019s quota and the Mutoko Town Board across 428,916 hectares.',
        icon: 'users',
    },
    {
        title: 'Our Mission',
        description: 'To provide quality, sustainable services and promote inclusive development with our communities.',
        icon: 'shield',
    },
    {
        title: 'Vision 2030',
        description: 'A vibrant, prosperous district aligned with Zimbabwe\u2019s Vision 2030 of an upper-middle-income society.',
        icon: 'leaf',
    },
];
