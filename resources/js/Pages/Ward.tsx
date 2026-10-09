import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type WardContent = { name: string; description: string | null; boundaries_description: string | null };

export default function Ward({ ward }: { ward: WardContent }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={ward.name} />
            <PageHero eyebrow="ELECTORAL WARD" title={ward.name} subtitle={ward.description || `Explore information about ${ward.name} and its place in Mutoko District.`} crumb={[{ label: t('wards'), href: `/${locale}/wards` }, { label: ward.name }]} />
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
                        <Link className="about-page-text-link" href={`/${locale}/wards`}>← Back to wards</Link>
                    </p>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
