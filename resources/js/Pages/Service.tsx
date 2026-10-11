import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero } from '../Components/public/PageHeader';
import { SourcedText } from '../Components/public/SourceButtons';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type ServiceContent = { slug: string; name: string; summary: string | null; description: string | null; requirements: string[] | null; steps: string[] | null; fees_information: string | null; seo_title: string | null; meta_description: string | null };
type ServiceDocument = { slug: string; title: string; category: string };

export default function Service({ service, department, documents = [] }: { service: ServiceContent; department: string | null; documents?: ServiceDocument[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    return (
        <PublicLayout>
            <Head title={service.seo_title || service.name}>
                {service.meta_description && <meta name="description" content={service.meta_description} />}
            </Head>
            <PageHero eyebrow={t('serviceGuideEyebrow')} title={service.name} subtitle={service.summary || service.description || `${t('serviceFindInfo')} ${service.name} ${t('serviceFindInfoTail')}`} crumb={[{ label: t('services'), href: `/${locale}/services` }, { label: service.name }]} />
            <section className="about-page-section" aria-label={service.name}>
                <div className="container">
                    {service.description && <div className="about-page-lead"><SourcedText text={service.description} /></div>}
                    {department && <p><strong>{t('department')}:</strong> {department}</p>}
                    <div className="about-mandate-grid">
                        {service.requirements?.length ? (
                            <div className="about-mandate-card">
                                <h3>{t('requirements')}</h3>
                                <ul>{service.requirements.map((item, index) => <li key={index}>{item}</li>)}</ul>
                            </div>
                        ) : null}
                        {service.steps?.length ? (
                            <div className="about-mandate-card">
                                <h3>{t('steps')}</h3>
                                <ol>{service.steps.map((item, index) => <li key={index}>{item}</li>)}</ol>
                            </div>
                        ) : null}
                        {service.fees_information && (
                            <div className="about-mandate-card">
                                <h3>{t('fees')}</h3>
                                <p>{service.fees_information}</p>
                            </div>
                        )}
                    </div>
                    {documents.length > 0 && (
                        <div style={{ marginTop: '24px' }}>
                            <h2>{t('relatedDocuments')}</h2>
                            <div className="about-gov-grid">
                                {documents.map((document) => (
                                    <Link key={document.slug} href={`/${locale}/documents/${document.slug}`} className="about-gov-card">
                                        <h3>{document.title}</h3>
                                        <p>{document.category}</p>
                                        <span className="about-gov-arrow">{t('viewDocument')} <span aria-hidden="true">→</span></span>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    )}
                    <p style={{ marginTop: '20px' }}>
                        <Link className="about-page-text-link" href={`/${locale}/contact?context=service:${service.slug}`}>{t('askAboutService')} <span aria-hidden="true">→</span></Link>
                    </p>
                </div>
            </section>
        </PublicLayout>
    );
}
