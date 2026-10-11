import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import '../../css/contact.css';

type DepartmentOption = { id: number; name: string; public_name?: string | null };

const FEEDBACK_CATEGORIES = [
    { value: 'general', label: 'general' },
    { value: 'feedback', label: 'feedback' },
    { value: 'complaint', label: 'complaint' },
    { value: 'services', label: 'services' },
    { value: 'other', label: 'other' },
] as const;

function FieldError({ errors, field }: { errors: Record<string, string>; field: string }) {
    if (!errors[field]) return null;
    return <p className="contact-field-error" role="alert" id={`feedback-${field}-error`}>{errors[field]}</p>;
}

function FeedbackIcon({ name }: { name: 'tracking' | 'response' | 'routing' | 'message' }) {
    const paths: Record<string, React.ReactNode> = {
        tracking: <><circle cx="12" cy="12" r="9" /><path d="m8.5 12.5 2.5 2.5 4.5-5.5" /></>,
        response: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3.5 2" /></>,
        routing: <><circle cx="6" cy="6" r="2.5" /><circle cx="18" cy="18" r="2.5" /><path d="M8.5 6H15a3 3 0 0 1 3 3v6.5M15.5 18H9a3 3 0 0 1-3-3V8.5" /></>,
        message: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></>,
    };
    return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>;
}

