import { useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import AccessibilitySettings from '../Components/public/AccessibilitySettings';
import { normalizeLocale, translate } from '../localization';
import type { ReactNode } from 'react';

type NavChild = { label: string; href: string };
type NavEntry = { label: string; href: string; children?: NavChild[] };

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
            { label: 'Transparency', href: '/transparency' },
        ],
    },
    {
        label: 'Services',
        href: '/services',
        children: [
            { label: 'All Services', href: '/services' },
            { label: 'Projects', href: '/projects' },
            { label: 'Rates', href: '/rates' },
            { label: 'Feedback', href: '/feedback' },
        ],
    },
    {
        label: 'Media',
        href: '/news',
        children: [
            { label: 'News', href: '/news' },
            { label: 'Notices', href: '/notices' },
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

export default function PublicLayout({ children }: { children: ReactNode }) {
    const { locale: rawLocale, localeCsrfToken, urgent_alerts: urgentAlerts } = usePage().props as { locale?: string; localeCsrfToken?: string; urgent_alerts?: { slug: string; title: string }[] };
    const locale = normalizeLocale(rawLocale);
    const t = (key: Parameters<typeof translate>[1]) => translate(locale, key);
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;
    const [open, setOpen] = useState(false);

    useEffect(() => { document.documentElement.lang = locale; }, [locale]);
    const menuButton = useRef<HTMLButtonElement>(null);

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

    return (
        <>
            <a className="skip-link" href="#main">{t('skip')}</a>
            {urgentAlerts && urgentAlerts.length > 0 && <div className="urgent-alert" role="alert"><p><strong>{t('urgentNotice')}: </strong>{urgentAlerts.map((alert, index) => <span key={alert.slug}>{index > 0 && ' — '}<a href={L(`/notices/${alert.slug}`)}>{alert.title}</a></span>)}</p></div>}

            {/* Top Utility Bar */}
            <div className="utility-bar">
                <div className="container utility-inner">
                    <div className="utility-location">
                        <svg className="utility-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                            <circle cx="12" cy="9" r="2.5" />
                        </svg>
                        <span>Mutoko, Mashonaland East, Zimbabwe</span>
                    </div>

                    <div className="utility-actions">
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

                        <div className="utility-socials" aria-label={t('socialPreviews')}>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                            </span>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </span>
                            <span className="social-pill" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Site Header */}
            <header className="site-header">
                <div className="container header-inner">
                    <a className="identity" href={L("/")} aria-label={t('councilHome')}>
                        <img
                            src="/images/logo.png"
                            srcSet="/images/logo@2x.png 2x"
                            alt={t('brandingAlt')}
                            className="identity-logo-img"
                            width="40"
                            height="40"
                        />
                        <span className="identity-text">
                            <span className="identity-title">Mutoko</span>
                            <span className="identity-subtitle">{t('councilName')}</span>
                            <small className="identity-tagline">{t('tagline')}</small>
                        </span>
                    </a>

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

                    <nav id="primary-navigation" className={open ? 'navigation open' : 'navigation'} aria-label={t('primaryNavigation')}>
                        {navigation.map(item =>
                            item.label === 'Home' ? (
                                <a key="home" aria-current="page" href={L("/")} className="nav-item nav-item-active">
                                    <span>{t('home')}</span>
                                    <span className="nav-active-indicator" aria-hidden="true" />
                                </a>
                            ) : item.children ? (
                                <div key={item.label} className="nav-group">
                                    <a href={L(item.href)} className="nav-item nav-parent">
                                        {t(item.label.toLowerCase() as Parameters<typeof translate>[1])}
                                        <span className="nav-caret" aria-hidden="true">▾</span>
                                    </a>
                                    <ul className="nav-dropdown" aria-label={item.label}>
                                        {item.children.map(child => (
                                            <li key={child.label}>
                                                <a href={L(child.href)} className="nav-dropdown-link">
                                                    {child.label}
                                                </a>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ) : item.label === 'Contact' ? (
                                <a key={item.label} href={L(item.href)} className="nav-cta">{t('contact')}</a>
                            ) : (
                                <a key={item.label} href={L(item.href)} className="nav-item">
                                    {t(item.label.toLowerCase() as Parameters<typeof translate>[1])}
                                </a>
                            )
                        )}
                        <a href={L("/search")} className="nav-search-pill" aria-label={t('search')}>
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <line x1="16.5" y1="16.5" x2="21" y2="21" />
                            </svg>
                            <span>{t('search')}</span>
                        </a>
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

            <main id="main" tabIndex={-1}>{children}</main>

            {/* Modern Clean Footer Matching Mockup */}
            <footer className="site-footer">
                <div className="container footer-grid">
                    <div className="footer-col-about">
                        <div className="footer-brand">
                            <img
                                src="/images/logo.png"
                                srcSet="/images/logo@2x.png 2x"
                                alt=""
                                aria-hidden="true"
                                className="footer-logo-img"
                                width="44"
                                height="44"
                            />
                            <div className="footer-brand-text">
                                <strong className="footer-brand-title">Mutoko</strong>
                                <strong className="footer-brand-subtitle">{t('councilName')}</strong>
                                <small className="footer-brand-tagline">{t('tagline')}</small>
                            </div>
                        </div>
                        <p className="footer-about-text">
                            {t('footerAbout')}
                        </p>
                        <div className="footer-social-row" aria-label={t('socialPreviews')}>
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
                        </div>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">{t('quickLinks')}</h3>
                        <ul className="footer-link-list">
                            <li><a href={L("/")}>{t('home')}</a></li>
                            <li><a href={L("/#about")}>{t('about')}</a></li>
                            <li><a href={L("/#services")}>{t('ourServices')}</a></li>
                            <li><a href={L("/investment")}>{t('development')}</a></li>
                            <li><a href={L("/tourism")}>{t('tourism')}</a></li>
                            <li><a href={L("/news")}>{t('media')}</a></li>
                            <li><a href={L("/contact")}>{t('contactUs')}</a></li>
                        </ul>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">{t('ourServices')}</h3>
                        <ul className="footer-link-list">
                            <li><a href={L("/services")}>{t('waterSupply')}</a></li>
                            <li><a href={L("/projects")}>{t('roadsInfrastructure')}</a></li>
                            <li><a href={L("/services")}>{t('healthSanitation')}</a></li>
                            <li><a href={L("/services")}>{t('environmentalManagement')}</a></li>
                            <li><a href={L("/investment")}>{t('developmentPlanning')}</a></li>
                            <li><a href={L("/feedback")}>{t('communityServices')}</a></li>
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
                                    <p>Mutoko Rural District Council</p>
                                    <p>Mutoko, Mashonaland East</p>
                                    <p>Zimbabwe</p>
                                </div>
                            </div>

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
