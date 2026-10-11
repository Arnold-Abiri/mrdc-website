import { useState, useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { normalizeLocale, translate } from '../../localization';

export type UrgentAlert = {
    slug: string;
    title: string;
    summary?: string | null;
    published_at?: string | null;
    category?: string | null;
};

export function EmergencyAlertCard() {
    const page = usePage();
    const { locale: rawLocale, urgent_alerts: alerts = [] } = page.props as {
        locale?: string;
        urgent_alerts?: UrgentAlert[];
    };
    const locale = normalizeLocale(rawLocale);
    const t = (key: Parameters<typeof translate>[1]) => translate(locale, key);
    const L = (path: string) =>
        path.startsWith('/#')
            ? `/${locale}${path.slice(1)}`
            : path === '/'
            ? `/${locale}`
            : path.startsWith('/') && !path.startsWith('//')
            ? `/${locale}${path}`
            : path;

    const alert = alerts[0];
    const storageKey = alert ? `mrdc_emergency_collapsed_${alert.slug}` : 'mrdc_emergency_collapsed';

    const [collapsed, setCollapsed] = useState<boolean>(() => {
        if (typeof window !== 'undefined') {
            try {
                return sessionStorage.getItem(storageKey) === 'true';
            } catch {
                return false;
            }
        }
        return false;
    });

    if (!alerts || alerts.length === 0) {
        return null;
    }

    const handleCollapse = () => {
        setCollapsed(true);
        if (typeof window !== 'undefined') {
            try {
                sessionStorage.setItem(storageKey, 'true');
            } catch {
                // Ignore storage errors
            }
        }
    };

    const handleExpand = () => {
        setCollapsed(false);
        if (typeof window !== 'undefined') {
            try {
                sessionStorage.removeItem(storageKey);
            } catch {
                // Ignore storage errors
            }
        }
    };

    if (collapsed) {
        return (
            <aside
                className="hero-emergency-collapsed"
                role="region"
                aria-label="Active emergency alert available"
            >
                <button
                    type="button"
                    onClick={handleExpand}
                    className="emergency-collapsed-pill"
                    aria-label={`Active Alert: ${alert.title}. Click to expand full civil protection advisory.`}
                >
                    <span className="emergency-beacon" aria-hidden="true">
                        <span className="beacon-ping" />
                        <span className="beacon-dot" />
                    </span>
                    <span className="collapsed-pill-label">
                        <strong>Active Alert:</strong> {alert.title.length > 32 ? `${alert.title.slice(0, 32)}…` : alert.title}
                    </span>
                    <span className="collapsed-pill-action" aria-hidden="true">
                        <span>View</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </span>
                </button>
            </aside>
        );
    }

    return (
        <aside
            className="hero-emergency-card"
            role="alert"
            aria-live="assertive"
            aria-label="Public Safety & Emergency Warning"
        >
            <div className="emergency-card-inner">
                {/* Header Badge & Minimize */}
                <div className="emergency-card-header">
                    <div className="emergency-badge">
                        <span className="emergency-beacon" aria-hidden="true">
                            <span className="beacon-ping" />
                            <span className="beacon-dot" />
                        </span>
                        <span className="emergency-badge-text">
                            {alert.category ? alert.category.toUpperCase() : 'CIVIL PROTECTION ALERT'}
                        </span>
                    </div>

                    <button
                        type="button"
                        className="emergency-dismiss-btn"
                        onClick={handleCollapse}
                        aria-label="Minimize emergency alert"
                        title="Minimize alert"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                    </button>
                </div>

                {/* Content */}
                <div className="emergency-card-body">
                    <div className="emergency-icon-title">
                        <span className="emergency-hazard-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                            </svg>
                        </span>
                        <h2 className="emergency-title">{alert.title}</h2>
                    </div>

                    {alert.summary && (
                        <p className="emergency-summary">{alert.summary}</p>
                    )}

                    <div className="emergency-contact-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                        <span>Council Desk: <strong>+263 771 592 888</strong></span>
                    </div>
                </div>

                {/* Footer Action */}
                <div className="emergency-card-footer">
                    <a
                        href={L(`/notices/${alert.slug}`)}
                        className="emergency-action-btn"
                    >
                        <span>Read Full Advisory</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            </div>
        </aside>
    );
}
