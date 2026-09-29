import {
    aboutPillars,
    eventPreviews,
    keyServices,
    newsPreviews,
    tourismDestinations,
    type AboutPillar,
    type EventPreview,
    type NewsPreview,
    type ServicePreview,
    type TourismPreview,
} from '../../fixtures/home';

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
        href: '/coming-soon?topic=tenders',
    },
    {
        icon: 'vacancies',
        title: 'Vacancies',
        description: 'Join our team',
        theme: 'red',
        href: '/coming-soon?topic=vacancies',
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
            <h2 className="sr-only">Quick access services</h2>
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

function AboutIcon({ name }: { name: AboutPillar['icon'] }) {
    switch (name) {
        case 'gear':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.488.488 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                </svg>
            );
        case 'shield':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v2h-2V7zm0 4h2v6h-2v-6z" />
                </svg>
            );
        case 'leaf':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s2-2 3-3c-4 0-7 2-8 4-1 2-1 4-1 4s2-2 5-2c0 0-2 2-3 4 3 0 6-2 7-4z" />
                </svg>
            );
        case 'users':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
            );
    }
}

export function AboutSection() {
    return (
        <section className="about-section" id="about" aria-labelledby="about-heading">
            <div className="container about-container">
                <div className="about-intro">
                    <div className="section-eyebrow-pill">
                        <span className="eyebrow-bar" aria-hidden="true" />
                        <span className="eyebrow-text">ABOUT US</span>
                    </div>
                    <h2 id="about-heading" className="about-heading">
                        A Vibrant and Prosperous Mutoko
                    </h2>
                    <p className="about-description">
                        Mutoko Rural District Council is committed to effective service delivery, sustainable development and inclusive growth for all our communities.
                    </p>
                    <div>
                        <a href="/coming-soon?topic=council" className="btn-section-primary">
                            <span>Learn More</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <div className="about-features-grid">
                    {aboutPillars.map(pillar => (
                        <div key={pillar.title} className="about-feature-card">
                            <span className={`about-icon-badge badge-${pillar.icon}`}>
                                <AboutIcon name={pillar.icon} />
                            </span>
                            <div className="about-feature-text">
                                <h3 className="about-feature-title">{pillar.title}</h3>
                                <p className="about-feature-desc">{pillar.description}</p>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="about-image-wrapper">
                    <img
                        src="/images/home/welcome-sign.webp"
                        alt="Welcome to Mutoko road entrance"
                        className="about-image"
                        width="320"
                        height="280"
                        loading="lazy"
                    />
                </div>
            </div>
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
                    <img src={item.image} alt={item.title} loading="lazy" width="480" height="270" />
                ) : (
                    <span>Image pending approval</span>
                )}
            </div>
            <div className="news-body">
                <div className="news-date-meta">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <time dateTime={item.date}>{item.date}</time>
                </div>
                <h3 className="news-title">{item.title}</h3>
                <p className="news-summary">{item.summary}</p>
                {item.href && (
                    <a href={item.href} className="news-readmore">
                        <span>Read More</span>
                        <span aria-hidden="true">→</span>
                    </a>
                )}
            </div>
        </article>
    );
}

function EventItem({ item }: { item: EventPreview }) {
    return (
        <article className="event-item-card">
            <div className="event-badge">
                <strong className="event-badge-day">{item.day}</strong>
                <span className="event-badge-month">{item.month}</span>
            </div>
            <div className="event-details">
                <h3 className="event-title">{item.title}</h3>
                <p className="event-location">{item.location}</p>
                <p className="event-time">{item.time}</p>
                {item.href && (
                    <a href={item.href} className="event-link">
                        Event details
                    </a>
                )}
            </div>
        </article>
    );
}

export function NewsAndEvents({ news = newsPreviews, events = eventPreviews }: { news?: NewsPreview[]; events?: EventPreview[] }) {
    return (
        <section className="updates-section" aria-label="News and events">
            <div className="container updates-grid">
                <div className="news-panel">
                    <div className="section-header-row">
                        <div>
                            <div className="section-eyebrow-pill">
                                <span className="eyebrow-bar" aria-hidden="true" />
                                <span className="eyebrow-text">LATEST NEWS</span>
                            </div>
                            <h2 className="section-title">News and Updates</h2>
                        </div>
                        <a href="/coming-soon?topic=news" className="header-viewall-link">
                            View All News <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <p className="section-subtext">
                        Stay informed about the latest developments, announcements and initiatives from Mutoko Rural District Council.
                    </p>
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
                    <div className="section-header-row">
                        <div>
                            <div className="section-eyebrow-pill">
                                <span className="eyebrow-bar" aria-hidden="true" />
                                <span className="eyebrow-text">UPCOMING EVENTS</span>
                            </div>
                            <h2 className="section-title">Events Calendar</h2>
                        </div>
                        <a href="/coming-soon?topic=events" className="header-viewall-link">
                            View All Events <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <div className="events-list">
                        {events.length ? (
                            events.map(item => <EventItem item={item} key={`${item.day}-${item.title}`} />)
                        ) : (
                            <EmptyPreview type="events" title="No upcoming events published" description="Confirmed council events will appear here." />
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}

function ServiceIcon({ name }: { name: ServicePreview['icon'] }) {
    switch (name) {
        case 'water':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                </svg>
            );
        case 'roads':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M18.11 3.5L16.29 20.5H7.71L5.89 3.5h12.22zM13 5h-2v3h2V5zm0 5h-2v3h2v-3zm0 5h-2v3h2v-3z" />
                </svg>
            );
        case 'health':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35zm-1-12.85v2h-2v2h2v2h2v-2h2v-2h-2v-2h-2z" />
                </svg>
            );
        case 'environment':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s2-2 3-3c-4 0-7 2-8 4-1 2-1 4-1 4s2-2 5-2c0 0-2 2-3 4 3 0 6-2 7-4z" />
                </svg>
            );
    }
}

