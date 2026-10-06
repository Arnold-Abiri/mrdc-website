import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

export default function ComingSoon({ topic }: { topic: string }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={topic + ' — coming soon'} />
            <section className="container coming-soon" aria-labelledby="coming-soon-title">
                <p className="eyebrow">Development preview</p>
                <h1 id="coming-soon-title">{topic} is coming soon</h1>
                <p>This part of the council website is being prepared. Approved information will be added before launch.</p>
                <a href={`/${locale}`}>{t('returnHomepage')}</a>
            </section>
        </PublicLayout>
    );
}
