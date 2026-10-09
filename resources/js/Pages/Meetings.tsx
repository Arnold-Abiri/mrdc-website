import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner, EmptyState } from '../Components/public/PageHeader';
type Meeting = { id: number; title: string; meeting_type: string; scheduled_date: string | null; scheduled_time: string | null; venue: string | null; meeting_status: string };
const meetingTypes: Record<string, string> = { full_council: 'fullCouncil', committee: 'committee', special: 'special', public_hearing: 'publicHearing' };
const meetingStatuses: Record<string, string> = { scheduled: 'scheduled', completed: 'completed', postponed: 'postponed', cancelled: 'cancelled' };
export default function Meetings({ meetings }: { meetings: Meeting[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={t('meetingSchedule')} />
            <PageHero eyebrow="MEETINGS" title={t('meetingSchedule')} subtitle={t('meetingScheduleDesc')} crumb={[{ label: t('meetings') }]} />
            <section className="about-page-section" aria-labelledby="meetings-list">
                <div className="container">
                    <span className="about-accent-eyebrow">COUNCIL MEETINGS</span>
                    <h2 id="meetings-list">{t('meetingSchedule')}</h2>
                    {meetings.length === 0 ? <EmptyState title={t('noMeetings')} text={t('noMeetings')} /> : (
                        <div className="about-mandate-grid">
                            {meetings.map(meeting => (
                                <div key={meeting.id} className="about-mandate-card">
                                    <h3><Link href={`/${locale}/meetings/${meeting.id}`}>{meeting.title}</Link></h3>
                                    <p>{t(meetingTypes[meeting.meeting_type] as 'fullCouncil')} — {meeting.scheduled_date}{meeting.scheduled_time ? ` ${meeting.scheduled_time}` : ''}{meeting.venue ? ` — ${meeting.venue}` : ''} — {t(meetingStatuses[meeting.meeting_status] as 'scheduled')}</p>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
