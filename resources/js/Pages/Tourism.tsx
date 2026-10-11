import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type TourismBlock = { type: string; text: string; url?: string | null };
type TourismPage = { slug: string; title: string; summary: string | null; blocks: TourismBlock[] } | null;
type Opportunity = { slug: string; title: string; sector: string | null; summary: string | null };

const ATTRACTIONS = [
    {
        title: 'Mutoko Ruins (Tere RaMakate)',
        tag: 'National Monument · Historical Heritage',
        desc: 'Dry-stone walled ruins built among granite outcrops between roughly 1000 and 1700 AD, featuring three main platforms and chevron-patterned walling in the Great Zimbabwe–Khami tradition. Oral history links the site to Chief Makate of the Budya people, later succeeded by Chief Nehoreka. Excavations have recovered iron arrowheads, copper bracelets and imported Chinese celadon, evidence of long-distance trade, displayed at the on-site Interpretive Centre.',
        location: 'Mutoko Rural, near Mutoko Centre',
        image: '/images/tourism/mutoko-ruins.webp',
        imageAlt: 'Dry-stone walls of the Mutoko Ruins winding among granite boulders',
    },
    {
        title: 'Ruchera Cave Rock Paintings',
        tag: 'Archaeological San Art · Prehistoric',
        desc: 'Sensational San hunter-gatherer rock art with distinct ochre paintings depicting elephants, kudu, hunter ceremonies, and domestic scenes on sheltered granite overhangs.',
        location: 'Ruchera Escarpment, Mutoko',
        image: '/images/tourism/ruchera-cave-art.webp',
        imageAlt: 'Red ochre San rock paintings of kudu, elephants and hunters on a granite cave wall',
    },
    {
        title: 'Mutoko Granite Balancing Rocks & Outcrops',
        tag: 'Geological Scenic Landscape',
        desc: 'Dramatic black and white granite castle kopjes and precariously poised boulder formations sculpted over millions of years by natural weathering across the Mutoko batholith.',
        location: 'District-wide Scenic Corridors',
        image: '/images/tourism/balancing-rocks.webp',
        imageAlt: 'Balancing granite boulders on a kopje overlooking the Mutoko bushveld',
    },
    {
        title: 'Nyadire River Eco-Trails & Picnic Spots',
        tag: 'Eco-Tourism · Birdwatching & Nature',
        desc: 'Serene riverine ecosystem along the Nyadire river offering premier birdwatching, indigenous flora, peaceful nature walking trails, and community fishing grounds.',
        location: 'Nyadire Basin, Mutoko',
        image: '/images/about/landscape-hills.webp',
        imageAlt: 'Green hills and countryside of the Nyadire basin',
    },
];

export default function Tourism({ page, opportunities }: { page: TourismPage; opportunities: Opportunity[] }) {
    const locale = usePublicLocale();

    return (
        <PublicLayout>
            <Head title="Tourism, Heritage & Cultural Attractions | Mutoko RDC" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/investment`}>Opportunities</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Tourism &amp; Heritage</span>
                    </nav>
                    <span className="about-accent-eyebrow">DESTINATION MUTOKO · HERITAGE &amp; CULTURE</span>
                    <h1>Discover Mutoko's Natural Wonder &amp; Heritage</h1>
                    <p className="about-page-hero-subtitle">
                        From prehistoric San cave rock paintings and the medieval Mutoko Ruins to majestic black granite landscapes and the Nyadire river basin.
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Tourism overview statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Ruins</div>
                                <div className="about-stat-label">National Heritage Monument</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Ruchera</div>
                                <div className="about-stat-label">Ancient Cave Paintings</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">143 km</div>
                                <div className="about-stat-label">2-Hour Drive from Harare</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">Granite</div>
                                <div className="about-stat-label">Iconic Balancing Kopjes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN ATTRACTIONS SECTION */}
            <section className="about-page-section" aria-labelledby="heritage-attractions">
                <div className="container">
                    <span className="about-accent-eyebrow">PLACES OF INTEREST</span>
                    <h2 id="heritage-attractions">Top Tourism &amp; Cultural Attractions</h2>
                    <p className="about-page-section-intro">
                        Experience archaeological depth, indigenous arts, and tranquil countryside getaways along Zimbabwe's scenic northeastern corridor.
                    </p>

                    <div className="tourism-attractions-grid">
                        {ATTRACTIONS.map((attraction, index) => (
                            <article key={index} className="tourism-card">
                                <div className="tourism-attraction-media">
                                    <img src={attraction.image} alt={attraction.imageAlt} loading="lazy" decoding="async" />
                                </div>
                                <div className="tourism-card-body">
                                    <span className="tourism-card-tag">{attraction.tag}</span>
                                    <h3 className="tourism-card-title">{attraction.title}</h3>
                                    <div className="vacancy-meta-row" style={{ marginBottom: '10px' }}>
                                        <span className="vacancy-meta-item">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span>{attraction.location}</span>
                                        </span>
                                    </div>
                                    <p className="tourism-card-desc">{attraction.desc}</p>
                                </div>
                            </article>
                        ))}
                    </div>

                    {/* TRAVEL LOGISTICS GUIDE */}
                    <div className="rates-guidance-card">
                        <h3>Visitor Guide &amp; Travel Logistics</h3>
                        <p>
                            <strong>Getting Here:</strong> Mutoko is accessible via the well-maintained A2 Harare–Nyamapanda highway, approximately 143 km (about 2 hours driving time) northeast of Harare. The district serves as an ideal stopover for regional travelers en route to Mozambique.
                        </p>
                        <p>
                            <strong>Accommodation &amp; Dining:</strong> Mutoko Centre features comfortable motels, guesthouses, and restaurants serving traditional Zimbabwean dishes and fresh local horticulture.
                        </p>
                        <p style={{ margin: 0, fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                            For guided community tours or heritage permit questions, visit Council Reception at Stand 366 Mutoko Centre or contact our Community Services Section.
                        </p>
                    </div>

                    {/* CMS EDITORIAL OR INVESTMENT OPPORTUNITIES */}
                    {opportunities.length > 0 && (
                        <div style={{ marginTop: '36px' }}>
                            <span className="about-accent-eyebrow">INVEST IN HOSPITALITY</span>
                            <h3 style={{ fontSize: '1.35rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 16px' }}>
                                Tourism &amp; Hospitality Investment
                            </h3>
                            <p style={{ color: '#64748B', fontSize: '0.92rem', marginBottom: 20 }}>
                                Mutoko RDC welcomes private partners interested in establishing eco-lodges, cultural villages, and conference facilities.
                            </p>

                            <div className="vacancies-modern-grid">
                                {opportunities.map((item) => (
                                    <article key={item.slug} className="vacancy-card-modern">
                                        <div className="vacancy-top-pills">
                                            <span className="vacancy-pill-grade">
                                                {item.sector ?? 'Tourism & Hospitality'}
                                            </span>
                                        </div>
                                        <h4 className="vacancy-title-modern">
                                            <Link href={`/${locale}/investment/${item.slug}`}>{item.title}</Link>
                                        </h4>
                                        {item.summary && (
                                            <p className="vacancy-summary-modern">{item.summary}</p>
                                        )}
                                        <div className="vacancy-card-footer-modern">
                                            <Link href={`/${locale}/investment/${item.slug}`} className="vacancy-view-cta">
                                                <span>View Investment Scope</span>
                                                <span aria-hidden="true">→</span>
                                            </Link>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}

