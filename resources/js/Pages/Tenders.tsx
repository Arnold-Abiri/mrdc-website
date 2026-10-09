import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type Tender = { slug: string; reference: string; title: string; category: string | null; closes_at: string | null; display_status: string };

export default function Tenders({ tenders }: { tenders: Tender[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('tendersProcurement')} />
            <PageHero eyebrow="PROCUREMENT" title={t('tendersProcurement')} subtitle="Open council procurement opportunities. Closed tenders remain listed for transparency but are clearly marked." crumb={[{ label: 'Tenders' }]} />
            <section className="about-page-section" aria-labelledby="tenders-list">
                <div className="container">
                    <span className="about-accent-eyebrow">OPEN OPPORTUNITIES</span>
                    <h2 id="tenders-list">Current Tenders</h2>
                    {tenders.length === 0 ? (
                        <EmptyState title="No tenders published" text="No tenders are published at this time. Please check back later." />
                    ) : (
                        <div className="about-focus-grid">
                            {tenders.map((tender) => (
                                <Link key={tender.slug} href={`/${locale}/tenders/${tender.slug}`} className="about-focus-card">
                                    <h3>{tender.title}</h3>
                                    <p>{tender.reference}{tender.category ? ` \u2014 ${tender.category}` : ''} \u2014 {tender.display_status}{tender.closes_at ? `, closes ${tender.closes_at}` : ''}</p>
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
