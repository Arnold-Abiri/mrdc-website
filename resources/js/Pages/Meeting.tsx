import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';
type MeetingDetail = { id: number; title: string; meeting_type: string; scheduled_date: string | null; scheduled_time: string | null; venue: string | null; meeting_status: string; summary: string | null; agenda: { slug: string; title: string } | null; minutes: { slug: string; title: string } | null };
const meetingTypes: Record<string, string> = { full_council: 'fullCouncil', committee: 'committee', special: 'special', public_hearing: 'publicHearing' };
const meetingStatuses: Record<string, string> = { scheduled: 'scheduled', completed: 'completed', postponed: 'postponed', cancelled: 'cancelled' };
export default function Meeting({ meeting }: { meeting: MeetingDetail }) {
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><p><Link href="/meetings">{t('meetings')}</Link></p><h1>{meeting.title}</h1><p>{t(meetingTypes[meeting.meeting_type] as 'fullCouncil')} — {t('meetingStatus')}: {t(meetingStatuses[meeting.meeting_status] as 'scheduled')}</p><p>{t('scheduledDate')}: {meeting.scheduled_date}{meeting.scheduled_time ? ` ${t('time')}: ${meeting.scheduled_time}` : ''}</p>{meeting.venue && <p>{t('venue')}: {meeting.venue}</p>}{meeting.summary && <p>{meeting.summary}</p>}<section><h2>{t('agenda')}</h2>{meeting.agenda ? <p><Link href={`/documents/${meeting.agenda.slug}`}>{meeting.agenda.title}</Link></p> : <p>{t('agendaPending')}</p>}</section><section><h2>{t('minutes')}</h2>{meeting.minutes ? <p><Link href={`/documents/${meeting.minutes.slug}`}>{meeting.minutes.title}</Link></p> : <p>{t('minutesPending')}</p>}</section></div></PublicLayout>;
}
