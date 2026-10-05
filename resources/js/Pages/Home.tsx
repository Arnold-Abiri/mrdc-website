import { Head } from '@inertiajs/react';
import {
    AboutSection,
    CtaBanner,
    Hero,
    ManagedHomepageContent,
    NewsAndEvents,
    QuickAccess,
    ValuePillars,
} from '../Components/public/HomeSections';
import PublicLayout from '../Layouts/PublicLayout';

type HomeService = { slug: string; name: string; summary: string | null };
type HomeDocument = { slug: string; title: string; description: string | null };
type HomeDepartment = { id: number; public_name: string; public_summary: string | null };
type HomeEditorial = { slug: string; title: string; summary: string | null; published_at: string | null };

export default function Home({ services, documents, departments, news, notices }: { services: HomeService[]; documents: HomeDocument[]; departments: HomeDepartment[]; news: HomeEditorial[]; notices: HomeEditorial[] }) {
    return (
        <PublicLayout>
            <Head title="Mutoko Rural District Council">
                <meta name="description" content="Development preview of the Mutoko Rural District Council website." />
            </Head>
                <Hero />
                <QuickAccess />
            <ValuePillars />
            <AboutSection />
            <NewsAndEvents news={news.map(item => ({ title: item.title, summary: item.summary ?? '', date: item.published_at ?? '', href: `/news/${item.slug}` }))} events={[]} />
            <ManagedHomepageContent services={services} documents={documents} departments={departments} notices={notices} />



                <CtaBanner />
        </PublicLayout>
    );
}


