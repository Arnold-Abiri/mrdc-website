import PublicLayout from '../Layouts/PublicLayout';
type Item = { slug: string; title: string; summary: string | null; body: string; published_at: string | null; expires_at?: string | null; image_url?: string | null };
type DocumentLink = { slug: string; title: string };
export default function EditorialDetail({ type, item, documents }: { type: string; item: Item; documents: DocumentLink[] }) {
    return <PublicLayout><div className="container coming-soon"><p>{type}</p><h1>{item.title}</h1>{item.summary && <p>{item.summary}</p>}{item.image_url && <img src={item.image_url} alt="" className="editorial-featured-image" />}<article className="whitespace-pre-wrap">{item.body}</article>{documents.length ? <section><h2>Related documents</h2><ul>{documents.map(document => <li key={document.slug}><a href={`/documents/${document.slug}`}>{document.title}</a></li>)}</ul></section> : null}</div></PublicLayout>;
}
