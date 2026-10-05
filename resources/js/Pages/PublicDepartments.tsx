import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
type Department = { id: number; public_name: string | null; public_summary: string | null };
export default function PublicDepartments({ departments }: { departments: Department[] }) {
    return <PublicLayout><div className="container coming-soon"><h1>Council departments</h1>{departments.length === 0 ? <p>No approved department information is available yet.</p> : <ul>{departments.map(department => <li key={department.id}><Link href={`/departments/${department.id}`}>{department.public_name}</Link>{department.public_summary && <p>{department.public_summary}</p>}</li>)}</ul>}</div></PublicLayout>;
}
