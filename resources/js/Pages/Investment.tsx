import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, EmptyState } from '../Components/public/PageHeader';
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null; location: string | null; opportunity_status: string };
const copy = {
    en: { intro: 'Mutoko sits 143km north-east of Harare on the Harare–Nyamapanda highway, 90km from Mozambique — strong in horticulture, solar energy, mining and agro-processing.', detail: 'Serviced commercial and industrial stands are available at Mutoko Centre and growth points. Call +263 771 592 888 or visit Stand 366 Mutoko Centre to discuss proposals. Figures and availability are confirmed with council before any commitment.' },
    sn: { intro: 'Mutoko iri makiromita 143 kuchamhembe kwakadziva kumabvazuva kweHarare mumugwagwa weHarare–Nyamapanda, uye makiromita 90 kubva kuMozambique. Ine mikana mukurima muriwo nemichero, simba rezuva, migodhi uye kugadzira zvigadzirwa zvekurima.', detail: 'Nzvimbo dzebhizimisi nemaindasitiri dzine zvivakwa zvinodiwa dziripo paMutoko Centre nedzimwe nzvimbo dziri kukura. Fonera +263 771 592 888 kana shanyira Stand 366 Mutoko Centre kuti mutaure nezvezvirongwa. Simbisai nhamba nekuwanikwa kwenzvimbo neKanzuru musati mazvipira.' },
    nd: { intro: 'IMutoko ikumakhilomitha ayi-143 enyakatho-mpumalanga yeHarare emgwaqweni weHarare–Nyamapanda, lakumakhilomitha ayi-90 ukusuka eMozambique. Kulamathuba ekulimeni, emandleni elanga, ezimayini lasekulungiseni imikhiqizo yezilimo.', detail: 'Izindawo zokuthengisa lezezimboni ezilezinsiza ziyatholakala eMutoko Centre lasezindaweni ezikhulayo. Fonela +263 771 592 888 kumbe uvakatshele eStand 366 Mutoko Centre ukuze lixoxe ngezinhlelo. Qinisekisani amanani lokutholakala lezisebenzi zoMkhandlu lingakazibophezeli.' },
};
export default function Investment({ opportunities }: { opportunities: Opportunity[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout>
        <Head title={t('investInMutoko')} />
        <PageHero eyebrow={t('investment')} title={t('investInMutoko')} subtitle={copy[locale].intro} crumb={[{ label: t('investment') }]} />
        <section className="about-page-section"><div className="container">
            <span className="about-accent-eyebrow">{t('whyMutoko')}</span>
            <h2>{t('investDistrict')}</h2>
            <p className="about-page-section-intro">{copy[locale].detail}</p>
            {opportunities.length === 0 ? <EmptyState title={t('noOpportunities')} text={t('noInvestmentDesc')} /> : <div className="about-focus-grid">{opportunities.map(item => <Link key={item.slug} href={`/${locale}/investment/${item.slug}`} className="about-focus-card"><h3>{item.title}</h3><p>{item.sector ? `${item.sector} — ` : ''}{item.opportunity_status}{item.location ? ` — ${item.location}` : ''}</p>{item.summary && <p>{item.summary}</p>}<span className="about-economic-link">{t('viewOpportunity')} <span aria-hidden="true">→</span></span></Link>)}</div>}
            <p style={{ marginTop: '20px' }}><Link className="about-page-text-link" href={`/${locale}/contact`}>{t('makeInvestmentEnquiry')} <span aria-hidden="true">→</span></Link></p>
        </div></section>
    </PublicLayout>;
}
