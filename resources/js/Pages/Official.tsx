import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
import { usePublicLocale } from '../usePublicTranslation';

type OfficialProfile = { name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };

export default function Official({ official }: { official: OfficialProfile }) {
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={official.name} />
            <PageHero eyebrow="COUNCIL OFFICIAL" title={official.name} subtitle={official.title} crumb={[{ label: 'Officials', href: `/${locale}/officials` }, { label: official.name }]} />
            <section className="about-page-section">
                <div className="container">
                    <div className="about-vision-card">
                        {official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} />}
                        <div>
                            <h3>{official.title}</h3>
                            {official.department && <p><strong>Department:</strong> {official.department}</p>}
                            {official.biography && <p>{official.biography}</p>}
                        </div>
                    </div>
                    <p style={{ marginTop: '20px' }}>
                        <Link className="about-page-text-link" href={`/${locale}/officials`}>← Back to officials</Link>
                    </p>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
