import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';

type DocumentItem = { slug: string; title: string; description: string | null; category: string };
export default function Documents({ documents }: { documents: DocumentItem[] }) {
    return <PublicLayout><div className="mx-auto max-w-5xl px-6 py-12"><h1 className="text-3xl font-bold">Council documents</h1>{documents.length === 0 ? <p className="mt-6">No approved documents are available yet.</p> : <ul className="mt-6 space-y-6">{documents.map(document => <li key={document.slug}><Link href={`/documents/${document.slug}`} className="font-semibold underline">{document.title}</Link><p>{document.description}</p><small>{document.category}</small></li>)}</ul>}</div></PublicLayout>;
}
