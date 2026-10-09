import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Block = { type: 'heading' | 'paragraph' | 'cta'; text: string; url?: string };
type Page = {
    slug: string;
    title: string;
    summary: string | null;
    blocks: Block[];
    seo_title: string | null;
    meta_description: string | null;
    is_review_content: boolean;
};

export default function CmsPage({ page }: { page: Page }) {
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={page.seo_title || page.title}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
            </Head>
            <main className="container coming-soon">
                <nav aria-label="Breadcrumb"><a href={`/${locale}`}>Home</a> / {page.title}</nav>
                <article>
                    <h1>{page.title}</h1>
                    {page.is_review_content && <p role="note">This page is under council review.</p>}
                    {page.summary && <p>{page.summary}</p>}
                    {page.blocks.map((block, index) => {
                        if (block.type === 'heading') return <h2 key={index}>{block.text}</h2>;
                        if (block.type === 'cta' && block.url?.startsWith('/') && !block.url.startsWith('//')) {
                            return <p key={index}><a href={block.url}>{block.text}</a></p>;
                        }
                        if (block.type === 'paragraph') return <p key={index}>{block.text}</p>;
                        return null;
                    })}
                </article>
            </main>
        </PublicLayout>
    );
}
