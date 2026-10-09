import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';

export default function ComingSoon({ topic }: { topic: string }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={topic + ' \u2014 coming soon'} />
            <PageHero eyebrow="DEVELOPMENT PREVIEW" title={`${topic} is coming soon`} subtitle="This part of the council website is being prepared. Approved information will be added before launch." crumb={[{ label: topic }]} />
            <section className="about-page-section" aria-labelledby="coming-soon-title">
                <div className="container">
                    <div className="about-vision-card">
                        <div>
                            <h3 id="coming-soon-title">{topic} is coming soon</h3>
                            <p>This part of the council website is being prepared. Approved information will be added before launch.</p>
                            <p><a href={`/${locale}`}>{t('returnHomepage')} \u2192</a></p>
                        </div>
                    </div>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
