import type { EventPreview, NewsPreview } from '../../fixtures/home';

const services = [
    { icon: 'building', title: 'Our Council', description: 'Council information and governance.' },
    { icon: 'grid', title: 'Our Services', description: 'Find public service information.' },
    { icon: 'chart', title: 'Development', description: 'Follow council development work.' },
    { icon: 'sun', title: 'Tourism', description: 'Explore visitor information.' },
    { icon: 'file', title: 'Tenders', description: 'View procurement opportunities.' },
    { icon: 'briefcase', title: 'Vacancies', description: 'Find council career notices.' },
] as const;

function Icon({ name }: { name: typeof services[number]['icon'] }) {
    const paths = {
        building: <><path d="M4 20V7l8-4 8 4v13"/><path d="M8 11h2m4 0h2M8 15h2m4 0h2M3 20h18"/></>,
        grid: <><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></>,
        chart: <><path d="M3 20h18M6 17V9m6 8V4m6 13v-6"/></>,
        sun: <><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></>,
        file: <><path d="M6 2h9l4 4v16H6zM15 2v5h4M9 12h7M9 16h7"/></>,
        briefcase: <><rect x="3" y="7" width="18" height="14" rx="1"/><path d="M8 7V4h8v3M3 13h18m-11 0v2h4v-2"/></>,
    };
    return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>;
}

export function Hero() {
    return <section className="hero" aria-labelledby="hero-title"><img className="hero-image" src="/images/hero-development.webp" width="1672" height="941" fetchPriority="high" alt="" /><div className="hero-shade"/><div className="container hero-content"><p className="hero-kicker">Welcome to</p><h1 id="hero-title">Mutoko Rural<br/> District Council</h1><p className="hero-statement">People. Development. Sustainable Communities.</p><p className="hero-description">Information about council services, public notices and local development in one place.</p><div className="hero-actions"><a className="button button-primary" href="#quick-access">Our Services <span aria-hidden="true">→</span></a><a className="button button-outline" href="#council-intro">About Council <span aria-hidden="true">→</span></a></div><p className="hero-asset-note">Development image · replace with approved local photography</p></div></section>;
}

export function QuickAccess() {
    return <section className="quick-access container" id="quick-access" aria-labelledby="quick-heading"><div className="section-heading"><div><p className="eyebrow">Find your way</p><h2 id="quick-heading">Quick access</h2></div><p>Direct routes to the information people look for most.</p></div><div className="quick-grid">{services.map(service => <article className="quick-card" key={service.title}><span className="quick-icon"><Icon name={service.icon}/></span><h3>{service.title}</h3><p>{service.description}</p><span className="future-label">Content coming soon <span aria-hidden="true">→</span></span></article>)}</div></section>;
}

function EmptyPreview({ type, title, description }: { type: 'news' | 'events'; title: string; description: string }) {
    return <div className="empty-state"><span className="empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">{type === 'news' ? <><path d="M4 4h13v16H4zM17 8h3v12h-3M7 8h7M7 12h7M7 16h5"/></> : <><rect x="3" y="5" width="18" height="16" rx="1"/><path d="M7 3v4m10-4v4M3 10h18m-13 4h3m-3 3h3"/></>}</svg></span><div><strong>{title}</strong><p>{description}</p></div></div>;
}

function NewsCard({ item }: { item: NewsPreview }) {
    return <article className="news-card"><div className="news-image">{item.image ? <img src={item.image} alt="" loading="lazy" width="480" height="270" /> : <span>Image pending approval</span>}</div><div className="news-body"><time>{item.date}</time><h3>{item.title}</h3><p>{item.summary}</p>{item.href && <a href={item.href}>Read more <span aria-hidden="true">→</span></a>}</div></article>;
}

function EventItem({ item }: { item: EventPreview }) {
    return <article className="event-item"><div className="event-date"><strong>{item.day}</strong><span>{item.month}</span></div><div><h3>{item.title}</h3><p>{item.time} · {item.location}</p>{item.href && <a href={item.href}>Event details</a>}</div></article>;
}

export function NewsAndEvents({ news, events }: { news: NewsPreview[]; events: EventPreview[] }) {
    return <section className="updates-section" aria-label="News and events"><div className="container updates-grid"><div><div className="section-heading"><div><p className="eyebrow">From the council</p><h2>Latest News</h2></div><span className="future-label">View All News →</span></div>{news.length ? <div className="news-grid">{news.map(item => <NewsCard item={item} key={item.title}/>)}</div> : <EmptyPreview type="news" title="No news published yet" description="Approved council news will appear here when available." />}</div><div className="events-panel"><div className="section-heading"><div><p className="eyebrow">What’s ahead</p><h2>Upcoming Events</h2></div></div>{events.length ? events.map(item => <EventItem item={item} key={`${item.day}-${item.title}`}/>) : <EmptyPreview type="events" title="No upcoming events published" description="Confirmed council events will appear here." />}</div></div></section>;
}

export function FeatureCallouts() {
    const features = [
        { number: '01', title: 'Invest in Mutoko', description: 'Investment information will be available after council review.', image: '/images/feature-invest-development.webp', position: 'center' },
        { number: '02', title: 'Explore Our Tourism', description: 'Approved visitor information and local imagery will be added here.', image: '/images/feature-tourism-development.webp', position: '65% center' },
        { number: '03', title: 'Engage With Us', description: 'Find ways to stay informed and participate in council matters.', image: '/images/feature-engage-development.webp', position: '62% center' },
    ];
    return <section className="features-section" aria-labelledby="features-heading"><div className="container"><div className="section-heading"><div><p className="eyebrow">Discover more</p><h2 id="features-heading">Mutoko at a glance</h2></div><p>Information and opportunities will be added as they are approved.</p></div><div className="feature-grid">{features.map(feature => <article className="feature-card" key={feature.title}><img src={feature.image} alt="" width="1100" height="619" loading="lazy" style={{ objectPosition: feature.position }} /><div className="feature-overlay"/><div className="feature-content"><span className="feature-number">{feature.number} / Explore</span><div><h3>{feature.title}</h3><p>{feature.description}</p><span className="feature-cta">Details coming soon <span aria-hidden="true">→</span></span></div></div></article>)}</div><p className="feature-image-note">Development images · replace with approved local photography before production</p></div></section>;
}
