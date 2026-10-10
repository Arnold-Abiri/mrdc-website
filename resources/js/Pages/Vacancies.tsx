import { useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';

type Vacancy = { slug: string; title: string; grade: string | null; reference: string | null; employment_type: string | null; description: string; opens_at: string | null; closes_at: string | null; is_open: boolean; department: string | null };

const employmentLabels: Record<string, string> = { full_time: 'Full-time', part_time: 'Part-time', contract: 'Contract', temporary: 'Temporary', internship: 'Internship' };

export function daysRemaining(closesAt: string | null): number | null {
    if (!closesAt) {
        return null;
    }
    const closing = new Date(`${closesAt}T23:59:59`);
    if (Number.isNaN(closing.getTime())) {
        return null;
    }
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.ceil((closing.getTime() - today.getTime()) / 86400000);
}

function VacancyCard({ vacancy, locale }: { vacancy: Vacancy; locale: string }) {
    const remaining = vacancy.is_open ? daysRemaining(vacancy.closes_at) : null;
    return (
        <Link href={`/${locale}/vacancies/${vacancy.slug}`} className="about-focus-card">
            <p className="about-card-badges">
                {vacancy.department && <span className="about-badge">{vacancy.department}</span>}
                {vacancy.employment_type && employmentLabels[vacancy.employment_type] && <span className="about-badge">{employmentLabels[vacancy.employment_type]}</span>}
                <span className={`about-badge ${vacancy.is_open ? 'about-badge-open' : 'about-badge-closed'}`}>{vacancy.is_open ? 'Open' : 'Closed'}</span>
            </p>
            <h3>{vacancy.title}</h3>
            {vacancy.grade && <p className="about-card-meta">{vacancy.grade}{vacancy.reference ? ` · ${vacancy.reference}` : ''}</p>}
            <p>{vacancy.description.length > 180 ? `${vacancy.description.slice(0, 180)}…` : vacancy.description}</p>
            <p className="about-card-meta">
                {vacancy.is_open
                    ? vacancy.closes_at ? `Closes ${vacancy.closes_at}${remaining !== null ? ` · ${remaining} day${remaining === 1 ? '' : 's'} remaining` : ''}` : 'Open for applications'
                    : vacancy.closes_at ? `Closed ${vacancy.closes_at}` : 'Applications closed'}
                {' · View opening →'}
            </p>
        </Link>
    );
}

export default function Vacancies({ vacancies }: { vacancies: Vacancy[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [query, setQuery] = useState('');
    const [department, setDepartment] = useState('all');
    const [showClosed, setShowClosed] = useState(true);

    const departments = useMemo(() => [...new Set(vacancies.map((vacancy) => vacancy.department).filter((name): name is string => Boolean(name)))].sort(), [vacancies]);
    const openCount = vacancies.filter((vacancy) => vacancy.is_open).length;

    const filtered = vacancies.filter((vacancy) => {
        if (!vacancy.is_open && !showClosed) {
            return false;
        }
        if (department !== 'all' && vacancy.department !== department) {
            return false;
        }
        if (query.trim() !== '' && !`${vacancy.title} ${vacancy.reference ?? ''} ${vacancy.grade ?? ''}`.toLowerCase().includes(query.trim().toLowerCase())) {
            return false;
        }
        return true;
    });
    const openVacancies = filtered.filter((vacancy) => vacancy.is_open);
    const closedVacancies = filtered.filter((vacancy) => !vacancy.is_open);

    return (
        <PublicLayout>
            <Head title={t('vacancies')} />
            <PageHero eyebrow="CAREERS" title={t('vacancies')} subtitle="Explore active public service job openings across Mutoko Rural District Council departments." crumb={[{ label: 'Vacancies' }]} />
            <section className="about-page-section" aria-labelledby="vacancies-list">
                <div className="container">
                    <span className="about-accent-eyebrow">WORK WITH US</span>
                    <h2 id="vacancies-list">Open Positions</h2>
                    <div className="about-stats-row" role="list">
                        <div className="about-stat" role="listitem"><strong>{openCount}</strong><span>Active {openCount === 1 ? 'vacancy' : 'vacancies'}</span></div>
                        <div className="about-stat" role="listitem"><strong>{departments.length}</strong><span>{departments.length === 1 ? 'Department' : 'Departments'}</span></div>
                        <div className="about-stat" role="listitem"><strong>{vacancies.length}</strong><span>Total adverts</span></div>
                    </div>
                    {vacancies.length === 0 ? (
                        <EmptyState title="No vacancies published" text="No vacancies are published at this time. Please check back later." />
                    ) : (
                        <>
                            <form className="about-filter-bar" onSubmit={(event) => event.preventDefault()} aria-label="Filter vacancies">
                                <label>Search <input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search by title or reference" /></label>
                                <label>Department
                                    <select value={department} onChange={(event) => setDepartment(event.target.value)}>
                                        <option value="all">All departments</option>
                                        {departments.map((name) => <option key={name} value={name}>{name}</option>)}
                                    </select>
                                </label>
                                <label className="about-filter-check"><input type="checkbox" checked={showClosed} onChange={(event) => setShowClosed(event.target.checked)} /> Show closed adverts</label>
                            </form>
                            {filtered.length === 0 ? (
                                <EmptyState title="No matching vacancies" text="Try clearing your search or choosing a different department." />
                            ) : (
                                <>
                                    {openVacancies.length > 0 && (
                                        <>
                                            <h3>Now hiring ({openVacancies.length})</h3>
                                            <div className="about-focus-grid">
                                                {openVacancies.map((vacancy) => <VacancyCard key={vacancy.slug} vacancy={vacancy} locale={locale} />)}
                                            </div>
                                        </>
                                    )}
                                    {closedVacancies.length > 0 && (
                                        <>
                                            <h3>Closed adverts ({closedVacancies.length})</h3>
                                            <div className="about-focus-grid">
                                                {closedVacancies.map((vacancy) => <VacancyCard key={vacancy.slug} vacancy={vacancy} locale={locale} />)}
                                            </div>
                                        </>
                                    )}
                                </>
                            )}
                            <div className="about-vision-card" style={{ marginTop: '24px' }}>
                                <div>
                                    <h3>How to apply</h3>
                                    <p>Submit a detailed CV with at least 3 contactable referees and copies of qualifications to the Chief Executive Officer, or email recruitment@mutokordc.co.zw. Female candidates are encouraged to apply — Mutoko RDC is an equal opportunity employer.</p>
                                </div>
                            </div>
                        </>
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
