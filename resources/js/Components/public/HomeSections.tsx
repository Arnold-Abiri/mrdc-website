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
import { useEffect, useState } from 'react';
import { usePublicLocale, usePublicTranslation } from '../../usePublicTranslation';

const services = [
    {
        icon: 'services',
        title: 'Our Services',
        description: 'Water, roads, health, sanitation & more',
        theme: 'emerald',
        href: '#services',
    },
    {
        icon: 'rates',
        title: 'Online Services',
        description: 'Rates, bills, fees & payments',
        theme: 'cyan',
        href: '/rates',
    },
    {
        icon: 'notices',
        title: 'Public Notices',
        description: 'Announcements & consultations',
        theme: 'blue',
        href: '/notices',
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
        icon: 'contact',
        title: 'Contact Directory',
        description: 'Reach the council & enquire',
        theme: 'green',
        href: '/contact',
    },
    {
        icon: 'feedback',
        title: 'Feedback',
        description: 'Complaints, feedback & service requests',
        theme: 'emerald',
        href: '/feedback',
    },
] as const;

function QuickIcon({ name }: { name: typeof services[number]['icon'] }) {
    switch (name) {
        case 'services':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M19 12h-2V8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2v4H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z" />
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
        case 'rates':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z" />
                </svg>
            );
        case 'notices':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 9h-2V5h2v6zm0 4h-2v-2h2v2z" />
                </svg>
            );
        case 'contact':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                </svg>
            );
        case 'feedback':
            return (
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c1.1 0 2-.9 2-2zm-9 9H7V9h4v2zm6 0h-4V9h4v2zm-6 4H7v-2h4v2zm6 0h-4v-2h4v2z" />
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
    const [isPaused, setIsPaused] = useState(false);
    const [isHovering, setIsHovering] = useState(false);
    const [hasFocus, setHasFocus] = useState(false);
    const rotationStopped = isPaused || isHovering || hasFocus;
    const currentIndex = slides.length > 0 ? index % slides.length : 0;
    const active = slides[currentIndex];
    const isCouncilWelcome = active?.headline === 'Mutoko Rural District Council';
    const headlineWords = active?.headline.trim().split(/\s+/) ?? [];
    const accentStart = Math.max(1, headlineWords.length - 2);

    useEffect(() => {
        const motionPreference = window.matchMedia?.('(prefers-reduced-motion: reduce)');
        const pauseForReducedMotion = () => {
            if (motionPreference?.matches) setIsPaused(true);
        };
        const pauseWhenHidden = () => {
            if (document.hidden) setIsPaused(true);
        };

        pauseForReducedMotion();
        motionPreference?.addEventListener('change', pauseForReducedMotion);
        document.addEventListener('visibilitychange', pauseWhenHidden);

        return () => {
            motionPreference?.removeEventListener('change', pauseForReducedMotion);
            document.removeEventListener('visibilitychange', pauseWhenHidden);
        };
    }, []);

    useEffect(() => {
        if (slides.length < 2 || rotationStopped) return;

        const timer = window.setInterval(() => setIndex(current => (current + 1) % slides.length), 8000);
        return () => window.clearInterval(timer);
    }, [slides.length, rotationStopped]);
    if (!active) {
    return (
        <section className="hero" aria-labelledby="hero-title">
            <SafeImage
                className="hero-image"
                src="/images/hero-clean.webp"
                width="1983"
                height="793"
                eager
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

                    <form action={`/${locale}/search`} method="get" role="search" className="hero-search-form">
                        <label htmlFor="hero-search-input" className="sr-only">{t('searchCouncilPages')}</label>
                        <div className="hero-search-wrapper">
                            <span className="hero-search-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="10.8" cy="10.8" r="6.8" />
                                    <path d="m16 16 4.6 4.6" />
                                </svg>
                            </span>
                            <input
                                id="hero-search-input"
                                name="q"
                                type="search"
                                maxLength={100}
                                placeholder={t('homeSearchPlaceholder')}
                                autoComplete="off"
                                className="hero-search-input"
                            />
                            <button type="submit" className="hero-search-button">
                                <span>{t('search')}</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg>
                            </button>
                        </div>
                    </form>

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
        <section className={`hero hero--managed${rotationStopped ? ' hero--paused' : ''}`} aria-labelledby="hero-title" aria-roledescription={slides.length > 1 ? 'carousel' : undefined} onMouseEnter={() => setIsHovering(true)} onMouseLeave={() => setIsHovering(false)} onFocusCapture={() => setHasFocus(true)} onBlurCapture={(event) => { if (!event.currentTarget.contains(event.relatedTarget as Node | null)) setHasFocus(false); }}>
            <SafeImage
                className="hero-image"
                key={`${currentIndex}-${active.image_url}`}
                src={active.image_url ?? '/images/hero-clean.webp'}
                width="1983"
                height="793"
                eager
                alt={isCouncilWelcome ? 'Scenic Mutoko landscape showing rocky kopje mountains, green valley and Mutoko town center' : active.headline}
            />
            <div className="hero-shade" />

            <div className="container hero-container">
                <div className="hero-content" key={currentIndex} aria-live={rotationStopped ? 'polite' : 'off'}>
                    <div className="hero-eyebrow-pill">
                        <span className="eyebrow-bar" aria-hidden="true" />
                        <span className="eyebrow-text">WELCOME TO</span>
                    </div>

                    <h1 id="hero-title" className="hero-heading">
                        {isCouncilWelcome ? <><span className="hero-heading-white">Mutoko Rural</span>{' '}<span className="hero-heading-green">District Council</span></> : headlineWords.length > 1 ? <><span className="hero-heading-white">{headlineWords.slice(0, accentStart).join(' ')}</span>{' '}<span className="hero-heading-green">{headlineWords.slice(accentStart).join(' ')}</span></> : <span className="hero-heading-white">{active.headline}</span>}
                    </h1>

                    {isCouncilWelcome && <p className="hero-statement">People. Development. Sustainable Communities.</p>}
                    {active.supporting_text && <p className="hero-description">{active.supporting_text}</p>}

                    <form action={`/${locale}/search`} method="get" role="search" className="hero-search-form">
                        <label htmlFor="hero-search-input-active" className="sr-only">{t('searchCouncilPages')}</label>
                        <div className="hero-search-wrapper">
                            <span className="hero-search-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="10.8" cy="10.8" r="6.8" />
                                    <path d="m16 16 4.6 4.6" />
                                </svg>
                            </span>
                            <input
                                id="hero-search-input-active"
                                name="q"
                                type="search"
                                maxLength={100}
                                placeholder={t('homeSearchPlaceholder')}
                                autoComplete="off"
                                className="hero-search-input"
                            />
                            <button type="submit" className="hero-search-button">
                                <span>{t('search')}</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div className="hero-actions">
                        {active.cta_label && active.cta_url ? <a className="btn-hero-primary" href={L(active.cta_url ?? "")}><span>{active.cta_label}</span><span aria-hidden="true">→</span></a> : <a className="btn-hero-primary" href="#services"><span>Our Services</span><span aria-hidden="true">→</span></a>}
                        <a className="btn-hero-outline" href="#about">
                            <span>About Council</span>
                        </a>
                    </div>
                </div>
                {slides.length > 1 && <div className="hero-carousel-controls" role="group" aria-label={t('slideNavigation')}>
                    <span className="hero-carousel-status" aria-live={rotationStopped ? 'polite' : 'off'}><strong>{String(currentIndex + 1).padStart(2, '0')}</strong><span> / {String(slides.length).padStart(2, '0')}</span></span>
                    <div className="hero-slide-picker" role="group" aria-label={t('chooseSlide')}>
                        {slides.map((slide, slideIndex) => <button key={`${slide.headline}-${slideIndex}`} type="button" className={`hero-slide-marker${slideIndex === currentIndex ? ' is-active' : ''}`} aria-label={`${t('showSlide')} ${slideIndex + 1}: ${slide.headline}`} aria-current={slideIndex === currentIndex ? 'true' : undefined} onClick={() => { setIsPaused(true); setIndex(slideIndex); }}><span aria-hidden="true" /></button>)}
                    </div>
                    <div className="hero-carousel-actions">
                        <button type="button" className="hero-carousel-button" aria-label={t('previousSlide')} onClick={() => { setIsPaused(true); setIndex(current => (current + slides.length - 1) % slides.length); }}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="m14 5-7 7 7 7" /></svg></button>
                        <button type="button" className="hero-carousel-button hero-carousel-pause" aria-label={isPaused ? t('resumeSlides') : t('pauseSlides')} aria-pressed={isPaused} onClick={() => setIsPaused(paused => !paused)}>{isPaused ? <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.2a1 1 0 0 1 1.5-.86l9.5 6.8a1 1 0 0 1 0 1.72l-9.5 6.8A1 1 0 0 1 8 18.8V5.2Z" /></svg> : <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1" /><rect x="14" y="5" width="4" height="14" rx="1" /></svg>}</button>
                        <button type="button" className="hero-carousel-button" aria-label={t('nextSlide')} onClick={() => { setIsPaused(true); setIndex(current => (current + 1) % slides.length); }}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="m10 5 7 7-7 7" /></svg></button>
                    </div>
                </div>}
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

export function AboutSection({ aboutHref }: { aboutHref?: string }) {
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
                    <div>
                        <a href={aboutHref ?? L("/about")} className="btn-section-primary">
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
                    <SafeImage
                        src="/images/home/welcome-sign.webp"
                        alt="Welcome to Mutoko road entrance"
                        className="about-image"
                        width="320"
                        height="280"
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

function SafeImage({ src, alt, className, width, height, eager = false, fallbackLabel = 'Image currently unavailable' }: { src: string; alt: string; className?: string; width?: number | string; height?: number | string; eager?: boolean; fallbackLabel?: string }) {
    const [broken, setBroken] = useState(false);
    if (broken || !src) {
        return (
            <span className={`img-fallback${className ? ` ${className}-fallback` : ''}`} role="img" aria-label={`${fallbackLabel}: ${alt}`}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <circle cx="9" cy="9" r="2" />
                    <path d="m21 15-3.5-3.5a1.5 1.5 0 0 0-2 0L6 21" />
                </svg>
                <span>{fallbackLabel}</span>
            </span>
        );
    }
    return <img className={className} src={src} alt={alt} width={width} height={height} fetchPriority={eager ? 'high' : undefined} loading={eager ? undefined : 'lazy'} onError={() => setBroken(true)} />;
}

function NewsCard({ item }: { item: NewsPreview }) {
    return (
        <article className="news-card">
            <div className="news-image">
                {item.image && <SafeImage src={item.image} alt={item.imageAlt ?? item.title} width="480" height="270" />}
                {item.imageCaption?.startsWith('AI-generated') && <span className="news-image-label">Editorial illustration</span>}
                {!item.image && <span className="img-fallback img-fallback-icon" role="img" aria-label={`No image available for ${item.title}`}>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="9" cy="9" r="2" />
                        <path d="m21 15-3.5-3.5a1.5 1.5 0 0 0-2 0L6 21" />
                    </svg>
                </span>}
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
    const t = usePublicTranslation();
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
                {(item.hasAgenda || item.hasMinutes) && (
                    <div className="event-doc-chips">
                        {item.hasAgenda && (
                            <span className="event-doc-chip chip-agenda">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                {t('agendaAvailable')}
                            </span>
                        )}
                        {item.hasMinutes && (
                            <span className="event-doc-chip chip-minutes">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                {t('minutesAvailable')}
                            </span>
                        )}
                    </div>
                )}
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
                        <a href={L("/meetings")} className="header-viewall-link">
                            View council meetings <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <div className="events-list">
                        {events.length ? (
                            events.map(item => <EventItem item={item} key={`${item.day}-${item.title}`} />)
                        ) : (
                            <EmptyPreview type="events" title="No upcoming meetings scheduled" description="Meeting dates, agendas and minutes are published here once confirmed." />
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

function StatIcon({ name }: { name: string | null }) {
    switch (name) {
        case 'wards':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                    <line x1="8" y1="2" x2="8" y2="18" />
                    <line x1="16" y1="6" x2="16" y2="22" />
                </svg>
            );
        case 'population':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            );
        case 'area':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
            );
        case 'education':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                </svg>
            );
        case 'history':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 14 14" />
                </svg>
            );
        case 'projects':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                </svg>
            );
        case 'tenders':
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
            );
        default:
            return (
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <line x1="18" y1="20" x2="18" y2="10" />
                    <line x1="12" y1="20" x2="12" y2="4" />
                    <line x1="6" y1="20" x2="6" y2="14" />
                </svg>
            );
    }
}

export function ManagedHomepageContent({ preview = false, services, documents, departments, notices, contacts = [], officials = [], wardCount = 0, statistics = [], tenders = [], investment = [], projects = [] }: { preview?: boolean; services: ManagedHomepageService[]; documents: ManagedHomepageDocument[]; departments: ManagedHomepageDepartment[]; notices: ManagedHomepageNotice[]; contacts?: ManagedHomepageContact[]; officials?: ManagedHomepageOfficial[]; wardCount?: number; statistics?: ManagedHomepageStatistic[]; tenders?: ManagedHomepageTender[]; investment?: ManagedHomepageInvestment[]; projects?: ManagedHomepageProject[] }) {
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    const P = (type: string, slug: string | number) => preview ? `/preview/${locale}/${type}/${slug}` : L(`/${type}/${slug}`);
    return <>
        {statistics.length > 0 && <section className="managed-section managed-stats-band" aria-label="District at a glance">
            <div className="container">
                <dl className="managed-stats-grid">
                    {statistics.slice(0, 4).map(item => <div key={item.label} className="managed-stat">
                        <span className="managed-stat-icon" aria-hidden="true">
                            <StatIcon name={item.icon} />
                        </span>
                        <div className="managed-stat-info">
                            <dd><strong>{item.value}{item.unit ? ` ${item.unit}` : ''}</strong></dd>
                            <dt>{item.label}</dt>
                        </div>
                    </div>)}
                </dl>
            </div>
        </section>}
        <section id="services" className="managed-section" aria-labelledby="managed-services-heading">
            <div className="container managed-split">
                <div className="managed-intro">
                    <p className="managed-eyebrow">Council services</p>
                    <h2 id="managed-services-heading">Services that keep Mutoko running</h2>
                    <p>Water, roads, health, planning and community services delivered across our wards. Start with the services residents use most.</p>
                    <a className="managed-viewall" href={L('/services')}>View all services <span aria-hidden="true">→</span></a>
                </div>
                <div className="managed-list">
                    {services.length ? services.slice(0, 6).map(item => <article key={item.slug} className="managed-row managed-service-card">
                        <div className="managed-row-main">
                            <span className="managed-row-badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            </span>
                            <div>
                                <h3><a href={P('services', item.slug)}>{item.name}</a></h3>
                                {item.summary && <p>{item.summary}</p>}
                            </div>
                        </div>
                        <a className="managed-row-link" href={P('services', item.slug)} aria-label={`Open ${item.name}`}>→</a>
                    </article>) : <p className="managed-empty">Approved services will appear here once published.</p>}
                </div>
            </div>
        </section>
        <section className="managed-section managed-alt" aria-labelledby="managed-notices-heading">
            <div className="container managed-split">
                <div className="managed-intro">
                    <p className="managed-eyebrow">Public notices</p>
                    <h2 id="managed-notices-heading">Notices residents should read</h2>
                    <p>Official announcements, closures, meetings and deadlines from the council.</p>
                    <a className="managed-viewall" href={L('/notices')}>View all notices <span aria-hidden="true">→</span></a>
                </div>
                <div className="managed-list">
                    {notices.length ? notices.slice(0, 5).map(item => <article key={item.slug} className="managed-row managed-notice">
                        <span className="notice-marker" aria-hidden="true" />
                        <div className="managed-row-main">
                            <span className="managed-notice-tag">Notice</span>
                            <div>
                                <h3><a href={P('notices', item.slug)}>{item.title}</a></h3>
                                {item.summary && <p>{item.summary}</p>}
                            </div>
                        </div>
                        <a className="managed-row-link" href={P('notices', item.slug)} aria-label={`Read notice: ${item.title}`}>→</a>
                    </article>) : <p className="managed-empty">No published notices at this time.</p>}
                </div>
            </div>
        </section>
        <section className="managed-section" aria-labelledby="managed-documents-heading">
            <div className="container managed-split">
                <div className="managed-intro">
                    <p className="managed-eyebrow">Document centre</p>
                    <h2 id="managed-documents-heading">Plans, reports and forms</h2>
                    <p>Key public documents approved for release by the council.</p>
                    <a className="managed-viewall" href={L('/documents')}>View document centre <span aria-hidden="true">→</span></a>
                </div>
                <div className="managed-list">
                    {documents.length ? documents.slice(0, 5).map(item => <article key={item.slug} className="managed-row managed-doc-card">
                        <div className="managed-row-main">
                            <span className="managed-doc-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </span>
                            <div>
                                <h3><a href={P('documents', item.slug)}>{item.title}</a></h3>
                                {item.description && <p>{item.description}</p>}
                            </div>
                        </div>
                        <a className="managed-row-link" href={P('documents', item.slug)} aria-label={`View document ${item.title}`}>→</a>
                    </article>) : <p className="managed-empty">No published documents at this time.</p>}
                </div>
            </div>
        </section>
        <section className="managed-strip" aria-label="Wards, leadership and contact">
            <div className="container managed-strip-grid">
                <article className="managed-strip-col" aria-labelledby="managed-wards-heading">
                    <div className="managed-strip-badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                        </svg>
                    </div>
                    <p className="managed-eyebrow">Wards</p>
                    <h2 id="managed-wards-heading">{wardCount ? `${wardCount} wards` : 'Ward directory'}</h2>
                    <p>{wardCount ? 'Find your ward, councillor and local services across all rural and urban wards.' : 'Ward profiles are being prepared for publication.'}</p>
                    <a className="managed-viewall" href={L('/wards')}>Explore our wards <span aria-hidden="true">→</span></a>
                </article>
                <article className="managed-strip-col" aria-labelledby="managed-officials-heading">
                    <div className="managed-strip-badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                    </div>
                    <p className="managed-eyebrow">Leadership</p>
                    <h2 id="managed-officials-heading">Council leadership</h2>
                    {officials.length ? <ul className="managed-mini-list">{officials.slice(0, 3).map(item => <li key={item.slug}><a href={P('officials', item.slug)}>{item.name}</a><span className="managed-title-sub"> — {item.title}</span></li>)}</ul> : <p>Leadership profiles are being prepared for publication.</p>}
                    <a className="managed-viewall" href={L('/officials')}>View council officials <span aria-hidden="true">→</span></a>
                </article>
                <article className="managed-strip-col" aria-labelledby="managed-contact-heading">
                    <div className="managed-strip-badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </div>
                    <p className="managed-eyebrow">Contact</p>
                    <h2 id="managed-contact-heading">Talk to the council</h2>
                    {contacts.length ? <ul className="managed-mini-list">{contacts.slice(0, 3).map((item, index) => <li key={`${item.office}-${item.type}-${index}`}><strong>{item.office}:</strong> {item.type === 'email' ? <a href={`mailto:${item.value}`}>{item.value}</a> : item.type === 'phone' ? <a href={`tel:${item.value}`}>{item.value}</a> : item.value}</li>)}</ul> : <p>Use our enquiry form and the team will respond.</p>}
                    <a className="managed-viewall" href={L('/contact')}>Make an enquiry <span aria-hidden="true">→</span></a>
                </article>
            </div>
        </section>
        {(tenders.length > 0 || investment.length > 0 || projects.length > 0 || departments.length > 0 || statistics.length > 0) && <section className="managed-section managed-alt" aria-label="More council information">
            <div className="container managed-columns">
                {tenders.length > 0 && <div className="managed-col-card"><h2>Tenders</h2><ul className="managed-mini-list">{tenders.slice(0, 3).map(item => <li key={item.slug}><a href={L(`/tenders/${item.slug}`)}>{item.title}</a> <span className="managed-ref-pill">({item.reference})</span></li>)}</ul><a className="managed-viewall" href={L('/tenders')}>View all tenders →</a></div>}
                {projects.length > 0 && <div className="managed-col-card"><h2>Projects</h2><ul className="managed-mini-list">{projects.slice(0, 3).map(item => <li key={item.slug}><a href={L(`/projects/${item.slug}`)}>{item.title}</a></li>)}</ul><a className="managed-viewall" href={L('/projects')}>View all projects →</a></div>}
                {investment.length > 0 && <div className="managed-col-card"><h2>Investment</h2><ul className="managed-mini-list">{investment.slice(0, 3).map(item => <li key={item.slug}><a href={P('investment', item.slug)}>{item.title}</a></li>)}</ul><a className="managed-viewall" href={L('/investment')}>Explore investment →</a></div>}
                {departments.length > 0 && <div className="managed-col-card"><h2>Departments</h2><ul className="managed-mini-list">{departments.slice(0, 4).map(item => <li key={item.id}><a href={L(`/departments/${item.id}`)}>{item.public_name}</a></li>)}</ul><a className="managed-viewall" href={L('/departments')}>View departments →</a></div>}
            </div>
        </section>}
    </>;
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

export function ExploreDirectory() {
    const locale = usePublicLocale();
    const L = (path: string) => `/${locale}${path}`;
    const groups: { title: string; blurb: string; links: { label: string; href: string; desc: string }[] }[] = [
        {
            title: 'Your Council',
            blurb: 'Who leads, decides and represents you.',
            links: [
                { label: 'Departments', href: '/departments', desc: 'What each department does' },
                { label: 'Officials', href: '/officials', desc: 'Management profiles' },
                { label: 'Wards', href: '/wards', desc: 'Find your ward & councillor' },
                { label: 'Meetings', href: '/meetings', desc: 'Agendas & minutes' },
                { label: 'Transparency', href: '/transparency', desc: 'Open governance' },
            ],
        },
        {
            title: 'Services & Living',
            blurb: 'Day-to-day services that keep Mutoko running.',
            links: [
                { label: 'All Services', href: '/services', desc: 'Water, roads, health & more' },
                { label: 'Projects', href: '/projects', desc: 'Development on the ground' },
                { label: 'Rates', href: '/rates', desc: 'Fees & payment info' },
                { label: 'Feedback', href: '/feedback', desc: 'Report & track issues' },
            ],
        },
        {
            title: 'News & Records',
            blurb: 'Stay informed and verify the record.',
            links: [
                { label: 'News', href: '/news', desc: 'Latest stories' },
                { label: 'Notices', href: '/notices', desc: 'Official announcements' },
                { label: 'Documents', href: '/documents', desc: 'Plans, reports & forms' },
            ],
        },
        {
            title: 'Grow With Us',
            blurb: 'Work, invest and visit Mutoko.',
            links: [
                { label: 'Investment', href: '/investment', desc: 'Solar, mining, horticulture' },
                { label: 'Tenders', href: '/tenders', desc: 'Do business with council' },
                { label: 'Vacancies', href: '/vacancies', desc: 'Join the team' },
                { label: 'Tourism', href: '/tourism', desc: 'Discover Mutoko' },
            ],
        },
    ];
    return (
        <section className="explore-directory" aria-labelledby="explore-heading">
            <div className="container">
                <div className="section-eyebrow-pill">
                    <span className="eyebrow-bar" aria-hidden="true" />
                    <span className="eyebrow-text">EXPLORE COUNCIL</span>
                </div>
                <h2 id="explore-heading" className="section-title">Everything in council, one glance</h2>
                <p className="section-subtext">Every service and office — organised by what you came to do.</p>
                <div className="explore-grid">
                    {groups.map(group => (
                        <article key={group.title} className="explore-card">
                            <h3>{group.title}</h3>
                            <p className="explore-blurb">{group.blurb}</p>
                            <ul>
                                {group.links.map(link => (
                                    <li key={link.label}>
                                        <a href={L(link.href)}>
                                            <span className="explore-link-label">{link.label}</span>
                                            <span className="explore-link-desc">{link.desc}</span>
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </article>
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

export function CitizenFeedbackBanner() {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const L = (path: string) => `/${locale}${path}`;
    return (
        <section className="citizen-feedback-section" aria-labelledby="feedback-callout-heading">
            <div className="container">
                <div className="citizen-feedback-card">
                    <div className="citizen-feedback-info">
                        <div className="citizen-feedback-pill">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <span>Citizen Participation &amp; Social Accountability</span>
                        </div>
                        <h2 id="feedback-callout-heading">{t('reportIssueTitle')}</h2>
                        <p>{t('reportIssueDesc')}</p>
                    </div>
                    <div>
                        <a href={L('/feedback')} className="btn-feedback-action">
                            <span>{t('reportIssueBtn')}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
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


