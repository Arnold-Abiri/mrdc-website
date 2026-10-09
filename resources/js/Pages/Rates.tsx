import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type RatesBlock = { type: string; text: string; url?: string | null };
type RatesPage = { slug: string; title: string; summary: string | null; blocks: RatesBlock[] } | null;
type RateSchedule = { slug: string; title: string; category: string; reference_date: string | null };

export default function Rates({ page, schedules }: { page: RatesPage; schedules: RateSchedule[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={t('rates')} />
            <PageHero eyebrow="RATES & CHARGES" title={t('rates')} subtitle={t('ratesDesc')} crumb={[{ label: 'Rates' }]} />
            <section className="about-page-section" aria-labelledby="rates-info">
                <div className="container">
                    <span className="about-accent-eyebrow">RATES INFORMATION</span>
                    <h2 id="rates-info">{page?.title ?? t('rates')}</h2>
                    {page === null ? (
                        <EmptyState title="Information pending" text={t('ratesPagePending')} />
                    ) : (
                        <div className="about-vision-card">
                            <div>
                                {page.summary && <p>{page.summary}</p>}
                                {page.blocks.map((block, index) => block.type === 'heading' ? <h3 key={index}>{block.text}</h3> : block.type === 'cta' && block.url ? <p key={index}><Link href={block.url}>{block.text}</Link></p> : <p key={index}>{block.text}</p>)}
                            </div>
                        </div>
                    )}
                </div>
            </section>
            <section className="about-page-section about-page-section-muted" aria-labelledby="rate-schedules">
                <div className="container">
                    <span className="about-accent-eyebrow">SCHEDULES</span>
                    <h2 id="rate-schedules">{t('rateSchedules')}</h2>
                    {schedules.length === 0 ? (
                        <EmptyState title="No schedules" text={t('ratesPagePending')} />
                    ) : (
                        <div className="about-focus-grid">
                            {schedules.map((schedule) => (
                                <Link key={schedule.slug} href={`/${locale}/documents/${schedule.slug}`} className="about-focus-card">
                                    <h3>{schedule.title}</h3>
                                    <p>{schedule.category}{schedule.reference_date ? ` \u2014 ${schedule.reference_date}` : ''}</p>
                                </Link>
                            ))}
                        </div>
                    )}
                    <p style={{ marginTop: '16px' }}><Link href={`/${locale}/contact`}>{t('contactCouncil')} \u2192</Link></p>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
