import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
import { formatDate } from '../formatDate';

type Meeting = {
    id: number;
    title: string;
    meeting_type: string;
    scheduled_date: string | null;
    scheduled_time: string | null;
    venue: string | null;
    meeting_status: string;
};

const meetingTypeLabels: Record<string, string> = {
    full_council: 'Full Council Ordinary Meeting',
    committee: 'Council Standing Committee',
    special: 'Special Council Session',
    public_hearing: 'Public Budget Hearing',
};

const meetingStatusBadges: Record<string, { label: string; color: string }> = {
    scheduled: { label: 'Notice Published', color: '#0B8F62' },
    completed: { label: 'Adjourned / Minutes Available', color: '#475569' },
    postponed: { label: 'Rescheduled', color: '#D97706' },
    cancelled: { label: 'Cancelled', color: '#DC2626' },
};

function parseDateParts(dateStr: string | null): { month: string; day: string; year: string } {
    if (!dateStr) return { month: 'TBD', day: '--', year: '----' };
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return { month: 'DATE', day: '00', year: '----' };
    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
    return {
        month: months[date.getMonth()],
        day: String(date.getDate()).padStart(2, '0'),
        year: String(date.getFullYear()),
    };
}

export default function Meetings({ meetings }: { meetings: Meeting[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    const [tab, setTab] = useState<'all' | 'full' | 'committee'>('all');

    const filteredMeetings = useMemo(() => {
        return meetings.filter((m) => {
            if (tab === 'full') return m.meeting_type === 'full_council' || m.meeting_type === 'special';
            if (tab === 'committee') return m.meeting_type === 'committee' || m.meeting_type === 'public_hearing';
            return true;
        });
    }, [meetings, tab]);

    return (
        <PublicLayout>
            <Head title="Council Meetings, Calendars & Public Hearings" />

            {/* HERO */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <Link href={`/${locale}`}>{t('home')}</Link>
                        <span aria-hidden="true">/</span>
                        <Link href={`/${locale}/departments`}>{t('wardsCrumbCouncil')}</Link>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('meetings')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{t('meetingsEyebrow')}</span>
                    <h1>{t('meetingsTitle')}</h1>
                    <p className="about-page-hero-subtitle">
                        {t('meetingsSubtitle')}
                    </p>
                </div>
            </header>

            {/* STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Meeting statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statOrdinary')}</div>
                                <div className="about-stat-label">{t('statMonthly')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statOpen')}</div>
                                <div className="about-stat-label">{t('statAccredited')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{meetings.length}</div>
                                <div className="about-stat-label">{t('statScheduledRecorded')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{t('statChambers')}</div>
                                <div className="about-stat-label">{t('statVenue')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAIN SESSIONS */}
            <section className="about-page-section" aria-labelledby="meetings-heading">
                <div className="container">
                    <span className="about-accent-eyebrow">{t('meetingsCalendarEyebrow')}</span>
                    <h2 id="meetings-heading">{t('meetingsCalendarTitle')}</h2>
                    <p className="about-page-section-intro">
                        {t('meetingsCalendarDesc')}
                    </p>

                    {/* TABS */}
                    <div className="meetings-tabs" role="tablist">
                        <button
                            type="button"
                            className={`meetings-tab-btn ${tab === 'all' ? 'active' : ''}`}
                            onClick={() => setTab('all')}
                        >
                            {t('allSessions')} ({meetings.length})
                        </button>
                        <button
                            type="button"
                            className={`meetings-tab-btn ${tab === 'full' ? 'active' : ''}`}
                            onClick={() => setTab('full')}
                        >
                            {t('fullCouncilSessions')}
                        </button>
                        <button
                            type="button"
                            className={`meetings-tab-btn ${tab === 'committee' ? 'active' : ''}`}
                            onClick={() => setTab('committee')}
                        >
                            {t('committeesHearings')}
                        </button>
                    </div>

                    {/* TIMELINE */}
                    {filteredMeetings.length === 0 ? (
                        <div style={{ padding: '48px 24px', textAlign: 'center', background: '#F8FAFC', borderRadius: 12, border: '1px dashed var(--color-border)' }}>
                            <p style={{ margin: 0, fontWeight: 600, color: '#475569' }}>
                                {t('noMeetingsCat')}
                            </p>
                        </div>
                    ) : (
                        <div className="meetings-timeline">
                            {filteredMeetings.map((meeting) => {
                                const dateParts = parseDateParts(meeting.scheduled_date);
                                const status = meetingStatusBadges[meeting.meeting_status] ?? {
                                    label: meeting.meeting_status,
                                    color: '#475569',
                                };
                                return (
                                    <article key={meeting.id} className="meeting-event-card">
                                        <div style={{ display: 'flex', gap: '20px', alignItems: 'center', flexGrow: 1 }}>
                                            <div className="meeting-date-badge" aria-label={`${t('scheduledFor')} ${formatDate(meeting.scheduled_date, locale)}`}>
                                                <span className="meeting-date-month">{dateParts.month}</span>
                                                <span className="meeting-date-day">{dateParts.day}</span>
                                                <span className="meeting-date-year">{dateParts.year}</span>
                                            </div>

                                            <div className="meeting-info-col">
                                                <span className="meeting-type-tag">
                                                    {meetingTypeLabels[meeting.meeting_type] ?? t('councilSessionFallback')}
                                                </span>
                                                <h3 className="meeting-event-title">
                                                    <Link href={`/${locale}/meetings/${meeting.id}`}>{meeting.title}</Link>
                                                </h3>
                                                <div className="meeting-meta-row">
                                                    <span className="meeting-meta-item">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <polyline points="12 6 12 12 16 14" />
                                                        </svg>
                                                        <span>{meeting.scheduled_time ?? '10:00 AM'}</span>
                                                    </span>
                                                    <span className="meeting-meta-item">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                            <circle cx="12" cy="10" r="3" />
                                                        </svg>
                                                        <span>{meeting.venue ?? 'Council Chambers, Stand 366 Mutoko Centre'}</span>
                                                    </span>
                                                    <span className="meeting-meta-item" style={{ color: status.color, fontWeight: 700 }}>
                                                        <span style={{ display: 'inline-block', width: 8, height: 8, borderRadius: '50%', background: status.color, marginRight: 4 }} />
                                                        <span>{status.label}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <Link
                                            href={`/${locale}/meetings/${meeting.id}`}
                                            className="btn-mandate-action"
                                            style={{ alignSelf: 'center' }}
                                        >
                                            <span>{t('viewAgendaMinutes')}</span>
                                            <span aria-hidden="true">→</span>
                                        </Link>
                                    </article>
                                );
                            })}
                        </div>
                    )}

                    {/* CIVIC PORTAL FOOTER */}
                    <div className="dept-portal-strip">
                        <div className="dept-portal-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <span>{t('resolutionsTitle')}</span>
                        </div>
                        <div className="dept-portal-grid">
                            <Link href={`/${locale}/transparency`} className="dept-portal-card">
                                <h4>{t('finTransBudgets')}</h4>
                                <p>{t('finTransBudgetsDesc')}</p>
                                <span>{t('accessFinReports')} →</span>
                            </Link>

                            <Link href={`/${locale}/officials`} className="dept-portal-card">
                                <h4>{t('officialsCouncillors')}</h4>
                                <p>{t('officialsCouncillorsDesc')}</p>
                                <span>{t('viewLeadership')} →</span>
                            </Link>

                            <Link href={`/${locale}/documents`} className="dept-portal-card">
                                <h4>{t('bylawsGazettes')}</h4>
                                <p>{t('bylawsGazettesDesc')}</p>
                                <span>{t('openDocRepo')} →</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

