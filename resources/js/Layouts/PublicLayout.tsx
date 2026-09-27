import { useEffect, useRef, useState } from 'react';
import type { ReactNode } from 'react';

const navigation = ['Home', 'About', 'Council', 'Services', 'Development', 'Tourism', 'Media', 'Engagement'];

export default function PublicLayout({ children }: { children: ReactNode }) {
    const [open, setOpen] = useState(false);
    const menuButton = useRef<HTMLButtonElement>(null);
    useEffect(() => {
        if (!open) return;
        const closeOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') { setOpen(false); menuButton.current?.focus(); }
        };
        document.addEventListener('keydown', closeOnEscape);
        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [open]);
    return <>
        <a className="skip-link" href="#main">Skip to main content</a>
        <div className="utility-bar"><div className="container utility-inner"><span>Official website of Mutoko Rural District Council</span><span className="utility-language">English <span aria-hidden="true">⌄</span></span></div></div>
        <header className="site-header"><div className="container header-inner">
            <a className="identity" href="/" aria-label="Mutoko Rural District Council home"><span className="identity-mark" aria-hidden="true">MRDC</span><span className="identity-name">Mutoko Rural<br/>District Council<small>Official council website</small></span></a>
            <button ref={menuButton} className="menu-toggle" type="button" aria-expanded={open} aria-controls="primary-navigation" onClick={() => setOpen(!open)}>{open ? 'Close menu' : 'Menu'}<span className="menu-lines" aria-hidden="true">☰</span></button>
            <nav id="primary-navigation" className={open ? 'navigation open' : 'navigation'} aria-label="Primary navigation">
                {navigation.map(item => item === 'Home' ? <a key={item} aria-current="page" href="/">{item}</a> : <span key={item} className="nav-future" aria-disabled="true">{item}</span>)}
                <span className="search-affordance" aria-label="Search will be available with published content" title="Search will be available with published content">⌕</span>
            </nav>
        </div></header>
        <main id="main">{children}</main>
        <footer className="site-footer"><div className="container footer-grid">
            <div className="footer-identity"><strong>Mutoko Rural District Council</strong><p>Service Delivery for Sustainable Communities</p><small>Official council website</small></div>
            <div><h2>Explore</h2><a href="/">Home</a><span>About Council</span><span>Development</span><span>Tourism</span></div>
            <div><h2>Public information</h2><span>Services</span><span>Tenders</span><span>Vacancies</span><span>News &amp; events</span></div>
            <div><h2>Contact &amp; policies</h2><p>Verified contact details will appear when supplied by the council.</p><span>Privacy</span><span>Accessibility</span><span>Sitemap</span></div>
        </div><div className="container footer-bottom"><span>© Mutoko Rural District Council</span><span>Public information subject to council approval</span></div></footer>
    </>;
}
