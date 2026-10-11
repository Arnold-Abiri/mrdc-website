import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero } from '../Components/public/PageHeader';

export default function ComingSoon({ topic }: { topic: string }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={`${topic} — ${t('comingSoon')}`} />
            <PageHero eyebrow={t('development')} title={`${topic} — ${t('comingSoon')}`} subtitle={t('comingSoonDesc')} crumb={[{ label: topic }]} />
            <section className="about-page-section" aria-labelledby="coming-soon-title">
                <div className="container">
                    <div className="about-vision-card">
                        <div>
                            <h3 id="coming-soon-title">{topic} — {t('comingSoon')}</h3>
                            <p>{t('comingSoonDesc')}</p>
                            <p><a href={`/${locale}`}>{t('returnHomepage')} \u2192</a></p>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
