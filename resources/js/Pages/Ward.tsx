import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type WardContent = { name: string; description: string | null; boundaries_description: string | null };

export default function Ward({ ward }: { ward: WardContent }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={ward.name} />
            <PageHero eyebrow={t('electoralWard')} title={ward.name} subtitle={ward.description || t('exploreWard')} crumb={[{ label: t('wards'), href: `/${locale}/wards` }, { label: ward.name }]} />
            <section className="about-page-section">
                <div className="container">
                    {ward.boundaries_description && (
                        <div className="about-vision-card">
                            <div>
                                <h3>{t('areaDescription')}</h3>
                                <p>{ward.boundaries_description}</p>
                            </div>
                        </div>
                    )}
                    <p style={{ marginTop: '20px' }}>
                        <Link className="about-page-text-link" href={`/${locale}/wards`}>← {t('backWards')}</Link>
                    </p>
                </div>
            </section>
        </PublicLayout>
    );
}
