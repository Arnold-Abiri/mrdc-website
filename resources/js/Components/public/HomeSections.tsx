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
import { useState } from 'react';
import { usePublicLocale, usePublicTranslation } from '../../usePublicTranslation';

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
        href: '/investment',
    },
    {
        icon: 'tourism',
        title: 'Tourism',
        description: "Explore Mutoko's natural beauty",
        theme: 'green',
        href: '/coming-soon?topic=tourism',
    },
    {
        icon: 'tenders',
        title: 'Tenders',
        description: 'Business opportunities',
        theme: 'amber',
        href: '/tenders',
    },
    {
        icon: 'vacancies',
        title: 'Vacancies',
        description: 'Join our team',
        theme: 'red',
        href: '/vacancies',
    },
    {
        icon: 'services',
        title: 'Feedback',
        description: 'Complaints, feedback & service requests',
        theme: 'emerald',
        href: '/feedback',
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

export type HeroSlide = { headline: string; supporting_text: string | null; cta_label: string | null; cta_url: string | null; image_url: string | null };

export function Hero({ slides = [] }: { slides?: HeroSlide[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    const [index, setIndex] = useState(0);
    const active = slides.length > 0 ? slides[index % slides.length] : undefined;
    if (!active) {
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

            </div>
        </section>
    );
    }
    return (
        <section className="hero" aria-labelledby="hero-title" aria-roledescription="carousel" aria-live="polite">
            <img
                className="hero-image"
                key={active.headline}
                src={active.image_url ?? '/images/hero-clean.webp'}
                width="1983"
                height="793"
                fetchPriority="high"
                alt={active.headline}
            />
            <div className="hero-shade" />

            <div className="container hero-container">
                <div className="hero-content">
                    <div className="hero-eyebrow-pill">
                        <span className="eyebrow-bar" aria-hidden="true" />
                        <span className="eyebrow-text">WELCOME TO</span>
                    </div>

                    <h1 id="hero-title" className="hero-heading">
                        <span className="hero-heading-white">{active.headline}</span>
                    </h1>

                    {active.supporting_text && <p className="hero-description">{active.supporting_text}</p>}

                    <div className="hero-actions">
                        {active.cta_label && active.cta_url ? <a className="btn-hero-primary" href={L(active.cta_url ?? "")}><span>{active.cta_label}</span><span aria-hidden="true">→</span></a> : <a className="btn-hero-primary" href="#services"><span>Our Services</span><span aria-hidden="true">→</span></a>}
                        <a className="btn-hero-outline" href="#about">
                            <span>About Council</span>
                        </a>
                    </div>
                    {slides.length > 1 && <div className="hero-carousel-controls">
                        <button type="button" className="hero-carousel-button" aria-label={t('previousSlide')} onClick={() => setIndex((index + slides.length - 1) % slides.length)}><span aria-hidden="true">‹</span></button>
                        <p className="hero-carousel-status" role="status">{index + 1} / {slides.length}</p>
                        <button type="button" className="hero-carousel-button" aria-label={t('nextSlide')} onClick={() => setIndex((index + 1) % slides.length)}><span aria-hidden="true">›</span></button>
                    </div>}
                </div>

            </div>
        </section>
    );
}

export function QuickAccess() {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    return (
        <section className="quick-access container" id="quick-access" aria-label="Quick Access Services">
            <h2 className="sr-only">Quick access services</h2>
            <div className="quick-grid">
                {services.map(service => (
                    <a className="quick-card" key={service.title} href={L(service.href)}>
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
            <div className="bottom-pattern-banner" style={{ backgroundImage: 'url(/images/bottom-pattern.png)' }} aria-hidden="true" />
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
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                        Mutoko Rural District Council covers 428,916 hectares, 143km north-east of Harare on the Harare–Nyamapanda highway and 90km from the Mozambique border. We serve 29 wards plus a women's quota and the Mutoko Town Board.
                    </p>
                    <p className="about-description">
                        Our mission is to provide quality, sustainable services with our communities. Our vision is a vibrant and prosperous Mutoko by 2030.
                    </p>
                    <div className="about-stats-row" aria-label="District figures">
                        <div className="about-stat"><strong>~163,000</strong><span>Population</span></div>
                        <div className="about-stat"><strong>84 + 44</strong><span>Primary &amp; secondary schools</span></div>
                        <div className="about-stat"><strong>29</strong><span>Wards served</span></div>
                        <div className="about-stat"><strong>428,916 ha</strong><span>District area</span></div>
                    </div>
                    <div>
                        <a href={L("/coming-soon?topic=council")} className="btn-section-primary">
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
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                    <a href={L(item.href)} className="news-readmore">
                        <span>Read More</span>
                        <span aria-hidden="true">→</span>
                    </a>
                )}
            </div>
        </article>
    );
}

function EventItem({ item }: { item: EventPreview }) {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                    <a href={L(item.href)} className="event-link">
                        Event details
                    </a>
                )}
            </div>
        </article>
    );
}

