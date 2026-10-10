import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type OfficialSummary = { slug: string; name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('councilOfficials')} />
            <PageHero eyebrow="LEADERSHIP" title={t('councilOfficials')} subtitle="Elected councillors for 29 wards and a professional management team headed by the Chief Executive Officer, based at Stand 366 Mutoko Centre." crumb={[{ label: t('councilOfficials') }]} />
            <section className="about-page-section about-page-section-muted" aria-labelledby="team-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">MANAGEMENT TEAM</span>
                    <h2 id="team-heading">Management team</h2>
                    {officials.length ? (
                        <div className="about-gov-grid">
                            {officials.map((official) => (
                                <Link key={official.slug} href={`/${locale}/officials/${official.slug}`} className="about-gov-card">
                                    {official.photo_url && <img className="official-portrait" src={official.photo_url} alt={official.name} width={240} height={240} loading="lazy" />}
                                    <h3>{official.name}</h3>
                                    <p>{official.title}</p>
                                    {official.department && <p>{official.department}</p>}
                                    {official.biography && <p>{official.biography}</p>}
                                    <span className="about-gov-arrow">View profile <span aria-hidden="true">→</span></span>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <EmptyState title="No profiles yet" text="No approved official information is available yet." />
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
