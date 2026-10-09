import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type Vacancy = { slug: string; title: string; grade: string | null; closes_at: string | null; is_open: boolean };

export default function Vacancies({ vacancies }: { vacancies: Vacancy[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('vacancies')} />
            <PageHero eyebrow="CAREERS" title={t('vacancies')} subtitle="Current employment opportunities with Mutoko Rural District Council. Expired vacancies are shown as closed." crumb={[{ label: 'Vacancies' }]} />
            <section className="about-page-section" aria-labelledby="vacancies-list">
                <div className="container">
                    <span className="about-accent-eyebrow">WORK WITH US</span>
                    <h2 id="vacancies-list">Open Positions</h2>
                    {vacancies.length === 0 ? (
                        <EmptyState title="No vacancies published" text="No vacancies are published at this time." />
                    ) : (
                        <div className="about-focus-grid">
                            {vacancies.map((vacancy) => (
                                <Link key={vacancy.slug} href={`/${locale}/vacancies/${vacancy.slug}`} className="about-focus-card">
                                    <h3>{vacancy.title}</h3>
                                    <p>{vacancy.grade ? `${vacancy.grade} \u2014 ` : ''}{vacancy.is_open ? 'Open' : 'Closed'}{vacancy.closes_at ? `, closes ${vacancy.closes_at}` : ''}</p>
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
