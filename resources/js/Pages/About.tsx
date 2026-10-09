import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Block = { type: 'heading' | 'paragraph' | 'cta'; text: string; url?: string };
type Page = { title: string; summary: string | null; blocks: Block[]; seo_title: string | null; meta_description: string | null; is_review_content: boolean; content_pending?: boolean };
type Official = { slug: string; name: string; title: string; photo_url: string | null };
type Opportunity = { slug: string; title: string; summary: string | null };
type Directory = { officials: Official[]; investment: Opportunity[]; pages: string[] };

function sections(blocks: Block[]): { heading: string; paragraphs: string[] }[] {
    const result: { heading: string; paragraphs: string[] }[] = [];
    for (const block of blocks) {
        if (block.type === 'heading') result.push({ heading: block.text, paragraphs: [] });
        if (block.type === 'paragraph' && result.length) result[result.length - 1].paragraphs.push(block.text);
    }
    return result;
}

function link(locale: string, path: string): string { return `/${locale}${path}`; }

export default function About({ page, about, preview = false }: { page: Page; about?: Directory | null; preview?: boolean }) {
    const locale = usePublicLocale();
    const content = sections(page.blocks);
    const find = (...words: string[]) => content.filter(section => words.some(word => section.heading.toLowerCase().includes(word)));
    const render = (items: typeof content) => items.map((item, index) => <div key={`${item.heading}-${index}`} className="about-page-copy"><h3>{item.heading}</h3>{item.paragraphs.map((paragraph, paragraphIndex) => <p key={paragraphIndex}>{paragraph}</p>)}</div>);
    const directory = about ?? { officials: [], investment: [], pages: [] };
    return <PublicLayout>
        <Head title={page.seo_title || page.title}>
            {page.meta_description && <meta name="description" content={page.meta_description} />}
            {preview && <meta name="robots" content="noindex, nofollow" />}
        </Head>
        <header className="about-page-hero">
            <div className="container">
                <nav className="about-page-breadcrumb" aria-label="Breadcrumb"><a href={`/${locale}`}>Home</a><span aria-hidden="true">/</span><span aria-current="page">About Us</span></nav>
                <p className="about-page-eyebrow">Our Council</p>
                <h1>{page.title}</h1>
                <p>Learn about our Council, our responsibilities, our communities, and our commitment to local development.</p>
            </div>
        </header>
        {page.is_review_content && <div className="container about-page-review" role="note">Stakeholder review content. Council approval is required before public publication.</div>}
        {page.content_pending && <div className="container about-page-review" role="note">Detailed Council profile information is under review. Explore the published directories and services below.</div>}
        <section className="about-page-section" aria-labelledby="who-we-are"><div className="container about-page-intro">
            <div><p className="about-page-eyebrow">About the Council</p><h2 id="who-we-are">Who We Are</h2>{page.summary && <p className="about-page-lead">{page.summary}</p>}{render(find('location'))}</div>
            <aside className="about-page-aside"><h3>Explore the Council</h3><p>Find public information about Council services, representatives and communities.</p><a href={link(locale, '/services')}>Council services <span aria-hidden="true">→</span></a><a href={link(locale, '/wards')}>Ward directory <span aria-hidden="true">→</span></a></aside>
        </div></section>
        <section className="about-page-section about-page-section-muted" aria-labelledby="our-history"><div className="container"><p className="about-page-eyebrow">Our background</p><h2 id="our-history">Our History</h2>{render(find('history'))}{find('history').length === 0 && <p>Historical milestones will appear here after Council review.</p>}</div></section>
        <section className="about-page-section" aria-labelledby="vision-mission"><div className="container"><p className="about-page-eyebrow">Our direction</p><h2 id="vision-mission">Vision, Mission and Values</h2>{render(find('vision', 'mission', 'values'))}{find('vision', 'mission', 'values').length === 0 && <p>Official statements will appear after Council approval.</p>}</div></section>
        <section className="about-page-section about-page-section-muted" aria-labelledby="our-mandate"><div className="container"><p className="about-page-eyebrow">What we do</p><h2 id="our-mandate">Our Mandate</h2>{render(find('mandate'))}{directory.pages.includes('mandate') && <a className="about-page-text-link" href={link(locale, '/pages/mandate')}>Read the mandate page <span aria-hidden="true">→</span></a>}</div></section>
        <section className="about-page-section" aria-labelledby="leadership"><div className="container"><p className="about-page-eyebrow">Council structure</p><h2 id="leadership">Leadership and Governance</h2><p>Elected councillors and the Council administration carry out distinct roles in local governance and service delivery.</p>{directory.officials.length > 0 && <div className="about-page-directory">{directory.officials.map(official => <a className="about-page-person" href={link(locale, `/officials/${official.slug}`)} key={official.slug}>{official.photo_url && <img src={official.photo_url} alt="" loading="lazy" />}<strong>{official.name}</strong><span>{official.title}</span></a>)}</div>}<div className="about-page-links"><a href={link(locale, '/officials')}>Leadership directory →</a><a href={link(locale, '/departments')}>Departments →</a>{directory.pages.includes('organogram') && <a href={link(locale, '/pages/organogram')}>Council organogram →</a>}</div></div></section>
        <section className="about-page-section about-page-section-muted" aria-labelledby="district"><div className="container"><p className="about-page-eyebrow">Place and people</p><h2 id="district">Our District and Communities</h2>{render(find('district'))}<a className="about-page-text-link" href={link(locale, '/wards')}>Explore the ward directory <span aria-hidden="true">→</span></a></div></section>
        <section className="about-page-section" aria-labelledby="priorities"><div className="container"><p className="about-page-eyebrow">Looking ahead</p><h2 id="priorities">Our Strategic Priorities</h2>{render(find('strategic', 'priorities'))}{find('strategic', 'priorities').length === 0 && <p>Approved priorities will be published here when available.</p>}<a className="about-page-text-link" href={link(locale, '/documents')}>Browse Council documents <span aria-hidden="true">→</span></a></div></section>
        <section className="about-page-section about-page-section-muted" aria-labelledby="investment"><div className="container"><p className="about-page-eyebrow">Opportunity</p><h2 id="investment">Development and Investment in Mutoko</h2>{directory.investment.length > 0 ? <div className="about-page-directory">{directory.investment.map(item => <a className="about-page-opportunity" href={link(locale, `/investment/${item.slug}`)} key={item.slug}><strong>{item.title}</strong>{item.summary && <span>{item.summary}</span>}</a>)}</div> : <p>Explore published opportunities in the investment directory.</p>}<a className="about-page-text-link" href={link(locale, '/investment')}>View investment opportunities <span aria-hidden="true">→</span></a></div></section>
        <section className="about-page-section" aria-labelledby="transparency"><div className="container"><p className="about-page-eyebrow">Public information</p><h2 id="transparency">Transparent and Accountable Governance</h2><p>Residents can use the website to find published records and contact the Council.</p><div className="about-page-links"><a href={link(locale, '/documents')}>Documents →</a><a href={link(locale, '/meetings')}>Council meetings →</a><a href={link(locale, '/transparency')}>Budgets and reports →</a><a href={link(locale, '/notices')}>Public notices →</a><a href={link(locale, '/feedback')}>Feedback →</a></div></div></section>
        <section className="about-page-cta" aria-labelledby="about-contact"><div className="container"><div><h2 id="about-contact">Connect with Mutoko Rural District Council</h2><p>We welcome enquiries, feedback, and participation from residents, businesses, and development partners.</p></div><div className="about-page-actions"><a className="btn-section-primary" href={link(locale, '/contact')}>Contact the Council</a><a href={link(locale, '/services')}>Explore Council Services →</a></div></div></section>
    </PublicLayout>;
}
