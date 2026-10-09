import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type OfficialSummary = { slug: string; name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
const TEAM = [
    { name: 'B. Tasarira', role: 'Chief Executive Officer' },
    { name: 'K.K. Chamisa', role: 'Town Board Administrator' },
    { name: 'R. Makore', role: 'Engineer' },
    { name: 'Z. Nhidza', role: 'Executive Officer — Social Services' },
    { name: 'T. Nyabonde', role: 'Executive Officer — Finance' },
    { name: 'D. Tshuma', role: 'Planner' },
    { name: 'O. Katuka', role: 'Executive Officer — Human Resources' },
    { name: 'T.K. Hambaguzha', role: 'Internal Audit' },
    { name: 'D.T. Mutangadura', role: 'Procurement' },
];

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
                    <div className="about-gov-grid">
                        {TEAM.map((m) => (
                            <div key={m.name} className="about-gov-card">
                                <h3>{m.name}</h3>
                                <p>{m.role}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            <section className="about-page-section" aria-label="Published officials">
                <div className="container">
                    <span className="about-accent-eyebrow">PUBLISHED PROFILES</span>
                    <h2>Official Profiles</h2>
                    {officials.length ? (
                        <div className="about-gov-grid">
                            {officials.map((official) => (
                                <Link key={official.slug} href={`/${locale}/officials/${official.slug}`} className="about-gov-card">
                                    {official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} loading="lazy" />}
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
