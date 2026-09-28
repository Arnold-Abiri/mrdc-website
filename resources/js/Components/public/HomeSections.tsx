import type { EventPreview, NewsPreview } from '../../fixtures/home';

const services = [
    {
        icon: 'council',
        title: 'Our Council',
        description: 'Leadership & governance structures',
        theme: 'blue',
        href: '#about',
    },
    {
        icon: 'services',
        title: 'Our Services',
        description: 'Water, roads, health, sanitation & more',
        theme: 'emerald',
        href: '#services',
    },
    {
        icon: 'development',
        title: 'Development',
        description: 'Projects & investment opportunities',
        theme: 'cyan',
        href: '#development',
    },
    {
        icon: 'tourism',
        title: 'Tourism',
        description: "Explore Mutoko's natural beauty",
        theme: 'green',
        href: '#tourism',
    },
    {
        icon: 'tenders',
        title: 'Tenders',
        description: 'Business opportunities',
        theme: 'amber',
        href: '#tenders',
    },
    {
        icon: 'vacancies',
        title: 'Vacancies',
        description: 'Join our team',
        theme: 'red',
        href: '#vacancies',
    },
] as const;

function QuickIcon({ name }: { name: typeof services[number]['icon'] }) {
    switch (name) {
        case 'council':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
            );
        case 'services':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M19 12h-2V8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2v4H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z" />
                </svg>
            );
        case 'development':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M4 19h16v2H4zM6 10h3v7H6zm5-5h3v12h-3zm5 8h3v4h-3z" />
                </svg>
            );
        case 'tourism':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <circle cx="12" cy="12" r="3.2" />
                    <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z" />
                </svg>
            );
        case 'tenders':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                </svg>
            );
        case 'vacancies':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" />
                </svg>
            );
    }
}

