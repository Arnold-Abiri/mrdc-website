import { Head } from '@inertiajs/react';
import {
    AboutSection,
    CtaBanner,
    DevelopmentSection,
    FeatureCallouts,
    Hero,
    KeyServicesSection,
    NewsAndEvents,
    QuickAccess,
    TourismSection,
    ValuePillars,
} from '../Components/public/HomeSections';
import { eventPreviews, newsPreviews } from '../fixtures/home';
import PublicLayout from '../Layouts/PublicLayout';

export default function Home() {
    return (
        <PublicLayout>
            <Head title="Mutoko Rural District Council">
                <meta name="description" content="Development preview of the Mutoko Rural District Council website." />
            </Head>
            <Hero />
            <QuickAccess />
            <ValuePillars />
            <AboutSection />
            <NewsAndEvents news={newsPreviews} events={eventPreviews} />
            <KeyServicesSection />
            <DevelopmentSection />
            <TourismSection />
            <FeatureCallouts />
            <CtaBanner />
        </PublicLayout>
    );
}


