import PublicLayout from '../Layouts/PublicLayout';
type OfficialProfile = { name: string; title: string; biography: string | null; department: string | null; photo_url: string | null };
export default function Official({ official }: { official: OfficialProfile }) {
    return <PublicLayout><div className="container coming-soon"><h1>{official.name}</h1>{official.photo_url && <img src={official.photo_url} alt={official.name} width={160} height={160} />}<p>{official.title}</p>{official.department && <p>{official.department}</p>}{official.biography && <p>{official.biography}</p>}</div></PublicLayout>;
}
