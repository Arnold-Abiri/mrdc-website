import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type WardSummary = { slug: string; name: string; description: string | null };

export default function Wards({ wards }: { wards: WardSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('wards')} />
            <PageHero eyebrow="OUR COMMUNITIES" title={t('wards')} subtitle="29 electoral wards across Mutoko District — find your ward and its local priorities." crumb={[{ label: t('wards') }]} />
            <section className="about-page-section">
                <div className="container">
                    {wards.length ? (
                        <div className="about-focus-grid">
                            {wards.map((ward, i) => (
                                <Link key={ward.slug} href={`/${locale}/wards/${ward.slug}`} className="about-focus-card">
                                    <div className={`about-focus-icon p${(i % 4) + 1}`} aria-hidden="true" />
                                    <h3>{ward.name}</h3>
                                    {ward.description && <p>{ward.description}</p>}
                                    <span className="about-gov-arrow">View ward <span aria-hidden="true">→</span></span>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <EmptyState title="No wards published" text="No approved ward information is available yet." />
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