export function KeyServicesSection({ services = keyServices }: { services?: ServicePreview[] }) {
    return (
        <section className="key-services-section" id="services" aria-labelledby="services-heading">
            <div className="container">
                <div className="section-header-row">
                    <div>
                        <div className="section-eyebrow-pill">
                            <span className="eyebrow-bar" aria-hidden="true" />
                            <span className="eyebrow-text">OUR SERVICES</span>
                        </div>
                        <h2 id="services-heading" className="section-title">Key Services</h2>
                    </div>
                    <a href="/coming-soon?topic=services" className="header-viewall-link">
                        View All Services <span aria-hidden="true">→</span>
                    </a>
                </div>
                <p className="section-subtext">
                    We provide essential services that improve the quality of life for our communities.
                </p>

                <div className="services-grid">
                    {services.map(item => (
                        <div key={item.id} className="service-card">
                            <div className="service-image-wrapper">
                                <img src={item.image} alt={item.title} loading="lazy" width="320" height="160" className="service-image" />
                                <div className={`service-icon-badge service-icon-${item.icon}`} aria-hidden="true">
                                    <ServiceIcon name={item.icon} />
                                </div>
                            </div>
                            <div className="service-card-body">
                                <h3 className="service-card-title">{item.title}</h3>
                                <p className="service-card-desc">{item.description}</p>
                                <a href={item.href} className="service-card-link">
                                    <span>Learn More</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function DevelopmentSection() {
    return (
        <section className="dev-banner-section" id="development" aria-labelledby="dev-heading">
            <div className="dev-banner-bg">
                <img
                    src="/images/home/dev-background.webp"
                    alt="Scenic Mutoko landscape development"
                    className="dev-banner-image"
                    loading="lazy"
                />
                <div className="dev-banner-overlay" />
            </div>

            <div className="container dev-banner-container">
                <div className="dev-banner-content">
                    <div className="section-eyebrow-pill">
                        <span className="eyebrow-bar" aria-hidden="true" />
                        <span className="eyebrow-text">DEVELOPMENT</span>
                    </div>
                    <h2 id="dev-heading" className="dev-banner-heading">
                        Building a Better Mutoko
                    </h2>
                    <p className="dev-banner-description">
                        We are implementing strategic projects and initiatives to promote economic growth, improve infrastructure and create opportunities for all our communities.
                    </p>
                    <div>
                        <a href="/coming-soon?topic=projects" className="btn-section-primary">
                            <span>Explore Development</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <div className="dev-stats-grid">
                    <div className="dev-stat-card">
                        <div className="dev-stat-icon badge-gold" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 19h16v2H4zM6 10h3v7H6zm5-5h3v12h-3zm5 8h3v4h-3z" />
                            </svg>
                        </div>
                        <div className="dev-stat-text">
                            <strong className="dev-stat-number">50+</strong>
                            <span className="dev-stat-title">Community Projects</span>
                            <span className="dev-stat-subtitle">Ongoing and planned</span>
                        </div>
                    </div>

                    <div className="dev-stat-card">
                        <div className="dev-stat-icon badge-blue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                            </svg>
                        </div>
                        <div className="dev-stat-text">
                            <strong className="dev-stat-number">12</strong>
                            <span className="dev-stat-title">Wards</span>
                            <span className="dev-stat-subtitle">Across the district</span>
                        </div>
                    </div>

                    <div className="dev-stat-card">
                        <div className="dev-stat-icon badge-leaf" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s2-2 3-3c-4 0-7 2-8 4-1 2-1 4-1 4s2-2 5-2c0 0-2 2-3 4 3 0 6-2 7-4z" />
                            </svg>
                        </div>
                        <div className="dev-stat-text">
                            <strong className="dev-stat-number">3</strong>
                            <span className="dev-stat-title">Growth Points</span>
                            <span className="dev-stat-subtitle">Driving local development</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export function TourismSection({ destinations = tourismDestinations }: { destinations?: TourismPreview[] }) {
    return (
        <section className="tourism-section" id="tourism" aria-labelledby="tourism-heading">
            <div className="container">
                <div className="section-header-row">
                    <div>
                        <div className="section-eyebrow-pill">
                            <span className="eyebrow-bar" aria-hidden="true" />
                            <span className="eyebrow-text">TOURISM</span>
                        </div>
                        <h2 id="tourism-heading" className="section-title">Discover Mutoko</h2>
                    </div>
                    <a href="/coming-soon?topic=tourism" className="header-viewall-link">
                        Explore Tourism <span aria-hidden="true">→</span>
                    </a>
                </div>
                <p className="section-subtext">
                    Explore the natural beauty, rich culture and unique attractions that make Mutoko a special destination.
                </p>

                <div className="tourism-grid">
                    {destinations.map(item => (
                        <a key={item.title} href={item.href} className="tourism-card">
                            <img src={item.image} alt={item.title} loading="lazy" className="tourism-card-image" />
                            <div className="tourism-card-gradient" />
                            <div className="tourism-card-content">
                                <h3 className="tourism-card-title">{item.title}</h3>
                                <div className="tourism-tag-row">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" />
                                    </svg>
                                    <span>{item.tag}</span>
                                </div>
                            </div>
                        </a>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function CtaBanner() {
    return (
        <section className="cta-banner-section" aria-labelledby="cta-heading">
            <div className="container cta-banner-container">
                <div className="cta-text-side">
                    <h2 id="cta-heading" className="cta-heading">
                        Partner with Us for a Better Mutoko
                    </h2>
                    <p className="cta-subtext">
                        Whether you are a community member, investor, development partner or visitor, we invite you to join us in building a prosperous and sustainable Mutoko.
                    </p>
                </div>
                <div className="cta-action-side">
                    <a href="/coming-soon?topic=contact" className="btn-cta-green">
                        <span>Get in Touch</span>
                        <span aria-hidden="true">→</span>
                    </a>
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