export function NewsAndEvents({ news = newsPreviews, events = eventPreviews }: { news?: NewsPreview[]; events?: EventPreview[] }) {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                        <a href={L("/news")} className="header-viewall-link">
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
                        <a href={L("/coming-soon?topic=events")} className="header-viewall-link">
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
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                    <a href={L("/coming-soon?topic=services")} className="header-viewall-link">
                        View All Services <span aria-hidden="true">→</span>
                    </a>
                </div>
                <p className="section-subtext">
                    From education and health to roads, water, business centres, property and recreation at Chikondoma Stadium — services that improve daily life across all 29 wards.
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
                                <a href={L(item.href)} className="service-card-link">
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


export type ManagedHomepageService = { slug: string; name: string; summary: string | null };
export type ManagedHomepageDocument = { slug: string; title: string; description: string | null };
export type ManagedHomepageDepartment = { id: number; public_name: string; public_summary: string | null };
export type ManagedHomepageNotice = { slug: string; title: string; summary: string | null };

export type ManagedHomepageContact = { office: string; type: 'phone' | 'email' | 'physical_address' | 'postal_address'; value: string };
export type ManagedHomepageOfficial = { slug: string; name: string; title: string };
export type ManagedHomepageStatistic = { label: string; value: string; unit: string | null; icon: string | null };
export type ManagedHomepageTender = { slug: string; reference: string; title: string; display_status: string };
export type ManagedHomepageInvestment = { slug: string; title: string; sector: string | null; summary: string | null };
export type ManagedHomepageProject = { slug: string; title: string; project_status: string; summary: string | null };

export function ManagedHomepageContent({ services, documents, departments, notices, contacts = [], officials = [], wardCount = 0, statistics = [], tenders = [], investment = [], projects = [] }: { services: ManagedHomepageService[]; documents: ManagedHomepageDocument[]; departments: ManagedHomepageDepartment[]; notices: ManagedHomepageNotice[]; contacts?: ManagedHomepageContact[]; officials?: ManagedHomepageOfficial[]; wardCount?: number; statistics?: ManagedHomepageStatistic[]; tenders?: ManagedHomepageTender[]; investment?: ManagedHomepageInvestment[]; projects?: ManagedHomepageProject[] }) {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    return <section id="services" className="container coming-soon" aria-label="Approved council information">
        <h2>Approved council information</h2>
        <section aria-labelledby="managed-services-heading"><h3 id="managed-services-heading">Services</h3>{services.length ? <ul>{services.map(item => <li key={item.slug}><a href={L(`/services/${item.slug}`)}>{item.name}</a>{item.summary && <p>{item.summary}</p>}</li>)}</ul> : <p>No approved services are available yet.</p>}</section>
        <section aria-labelledby="managed-departments-heading"><h3 id="managed-departments-heading">Departments</h3>{departments.length ? <ul>{departments.map(item => <li key={item.id}><a href={L(`/departments/${item.id}`)}>{item.public_name}</a>{item.public_summary && <p>{item.public_summary}</p>}</li>)}</ul> : <p>No approved department information is available yet.</p>}</section>
        <section aria-labelledby="managed-documents-heading"><h3 id="managed-documents-heading">Documents</h3>{documents.length ? <ul>{documents.map(item => <li key={item.slug}><a href={L(`/documents/${item.slug}`)}>{item.title}</a>{item.description && <p>{item.description}</p>}</li>)}</ul> : <p>No approved documents are available yet.</p>}</section>
        <section aria-labelledby="managed-notices-heading"><h3 id="managed-notices-heading">Notices</h3>{notices.length ? <ul>{notices.map(item => <li key={item.slug}><a href={L(`/notices/${item.slug}`)}>{item.title}</a>{item.summary && <p>{item.summary}</p>}</li>)}</ul> : <p>No approved notices are available yet.</p>}</section>
        <section aria-labelledby="managed-officials-heading"><h3 id="managed-officials-heading">Council leadership</h3>{officials.length ? <ul>{officials.map(item => <li key={item.slug}><a href={L(`/officials/${item.slug}`)}>{item.name}</a> — {item.title}</li>)}</ul> : <p>No approved leadership profiles are available yet.</p>}<a href={L("/officials")}>View council officials</a></section>
        <section aria-labelledby="managed-wards-heading"><h3 id="managed-wards-heading">Wards</h3><p>{wardCount ? `${wardCount} approved ward ${wardCount === 1 ? 'profile' : 'profiles'} available.` : 'No approved ward profiles are available yet.'}</p><a href={L("/wards")}>View ward directory</a></section>
        <section aria-labelledby="managed-contacts-heading"><h3 id="managed-contacts-heading">Council contacts</h3>{contacts.length ? <ul>{contacts.map((item, index) => <li key={`${item.office}-${item.type}-${index}`}><strong>{item.office}:</strong> {item.type === 'email' ? <a href={`mailto:${item.value}`}>{item.value}</a> : item.type === 'phone' ? <a href={`tel:${item.value}`}>{item.value}</a> : item.value}</li>)}</ul> : <p>No approved contact details are available yet.</p>}<a href={L("/contact")}>Contact the council</a></section>
        <section aria-labelledby="managed-statistics-heading"><h3 id="managed-statistics-heading">Our district</h3>{statistics.length ? <ul>{statistics.map(item => <li key={item.label}><strong>{item.value}{item.unit ? ` ${item.unit}` : ''}</strong> — {item.label}</li>)}</ul> : <p>District statistics are being verified for publication.</p>}</section>
        <section aria-labelledby="managed-tenders-heading"><h3 id="managed-tenders-heading">Tenders</h3>{tenders.length ? <ul>{tenders.map(item => <li key={item.slug}><a href={L(`/tenders/${item.slug}`)}>{item.title}</a> — {item.reference} ({item.display_status})</li>)}</ul> : <p>No tenders are published at this time.</p>}<a href={L("/tenders")}>View all tenders</a></section>
        <section aria-labelledby="managed-investment-heading"><h3 id="managed-investment-heading">Invest in Mutoko</h3>{investment.length ? <ul>{investment.map(item => <li key={item.slug}><a href={L(`/investment/${item.slug}`)}>{item.title}</a>{item.sector ? ` — ${item.sector}` : ''}</li>)}</ul> : <p>Investment opportunities are being prepared for publication.</p>}<a href={L("/investment")}>Explore investment</a></section>
        <section aria-labelledby="managed-projects-heading"><h3 id="managed-projects-heading">Projects and programmes</h3>{projects.length ? <ul>{projects.map(item => <li key={item.slug}><a href={L(`/projects/${item.slug}`)}>{item.title}</a> — {item.project_status}</li>)}</ul> : <p>Project profiles are being prepared for publication.</p>}<a href={L("/projects")}>View all projects</a></section>
    </section>;
}

export function DevelopmentSection() {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                        <a href={L("/coming-soon?topic=projects")} className="btn-section-primary">
                            <span>Explore Development</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </section>
    );
}

export function TourismSection({ destinations = tourismDestinations }: { destinations?: TourismPreview[] }) {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                    <a href={L("/coming-soon?topic=tourism")} className="header-viewall-link">
                        Explore Tourism <span aria-hidden="true">→</span>
                    </a>
                </div>
                <p className="section-subtext">
                    Explore the natural beauty, rich culture and unique attractions that make Mutoko a special destination.
                </p>

                <div className="tourism-grid">
                    {destinations.map(item => (
                        <a key={item.title} href={L(item.href)} className="tourism-card">
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
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
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
                    <a href={L("/contact")} className="btn-cta-green">
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


