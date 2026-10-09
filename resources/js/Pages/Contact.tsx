import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type PublicContact = { office: string; type: 'phone' | 'email' | 'physical_address' | 'postal_address'; value: string };

type ContactDepartment = { id: number; name: string; public_name: string | null };

type EnquiryContext = { type: string; reference: string; title: string } | null;

const CATEGORIES = [
    { value: 'general', label: 'General Enquiry' },
    { value: 'services', label: 'Council Services' },
    { value: 'feedback', label: 'Feedback' },
    { value: 'complaint', label: 'Complaint' },
    { value: 'other', label: 'Other' },
] as const;

function FieldError({ errors, field }: { errors: Record<string, string>; field: string }) {
    if (!errors[field]) {
        return null;
    }
    return <p className="contact-field-error" role="alert" id={`contact-${field}-error`}>{errors[field]}</p>;
}

export default function Contact({ csrfToken, submitted, contacts, departments = [], context = null }: { csrfToken: string; submitted: boolean; contacts: PublicContact[]; departments?: ContactDepartment[]; context?: EnquiryContext }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = (errors ?? {}) as Record<string, string>;
    return <PublicLayout>
        <Head title={t('contact')} />
        <header className="about-page-hero">
            <div className="about-page-hero-overlay" aria-hidden="true" />
            <div className="container">
                <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                    <a href={`/${locale}`}>Home</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Contact Us</span>
                </nav>
                <h1>Contact Mutoko Rural District Council</h1>
                <p className="about-page-hero-subtitle">
                    Get in touch with the Council for information, enquiries, feedback, and assistance with local services.
                </p>
            </div>
        </header>

        <div className="container contact-page">
            <section className="contact-columns" aria-label="Contact information and enquiry form">
                <div className="contact-info-col">
                    <h2 id="public-contacts-heading">Get in Touch</h2>
                    {contacts.length > 0 ? <ul className="contact-details-list">{contacts.map((contact, index) => <li key={`${contact.office}-${contact.type}-${index}`}><strong>{contact.office}:</strong> {contact.type === 'email' ? <a href={`mailto:${contact.value}`}>{contact.value}</a> : contact.type === 'phone' ? <a href={`tel:${contact.value.replace(/\s+/g, '')}`}>{contact.value}</a> : <span>{contact.value}</span>}</li>)}</ul>
                        : <p>Official contact details are being verified for publication. Please use the enquiry form and the council team will respond.</p>}

                    <h3>Contact the Right Department</h3>
                    {departments.length > 0 ? <ul className="contact-details-list">{departments.map(department => <li key={department.id}>{department.public_name ?? department.name}</li>)}</ul>
                        : <p>Departmental contacts are being compiled. For any departmental matter, address your enquiry using the form and it will be routed internally.</p>}

                    <h3>Looking for Something Specific?</h3>
                    <ul className="contact-details-list contact-link-list">
                        <li><a href={`/${locale}/services`}>Council Services</a></li>
                        <li><a href={`/${locale}/notices`}>Public Notices</a></li>
                        <li><a href={`/${locale}/documents`}>Documents and Downloads</a></li>
                        <li><a href={`/${locale}/wards`}>Wards Directory</a></li>
                    </ul>
                </div>

                <div className="contact-form-col">
                    <h2>Send Us a Message</h2>
                    {submitted && <p className="contact-success" role="status">Thank you for contacting Mutoko Rural District Council. Your enquiry has been received.</p>}
                    {Object.keys(formErrors).length > 0 && <div className="contact-error-summary" role="alert"><p>Please correct the following:</p><ul>{Object.entries(formErrors).map(([field, message]) => <li key={field}>{message}</li>)}</ul></div>}
                    {context && <p role="note">About: <strong>{context.title}</strong></p>}
                    <form className="contact-form" action={`/${locale}/contact`} method="post">
                        <input type="hidden" name="_token" value={csrfToken} />
                        {context && <><input type="hidden" name="context_type" value={context.type} /><input type="hidden" name="context_reference" value={context.reference} /><input type="hidden" name="category" value={context.type === 'investment' ? 'investment_enquiry' : 'service_enquiry'} /></>}
                        <div className="contact-field">
                            <label htmlFor="contact-name">{t('name')}</label>
                            <input id="contact-name" name="name" required maxLength={160} autoComplete="name" aria-describedby={formErrors.name ? 'contact-name-error' : undefined} />
                            <FieldError errors={formErrors} field="name" />
                        </div>
                        <div className="contact-field">
                            <label htmlFor="contact-email">{t('email')}</label>
                            <input id="contact-email" name="email" type="email" required maxLength={254} autoComplete="email" aria-describedby={formErrors.email ? 'contact-email-error' : undefined} />
                            <FieldError errors={formErrors} field="email" />
                        </div>
                        <div className="contact-field">
                            <label htmlFor="contact-phone">{t('phoneOptional')}</label>
                            <input id="contact-phone" name="phone" type="tel" maxLength={40} autoComplete="tel" aria-describedby={formErrors.phone ? 'contact-phone-error' : undefined} />
                            <FieldError errors={formErrors} field="phone" />
                        </div>
                        <div className="contact-field">
                            <label htmlFor="contact-organisation">{t('organisation')}</label>
                            <input id="contact-organisation" name="organisation" maxLength={255} autoComplete="organization" />
                            <FieldError errors={formErrors} field="organisation" />
                        </div>
                        {!context && <div className="contact-field">
                            <label htmlFor="contact-category">{t('category')}</label>
                            <select id="contact-category" name="category" required aria-describedby={formErrors.category ? 'contact-category-error' : undefined}>
                                {CATEGORIES.map(category => <option key={category.value} value={category.value}>{category.label}</option>)}
                            </select>
                            <FieldError errors={formErrors} field="category" />
                        </div>}
                        <div className="contact-field">
                            <label htmlFor="contact-subject">{t('subject')}</label>
                            <input id="contact-subject" name="subject" required maxLength={200} aria-describedby={formErrors.subject ? 'contact-subject-error' : undefined} />
                            <FieldError errors={formErrors} field="subject" />
                        </div>
                        <div className="contact-field">
                            <label htmlFor="contact-message">{t('message')}</label>
                            <textarea id="contact-message" name="message" required minLength={10} maxLength={5000} rows={6} aria-describedby={formErrors.message ? 'contact-message-error' : undefined} />
                            <FieldError errors={formErrors} field="message" />
                        </div>
                        <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}>
                            <label htmlFor="contact-website">{t('website')}</label>
                            <input id="contact-website" name="website" tabIndex={-1} autoComplete="off" />
                        </div>
                        <div className="contact-field contact-consent">
                            <input id="contact-consent" name="consent_given" type="checkbox" value="1" required />
                            <label htmlFor="contact-consent">I understand the council will use these details to respond to my enquiry.</label>
                        </div>
                        <p className="contact-privacy-note">{t('enquiryHelp')}</p>
                        <button className="btn-section-primary" type="submit">Submit Enquiry</button>
                    </form>
                </div>
            </section>
        </div>
    </PublicLayout>;
}
