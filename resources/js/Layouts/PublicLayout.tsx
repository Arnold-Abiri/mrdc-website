import { useState } from 'react';
import type { ReactNode } from 'react';

const navigation = ['Home', 'About', 'Council', 'Services', 'Development', 'Tourism', 'Media', 'Engagement'];

export default function PublicLayout({ children }: { children: ReactNode }) {
    const [open, setOpen] = useState(false);
    return <>
        <a className="skip-link" href="#main">Skip to main content</a>
        <div className="utility-bar"><div className="container">Official website of Mutoko Rural District Council <span>English</span></div></div>
        <header className="site-header"><div className="container header-inner">
            <a className="identity" href="/" aria-label="Mutoko Rural District Council home"><span className="identity-mark" aria-hidden="true">MRDC</span><span>Mutoko Rural<br/>District Council</span></a>
            <div className="site-tools" aria-label="Site tools">
                <label>Search <input type="search" placeholder="Available with published content" disabled /></label>
                <label>Language <select disabled defaultValue="en"><option value="en">English</option></select></label>
            </div>
            <button className="menu-toggle" type="button" aria-expanded={open} aria-controls="primary-navigation" onClick={() => setOpen(!open)}>{open ? 'Close menu' : 'Menu'}</button>
            <nav id="primary-navigation" className={open ? 'navigation open' : 'navigation'} aria-label="Primary navigation">
                {navigation.map(item => item === 'Home' ? <a key={item} aria-current="page" href="/">{item}</a> : <span key={item} className="nav-future">{item}</span>)}
            </nav>
        </div></header>
        <main id="main">{children}</main>
        <footer className="site-footer"><div className="container"><strong>Mutoko Rural District Council</strong><p>Service Delivery for Sustainable Communities</p><small>Official council website</small></div></footer>
    </>;
}
