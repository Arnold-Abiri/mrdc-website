import PublicLayout from '../Layouts/PublicLayout';
type WardContent = { name: string; description: string | null; boundaries_description: string | null };
export default function Ward({ ward }: { ward: WardContent }) {
    return <PublicLayout><div className="container coming-soon"><h1>{ward.name}</h1>{ward.description && <p>{ward.description}</p>}{ward.boundaries_description && <section><h2>Area description</h2><p>{ward.boundaries_description}</p></section>}</div></PublicLayout>;
}
