import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type OfficialSummary = { slug: string; name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
const TEAM = [
    { name: 'B. Tasarira', role: 'Chief Executive Officer' },
    { name: 'K.K. Chamisa', role: 'Town Board Administrator' },
    { name: 'R. Makore', role: 'Engineer' },
    { name: 'Z. Nhidza', role: 'Executive Officer — Social Services' },
    { name: 'T. Nyabonde', role: 'Executive Officer — Finance' },
    { name: 'D. Tshuma', role: 'Planner' },
    { name: 'O. Katuka', role: 'Executive Officer — Human Resources' },
    { name: 'T.K. Hambaguzha', role: 'Internal Audit' },
    { name: 'D.T. Mutangadura', role: 'Procurement' },
];
export default function Officials({ officials }: { officials: OfficialSummary[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container max-w-5xl coming-soon"><h1>{t('councilOfficials')}</h1><p>Mutoko Rural District Council is led by elected councillors for 29 wards and a professional management team headed by the Chief Executive Officer, based at Stand 366 Mutoko Centre.</p><section aria-labelledby="team-heading"><h2 id="team-heading">Management team</h2><ul>{TEAM.map(m => <li key={m.name}><strong>{m.name}</strong> — {m.role}</li>)}</ul></section>{officials.length ? <ul>{officials.map(official => <li key={official.slug}><Link href={`/${locale}/officials/${official.slug}`}>{official.name}</Link>{official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} />}<p>{official.title}</p>{official.department && <p>{official.department}</p>}{official.biography && <p>{official.biography}</p>}</li>)}</ul> : <p>No approved official information is available yet.</p>}</div></PublicLayout>;
}
