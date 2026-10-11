import { useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import AccessibilitySettings from '../Components/public/AccessibilitySettings';
import { ConnectBanner } from '../Components/public/PageHeader';
import { normalizeLocale, translate } from '../localization';
import type { ReactNode } from 'react';
import { useSiteSettings, whatsappLinkFor, type SiteSettings } from '../useSiteSettings';

type NavChild = { label: string; href: string };
type NavEntry = { label: string; href: string; children?: NavChild[] };

const navigationTranslationKeys: Record<string, Parameters<typeof translate>[1]> = {
    Departments: 'councilDepartments', Officials: 'officials', Wards: 'wards', Meetings: 'meetings',
    'Financial Transparency': 'transparency', 'All Services': 'services', Projects: 'projects',
    Rates: 'rates', Feedback: 'feedback', News: 'news', Notices: 'notices', Gallery: 'gallery',
    Documents: 'documents', Investment: 'investment', Tenders: 'tenders', Vacancies: 'vacancies',
    Tourism: 'tourism',
};

const navigation: NavEntry[] = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    {
        label: 'Council',
        href: '/departments',
        children: [
            { label: 'Departments', href: '/departments' },
            { label: 'Officials', href: '/officials' },
            { label: 'Wards', href: '/wards' },
            { label: 'Meetings', href: '/meetings' },
            { label: 'Financial Transparency', href: '/transparency' },
        ],
    },
    {
        label: 'Services',
        href: '/services',
        children: [
            { label: 'All Services', href: '/services' },
            { label: 'Projects', href: '/projects' },
            { label: 'Rates', href: '/rates' },
            { label: 'FAQs', href: '/faqs' },
            { label: 'Feedback', href: '/feedback' },
        ],
    },
    {
        label: 'Media',
        href: '/news',
        children: [
            { label: 'News', href: '/news' },
            { label: 'Notices', href: '/notices' },
            { label: 'Gallery', href: '/galleries' },
            { label: 'Documents', href: '/documents' },
        ],
    },
    {
        label: 'Opportunities',
        href: '/investment',
        children: [
            { label: 'Investment', href: '/investment' },
            { label: 'Tenders', href: '/tenders' },
            { label: 'Vacancies', href: '/vacancies' },
            { label: 'Tourism', href: '/tourism' },
        ],
    },
    { label: 'Contact', href: '/contact' },
];

type TopbarNotice = { slug: string; title: string; is_urgent?: boolean };

function TopbarTicker({ notices = [] }: { notices?: TopbarNotice[] }) {
    const locale = normalizeLocale(usePage().props.locale);
    const t = (key: Parameters<typeof translate>[1]) => translate(locale, key);
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPaused, setIsPaused] = useState(false);

    const items: TopbarNotice[] = notices.length > 0 ? notices : [
        {
            slug: 'welcome',
            title: t('welcomeTicker'),
            is_urgent: false,
        },
    ];

    useEffect(() => {
        if (isPaused || items.length <= 1) return;
        const interval = setInterval(() => {
            setCurrentIndex((prev) => (prev + 1) % items.length);
        }, 5500);
        return () => clearInterval(interval);
    }, [isPaused, items.length]);

    const active = items[currentIndex] || items[0];

    return (
        <div
            className="topbar-ticker"
            role="region"
            aria-label={t('councilAnnouncements')}
            onMouseEnter={() => setIsPaused(true)}
            onMouseLeave={() => setIsPaused(false)}
            onFocusCapture={() => setIsPaused(true)}
            onBlurCapture={() => setIsPaused(false)}
        >
            <span className={`topbar-ticker-badge ${active.is_urgent ? 'badge-urgent' : 'badge-notice'}`}>
                {active.is_urgent ? t('urgentAlert') : t('noticeSingular')}
            </span>
            <div className="topbar-ticker-content" aria-live="polite" aria-atomic="true">
                {active.slug !== 'welcome' ? (
                    <a href={L(`/notices/${active.slug}`)} className="topbar-ticker-link">
                        <span className="topbar-ticker-title">{active.title}</span>
                        <span className="topbar-ticker-arrow" aria-hidden="true">→</span>
                    </a>
                ) : (
                    <span className="topbar-ticker-text">{active.title}</span>
                )}
            </div>
            {items.length > 1 && (
                <div className="topbar-ticker-controls" aria-label={t('councilAnnouncements')}>
                    <button
                        type="button"
                        className="topbar-ticker-arrow-btn"
                        onClick={() => setCurrentIndex((prev) => (prev - 1 + items.length) % items.length)}
                        aria-label={t('previousAnnouncement')}
                    >
                        ‹
                    </button>
                    <span className="topbar-ticker-index" aria-hidden="true">
                        {currentIndex + 1}/{items.length}
                    </span>
                    <button
                        type="button"
                        className="topbar-ticker-arrow-btn"
                        onClick={() => setCurrentIndex((prev) => (prev + 1) % items.length)}
                        aria-label={t('nextAnnouncement')}
                    >
                        ›
                    </button>
                </div>
            )}
        </div>
    );
}

