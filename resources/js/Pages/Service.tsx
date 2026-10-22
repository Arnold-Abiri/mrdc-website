import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { PageHero } from '../Components/public/PageHeader';
import { SourcedText } from '../Components/public/SourceButtons';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type ServiceContent = {
    slug: string;
    name: string;
    summary: string | null;
    description: string | null;
    requirements: string[] | null;
    steps: string[] | null;
    fees_information: string | null;
    seo_title: string | null;
    meta_description: string | null;
};

type ServiceDocument = {
    slug: string;
    title: string;
    category: string;
};

type RelatedService = {
    slug: string;
    name: string;
    summary: string | null;
};

export default function Service({
    service,
    department,
    documents = [],
    relatedServices = [],
}: {
    service: ServiceContent;
    department: string | null;
    documents?: ServiceDocument[];
    relatedServices?: RelatedService[];
}) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();

    return (
        <PublicLayout>
            <Head title={service.seo_title || service.name}>
                {service.meta_description && <meta name="description" content={service.meta_description} />}
            </Head>

            <PageHero
                eyebrow={t('serviceGuideEyebrow')}
                title={service.name}
                subtitle={service.summary || service.description || `${t('serviceFindInfo')} ${service.name} ${t('serviceFindInfoTail')}`}
                crumb={[
                    { label: t('services'), href: `/${locale}/services` },
                    { label: service.name },
                ]}
            />

            <section className="about-page-section" aria-label={service.name}>
                <div className="container">
                    <div className="service-detail-container">
                        {/* MAIN CONTENT COLUMN */}
                        <div className="service-main-content">
                            {/* OVERVIEW CARD */}
                            <div className="service-overview-card">
                                <div className="service-meta-strip">
                                    <div className="service-meta-item">
                                        <span className="service-meta-badge">MRDC OFFICIAL GUIDE</span>
                                    </div>
                                    {department && (
                                        <div className="service-meta-item">
                                            <strong>{t('department')}:</strong>
                                            <span>{department}</span>
                                        </div>
                                    )}
                                </div>

                                {service.description && (
                                    <div className="service-lead-text">
                                        <SourcedText text={service.description} />
                                    </div>
                                )}
                            </div>

                            {/* APPLICATION / SERVICE PROCEDURE TIMELINE */}
                            {service.steps && service.steps.length > 0 && (
                                <div>
                                    <h3 className="service-section-title">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                            <polyline points="9 11 12 14 22 4" />
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                        </svg>
                                        <span>{t('steps')}</span>
                                    </h3>

                                    <ol className="service-steps-timeline">
                                        {service.steps.map((stepText, index) => {
                                            const parts = stepText.split(': ');
                                            const stepTitle = parts.length > 1 ? parts[0] : `Step ${index + 1}`;
                                            const stepDesc = parts.length > 1 ? parts.slice(1).join(': ') : stepText;

                                            return (
                                                <li key={index} className="service-step-item">
                                                    <div className="service-step-number" aria-hidden="true">
                                                        {index + 1}
                                                    </div>
                                                    <div className="service-step-body">
                                                        <strong>{stepTitle}</strong>
                                                        <p>{stepDesc}</p>
                                                    </div>
                                                </li>
                                            );
                                        })}
                                    </ol>
                                </div>
                            )}

                            {/* PREREQUISITES & REQUIRED DOCUMENTS */}
                            {service.requirements && service.requirements.length > 0 && (
                                <div>
                                    <h3 className="service-section-title">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <span>{t('requirements')}</span>
                                    </h3>

                                    <ul className="service-req-list">
                                        {service.requirements.map((req, index) => (
                                            <li key={index} className="service-req-item">
                                                <svg className="service-req-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                    <polyline points="22 4 12 14.01 9 11.01" />
                                                </svg>
                                                <span>{req}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>

                        {/* SIDEBAR COLUMN */}
                        <aside className="service-sidebar" aria-label="Service Information and Actions">
                            {/* STATUTORY FEES BOX */}
                            {service.fees_information && (
                                <div className="service-fees-box">
                                    <h4>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="12" y1="16" x2="12" y2="12" />
                                            <line x1="12" y1="8" x2="12.01" y2="8" />
                                        </svg>
                                        <span>{t('fees')}</span>
                                    </h4>
                                    <p>{service.fees_information}</p>
                                </div>
                            )}

                            {/* CIVIC HELPDESK & DIRECT ENQUIRY */}
                            <div className="service-help-box">
                                <h4>{t('civicEnquiries')}</h4>
                                <p>
                                    Need direct guidance or have questions regarding this municipal procedure? Contact Stand 366 Civic Centre or submit an enquiry online.
                                </p>
                                <Link
                                    className="service-help-cta"
                                    href={`/${locale}/contact?context=service:${service.slug}`}
                                >
                                    <span>{t('askAboutService')}</span>
                                    <span aria-hidden="true">→</span>
                                </Link>
                            </div>

                            {/* DOWNLOADABLE STATUTORY FORMS / BY-LAWS */}
                            {documents.length > 0 && (
                                <div className="service-sidebar-card">
                                    <h4 style={{ fontSize: '1.05rem', fontWeight: 800, color: 'var(--color-civic-navy)', margin: '0 0 14px' }}>
                                        {t('relatedDocuments')}
                                    </h4>
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                                        {documents.map((doc) => (
                                            <Link
                                                key={doc.slug}
                                                href={`/${locale}/documents/${doc.slug}`}
                                                className="about-gov-card"
                                                style={{ padding: '14px 16px', background: '#F8FAFC' }}
                                            >
                                                <h5 style={{ margin: '0 0 4px', fontSize: '0.92rem', fontWeight: 700, color: 'var(--color-civic-navy)' }}>
                                                    {doc.title}
                                                </h5>
                                                <span style={{ fontSize: '0.78rem', color: '#64748B' }}>
                                                    {doc.category}
                                                </span>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </aside>
                    </div>

                    {/* RELATED MUNICIPAL SERVICES SECTION */}
                    {relatedServices.length > 0 && (
                        <div className="related-services-section">
                            <h3 className="service-section-title" style={{ marginBottom: '20px' }}>
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                                <span>Other Council Services</span>
                            </h3>

                            <div className="related-services-grid">
                                {relatedServices.map((rel) => (
                                    <Link
                                        key={rel.slug}
                                        href={`/${locale}/services/${rel.slug}`}
                                        className="related-service-card"
                                    >
                                        <h4>{rel.name}</h4>
                                        <p>{rel.summary || t('guideFallback')}</p>
                                        <span className="related-service-link">
                                            <span>{t('viewProcedure')}</span>
                                            <span aria-hidden="true">→</span>
                                        </span>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>
        </PublicLayout>
    );
}
