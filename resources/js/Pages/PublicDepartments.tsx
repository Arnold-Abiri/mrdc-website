import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Department = { id: number; public_name: string | null; public_summary: string | null };

const DEPT_META: Record<string, { icon: string; focus: string[] }> = {
    default: { icon: 'M12 1 2 6v2h20V6L12 1zm-7 9v8h3v-8H5zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zM2 20v2h20v-2H2z', focus: [] },
};

function matchMeta(name: string): { icon: string; focus: string[] } {
    const n = name.toLowerCase();
    if (n.includes('financ')) return { icon: DEPT_META.default.icon, focus: ['Billing & rates', 'Budgets'] };
    if (n.includes('health') || n.includes('social')) return { icon: DEPT_META.default.icon, focus: ['Clinics', 'Welfare support'] };
    if (n.includes('engineer') || n.includes('works') || n.includes('roads') || n.includes('water')) return { icon: DEPT_META.default.icon, focus: ['Roads & water points', 'Maintenance'] };
    if (n.includes('plan')) return { icon: DEPT_META.default.icon, focus: ['Stands & leases', 'Development control'] };
    if (n.includes('admin') || n.includes('human') || n.includes('audit')) return { icon: DEPT_META.default.icon, focus: ['Records', 'Customer care'] };
    return DEPT_META.default;
}

export default function PublicDepartments({ departments }: { departments: Department[] }) {
    const locale = usePublicLocale();
    const [query, setQuery] = useState('');
    const filtered = useMemo(() => {
        const q = query.trim().toLowerCase();
        if (!q) return departments;
        return departments.filter((d) => `${d.public_name ?? ''} ${d.public_summary ?? ''}`.toLowerCase().includes(q));
    }, [departments, query]);

    return (
        <PublicLayout>
            <Head title="Council Departments" />
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Departments</span>
                    </nav>
                    <span className="about-accent-eyebrow">COUNCIL DEPARTMENTS</span>
                    <h1>Departments that serve Mutoko</h1>
                    <p className="about-page-hero-subtitle">One council, 29 wards — from Stand 366 Mutoko Centre to every clinic, school, market and road gang.</p>
                </div>
            </header>

            <section className="about-stats-ribbon" aria-label="Department coverage">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item"><div><div className="about-stat-num">{departments.length}</div><div className="about-stat-label">Council departments</div></div></div>
                        <div className="about-stat-item"><div><div className="about-stat-num">29</div><div className="about-stat-label">Wards covered</div></div></div>
                        <div className="about-stat-item"><div><div className="about-stat-num">1</div><div className="about-stat-label">Service centre — Mutoko Centre</div></div></div>
                    </div>
                </div>
            </section>

            <section className="about-page-section" aria-labelledby="dept-roster">
                <div className="container">
                    <span className="about-accent-eyebrow">DIRECTORATES ROSTER</span>
                    <h2 id="dept-roster">Find the right department</h2>
                    <p className="about-page-section-intro">Select a department to see its mandate, leadership, services and how to contact it — modelled on Harare&apos;s directorate directory but kept in Mutoko&apos;s theme.</p>
                    <div style={{ margin: '16px 0 24px', maxWidth: 480 }}>
                        <label className="sr-only" htmlFor="dept-search">Search departments</label>
                        <input id="dept-search" type="search" value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Search departments — e.g. Finance, Health, Works…" style={{ width: '100%', padding: '12px 16px', borderRadius: 12, border: '1px solid #e2e8f0' }} />
                    </div>
                    {filtered.length === 0 ? (
                        <p>{departments.length === 0 ? 'No approved department information is available yet.' : `No departments match “${query}”.`}</p>
                    ) : (
                        <div className="about-gov-grid">
                            {filtered.map((d) => {
                                const meta = matchMeta(d.public_name ?? '');
                                return (
                                    <Link key={d.id} href={`/${locale}/departments/${d.id}`} className="about-gov-card">
                                        <div className="about-gov-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d={meta.icon} /></svg>
                                        </div>
                                        <span className="about-accent-eyebrow">DIRECTORATE</span>
                                        <h3>{d.public_name ?? `Department ${d.id}`}</h3>
                                        <p>{d.public_summary ?? 'Mandate and service details are being finalised for publication.'}</p>
                                        {meta.focus.length > 0 && <p style={{ fontSize: '.85rem', opacity: .8 }}>{meta.focus.join(' · ')}</p>}
                                        <span className="about-gov-arrow">Explore department <span aria-hidden="true">→</span></span>
                                    </Link>
                                );
                            })}
                        </div>
                    )}
                    <p style={{ marginTop: 24 }}>Not sure where to go? <Link className="about-page-text-link" href={`/${locale}/contact`}>Contact the council →</Link> or browse <Link className="about-page-text-link" href={`/${locale}/services`}>all services →</Link></p>
                </div>
            </section>
        </PublicLayout>
    );
}
