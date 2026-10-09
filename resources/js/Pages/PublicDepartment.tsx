import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';

type Profile = { public_name: string | null; public_summary: string | null; public_description: string | null; responsibilities: string[] | null };
type Head = { slug: string; name: string; title: string } | null;
type OfficialItem = { slug: string; name: string; title: string };
type ServiceItem = { slug: string; name: string; summary: string | null };
type ContactItem = { office: string; type: string; value: string };
type DocumentItem = { slug: string; title: string; category: string };
type EditorialItem = { slug: string; title: string };

export default function PublicDepartment({ department, head, officials = [], services = [], contacts = [], documents = [], news = [], notices = [] }: { department: Profile; head: Head; officials?: OfficialItem[]; services?: ServiceItem[]; contacts?: ContactItem[]; documents?: DocumentItem[]; news?: EditorialItem[]; notices?: EditorialItem[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const name = department.public_name ?? 'Department';
    return (
        <PublicLayout>
            <Head title={name} />
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <Link href={`/${locale}`}>Home</Link><span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>Departments</Link><span aria-hidden="true">/</span>
                        <span aria-current="page">{name}</span>
                    </nav>
                    <span className="about-accent-eyebrow">CITY DIRECTORATE · MUTOKO RDC</span>
                    <h1>{name}</h1>
                    <p className="about-page-hero-subtitle">{department.public_summary || `Learn about the work, services and contact information for ${name}.`}</p>
                    <p style={{ marginTop: 12 }}>
                        {head ? <>Led by <Link className="about-page-text-link" href={`/${locale}/officials/${head.slug}`} style={{ color: '#fff' }}>{head.name}</Link> — {head.title}</> : 'Department leadership to be announced.'}
                        {contacts[0] && <> · {contacts[0].value}</>}
                    </p>
                </div>
            </header>

            <section className="about-page-section" aria-labelledby="mandate">
                <div className="container">
                    <div className="about-whoweare-grid">
                        <div className="about-whoweare-copy">
                            <span className="about-accent-eyebrow">OVERVIEW &amp; MANDATE</span>
                            <h2 id="mandate">What this department does</h2>
                            {department.public_description ? <p>{department.public_description}</p> : <p>General operations and functions of {name} across Mutoko&apos;s 29 wards — clinics, schools, roads, water points, markets and growth points.</p>}
                            {department.responsibilities?.length ? (
                                <ul className="about-page-copy">{department.responsibilities.map((item, i) => <li key={i}>{item}</li>)}</ul>
                            ) : null}
                        </div>
                        <div>
                            <div className="about-vision-card">
                                <div><h3>{t('headOfDepartment')}</h3>
                                {head ? <p><Link className="about-page-text-link" href={`/${locale}/officials/${head.slug}`}>{head.name}</Link> — {head.title}</p> : <p>To be announced.</p>}
                                {officials.length > 0 && <ul>{officials.slice(0, 5).map((o) => <li key={o.slug}><Link href={`/${locale}/officials/${o.slug}`}>{o.name}</Link> — {o.title}</li>)}</ul>}
                                </div>
                            </div>
                            {contacts.length > 0 && (
                                <div className="about-vision-card" style={{ marginTop: 16 }}>
                                    <div><h3>{t('departmentContacts')}</h3>
                                    <ul>{contacts.map((c, i) => <li key={`${c.office}-${i}`}><strong>{c.office}:</strong> {c.value}</li>)}</ul>
                                    <p><Link className="about-page-text-link" href={`/${locale}/contact`}>Contact this department →</Link></p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {services.length > 0 && (
                <section className="about-page-section about-page-section-muted" aria-labelledby="dept-services">
                    <div className="container">
                        <span className="about-accent-eyebrow">SERVICES &amp; DIVISIONS</span>
                        <h2 id="dept-services">Services from this department</h2>
                        <div className="about-mandate-grid">
                            {services.map((s) => (
                                <Link key={s.slug} href={`/${locale}/services/${s.slug}`} className="about-mandate-card">
                                    <h3>{s.name}</h3>
                                    {s.summary && <p>{s.summary}</p>}
                                    <span className="about-gov-arrow">Open service <span aria-hidden="true">→</span></span>
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {(documents.length > 0 || news.length > 0 || notices.length > 0) && (
                <section className="about-page-section" aria-labelledby="dept-related">
                    <div className="container">
                        <span className="about-accent-eyebrow">DOCUMENTS &amp; UPDATES</span>
                        <h2 id="dept-related">Related documents and news</h2>
                        <div className="about-focus-grid">
                            {documents.slice(0, 4).map((d) => (
                                <Link key={d.slug} href={`/${locale}/documents/${d.slug}`} className="about-focus-card"><h3>{d.title}</h3><p>{d.category}</p></Link>
                            ))}
                            {news.map((n) => <Link key={`news-${n.slug}`} href={`/${locale}/news/${n.slug}`} className="about-focus-card"><h3>{n.title}</h3><p>News</p></Link>)}
                            {notices.map((n) => <Link key={`notice-${n.slug}`} href={`/${locale}/notices/${n.slug}`} className="about-focus-card"><h3>{n.title}</h3><p>Notice</p></Link>)}
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
