import { Link } from '@inertiajs/react';
import { usePublicLocale, usePublicTranslation } from '../../usePublicTranslation';
import { extractSources, SourceButtons } from './SourceButtons';

export function PageHero({ eyebrow, title, subtitle, crumb }: { eyebrow: string; title: string; subtitle?: string; crumb: { label: string; href?: string }[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const heroCopy = extractSources(subtitle);
    return (
        <header className="about-page-hero">
            <div className="about-page-hero-overlay" aria-hidden="true" />
            <div className="container">
                <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                    <Link href={`/${locale}`}>{t('home')}</Link>
                    {crumb.map((c, i) => (
                        <span key={i} style={{ display: 'contents' }}>
                            <span aria-hidden="true">/</span>
                            {c.href ? <Link href={c.href}>{c.label}</Link> : <span aria-current="page">{c.label}</span>}
                        </span>
                    ))}
                </nav>
                <span className="about-accent-eyebrow">{eyebrow}</span>
                <h1>{title}</h1>
                {heroCopy.body && <p className="about-page-hero-subtitle">{heroCopy.body}</p>}
                {heroCopy.sources.length > 0 && (
                    <div className="about-page-hero-sources">
                        <SourceButtons sources={heroCopy.sources} />
                    </div>
                )}
            </div>
        </header>
    );
}

export function ConnectBanner({ title, text }: { title?: string; text?: string }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return (
        <section className="cta-banner-section" aria-labelledby="connect-banner">
            <div className="container cta-banner-container">
                <div className="cta-text-side">
                    <h2 id="connect-banner" className="cta-heading">{title ?? t('connectCouncil')}</h2>
                    <p className="cta-subtext">{text ?? t('connectCouncilDesc')}</p>
                </div>
                <div className="cta-action-side">
                    <Link href={`/${locale}/contact`} className="btn-cta-green">
                        <span>{t('contactUs')}</span>
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

export const PageHeader = PageHero;

