import { Link } from '@inertiajs/react';
import { usePublicLocale } from '../../usePublicTranslation';

export function PageHero({ eyebrow, title, subtitle, crumb }: { eyebrow: string; title: string; subtitle?: string; crumb: { label: string; href?: string }[] }) {
    const locale = usePublicLocale();
    return (
        <header className="about-page-hero">
            <div className="about-page-hero-overlay" aria-hidden="true" />
            <div className="container">
                <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                    <Link href={`/${locale}`}>Home</Link>
                    {crumb.map((c, i) => (
                        <span key={i} style={{ display: 'contents' }}>
                            <span aria-hidden="true">/</span>
                            {c.href ? <Link href={c.href}>{c.label}</Link> : <span aria-current="page">{c.label}</span>}
                        </span>
                    ))}
                </nav>
                <span className="about-accent-eyebrow">{eyebrow}</span>
                <h1>{title}</h1>
                {subtitle && <p className="about-page-hero-subtitle">{subtitle}</p>}
            </div>
        </header>
    );
}

export function ConnectBanner({ title = 'Connect with Your Council', text = 'Questions about this page? Reach the right team through one enquiry form.' }: { title?: string; text?: string }) {
    const locale = usePublicLocale();
    return (
        <section className="about-connect-cta" aria-labelledby="connect-banner">
            <div className="container">
                <div className="about-connect-inner">
                    <div className="about-connect-info">
                        <div>
                            <h2 id="connect-banner">{title}</h2>
                            <p>{text}</p>
                        </div>
                    </div>
                    <Link href={`/${locale}/contact`} className="btn-connect-gold">
                        <span>Contact Us</span>
                        <span aria-hidden="true">→</span>
                    </Link>
                </div>
            </div>
        </section>
    );
}

export function EmptyState({ title, text }: { title: string; text: string }) {
    return (
        <div className="about-vision-card" role="status">
            <div>
                <h3>{title}</h3>
                <p>{text}</p>
            </div>
        </div>
    );
}