export default function PublicLayout({ children }: { children: ReactNode }) {
    const page = usePage();
    const { locale: rawLocale, localeCsrfToken, urgent_alerts: urgentAlerts, topbar_notices: topbarNotices } = page.props as { locale?: string; localeCsrfToken?: string; urgent_alerts?: { slug: string; title: string }[]; topbar_notices?: TopbarNotice[] };
    const locale = normalizeLocale(rawLocale);
    const t = (key: Parameters<typeof translate>[1]) => translate(locale, key);
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    const settings: SiteSettings = useSiteSettings();
    const logoMediaId = settings.logo_media_id?.trim();
    const logoUrl = logoMediaId && /^\d+$/.test(logoMediaId)
        ? L(`/managed-media/${logoMediaId}`)
        : '/images/council-crest-light.webp';
    const footerPhone = settings.phone?.trim() || null;
    const footerEmail = settings.email?.trim() || null;
    const footerAddress = settings.physical_address?.trim() || null;
    const footerHours = settings.office_hours?.trim() || null;
    const footerWhatsapp = whatsappLinkFor(settings.whatsapp_number);
    const socialLinks = [
        settings.facebook_url?.trim() ? { href: settings.facebook_url.trim(), label: 'Facebook', path: 'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z' } : null,
        settings.x_url?.trim() ? { href: settings.x_url.trim(), label: 'X', path: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' } : null,
        settings.youtube_url?.trim() ? { href: settings.youtube_url.trim(), label: 'YouTube', path: 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' } : null,
    ].filter((entry): entry is { href: string; label: string; path: string } => entry !== null);
    const [open, setOpen] = useState(false);

    // Dynamic current path detection without locale prefix
    const currentUrl = page.url || '/';
    const rawPathname = currentUrl.split('?')[0].split('#')[0] || '/';
    const normalizedCurrentPath = rawPathname.replace(new RegExp(`^/(?:en|sn|nd)(?=/|$)`), '') || '/';

    const isPathActive = (href: string) => {
        if (href === '/') return normalizedCurrentPath === '/';
        return normalizedCurrentPath === href || normalizedCurrentPath.startsWith(`${href}/`);
    };

    const isEntryActive = (item: NavEntry) => {
        if (isPathActive(item.href)) return true;
        return Boolean(item.children?.some(child => isPathActive(child.href)));
    };

    useEffect(() => { document.documentElement.lang = locale; }, [locale]);
    const menuButton = useRef<HTMLButtonElement>(null);

    const [searchOpen, setSearchOpen] = useState(false);
    const searchInputRef = useRef<HTMLInputElement>(null);
    const openSearchBtnRef = useRef<HTMLButtonElement>(null);

    useEffect(() => {
        if (!open) return;
        const closeOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setOpen(false);
                menuButton.current?.focus();
            }
        };
        document.addEventListener('keydown', closeOnEscape);
        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [open]);

    useEffect(() => {
        if (!searchOpen) return;
        document.body.style.overflow = 'hidden';
        const timer = setTimeout(() => {
            searchInputRef.current?.focus();
        }, 60);

        const closeSearchOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setSearchOpen(false);
                openSearchBtnRef.current?.focus();
            }
        };
        document.addEventListener('keydown', closeSearchOnEscape);

        return () => {
            document.body.style.overflow = '';
            clearTimeout(timer);
            document.removeEventListener('keydown', closeSearchOnEscape);
        };
    }, [searchOpen]);

    const openSearch = () => {
        setOpen(false);
        setSearchOpen(true);
    };

    const closeSearch = () => {
        setSearchOpen(false);
        openSearchBtnRef.current?.focus();
    };

    return (
        <>
            <a className="skip-link" href="#main">{t('skip')}</a>
            {normalizedCurrentPath !== '/' && urgentAlerts && urgentAlerts.length > 0 && <div className="urgent-alert" role="alert"><p><strong>{t('urgentNotice')}: </strong>{urgentAlerts.map((alert, index) => <span key={alert.slug}>{index > 0 && ' — '}<a href={L(`/notices/${alert.slug}`)}>{alert.title}</a></span>)}</p></div>}

            {/* Top Utility Bar */}
            <div className="utility-bar">
                <div className="container utility-inner">
                    <TopbarTicker notices={topbarNotices} />

                    <div className="utility-actions">
                        <a href={L('/contact')} className="utility-location" title={t('locationTitle')}>
                            <svg className="utility-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                <circle cx="12" cy="9" r="2.5" />
                            </svg>
                            <span>{t('locationShort')}</span>
                        </a>

                        <AccessibilitySettings />

                        <form action="/locale" method="post" className="utility-language-badge">
                            <input type="hidden" name="_token" value={localeCsrfToken ?? ''} />
                            <label htmlFor="public-locale" className="sr-only">{t('language')}</label>
                            <select id="public-locale" name="locale" value={locale} onChange={event => event.currentTarget.form?.requestSubmit()}>
                                <option value="en">{t('english')}</option>
                                <option value="sn">{t('shona')}</option>
                                <option value="nd">{t('ndebele')}</option>
                            </select>
                            <noscript><button type="submit">{t('setLanguage')}</button></noscript>
                        </form>
                    </div>
                </div>
            </div>

            {/* Site Header */}
            <header className="site-header">
                <div className="container header-inner">
                    <a className="identity" href={L("/")} aria-label={t('councilHome')}>
                        <img
                            src={logoUrl}
                            alt={t('brandingAlt')}
                            className="identity-logo-img"
                            width="48"
                            height="48"
                        />
                        <span className="identity-text">
                            <span className="identity-title">Mutoko</span>
                            <span className="identity-subtitle">{t('councilName')}</span>
                            <small className="identity-tagline">{t('tagline')}</small>
                        </span>
                    </a>

                    <div className="header-actions">
                        <button
                            ref={openSearchBtnRef}
                            type="button"
                            className="header-search-icon-btn"
                            aria-label={t('search')}
                            title={t('search')}
                            onClick={openSearch}
                        >
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2.3" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <line x1="16.5" y1="16.5" x2="21" y2="21" />
                            </svg>
                        </button>

                        <button
                            ref={menuButton}
                            className="menu-toggle"
                            type="button"
                            aria-expanded={open}
                            aria-controls="primary-navigation"
                            onClick={() => setOpen(!open)}
                        >
                            {open ? t('closeMenu') : t('menu')}
                            <span className="menu-lines" aria-hidden="true">☰</span>
                        </button>
                    </div>

                    <nav id="primary-navigation" className={open ? 'navigation open' : 'navigation'} aria-label={t('primaryNavigation')}>
                        {navigation.map(item => {
                            const active = isEntryActive(item);
                            if (item.label === 'Home') {
                                return (
                                    <a
                                        key="home"
                                        aria-current={active ? 'page' : undefined}
                                        href={L("/")}
                                        className={`nav-item${active ? ' nav-item-active' : ''}`}
                                    >
                                        <span>{t('home')}</span>
                                        {active && <span className="nav-active-indicator" aria-hidden="true" />}
                                    </a>
                                );
                            }

                            if (item.children) {
                                return (
                                    <div key={item.label} className="nav-group">
                                        <a
                                            href={L(item.href)}
                                            className={`nav-item nav-parent${active ? ' nav-item-active' : ''}`}
                                            aria-haspopup="true"
                                            aria-current={isPathActive(item.href) ? 'page' : undefined}
                                        >
                                            <span>{t(item.label.toLowerCase() as Parameters<typeof translate>[1])}</span>
                                            <span className="nav-caret" aria-hidden="true">▾</span>
                                            {active && <span className="nav-active-indicator" aria-hidden="true" />}
                                        </a>
                                        <ul className="nav-dropdown" aria-label={t(item.label.toLowerCase() as Parameters<typeof translate>[1])}>
                                            {item.children.map(child => {
                                                const childActive = isPathActive(child.href);
                                                return (
                                                    <li key={child.label}>
                                                        <a
                                                            href={L(child.href)}
                                                            className={`nav-dropdown-link${childActive ? ' nav-dropdown-link-active' : ''}`}
                                                            aria-current={childActive ? 'page' : undefined}
                                                        >
                                                            {locale !== 'en' && navigationTranslationKeys[child.label] ? t(navigationTranslationKeys[child.label]) : child.label}
                                                        </a>
                                                    </li>
                                                );
                                            })}
                                        </ul>
                                    </div>
                                );
                            }

                            if (item.label === 'Contact') {
                                return (
                                    <a
                                        key={item.label}
                                        href={L(item.href)}
                                        className={`nav-cta${active ? ' nav-cta-active' : ''}`}
                                        aria-current={active ? 'page' : undefined}
                                    >
                                        {t('contact')}
                                    </a>
                                );
                            }

                            return (
                                <a
                                    key={item.label}
                                    href={L(item.href)}
                                    className={`nav-item${active ? ' nav-item-active' : ''}`}
                                    aria-current={active ? 'page' : undefined}
                                >
                                    <span>{t(item.label.toLowerCase() as Parameters<typeof translate>[1])}</span>
                                    {active && <span className="nav-active-indicator" aria-hidden="true" />}
                                </a>
                            );
                        })}
                    </nav>
                </div>

                {/* Curved bottom edge matching Theme 1 mockup */}
                <div className="header-bottom-curve" aria-hidden="true">
                    <svg
                        viewBox="0 0 1440 40"
                        preserveAspectRatio="none"
                        fill="currentColor"
                        className="header-curve-svg"
                    >
                        <path d="M0,0 L1440,0 L1440,16 C1240,36 960,40 720,40 C480,40 200,36 0,16 Z" />
                    </svg>
                </div>
            </header>

            {/* Harare-style Fullscreen Search Modal Overlay */}
            <div
                id="search-overlay"
                className={`search-overlay${searchOpen ? ' open' : ''}`}
                role="dialog"
                aria-modal="true"
                aria-label={t('search')}
                aria-hidden={!searchOpen}
                onClick={(e) => { if (e.target === e.currentTarget) closeSearch(); }}
            >
                <button
                    type="button"
                    className="search-overlay-close"
                    aria-label={t('closeMenu')}
                    onClick={closeSearch}
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>

                <div className="search-overlay-box">
                    <div className="search-overlay-label">
                        {t('searchOverlayTitle')}
                    </div>

                    <form className="search-overlay-form" action={`/${locale}/search`} method="get" role="search">
                        <svg className="search-overlay-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="16.5" y1="16.5" x2="21" y2="21" />
                        </svg>

                        <input
                            ref={searchInputRef}
                            className="search-overlay-input"
                            id="search-overlay-input"
                            type="search"
                            name="q"
                            placeholder={t('searchOverlayPlaceholder')}
                            autoComplete="off"
                            aria-label={t('searchCouncilPages')}
                        />

                        <button className="search-overlay-submit" type="submit" aria-label={t('search')}>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </form>

                    <div className="search-overlay-hints" aria-label={t('searchSuggestions')}>
                        <span className="search-hints-title">{t('popularSearches')}</span>
                        <a className="search-hint" href={L('/rates')}>{t('ratesPayments')}</a>
                        <a className="search-hint" href={L('/services')}>{t('councilServices')}</a>
                        <a className="search-hint" href={L('/tenders')}>{t('tendersProcurementShort')}</a>
                        <a className="search-hint" href={L('/notices')}>{t('publicNotices')}</a>
                        <a className="search-hint" href={L('/documents')}>{t('documentsBylawsShort')}</a>
                        <a className="search-hint" href={L('/wards')}>{t('wardsCouncillorsShort')}</a>
                        <a className="search-hint" href={L('/feedback')}>{t('feedbackFaults')}</a>
                    </div>
                </div>
            </div>

            <main id="main" tabIndex={-1}>
                {children}
                {normalizedCurrentPath !== '/' && <ConnectBanner />}
            </main>

            {/* Modern Clean Footer Matching Mockup */}
            <footer className="site-footer">
                <div className="container footer-grid">
                    <div className="footer-col-about">
                        <a className="footer-brand" href={L('/')} aria-label={t('councilHome')}>
                            <img
                                src={logoUrl}
                                alt=""
                                aria-hidden="true"
                                className="footer-logo-img"
                                width="52"
                                height="52"
                            />
                            <span className="footer-brand-text">
                                <strong className="footer-brand-title">Mutoko</strong>
                                <strong className="footer-brand-subtitle">{t('councilName')}</strong>
                                <small className="footer-brand-tagline">{t('tagline')}</small>
                            </span>
                        </a>
                        <p className="footer-about-text">
                            {t('footerAbout')}
                        </p>
                        <div className="footer-social-row" aria-label={t('socialPreviews')}>
                            {socialLinks.length > 0 ? socialLinks.map(link => (
                                <a key={link.label} className="social-pill" href={link.href} target="_blank" rel="noopener noreferrer" aria-label={link.label}>
                                    <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                        <path d={link.path} />
                                    </svg>
                                </a>
                            )) : (
                                <>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                                </svg>
                            </span>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </span>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </span>
                                </>
                            )}
                        </div>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">{t('quickLinks')}</h3>
                        <ul className="footer-link-list">
                            <li><a href={L("/")}>{t('home')}</a></li>
                            <li><a href={L("/about")}>{t('about')}</a></li>
                            <li><a href={L("/services")}>{t('ourServices')}</a></li>
                            <li><a href={L("/faqs")}>FAQs</a></li>
                            <li><a href={L("/investment")}>{t('development')}</a></li>
                            <li><a href={L("/tourism")}>{t('tourism')}</a></li>
                            <li><a href={L("/news")}>{t('media')}</a></li>
                            <li><a href={L("/contact")}>{t('contactUs')}</a></li>
                        </ul>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">{t('ourServices')}</h3>
                        <ul className="footer-link-list">
                            <li><a href={L("/services/business-centre-servicing")}>{t('waterSupply')}</a></li>
                            <li><a href={L("/services/roads-and-works")}>{t('roadsInfrastructure')}</a></li>
                            <li><a href={L("/services/health-services")}>{t('healthSanitation')}</a></li>
                            <li><a href={L("/services/environmental-protection")}>{t('environmentalManagement')}</a></li>
                            <li><a href={L("/services/property-management")}>{t('developmentPlanning')}</a></li>
                            <li><a href={L("/services/welfare-services")}>{t('communityServices')}</a></li>
                        </ul>
                    </div>

                    <div className="footer-col-contact">
                        <h3 className="footer-heading">{t('contactUs')}</h3>
                        <div className="footer-contact-list">
                            <div className="footer-contact-item">
                                <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <div>
                                    <p className="footer-contact-org">Mutoko Rural District Council</p>
                                    {footerAddress ? (
                                        footerAddress.split('\n').filter(line => line.trim() && line.trim().toLowerCase() !== 'mutoko rural district council').map((line, index) => <p key={index} className="footer-contact-subline">{line}</p>)
                                    ) : (
                                        <>
                                            <p className="footer-contact-subline">{t('hqAddress')}</p>
                                            <p className="footer-contact-subline">{t('addressRegion')}</p>
                                            <p className="footer-contact-subline">{t('addressCountry')}</p>
                                        </>
                                    )}
                                </div>
                            </div>
                            {footerPhone && (
                                <div className="footer-contact-item">
                                    <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                    <div><p><a href={`tel:${footerPhone.replace(/\s+/g, '')}`}>{footerPhone}</a></p></div>
                                </div>
                            )}
                            {footerEmail && (
                                <div className="footer-contact-item">
                                    <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                        <rect x="2" y="4" width="20" height="16" rx="2" />
                                        <path d="m22 7-10 6L2 7" />
                                    </svg>
                                    <div><p><a href={`mailto:${footerEmail}`}>{footerEmail}</a></p></div>
                                </div>
                            )}
                            {footerWhatsapp && (
                                <div className="footer-contact-item">
                                    <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z" />
                                    </svg>
                                    <div><p><a href={footerWhatsapp} target="_blank" rel="noopener noreferrer">{t('whatsappDesk')}</a></p></div>
                                </div>
                            )}
                            {footerHours && (
                                <div className="footer-contact-item">
                                    <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    <div><p>{footerHours}</p></div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Dark Blue Copyright Bar */}
                <div className="footer-bottom-bar">
                    <div className="container footer-bottom-inner">
                        <span className="footer-copyright">
                            {t('previewCopyright')}
                        </span>
                        <div className="footer-legal-links">
                            <a href={L("/transparency")}>{t('privacyPolicy')}</a>
                            <span className="footer-legal-divider" aria-hidden="true">|</span>
                            <a href={L("/transparency")}>{t('termsOfUse')}</a>
                            <span className="footer-legal-divider" aria-hidden="true">|</span>
                            <a href="/sitemap.xml">{t('siteMap')}</a>
                        </div>
                    </div>
                </div>

                {/* Bottom African Pattern Banner */}
                <div className="bottom-pattern-banner" style={{ backgroundImage: 'url(/images/bottom-pattern.png)' }} aria-hidden="true" />
            </footer>
        </>
    );
}
