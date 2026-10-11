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
    const name = department.public_name ?? t('deptProfileFallback');
    return (
        <PublicLayout>
            <Head title={name} />
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link><span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('councilDepartments')}</Link><span aria-hidden="true">/</span>
                        <span aria-current="page">{name}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('deptProfileEyebrow')}</span>
                    <h1>{name}</h1>
                    <p className="about-page-hero-subtitle">{department.public_summary || `${t('deptLearnAbout')} ${name}.`}</p>
                    <p style={{ marginTop: 12, color: 'rgba(255, 255, 255, 0.9)', fontSize: '0.92rem' }}>
                        {head ? (
                            <>
                                <strong>{t('dirLeadership')}:</strong>{' '}
                                <Link className="about-page-text-link" href={`/${locale}/officials/${head.slug}`} style={{ color: '#FCD34D', textDecoration: 'underline' }}>
                                    {head.name}
                                </Link>{' '}
                                — {head.title}
                            </>
                        ) : (
                            t('dirLeadershipTba')
                        )}
                        {contacts[0] && <> · <span style={{ opacity: 0.9 }}>{contacts[0].value}</span></>}
                    </p>
                </div>
            </header>

            <section className="about-page-section" aria-labelledby="mandate">
                <div className="container">
                    <div className="about-whoweare-grid">
                        <div className="about-whoweare-copy">
                            <span className="about-accent-eyebrow">{t('overviewMandate')}</span>
                            <h2 id="mandate">{t('whatDeptDoes')}</h2>
                            {department.public_description ? <p>{department.public_description}</p> : <p>{t('deptOpsFallback')} {name} {t('deptOpsFallbackTail')}</p>}
                            {department.responsibilities?.length ? (
                                <ul className="about-page-copy">{department.responsibilities.map((item, i) => <li key={i}>{item}</li>)}</ul>
                            ) : null}
                        </div>
                        <div>
                            <div className="about-vision-card">
                                <div><h3>{t('headOfDepartment')}</h3>
                                {head ? <p><Link className="about-page-text-link" href={`/${locale}/officials/${head.slug}`}>{head.name}</Link> — {head.title}</p> : <p>{t('tbaShort')}</p>}
                                {officials.length > 0 && <ul>{officials.slice(0, 5).map((o) => <li key={o.slug}><Link href={`/${locale}/officials/${o.slug}`}>{o.name}</Link> — {o.title}</li>)}</ul>}
                                </div>
                            </div>
                            {contacts.length > 0 && (
                                <div className="about-vision-card" style={{ marginTop: 16 }}>
                                    <div><h3>{t('departmentContacts')}</h3>
                                    <ul>{contacts.map((c, i) => <li key={`${c.office}-${i}`}><strong>{c.office}:</strong> {c.value}</li>)}</ul>
                                    <p><Link className="about-page-text-link" href={`/${locale}/contact`}>{t('contactDeptLink')} →</Link></p>
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
                        <span className="about-accent-eyebrow">{t('servicesDivisions')}</span>
                        <h2 id="dept-services">{t('servicesFromDept')}</h2>
                        <div className="about-mandate-grid">
                            {services.map((s) => (
                                <Link key={s.slug} href={`/${locale}/services/${s.slug}`} className="about-mandate-card">
                                    <h3>{s.name}</h3>
                                    {s.summary && <p>{s.summary}</p>}
                                    <span className="about-gov-arrow">{t('openService')} <span aria-hidden="true">→</span></span>
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {(documents.length > 0 || news.length > 0 || notices.length > 0) && (
                <section className="about-page-section" aria-labelledby="dept-related">
                    <div className="container">
                        <span className="about-accent-eyebrow">{t('docsUpdates')}</span>
                        <h2 id="dept-related">{t('relatedDocsNews')}</h2>
                        <div className="about-focus-grid">
                            {documents.slice(0, 4).map((d) => (
                                <Link key={d.slug} href={`/${locale}/documents/${d.slug}`} className="about-focus-card"><h3>{d.title}</h3><p>{d.category}</p></Link>
                            ))}
                            {news.map((n) => <Link key={`news-${n.slug}`} href={`/${locale}/news/${n.slug}`} className="about-focus-card"><h3>{n.title}</h3><p>{t('newsBadge')}</p></Link>)}
                            {notices.map((n) => <Link key={`notice-${n.slug}`} href={`/${locale}/notices/${n.slug}`} className="about-focus-card"><h3>{n.title}</h3><p>{t('noticeBadge')}</p></Link>)}
                        </div>
                    </div>
                </section>
            )}
        </PublicLayout>
    );
}
