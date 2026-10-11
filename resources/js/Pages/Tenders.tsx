import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, EmptyState } from '../Components/public/PageHeader';

type Tender = { slug: string; reference: string; title: string; category: string | null; closes_at: string | null; display_status: string };
const copy = {
    en: { intro: 'Open council procurement opportunities. Closed tenders remain listed for transparency but are clearly marked.', heading: 'Open opportunities', none: 'No tenders published', noneDetail: 'No tenders are published at this time. Please check back later.', closes: 'closes' },
    sn: { intro: 'Mikana yekutenga kwezvinhu nemabasa yeKanzuru iripo. Matenda akavharwa anoramba achionekwa kuitira kujeka, asi anoratidzwa kuti akavharwa.', heading: 'Mikana iripo', none: 'Hapana matenda akaburitswa', noneDetail: 'Hapana matenda akaburitswa pari zvino. Tapota dzokai zvakare.', closes: 'rinovhara' },
    nd: { intro: 'Amathuba okuthengwa kwempahla lezinkonzo zoMkhandlu. Amathenda avaliwe ayaqhubeka ekhanya ukuze kube lokucaca, kodwa aboniswa ukuthi avaliwe.', heading: 'Amathuba akhona', none: 'Awekho amathenda akhishiwe', noneDetail: 'Awekho amathenda akhishiwe okwamanje. Sicela uphinde ukhangele.', closes: 'livalwa' },
};
const statusLabels = {
    en: { open: 'Open', closed: 'Closed', awarded: 'Awarded' },
    sn: { open: 'Rakavhurwa', closed: 'Rakavharwa', awarded: 'Rapihwa' },
    nd: { open: 'Livuliwe', closed: 'Livaliwe', awarded: 'Linikiwe' },
};

export default function Tenders({ tenders }: { tenders: Tender[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <PublicLayout>
            <Head title={t('tendersProcurement')} />
            <PageHero eyebrow={t('tendersProcurement')} title={t('tendersProcurement')} subtitle={copy[locale].intro} crumb={[{ label: t('tenders') }]} />
            <section className="about-page-section" aria-labelledby="tenders-list">
                <div className="container">
                    <span className="about-accent-eyebrow">{copy[locale].heading}</span>
                    <h2 id="tenders-list">{t('currentTenders')}</h2>
                    {tenders.length === 0 ? (
                        <EmptyState title={copy[locale].none} text={copy[locale].noneDetail} />
                    ) : (
                        <div className="about-focus-grid">
                            {tenders.map((tender) => (
                                <Link key={tender.slug} href={`/${locale}/tenders/${tender.slug}`} className="about-focus-card">
                                    <h3>{tender.title}</h3>
                                    <p>{tender.reference}{tender.category ? ` \u2014 ${tender.category}` : ''} \u2014 {statusLabels[locale][tender.display_status as keyof typeof statusLabels.en] ?? tender.display_status}{tender.closes_at ? `, ${copy[locale].closes} ${tender.closes_at}` : ''}</p>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}
