import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';
import About from './About';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';

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

export default function CmsPage({ page, about, preview }: { page: Page; about?: { officials: { slug: string; name: string; title: string; photo_url: string | null }[]; investment: { slug: string; title: string; summary: string | null }[]; pages: string[] } | null; preview?: boolean }) {
    const locale = usePublicLocale();
    if (page.slug === 'about-mutoko') return <About page={page} about={about} preview={preview} />;
    return (
        <PublicLayout>
            <Head title={page.seo_title || page.title}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
                {preview && <meta name="robots" content="noindex, nofollow" />}
            </Head>
            <PageHero eyebrow="COUNCIL INFORMATION" title={page.title} subtitle={page.summary || `Find council information about ${page.title}.`} crumb={[{ label: page.title }]} />
            {page.is_review_content && <div className="container about-page-review" role="note"><strong>Stakeholder review content.</strong> Council approval is required before public publication.</div>}
            <section className="about-page-section"><div className="container">
                <article>
                    {page.blocks.map((block, index) => {
                        if (block.type === 'heading') return <h2 key={index} style={{ marginTop: index === 0 ? 0 : '24px' }}>{block.text}</h2>;
                        if (block.type === 'cta' && block.url?.startsWith('/') && !block.url.startsWith('//')) {
                            return <p key={index}><a className="about-page-text-link" href={block.url}>{block.text} <span aria-hidden="true">→</span></a></p>;
                        }
                        if (block.type === 'paragraph') return <div key={index} className="about-page-copy"><p>{block.text}</p></div>;
                        return null;
                    })}
                    <p style={{ marginTop: '20px' }}><a className="about-page-text-link" href={`/${locale}/contact`}>Contact the council <span aria-hidden="true">→</span></a></p>
                </article>
            </div></section>
            <ConnectBanner />
        </PublicLayout>
    );
}
