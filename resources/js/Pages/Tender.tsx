import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';

type TenderDetail = { slug: string; reference: string; title: string; category: string | null; description: string; opens_at: string | null; closes_at: string | null; display_status: string; contact_instructions: string | null; award_status: string; awarded_to: string | null; awarded_at: string | null; award_amount: string | null; award_reference: string | null; award_remarks: string | null; document: { slug: string; title: string } | null };

export default function Tender({ tender, department }: { tender: TenderDetail; department: string | null }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={tender.title} />
            <PageHero eyebrow="PROCUREMENT" title={tender.title} subtitle={`${tender.reference} \u2014 Status: ${tender.display_status}`} crumb={[{ label: t('tenders'), href: `/${locale}/tenders` }, { label: tender.title }]} />
            <section className="about-page-section" aria-labelledby="tender-detail">
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Back"><Link href={`/${locale}/tenders`}>\u2190 {t('tenders')}</Link></nav>
                    <div className="about-mandate-grid">
                        <div className="about-mandate-card">
                            <h3 id="tender-detail">Details</h3>
                            {tender.category && <p>Category: {tender.category}</p>}
                            {tender.opens_at && <p>Opens: {tender.opens_at}</p>}
                            {tender.closes_at && <p>Closes: {tender.closes_at}</p>}
                            <p>{tender.description}</p>
                            {department && <p>Responsible department: {department}</p>}
                        </div>
                        {tender.contact_instructions && (
                            <div className="about-mandate-card">
                                <h3>{t('howToApply')}</h3>
                                <p>{tender.contact_instructions}</p>
                            </div>
                        )}
                        {tender.document && (
                            <div className="about-mandate-card">
                                <h3>{t('tenderDocument')}</h3>
                                <p><Link href={`/${locale}/documents/${tender.document.slug}`}>{tender.document.title}</Link></p>
                            </div>
                        )}
                        {tender.award_status === 'awarded' && (
                            <div className="about-mandate-card" aria-label={t('award')}>
                                <h3>{t('award')}</h3>
                                {tender.awarded_to && <p>{t('awardedTo')}: {tender.awarded_to}</p>}
                                {tender.awarded_at && <p>{t('awardedAt')}: {tender.awarded_at}</p>}
                                {tender.award_amount && <p>{t('awardAmount')}: {tender.award_amount}</p>}
                                {tender.award_reference && <p>{t('reference')}: {tender.award_reference}</p>}
                                {tender.award_remarks && <p>{tender.award_remarks}</p>}
                            </div>
                        )}
                    </div>
                    {tender.display_status !== 'open' && <p style={{ marginTop: '16px' }}>This tender is not open for submissions.</p>}
                    <p style={{ marginTop: '16px' }}><Link href={`/${locale}/contact`}>{t('askAboutTender')} \u2192</Link></p>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
