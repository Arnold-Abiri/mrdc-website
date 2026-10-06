import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale, usePublicTranslation } from '../usePublicTranslation';
type Department = { id: number; public_name: string | null; public_summary: string | null };
export default function PublicDepartments({ departments }: { departments: Department[] }) {
    const locale = usePublicLocale();
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{t('councilDepartments')}</h1>{departments.length === 0 ? <p>No approved department information is available yet.</p> : <ul>{departments.map(department => <li key={department.id}><Link href={`/${locale}/departments/${department.id}`}>{department.public_name}</Link>{department.public_summary && <p>{department.public_summary}</p>}</li>)}</ul>}</div></PublicLayout>;
}
