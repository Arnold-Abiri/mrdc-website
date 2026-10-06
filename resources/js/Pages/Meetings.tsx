import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
type Meeting = { id: number; title: string; meeting_type: string; scheduled_date: string | null; scheduled_time: string | null; venue: string | null; meeting_status: string };
const meetingTypes: Record<string, string> = { full_council: 'fullCouncil', committee: 'committee', special: 'special', public_hearing: 'publicHearing' };
const meetingStatuses: Record<string, string> = { scheduled: 'scheduled', completed: 'completed', postponed: 'postponed', cancelled: 'cancelled' };
export default function Meetings({ meetings }: { meetings: Meeting[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return <PublicLayout><div className="container coming-soon"><h1>{t('meetingSchedule')}</h1><p>{t('meetingScheduleDesc')}</p>{meetings.length === 0 ? <p>{t('noMeetings')}</p> : <ul>{meetings.map(meeting => <li key={meeting.id}><Link href={`/${locale}/meetings/${meeting.id}`}>{meeting.title}</Link><p>{t(meetingTypes[meeting.meeting_type] as 'fullCouncil')} — {meeting.scheduled_date}{meeting.scheduled_time ? ` ${meeting.scheduled_time}` : ''}{meeting.venue ? ` — ${meeting.venue}` : ''} — {t(meetingStatuses[meeting.meeting_status] as 'scheduled')}</p></li>)}</ul>}</div></PublicLayout>;
}
