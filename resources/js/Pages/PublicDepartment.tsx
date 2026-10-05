import PublicLayout from '../Layouts/PublicLayout';
type Profile = { public_name: string | null; public_summary: string | null; public_description: string | null; responsibilities: string[] | null };
export default function PublicDepartment({ department }: { department: Profile }) {
    return <PublicLayout><div className="container coming-soon"><h1>{department.public_name}</h1>{department.public_summary && <p>{department.public_summary}</p>}{department.public_description && <p>{department.public_description}</p>}{department.responsibilities?.length ? <section><h2>Responsibilities</h2><ul>{department.responsibilities.map((item, index) => <li key={index}>{item}</li>)}</ul></section> : null}</div></PublicLayout>;
}
