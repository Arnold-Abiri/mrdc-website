import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type Service = { slug: string; name: string; summary: string | null };
const STATIC_SERVICES = [
    { title: 'Education', text: 'Support for 84 primary and 44 secondary schools: classrooms, teacher housing and facilities.' },
    { title: 'Environment & Conservation', text: 'Wetland and woodland protection, anti-litter and community clean-up campaigns.' },
    { title: 'Roads & Works', text: 'Grading and maintenance of the district road network, bridges, drifts and public buildings.' },
    { title: 'Health', text: 'Support for rural health centres, outreach and sanitation programmes in every ward.' },
    { title: 'Business Centres & Property', text: 'Serviced stands, markets, leases and development control at Mutoko Centre and growth points.' },
    { title: 'Recreation & Welfare', text: 'Chikondoma Stadium, halls, sport and social welfare support for vulnerable households.' },
];

export default function Services({ services }: { services: Service[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('services')} />
            <PageHero eyebrow="OUR SERVICES" title={t('services')} subtitle="Everyday services across 29 wards — from classrooms and clinics to roads, water points, markets and Chikondoma Stadium." crumb={[{ label: t('services') }]} />
            <section className="about-page-section about-page-section-muted" aria-label="Service areas">
                <div className="container">
                    <span className="about-accent-eyebrow">SERVICE AREAS</span>
                    <h2>What We Deliver</h2>
                    <div className="about-focus-grid">
                        {STATIC_SERVICES.map((s, i) => (
                            <div key={s.title} className="about-focus-card">
                                <div className={`about-focus-icon p${(i % 4) + 1}`} aria-hidden="true" />
                                <h3>{s.title}</h3>
                                <p>{s.text}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            <section className="about-page-section" aria-label="Published services">
                <div className="container">
                    <span className="about-accent-eyebrow">PUBLISHED SERVICES</span>
                    <h2>Approved Service Guides</h2>
                    {services.length === 0 ? (
                        <EmptyState title="No approved services yet" text="No approved services are available yet. Contact the council on Stand 366 Mutoko Centre, P Box 130 Mutoko, Zimbabwe, or +263 771 592 888." />
                    ) : (
                        <div className="about-mandate-grid">
                            {services.map((service) => (
                                <Link key={service.slug} href={`/${locale}/services/${service.slug}`} className="about-mandate-card">
                                    <h3>{service.name}</h3>
                                    {service.summary && <p>{service.summary}</p>}
                                    <span className="about-gov-arrow">View guide <span aria-hidden="true">→</span></span>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
