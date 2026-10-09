import { Head } from '@inertiajs/react';
import { organizationSchema, SeoHead } from '../Seo';
import {
    AboutSection,
    CitizenFeedbackBanner,
    CtaBanner,
    ExploreDirectory,
    Hero,
    ManagedHomepageContent,
    NewsAndEvents,
    QuickAccess,
    ValuePillars,
    type HeroSlide,
} from '../Components/public/HomeSections';
import { usePublicLocale } from '../usePublicTranslation';
import PublicLayout from '../Layouts/PublicLayout';

type HomeService = { slug: string; name: string; summary: string | null };
type HomeDocument = { slug: string; title: string; description: string | null };
type HomeDepartment = { id: number; public_name: string; public_summary: string | null };
type HomeContact = { office: string; type: 'phone' | 'email' | 'physical_address' | 'postal_address'; value: string };
type HomeOfficial = { slug: string; name: string; title: string };
type HomeEditorial = { slug: string; title: string; summary: string | null; published_at: string | null; image_url?: string | null; image_alt?: string | null; image_caption?: string | null };
type HomeStatistic = { label: string; value: string; unit: string | null; icon: string | null };
type HomeTender = { slug: string; reference: string; title: string; display_status: string };
type HomeInvestment = { slug: string; title: string; sector: string | null; summary: string | null };
type HomeProject = { slug: string; title: string; project_status: string; summary: string | null };

type HomeMeeting = { id: number; title: string; meeting_type: string; scheduled_date: string | null; scheduled_time: string | null; venue: string | null; has_agenda?: boolean; has_minutes?: boolean };

function formatEventParts(scheduledDate: string | null): { day: string; month: string } {
    if (!scheduledDate) {
        return { day: '—', month: '' };
    }
    const parsed = new Date(scheduledDate);
    if (Number.isNaN(parsed.getTime())) {
        return { day: '—', month: '' };
    }
    return {
        day: String(parsed.getDate()).padStart(2, '0'),
        month: parsed.toLocaleDateString('en-GB', { month: 'short' }).toUpperCase(),
    };
}

export default function Home({ services, documents, departments, news, notices, contacts, officials, ward_count, statistics, tenders, investment, slides, projects, meetings = [], preview = false }: { services: HomeService[]; documents: HomeDocument[]; departments: HomeDepartment[]; news: HomeEditorial[]; notices: HomeEditorial[]; contacts: HomeContact[]; officials: HomeOfficial[]; ward_count: number; statistics: HomeStatistic[]; tenders: HomeTender[]; investment: HomeInvestment[]; slides: HeroSlide[]; projects: HomeProject[]; meetings?: HomeMeeting[]; preview?: boolean }) {
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title="Mutoko Rural District Council">
                <meta name="description" content="Mutoko Rural District Council — local government services, public notices, news, documents, wards and development across Mutoko District, Mashonaland East, Zimbabwe." />
                {preview && <meta name="robots" content="noindex, nofollow" />}
            </Head>
            <SeoHead title="Mutoko Rural District Council" description="Mutoko Rural District Council — local government services, public notices, news, documents, wards and development across Mutoko District, Mashonaland East, Zimbabwe." type="website" schema={organizationSchema()} />
                {preview && <div className="preview-banner" role="note"><p><strong>Stakeholder review preview.</strong> Content on this preview is pending council approval. This is not the public website.</p></div>}
                <Hero slides={slides} />
                <QuickAccess />
            <ValuePillars />
            <AboutSection aboutHref={preview ? `/preview/${locale}/pages/about-mutoko` : undefined} />
            <NewsAndEvents news={news.map(item => ({ title: item.title, summary: item.summary ?? '', image: item.image_url ?? undefined, imageAlt: item.image_alt ?? undefined, imageCaption: item.image_caption ?? undefined, date: item.published_at ? new Date(item.published_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '', href: preview ? `/preview/${locale}/news/${item.slug}` : `/${locale}/news/${item.slug}` }))} events={meetings.map(item => {
                const parts = formatEventParts(item.scheduled_date);
                const prettyDate = item.scheduled_date && !Number.isNaN(new Date(item.scheduled_date).getTime())
                    ? new Date(item.scheduled_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
                    : '';
                return {
                    day: parts.day,
                    month: parts.month,
                    title: item.title,
                    location: item.venue ?? '',
                    time: [prettyDate, item.scheduled_time].filter(Boolean).join(' · '),
                    href: preview ? `/preview/${locale}/meetings/${item.id}` : `/meetings/${item.id}`,
                    hasAgenda: item.has_agenda,
                    hasMinutes: item.has_minutes,
                };
            })} />
            <ManagedHomepageContent preview={preview} services={services} documents={documents} departments={departments} notices={notices} contacts={contacts} officials={officials} wardCount={ward_count} statistics={statistics} tenders={tenders} investment={investment} projects={projects} />

            <CitizenFeedbackBanner />

            <ExploreDirectory />

            <CtaBanner />
        </PublicLayout>
    );
}


