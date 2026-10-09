import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicLocale } from '../usePublicTranslation';

type Block = { type: 'heading' | 'paragraph' | 'cta'; text: string; url?: string };
type Page = {
    title: string;
    summary: string | null;
    blocks: Block[];
    seo_title: string | null;
    meta_description: string | null;
    is_review_content: boolean;
    content_pending?: boolean;
};
type Official = { slug: string; name: string; title: string; photo_url: string | null };
type Opportunity = { slug: string; title: string; summary: string | null; sector?: string | null };
type Directory = {
    ward_count?: number;
    officials: Official[];
    investment: Opportunity[];
    pages: string[];
};

function sections(blocks: Block[]): { heading: string; paragraphs: string[] }[] {
    const result: { heading: string; paragraphs: string[] }[] = [];
    for (const block of blocks) {
        if (block.type === 'heading') result.push({ heading: block.text, paragraphs: [] });
        if (block.type === 'paragraph' && result.length) result[result.length - 1].paragraphs.push(block.text);
    }
    return result;
}

function link(locale: string, path: string): string {
    return `/${locale}${path}`;
}

export default function About({
    page,
    about,
    preview = false,
}: {
    page: Page;
    about?: Directory | null;
    preview?: boolean;
}) {
    const locale = usePublicLocale();
    const content = sections(page.blocks);
    const find = (...words: string[]) =>
        content.filter(section => words.some(word => section.heading.toLowerCase().includes(word)));
    const render = (items: typeof content) =>
        items.map((item, index) => (
            <div key={`${item.heading}-${index}`} className="about-page-copy">
                <h3>{item.heading}</h3>
                {item.paragraphs.map((paragraph, paragraphIndex) => (
                    <p key={paragraphIndex}>{paragraph}</p>
                ))}
            </div>
        ));

    const directory = about ?? { ward_count: 29, officials: [], investment: [], pages: [] };
    const wardCount = directory.ward_count ?? 29;

    return (
        <PublicLayout>
            <Head title={page.seo_title || page.title}>
                {page.meta_description && <meta name="description" content={page.meta_description} />}
                {preview && <meta name="robots" content="noindex, nofollow" />}
            </Head>

            {/* 1. HERO HEADER */}
            <header className="about-page-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <a href={`/${locale}`}>Home</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">About Us</span>
                    </nav>
                    <span className="about-accent-eyebrow">ABOUT US</span>
                    <h1>{page.title}</h1>
                    <p className="about-page-hero-subtitle">
                        Serving communities. Advancing sustainable development.
                    </p>
                </div>
            </header>

            {/* Review / Pending notice */}
            {page.is_review_content && (
                <div className="container about-page-review" role="note">
                    <strong>Stakeholder review content.</strong> Council approval is required before public publication.
                </div>
            )}
            {page.content_pending && (
                <div className="container about-page-review" role="note">
                    Detailed Council profile information is under review. Explore the published directories and services below.
                </div>
            )}

            {/* 2. WHO WE ARE */}
            <section className="about-page-section" aria-labelledby="who-we-are">
                <div className="container">
                    <div className="about-whoweare-grid">
                        <div className="about-whoweare-copy">
                            <span className="about-accent-eyebrow">WHO WE ARE</span>
                            <h2 id="who-we-are">Mutoko Rural District Council</h2>
                            {page.summary ? (
                                <p className="about-page-lead">{page.summary}</p>
                            ) : (
                                <>
                                    <p>
                                        Mutoko Rural District Council is a local authority established under the laws of Zimbabwe to provide governance and quality services to the people of Mutoko District. We are committed to improving the quality of life for our communities through sustainable development, effective service delivery and responsible stewardship of our resources.
                                    </p>
                                    <p>
                                        The Council works with local communities, Government and development partners to create a cleaner, safer, healthier and more prosperous district for present and future generations.
                                    </p>
                                </>
                            )}
                            {render(find('location'))}
                        </div>

                        <figure className="about-whoweare-figure">
                            <img
                                src="/images/about/landscape-hills.webp"
                                alt="Typical landscape in Mutoko District with granite rocks and savanna"
                                className="about-whoweare-img"
                                width="660"
                                height="364"
                                loading="lazy"
                            />
                            <figcaption className="about-whoweare-caption">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <span>Typical landscape in Mutoko District, Mashonaland East</span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </section>

            {/* 3. KEY STATS RIBBON */}
            <section className="about-stats-ribbon" aria-label="Council Key Statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">{wardCount}</div>
                                <div className="about-stat-label">Electoral Wards</div>
                                <p className="about-stat-desc">Mutoko Rural District Council is composed of {wardCount} electoral wards.</p>
                            </div>
                        </div>

                        <div className="about-stat-item">
                            <div className="about-stat-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 1L2 6v2h20V6L12 1zm-7 9v8h3v-8H5zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zM2 20v2h20v-2H2z" />
                                </svg>
                            </div>
                            <div>
                                <div className="about-stat-num">1995</div>
                                <div className="about-stat-label">Council Amalgamation</div>
                                <p className="about-stat-desc">Formed in 1995 through the amalgamation of three predecessor councils.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 4. VISION & MISSION */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="vision-mission">
                <div className="container">
                    <div className="about-vision-grid">
                        <div className="about-vision-card">
                            <div className="about-vision-icon-wrap" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="vision-mission">Our Vision</h3>
                                <p>A council with socially and economically empowered communities by 2030.</p>
                            </div>
                        </div>

                        <div className="about-vision-card">
                            <div className="about-vision-icon-wrap about-vision-icon-target" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3>Our Mission</h3>
                                <p>To promote sustainable development through the provision of quality services.</p>
                            </div>
                        </div>
                    </div>
                    {render(find('values'))}
                </div>
            </section>

            {/* 5. OUR MANDATE */}
            <section className="about-page-section" aria-labelledby="our-mandate">
                <div className="container">
                    <span className="about-accent-eyebrow">OUR MANDATE</span>
                    <h2 id="our-mandate">Working for Our Communities</h2>
                    <p className="about-page-section-intro">
                        As a rural district council, our mandate is to provide and facilitate services that promote the social and economic development of our communities. Our key mandate areas include:
                    </p>

                    <div className="about-mandate-grid">
                        <div className="about-mandate-card">
                            <div className="about-mandate-icon services" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                </svg>
                            </div>
                            <h3>Local Services</h3>
                            <p>Provision of quality local authority services to our communities.</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon infra" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 12h-2V8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2v4H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z" />
                                </svg>
                            </div>
                            <h3>Infrastructure Development</h3>
                            <p>Planning and development of local infrastructure.</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon env" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                </svg>
                            </div>
                            <h3>Environmental Stewardship</h3>
                            <p>Sustainable management of natural resources and a cleaner, healthier environment.</p>
                        </div>

                        <div className="about-mandate-card">
                            <div className="about-mandate-icon participate" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>Public Participation</h3>
                            <p>Engaging communities in development planning and decision making.</p>
                        </div>
                    </div>

                    {render(find('mandate'))}
                    {directory.pages.includes('mandate') && (
                        <p style={{ marginTop: '20px' }}>
                            <a className="about-page-text-link" href={link(locale, '/pages/mandate')}>
                                Read the complete mandate document <span aria-hidden="true">→</span>
                            </a>
                        </p>
                    )}
                </div>
            </section>

            {/* 6. OUR HISTORY */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="our-history">
                <div className="container">
                    <div className="about-history-layout">
                        <div className="about-history-img-wrap">
                            <img
                                src="/images/about/history-landscape.webp"
                                alt="Mutoko historical landscape with ancient baobab tree"
                                width="502"
                                height="366"
                                loading="lazy"
                            />
                        </div>

                        <div>
                            <span className="about-accent-eyebrow">OUR HISTORY</span>
                            <h2 id="our-history">A Journey of Service</h2>

                            <div className="about-timeline-tree">
                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1902</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        Mutoko established as an administrative district.
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1987</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        District Council offices opened in Mutoko.
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1994</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        Three predecessor councils operated in the district.
                                    </div>
                                </div>

                                <div className="about-timeline-item">
                                    <span className="about-timeline-year">1995</span>
                                    <span className="about-timeline-node" aria-hidden="true" />
                                    <div className="about-timeline-content">
                                        Mutoko Rural District Council formed through the amalgamation of the three councils.
                                    </div>
                                </div>
                            </div>

                            {render(find('history'))}
                        </div>
                    </div>
                </div>
            </section>

            {/* 7. LEADERSHIP & GOVERNANCE */}
            <section className="about-page-section" aria-labelledby="leadership">
                <div className="container">
                    <span className="about-accent-eyebrow">LEADERSHIP &amp; GOVERNANCE</span>
                    <h2 id="leadership">Our Council</h2>
                    <p className="about-page-section-intro">
                        The Council is led by elected councillors and supported by the administration to ensure effective governance and service delivery.
                    </p>

                    <div className="about-gov-grid">
                        <a href={link(locale, '/officials')} className="about-gov-card">
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <h3>Council Chairperson</h3>
                            <p>Provides political leadership to the Council.</p>
                            <span className="about-gov-arrow">Learn more <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/officials')} className="about-gov-card">
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                                </svg>
                            </div>
                            <h3>Chief Executive Officer</h3>
                            <p>Oversees the day-to-day administration and implementation of Council programmes.</p>
                            <span className="about-gov-arrow">Learn more <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/wards')} className="about-gov-card">
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>Councillors</h3>
                            <p>Represent the interests of communities from the 29 electoral wards.</p>
                            <span className="about-gov-arrow">Learn more <span aria-hidden="true">→</span></span>
                        </a>

                        <a href={link(locale, '/departments')} className="about-gov-card">
                            <div className="about-gov-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 1L2 6v2h20V6L12 1zm-7 9v8h3v-8H5zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zm5 0v8h3v-8h-3zM2 20v2h20v-2H2z" />
                                </svg>
                            </div>
                            <h3>Departments</h3>
                            <p>Responsible for the delivery of key services and programmes.</p>
                            <span className="about-gov-arrow">Learn more <span aria-hidden="true">→</span></span>
                        </a>
                    </div>

                    {directory.officials.length > 0 && (
                        <div style={{ marginTop: '28px' }}>
                            <h3 style={{ fontSize: '1.15rem', color: 'var(--color-civic-navy)', marginBottom: '16px' }}>Key Appointed Officials</h3>
                            <div className="about-page-directory">
                                {directory.officials.map(official => (
                                    <a className="about-page-person" href={link(locale, `/officials/${official.slug}`)} key={official.slug}>
                                        {official.photo_url && <img src={official.photo_url} alt="" loading="lazy" />}
                                        <strong>{official.name}</strong>
                                        <span>{official.title}</span>
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            {/* 8. OUR DISTRICT & COMMUNITIES */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="district">
                <div className="container">
                    <span className="about-accent-eyebrow">OUR DISTRICT &amp; WARDS</span>
                    <h2 id="district">Our Communities</h2>

                    <div className="about-district-layout">
                        <div className="about-district-info">
                            <p>
                                Mutoko District is a vibrant rural district in Mashonaland East Province, with {wardCount} electoral wards. The Council works closely with all wards to identify local priorities and deliver services that meet the needs of our communities.
                            </p>
                            <a href={link(locale, '/wards')} className="btn-ward-dir">
                                <span>View Ward Directory</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            {render(find('district'))}
                        </div>

                        {/* District Graphic Map Box */}
                        <div className="about-map-box">
                            <div className="about-map-compass" aria-hidden="true">
                                <span>▲</span>
                                <span>N</span>
                            </div>
                            <img
                                src="/images/about/district-map.webp"
                                alt="Stylized outline map of Mutoko District"
                                className="about-map-svg"
                                width="330"
                                height="340"
                                loading="lazy"
                            />
                        </div>

                        <div className="about-district-photo">
                            <img
                                src="/images/about/district-communities.webp"
                                alt="Communities across Mutoko District"
                                width="386"
                                height="300"
                                loading="lazy"
                            />
                            <div className="about-district-photo-caption">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" style={{ width: '13px', height: '13px', color: '#FBBF24' }}>
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <span>Communities across Mutoko District</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 9. STRATEGIC PRIORITIES */}
            <section className="about-page-section" aria-labelledby="priorities">
                <div className="container">
                    <span className="about-accent-eyebrow">STRATEGIC PRIORITIES</span>
                    <h2 id="priorities">Our Focus Areas</h2>
                    <p className="about-page-section-intro">
                        We are committed to the following strategic priorities to achieve our vision and mission.
                    </p>

                    <div className="about-focus-grid">
                        <div className="about-focus-card">
                            <div className="about-focus-icon p1" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <h3>Improved Service Delivery</h3>
                            <p>Reliable and quality services for all communities.</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p2" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z" />
                                </svg>
                            </div>
                            <h3>Transparent Governance</h3>
                            <p>Accountability, integrity and prudent use of resources.</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p3" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                </svg>
                            </div>
                            <h3>Community Participation</h3>
                            <p>Inclusive decision making and active community engagement.</p>
                        </div>

                        <div className="about-focus-card">
                            <div className="about-focus-icon p4" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                </svg>
                            </div>
                            <h3>Sustainable Local Development</h3>
                            <p>Creating opportunities for resilient and prosperous communities.</p>
                        </div>
                    </div>

                    {render(find('strategic', 'priorities'))}
                </div>
            </section>

            {/* 10. INVESTMENT & ECONOMIC OPPORTUNITIES */}
            <section className="about-page-section about-page-section-muted" aria-labelledby="investment">
                <div className="container">
                    <span className="about-accent-eyebrow">INVESTMENT &amp; ECONOMIC OPPORTUNITIES</span>
                    <h2 id="investment">Building a Prosperous Mutoko</h2>
                    <p className="about-page-section-intro">
                        Mutoko District offers opportunities for investment in key sectors, supporting local economic growth and livelihoods.
                    </p>

                    <div className="about-economic-grid">
                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-agriculture.webp"
                                alt="Agriculture & Horticulture in Mutoko"
                                className="about-economic-thumb"
                                width="140"
                                height="140"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2-8 2s-3-2-7 1c-3.1 2.33-4.5 6.04-4.8 9.5L5.7 13C6.7 9.8 9.9 7.6 17 8z" />
                                    </svg>
                                    <h3>Agriculture &amp; Horticulture</h3>
                                </div>
                                <p>Crop and livestock production opportunities.</p>
                                <span className="about-economic-link">Explore opportunities <span aria-hidden="true">→</span></span>
                            </div>
                        </a>

                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-mining.webp"
                                alt="Mining & Mineral Value Addition in Mutoko"
                                className="about-economic-thumb"
                                width="140"
                                height="140"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                    </svg>
                                    <h3>Mining &amp; Mineral Value Addition</h3>
                                </div>
                                <p>Potential for mineral development and value addition.</p>
                                <span className="about-economic-link">Explore opportunities <span aria-hidden="true">→</span></span>
                            </div>
                        </a>

                        <a href={link(locale, '/investment')} className="about-economic-card">
                            <img
                                src="/images/about/econ-solar.webp"
                                alt="Renewable Energy in Mutoko"
                                className="about-economic-thumb"
                                width="140"
                                height="140"
                                loading="lazy"
                            />
                            <div className="about-economic-body">
                                <div className="about-economic-header">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
                                    </svg>
                                    <h3>Renewable Energy</h3>
                                </div>
                                <p>Opportunities in solar and alternative energy solutions.</p>
                                <span className="about-economic-link">Explore opportunities <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </div>

                    {directory.investment.length > 0 && (
                        <div style={{ marginTop: '24px' }}>
                            <div className="about-page-directory">
                                {directory.investment.map(item => (
                                    <a className="about-page-opportunity" href={link(locale, `/investment/${item.slug}`)} key={item.slug}>
                                        <strong>{item.title}</strong>
                                        {item.summary && <span>{item.summary}</span>}
                                    </a>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            {/* 11. CONNECT WITH YOUR COUNCIL CTA */}
            <section className="about-connect-cta" aria-labelledby="about-contact">
                <div className="container">
                    <div className="about-connect-inner">
                        <div className="about-connect-info">
                            <div className="about-connect-icon-box" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <div>
                                <h2 id="about-contact">Connect with Your Council</h2>
                                <p>We value your feedback and encourage you to engage with us.</p>
                            </div>
                        </div>

                        <a href={link(locale, '/contact')} className="btn-connect-gold">
                            <span>Contact Us</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
