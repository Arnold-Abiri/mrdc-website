import { Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';
type ServiceContent = { slug: string; name: string; summary: string | null; description: string | null; requirements: string[] | null; steps: string[] | null; fees_information: string | null; seo_title: string | null; meta_description: string | null };
type ServiceDocument = { slug: string; title: string; category: string };
export default function Service({ service, department, documents = [] }: { service: ServiceContent; department: string | null; documents?: ServiceDocument[] }) {
    const t = usePublicTranslation();
    return <PublicLayout><div className="container coming-soon"><h1>{service.name}</h1>{service.summary && <p>{service.summary}</p>}{service.description && <p>{service.description}</p>}{department && <p>{t('department')}: {department}</p>}{service.requirements?.length ? <section><h2>Requirements</h2><ul>{service.requirements.map((item, index) => <li key={index}>{item}</li>)}</ul></section> : null}{service.steps?.length ? <section><h2>Steps</h2><ol>{service.steps.map((item, index) => <li key={index}>{item}</li>)}</ol></section> : null}{service.fees_information && <section><h2>Fees</h2><p>{service.fees_information}</p></section>}{documents.length > 0 && <section><h2>{t('relatedDocuments')}</h2><ul>{documents.map(document => <li key={document.slug}><Link href={`/documents/${document.slug}`}>{document.title}</Link> — {document.category}</li>)}</ul></section>}<p><Link href={`/contact?context=service:${service.slug}`}>{t('askAboutService')}</Link></p></div></PublicLayout>;
}
