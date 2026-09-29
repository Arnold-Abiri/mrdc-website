import { useEffect, useRef, useState } from 'react';
import type { ReactNode } from 'react';

const navigation = ['Home', 'About', 'Council', 'Services', 'Development', 'Tourism', 'Media', 'Engagement'];

export default function PublicLayout({ children }: { children: ReactNode }) {
    const [open, setOpen] = useState(false);
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
            <a className="skip-link" href="#main">Skip to main content</a>

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
                        <a href="tel:+263712345678" className="utility-link">
                            <svg className="utility-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <span>+263 71 234 5678</span>
                        </a>

                        <a href="mailto:info@mutokordc.gov.zw" className="utility-link">
                            <svg className="utility-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <span>info@mutokordc.gov.zw</span>
                        </a>

                        <div className="utility-language-badge">
                            <span>English</span>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>

                        <div className="utility-socials" aria-label="Social media links">
                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" className="social-pill social-fb" aria-label="Facebook">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                            </a>
                            <a href="https://x.com" target="_blank" rel="noopener noreferrer" className="social-pill social-x" aria-label="X (Twitter)">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" className="social-pill social-yt" aria-label="YouTube">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {/* Site Header */}
            <header className="site-header">
                <div className="container header-inner">
                    <a className="identity" href="/" aria-label="Mutoko Rural District Council home">
                        <img
                            src="/images/logo.png"
                            srcSet="/images/logo@2x.png 2x"
                            alt="Mutoko Rural District Council Crest"
                            className="identity-logo-img"
                            width="40"
                            height="40"
                        />
                        <span className="identity-text">
                            <span className="identity-title">Mutoko</span>
                            <span className="identity-subtitle">Rural District Council</span>
                            <small className="identity-tagline">Service Delivery for Sustainable Communities</small>
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
                        {open ? 'Close menu' : 'Menu'}
                        <span className="menu-lines" aria-hidden="true">☰</span>
                    </button>

                    <nav id="primary-navigation" className={open ? 'navigation open' : 'navigation'} aria-label="Primary navigation">
                        {navigation.map(item =>
                            item === 'Home' ? (
                                <a key={item} aria-current="page" href="/" className="nav-item nav-item-active">
                                    <span>{item}</span>
                                    <span className="nav-active-indicator" aria-hidden="true" />
                                </a>
                            ) : (
                                <a key={item} href={`#${item.toLowerCase()}`} className="nav-item">
                                    {item}
                                </a>
                            )
                        )}
                        <a href="#search" className="header-search-pill" aria-label="Search council website">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <span>Search</span>
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

            <main id="main">{children}</main>

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
                                <strong className="footer-brand-subtitle">Rural District Council</strong>
                                <small className="footer-brand-tagline">Service Delivery for Sustainable Communities</small>
                            </div>
                        </div>
                        <p className="footer-about-text">
                            Working with our communities to deliver quality services, promote local development and build a better Mutoko.
                        </p>
                        <div className="footer-social-row" aria-label="Follow Mutoko Council">
                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" className="social-pill social-fb" aria-label="Facebook">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                                </svg>
                            </a>
                            <a href="https://x.com" target="_blank" rel="noopener noreferrer" className="social-pill social-x" aria-label="X (Twitter)">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                            <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" className="social-pill social-yt" aria-label="YouTube">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="13" height="13" aria-hidden="true">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">Quick Links</h3>
                        <ul className="footer-link-list">
                            <li><a href="/">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#services">Our Services</a></li>
                            <li><a href="#development">Development</a></li>
                            <li><a href="#tourism">Tourism</a></li>
                            <li><a href="#media">Media</a></li>
                            <li><a href="#contact">Contact Us</a></li>
                        </ul>
                    </div>

                    <div className="footer-col-nav">
                        <h3 className="footer-heading">Our Services</h3>
                        <ul className="footer-link-list">
                            <li><a href="#services-water">Water Supply</a></li>
                            <li><a href="#services-roads">Roads &amp; Infrastructure</a></li>
                            <li><a href="#services-health">Health &amp; Sanitation</a></li>
                            <li><a href="#services-environment">Environmental Management</a></li>
                            <li><a href="#services-development">Development Planning</a></li>
                            <li><a href="#services-community">Community Services</a></li>
                        </ul>
                    </div>

                    <div className="footer-col-contact">
                        <h3 className="footer-heading">Contact Us</h3>
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

                            <a href="tel:+263712345678" className="footer-contact-item footer-contact-link">
                                <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                <span>+263 71 234 5678</span>
                            </a>

                            <a href="mailto:info@mutokordc.gov.zw" className="footer-contact-item footer-contact-link">
                                <svg className="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>
                                <span>info@mutokordc.gov.zw</span>
                            </a>
                        </div>
                    </div>
                </div>

                {/* Dark Blue Copyright Bar */}
                <div className="footer-bottom-bar">
                    <div className="container footer-bottom-inner">
                        <span className="footer-copyright">
                            © {new Date().getFullYear()} Mutoko Rural District Council. All Rights Reserved.
                        </span>
                        <div className="footer-legal-links">
                            <a href="#privacy">Privacy Policy</a>
                            <span className="footer-legal-divider" aria-hidden="true">|</span>
                            <a href="#terms">Terms of Use</a>
                            <span className="footer-legal-divider" aria-hidden="true">|</span>
                            <a href="/sitemap.xml">Site Map</a>
                        </div>
                    </div>
                </div>

                {/* Bottom African Pattern Banner */}
                <div className="bottom-pattern-banner" aria-hidden="true" />
            </footer>
        </>
    );
}