export function Hero() {
    return (
        <section className="hero" aria-labelledby="hero-title">
            <img
                className="hero-image"
                src="/images/hero-clean.webp"
                width="1983"
                height="793"
                fetchPriority="high"
                alt="Scenic Mutoko landscape showing rocky kopje mountains, green valley and Mutoko town center"
            />
            <div className="hero-shade" />

            <div className="container hero-container">
                <div className="hero-content">
                    <div className="hero-eyebrow-pill">
                        <span className="eyebrow-bar" aria-hidden="true" />
                        <span className="eyebrow-text">WELCOME TO</span>
                    </div>

                    <h1 id="hero-title" className="hero-heading">
                        <span className="hero-heading-white">Mutoko Rural</span>{' '}
                        <span className="hero-heading-green">District Council</span>
                    </h1>

                    <p className="hero-statement">People. Development. Sustainable Communities.</p>

                    <p className="hero-description">
                        Working with our communities to deliver quality services, promote local development and build a better Mutoko.
                    </p>

                    <div className="hero-actions">
                        <a className="btn-hero-primary" href="#services">
                            <span>Our Services</span>
                            <span aria-hidden="true">→</span>
                        </a>
                        <a className="btn-hero-outline" href="#about">
                            <span>About Council</span>
                        </a>
                    </div>
                </div>

                {/* Floating Glassmorphism Quick Stats Card */}
                <div className="hero-stats-card" aria-label="Mutoko District key figures">
                    <div className="stats-row">
                        <div className="stats-icon-badge badge-gold" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                        </div>
                        <div className="stats-text">
                            <strong className="stats-number">12</strong>
                            <span className="stats-label">Wards</span>
                        </div>
                    </div>

                    <div className="stats-row">
                        <div className="stats-icon-badge badge-green" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 19h16v2H4zM6 10h3v7H6zm5-5h3v12h-3zm5 8h3v4h-3z" />
                            </svg>
                        </div>
                        <div className="stats-text">
                            <strong className="stats-number">50+</strong>
                            <span className="stats-label">Community Projects</span>
                        </div>
                    </div>

                    <div className="stats-row">
                        <div className="stats-icon-badge badge-leaf" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s2-2 3-3c-4 0-7 2-8 4-1 2-1 4-1 4s2-2 5-2c0 0-2 2-3 4 3 0 6-2 7-4z" />
                            </svg>
                        </div>
                        <div className="stats-text">
                            <strong className="stats-number">3</strong>
                            <span className="stats-label">Growth Points</span>
                        </div>
                    </div>

                    <div className="stats-row">
                        <div className="stats-icon-badge badge-pin" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" />
                            </svg>
                        </div>
                        <div className="stats-text">
                            <strong className="stats-number">1</strong>
                            <span className="stats-label">Shared Vision</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export function QuickAccess() {
    return (
        <section className="quick-access container" id="quick-access" aria-label="Quick Access Services">
            <div className="quick-grid">
                {services.map(service => (
                    <a className="quick-card" key={service.title} href={service.href}>
                        <div className="quick-card-top">
                            <span className={`quick-icon-badge badge-${service.theme}`}>
                                <QuickIcon name={service.icon} />
                            </span>
                            <span className="quick-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg>
                            </span>
                        </div>
                        <h3 className="quick-card-title">{service.title}</h3>
                        <p className="quick-card-desc">{service.description}</p>
                    </a>
                ))}
            </div>
        </section>
    );
}

export function ValuePillars() {
    return (
        <section className="value-pillars-section" aria-label="Council Values and Strategic Pillars">
            <div className="container">
                <div className="value-pillars-grid">
                    <div className="value-pillar-item">
                        <span className="pillar-icon-badge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s2-2 3-3c-4 0-7 2-8 4-1 2-1 4-1 4s2-2 5-2c0 0-2 2-3 4 3 0 6-2 7-4z" />
                            </svg>
                        </span>
                        <div className="pillar-text">
                            <strong>Sustainable Development</strong>
                            <p>A cleaner, greener and more resilient Mutoko</p>
                        </div>
                    </div>

                    <div className="value-pillar-item">
                        <span className="pillar-icon-badge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                        </span>
                        <div className="pillar-text">
                            <strong>Community Empowerment</strong>
                            <p>People at the heart of progress</p>
                        </div>
                    </div>

                    <div className="value-pillar-item">
                        <span className="pillar-icon-badge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 19h16v2H4zM6 10h3v7H6zm5-5h3v12h-3zm5 8h3v4h-3z" />
                            </svg>
                        </span>
                        <div className="pillar-text">
                            <strong>Shared Prosperity</strong>
                            <p>Opportunities for a better tomorrow</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Decorative bottom pattern banner */}
            <div className="bottom-pattern-banner" aria-hidden="true" />
        </section>
    );
}

function EmptyPreview({ type, title, description }: { type: 'news' | 'events'; title: string; description: string }) {
    return (
        <div className="empty-state">
            <span className="empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
                    {type === 'news' ? (
                        <>
                            <path d="M4 4h13v16H4zM17 8h3v12h-3M7 8h7M7 12h7M7 16h5" />
                        </>
                    ) : (
                        <>
                            <rect x="3" y="5" width="18" height="16" rx="1" />
                            <path d="M7 3v4m10-4v4M3 10h18m-13 4h3m-3 3h3" />
                        </>
                    )}
                </svg>
            </span>
            <div>
                <strong>{title}</strong>
                <p>{description}</p>
            </div>
        </div>
    );
}

function NewsCard({ item }: { item: NewsPreview }) {
    return (
        <article className="news-card">
            <div className="news-image">
                {item.image ? (
                    <img src={item.image} alt="" loading="lazy" width="480" height="270" />
                ) : (
                    <span>Image pending approval</span>
                )}
            </div>
            <div className="news-body">
                <time>{item.date}</time>
                <h3>{item.title}</h3>
                <p>{item.summary}</p>
                {item.href && (
                    <a href={item.href}>
                        Read more <span aria-hidden="true">→</span>
                    </a>
                )}
            </div>
        </article>
    );
}

function EventItem({ item }: { item: EventPreview }) {
    return (
        <article className="event-item">
            <div className="event-date">
                <strong>{item.day}</strong>
                <span>{item.month}</span>
            </div>
            <div>
                <h3>{item.title}</h3>
                <p>{item.time} · {item.location}</p>
                {item.href && <a href={item.href}>Event details</a>}
            </div>
        </article>
    );
}

export function NewsAndEvents({ news, events }: { news: NewsPreview[]; events: EventPreview[] }) {
    return (
        <section className="updates-section" aria-label="News and events">
            <div className="container updates-grid">
                <div>
                    <div className="section-heading">
                        <div>
                            <p className="eyebrow">From the council</p>
                            <h2>Latest News</h2>
                        </div>
                        <span className="future-label">View All News →</span>
                    </div>
                    {news.length ? (
                        <div className="news-grid">
                            {news.map(item => (
                                <NewsCard item={item} key={item.title} />
                            ))}
                        </div>
                    ) : (
                        <EmptyPreview type="news" title="No news published yet" description="Approved council news will appear here when available." />
                    )}
                </div>
                <div className="events-panel">
                    <div className="section-heading">
                        <div>
                            <p className="eyebrow">What’s ahead</p>
                            <h2>Upcoming Events</h2>
                        </div>
                    </div>
                    {events.length ? (
                        events.map(item => <EventItem item={item} key={`${item.day}-${item.title}`} />)
                    ) : (
                        <EmptyPreview type="events" title="No upcoming events published" description="Confirmed council events will appear here." />
                    )}
                </div>
            </div>
        </section>
    );
}

export function FeatureCallouts() {
    const features = [
        {
            number: '01',
            title: 'Invest in Mutoko',
            description: 'Investment information will be available after council review.',
            image: '/images/feature-invest-development.webp',
            position: 'center',
        },
        {
            number: '02',
            title: 'Explore Our Tourism',
            description: 'Approved visitor information and local imagery will be added here.',
            image: '/images/feature-tourism-development.webp',
            position: '65% center',
        },
        {
            number: '03',
            title: 'Engage With Us',
            description: 'Find ways to stay informed and participate in council matters.',
            image: '/images/feature-engage-development.webp',
            position: '62% center',
        },
    ];
    return (
        <section className="features-section" aria-labelledby="features-heading">
            <div className="container">
                <div className="section-heading">
                    <div>
                        <p className="eyebrow">Discover more</p>
                        <h2 id="features-heading">Mutoko at a glance</h2>
                    </div>
                </div>
                <div className="feature-grid">
                    {features.map(feature => (
                        <article className="feature-card" key={feature.title}>
                            <img src={feature.image} alt="" width="1100" height="619" loading="lazy" style={{ objectPosition: feature.position }} />
                            <div className="feature-overlay" />
                            <div className="feature-content">
                                <span className="feature-number">{feature.number} / Explore</span>
                                <div>
                                    <h3>{feature.title}</h3>
                                    <p>{feature.description}</p>
                                    <span className="feature-cta">
                                        Details coming soon <span aria-hidden="true">→</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}