export default function Feedback({ csrfToken, submitted, reference, departments = [] }: { csrfToken: string; submitted: boolean; reference: string | null; departments?: DepartmentOption[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = (errors ?? {}) as Record<string, string>;

    return (
        <PublicLayout>
            <Head title={t('feedbackPage')} />
            <header className="about-page-hero contact-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                        <a href={`/${locale}`}>{t('home')}</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('feedbackPage')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">HAVE YOUR SAY</span>
                    <h1>{t('feedbackPage')}</h1>
                    <p className="about-page-hero-subtitle">{t('feedbackDesc')}</p>
                </div>
            </header>

            <main id="main" className="contact-page">
                <div className="container">
                    <section className="contact-columns" aria-label="How feedback works and feedback form">
                        <div className="contact-panel contact-info-col">
                            <span className="contact-eyebrow">HOW IT WORKS</span>
                            <h2 id="feedback-info-heading">What happens next</h2>
                            <p className="contact-intro">Every submission is recorded in the Council Registry and routed to the relevant team for resolution.</p>
                            <ul className="contact-details-list">
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="tracking" /></span>
                                    <div><strong>Tracked submission</strong><span className="contact-office">Each message receives a unique tracking reference number.</span></div>
                                </li>
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="response" /></span>
                                    <div><strong>48-hour response</strong><span className="contact-office">Standard initial response within two working days.</span></div>
                                </li>
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="routing" /></span>
                                    <div><strong>Direct routing</strong><span className="contact-office">Choose a directorate or let the secretariat route it for you.</span></div>
                                </li>
                            </ul>
                            <div className="contact-info-note"><strong>Complaints stay private.</strong><p>Complaints are handled confidentially by council staff and never published.</p></div>
                        </div>

                        <div className="contact-panel contact-form-col" id="feedback-form">
                            <div className="contact-form-heading">
                                <div>
                                    <span className="contact-eyebrow">SEND FEEDBACK</span>
                                    <h2>Share your feedback</h2>
                                    <p className="contact-intro">Submit suggestions, compliments, service enquiries or confidential complaints.</p>
                                </div>
                                <span className="contact-form-heading-icon" aria-hidden="true"><FeedbackIcon name="message" /></span>
                            </div>
                            {submitted && <p className="contact-success" role="status">{t('feedbackReceived')}: <strong>{reference}</strong></p>}
                            {Object.keys(formErrors).length > 0 && (
                                <div className="contact-error-summary" role="alert">
                                    <p>{t('formErrorsNotice')}:</p>
                                    <ul>{Object.entries(formErrors).map(([field, message]) => <li key={field}>{message}</li>)}</ul>
                                </div>
                            )}
                            <form className="contact-form" action={`/${locale}/feedback`} method="post">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <div className="contact-field">
                                    <label htmlFor="feedback-name">{t('name')} <span aria-hidden="true">*</span></label>
                                    <input
                                        id="feedback-name"
                                        name="name"
                                        required
                                        maxLength={160}
                                        autoComplete="name"
                                        placeholder={t('enterFullName')}
                                        aria-describedby={formErrors.name ? 'feedback-name-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="name" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-email">{t('email')} <span aria-hidden="true">*</span></label>
                                    <input
                                        id="feedback-email"
                                        name="email"
                                        type="email"
                                        required
                                        maxLength={254}
                                        autoComplete="email"
                                        placeholder={t('enterEmail')}
                                        aria-describedby={formErrors.email ? 'feedback-email-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="email" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-phone">{t('phoneOptional')}</label>
                                    <input
                                        id="feedback-phone"
                                        name="phone"
                                        type="tel"
                                        maxLength={40}
                                        autoComplete="tel"
                                        placeholder={t('enterPhone')}
                                        aria-describedby={formErrors.phone ? 'feedback-phone-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="phone" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-organisation">{t('organisation')}</label>
                                    <input
                                        id="feedback-organisation"
                                        name="organisation"
                                        maxLength={255}
                                        autoComplete="organization"
                                        placeholder={t('enterOrganisation')}
                                        aria-describedby={formErrors.organisation ? 'feedback-organisation-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="organisation" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-category">{t('category')} <span aria-hidden="true">*</span></label>
                                    <select
                                        id="feedback-category"
                                        name="category"
                                        required
                                        defaultValue="general"
                                        aria-describedby={formErrors.category ? 'feedback-category-error' : undefined}
                                    >
                                        {FEEDBACK_CATEGORIES.map(category => (
                                            <option key={category.value} value={category.value}>{t(category.label)}</option>
                                        ))}
                                    </select>
                                    <FieldError errors={formErrors} field="category" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-department">{t('selectDepartment')}</label>
                                    <select
                                        id="feedback-department"
                                        name="department_id"
                                        defaultValue=""
                                        aria-describedby={formErrors.department_id ? 'feedback-department_id-error' : undefined}
                                    >
                                        <option value="">—</option>
                                        {departments.map((department) => (
                                            <option key={department.id} value={department.id}>{department.public_name ?? department.name}</option>
                                        ))}
                                    </select>
                                    <FieldError errors={formErrors} field="department_id" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-subject">{t('subject')} <span aria-hidden="true">*</span></label>
                                    <input
                                        id="feedback-subject"
                                        name="subject"
                                        required
                                        maxLength={200}
                                        placeholder={t('describeEnquiry')}
                                        aria-describedby={formErrors.subject ? 'feedback-subject-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="subject" />
                                </div>

                                <div className="contact-field contact-field-full">
                                    <label htmlFor="feedback-message">{t('message')} <span aria-hidden="true">*</span></label>
                                    <textarea
                                        id="feedback-message"
                                        name="message"
                                        required
                                        minLength={10}
                                        maxLength={5000}
                                        rows={6}
                                        placeholder={t('howCanWeHelp')}
                                        aria-describedby={formErrors.message ? 'feedback-message-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="message" />
                                </div>

                                <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}>
                                    <label htmlFor="feedback-website">{t('website')}</label>
                                    <input id="feedback-website" name="website" tabIndex={-1} autoComplete="off" />
                                </div>

                                <div className="contact-form-footer contact-field-full">
                                    <div className="contact-field contact-consent">
                                        <input id="feedback-consent" type="checkbox" name="consent_given" value="1" required />
                                        <label htmlFor="feedback-consent">{t('consentText')} <span aria-hidden="true">*</span></label>
                                    </div>
                                    <p className="contact-privacy-note">{t('enquiryHelp')}</p>
                                    <button className="contact-submit" type="submit">
                                        {t('submitFeedback')} <span aria-hidden="true">→</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    {departments.length > 0 && (
                        <section className="contact-departments" aria-labelledby="feedback-departments-heading">
                            <div className="contact-section-heading">
                                <div>
                                    <span className="contact-eyebrow">CONTACT THE RIGHT TEAM</span>
                                    <h2 id="feedback-departments-heading">Council Departments</h2>
                                    <p>Select the relevant department in your feedback submission for direct routing.</p>
                                </div>
                                <a href={`/${locale}/departments`}>View all departments <span aria-hidden="true">→</span></a>
                            </div>
                            <ul className="contact-department-grid">
                                {departments.map(department => (
                                    <li key={department.id}>
                                        <span className="contact-department-mark" aria-hidden="true">{(department.public_name ?? department.name).charAt(0)}</span>
                                        <strong>{department.public_name ?? department.name}</strong>
                                        <span>Route feedback directly above <span aria-hidden="true">→</span></span>
                                    </li>
                                ))}
                            </ul>
                        </section>
                    )}

                    <section className="contact-quick-links" aria-labelledby="feedback-links-heading">
                        <div className="contact-section-heading">
                            <div>
                                <span className="contact-eyebrow">LOOKING FOR SOMETHING SPECIFIC?</span>
                                <h2 id="feedback-links-heading">Quick Links</h2>
                                <p>Find information and services across the Council website.</p>
                            </div>
                        </div>
                        <div className="contact-link-grid">
                            <a href={`/${locale}/contact`}>Contact Us &amp; Switchboard <span aria-hidden="true">→</span></a>
                            <a href={`/${locale}/services`}>Council Services <span aria-hidden="true">→</span></a>
                            <a href={`/${locale}/notices`}>Public Notices <span aria-hidden="true">→</span></a>
                            <a href={`/${locale}/documents`}>Documents and Downloads <span aria-hidden="true">→</span></a>
                        </div>
                    </section>
                </div>
            </main>
        </PublicLayout>
    );
}

