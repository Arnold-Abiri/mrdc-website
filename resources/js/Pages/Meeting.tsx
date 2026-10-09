import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
type MeetingDetail = { id: number; title: string; meeting_type: string; scheduled_date: string | null; scheduled_time: string | null; venue: string | null; meeting_status: string; summary: string | null; agenda: { slug: string; title: string } | null; minutes: { slug: string; title: string } | null };
const meetingTypes: Record<string, string> = { full_council: 'fullCouncil', committee: 'committee', special: 'special', public_hearing: 'publicHearing' };
const meetingStatuses: Record<string, string> = { scheduled: 'scheduled', completed: 'completed', postponed: 'postponed', cancelled: 'cancelled' };
export default function Meeting({ meeting }: { meeting: MeetingDetail }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={meeting.title} />
            <PageHero eyebrow="MEETING" title={meeting.title} subtitle={`${t(meetingTypes[meeting.meeting_type] as 'fullCouncil')} — ${t('meetingStatus')}: ${t(meetingStatuses[meeting.meeting_status] as 'scheduled')}`} crumb={[{ label: t('meetings'), href: `/${locale}/meetings` }, { label: meeting.title }]} />
            <section className="about-page-section" aria-labelledby="meeting-detail">
                <div className="container">
                    <div className="about-vision-card">
                        <div>
                            <h3 id="meeting-detail">{meeting.title}</h3>
                            <p>{t('scheduledDate')}: {meeting.scheduled_date}{meeting.scheduled_time ? ` ${t('time')}: ${meeting.scheduled_time}` : ''}</p>
                            {meeting.venue && <p>{t('venue')}: {meeting.venue}</p>}
                            {meeting.summary && <p>{meeting.summary}</p>}
                        </div>
                    </div>
                    <div className="about-focus-grid" style={{ marginTop: '24px' }}>
                        <div className="about-focus-card"><h3>{t('agenda')}</h3>{meeting.agenda ? <p><Link href={`/${locale}/documents/${meeting.agenda.slug}`}>{meeting.agenda.title}</Link></p> : <p>{t('agendaPending')}</p>}</div>
                        <div className="about-focus-card"><h3>{t('minutes')}</h3>{meeting.minutes ? <p><Link href={`/${locale}/documents/${meeting.minutes.slug}`}>{meeting.minutes.title}</Link></p> : <p>{t('minutesPending')}</p>}</div>
                    </div>
                </div>
            </section>
            <ConnectBanner />
        </PublicLayout>
    );
}
