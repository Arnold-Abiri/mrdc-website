import PublicLayout from '../Layouts/PublicLayout';

type DocumentItem = { slug: string; title: string; description: string | null; category: string; published_at: string | null };
export default function Document({ document }: { document: DocumentItem }) {
    return <PublicLayout><div className="mx-auto max-w-5xl px-6 py-12"><h1 className="text-3xl font-bold">{document.title}</h1><p className="mt-4">{document.description}</p><p className="mt-4">{document.category}</p><a className="mt-6 inline-block underline" href={`/documents/${document.slug}/download`}>Download document</a></div></PublicLayout>;
}
