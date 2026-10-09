import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import '../../css/contact.css';

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

const contactLabels: Record<PublicContact['type'], string> = {
    phone: 'Telephone',
    email: 'Email',
    physical_address: 'Visit the Council',
    postal_address: 'Postal address',
};

function ContactIcon({ type }: { type: PublicContact['type'] }) {
    const paths: Record<PublicContact['type'], React.ReactNode> = {
        phone: <path d="M6.5 3.5h3l1.3 4-1.8 1.8a15 15 0 0 0 5.7 5.7l1.8-1.8 4 1.3v3c0 1-.8 1.8-1.8 1.8A15.7 15.7 0 0 1 3.7 5.3c0-1 .8-1.8 1.8-1.8Z" />,
        email: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></>,
        physical_address: <><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" /><circle cx="12" cy="10" r="2" /></>,
        postal_address: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></>,
    };
    return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[type]}</svg>;
}

function FieldError({ errors, field }: { errors: Record<string, string>; field: string }) {
    if (!errors[field]) return null;
    return <p className="contact-field-error" role="alert" id={`contact-${field}-error`}>{errors[field]}</p>;
}

export default function Contact({ csrfToken, submitted, contacts, departments = [], context = null }: { csrfToken: string; submitted: boolean; contacts: PublicContact[]; departments?: ContactDepartment[]; context?: EnquiryContext }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = (errors ?? {}) as Record<string, string>;

    return <PublicLayout>
        <Head title={t('contact')} />
        <header className="about-page-hero contact-hero">
            <div className="about-page-hero-overlay" aria-hidden="true" />
            <div className="container">
                <nav className="about-page-breadcrumb" aria-label="Breadcrumb">
                    <a href={`/${locale}`}>Home</a><span aria-hidden="true">/</span><span aria-current="page">Contact Us</span>
                </nav>
                <span className="about-accent-eyebrow">CONTACT US</span>
                <h1>Contact Mutoko Rural District Council</h1>
                <p className="about-page-hero-subtitle">Questions, feedback or assistance with local services? We are here to help you reach the right team.</p>
            </div>
        </header>

        <main id="main" className="contact-page">
            <div className="container">
                <section className="contact-columns" aria-label="Contact information and enquiry form">
                    <div className="contact-panel contact-info-col">
                        <span className="contact-eyebrow">GET IN TOUCH</span>
                        <h2 id="public-contacts-heading">Our Contact Information</h2>
                        <p className="contact-intro">Contact the Council through the published channels below, or send an enquiry using the form.</p>
                        {contacts.length > 0 ? <ul className="contact-details-list">{contacts.map((contact, index) => <li key={`${contact.office}-${contact.type}-${index}`}>
                            <span className="contact-detail-icon"><ContactIcon type={contact.type} /></span>
                            <div><strong>{contactLabels[contact.type]}</strong><span className="contact-office">{contact.office}</span>
                                {contact.type === 'email' ? <a href={`mailto:${contact.value}`}>{contact.value}</a> : contact.type === 'phone' ? <a href={`tel:${contact.value.replace(/\s+/g, '')}`}>{contact.value}</a> : <span>{contact.value}</span>}
                            </div>
                        </li>)}</ul> : <div className="contact-empty"><span className="contact-detail-icon"><ContactIcon type="email" /></span><p>Official contact details are being verified for publication. Please use the enquiry form and the council team will respond.</p></div>}
                        <div className="contact-info-note"><strong>Need help choosing a department?</strong><p>Send us the details and we will route your enquiry to the appropriate team.</p></div>
                    </div>

                    <div className="contact-panel contact-form-col" id="enquiry-form">
                        <div className="contact-form-heading">
                            <div>
                                <span className="contact-eyebrow">SEND US A MESSAGE</span>
                                <h2>Enquiry Form</h2>
                                <p className="contact-intro">Tell us what you need and the appropriate team will get back to you.</p>
                            </div>
                            <span className="contact-form-heading-icon" aria-hidden="true"><ContactIcon type="email" /></span>
                        </div>
                        {submitted && <p className="contact-success" role="status">Thank you for contacting Mutoko Rural District Council. Your enquiry has been received.</p>}
                        {Object.keys(formErrors).length > 0 && <div className="contact-error-summary" role="alert"><p>Please correct the following:</p><ul>{Object.entries(formErrors).map(([field, message]) => <li key={field}>{message}</li>)}</ul></div>}
                        {context && <p className="contact-context" role="note">About: <strong>{context.title}</strong></p>}
                        <form className="contact-form" action={`/${locale}/contact`} method="post">
                            <input type="hidden" name="_token" value={csrfToken} />
                            {context && <><input type="hidden" name="context_type" value={context.type} /><input type="hidden" name="context_reference" value={context.reference} /><input type="hidden" name="category" value={context.type === 'investment' ? 'investment_enquiry' : 'service_enquiry'} /></>}
                            <div className="contact-field"><label htmlFor="contact-name">{t('name')} <span aria-hidden="true">*</span></label><input id="contact-name" name="name" required maxLength={160} autoComplete="name" placeholder="Enter your full name" aria-describedby={formErrors.name ? 'contact-name-error' : undefined} /><FieldError errors={formErrors} field="name" /></div>
                            <div className="contact-field"><label htmlFor="contact-email">{t('email')} <span aria-hidden="true">*</span></label><input id="contact-email" name="email" type="email" required maxLength={254} autoComplete="email" placeholder="Enter your email address" aria-describedby={formErrors.email ? 'contact-email-error' : undefined} /><FieldError errors={formErrors} field="email" /></div>
                            <div className="contact-field"><label htmlFor="contact-phone">{t('phoneOptional')}</label><input id="contact-phone" name="phone" type="tel" maxLength={40} autoComplete="tel" placeholder="Enter your phone number" aria-describedby={formErrors.phone ? 'contact-phone-error' : undefined} /><FieldError errors={formErrors} field="phone" /></div>
                            <div className="contact-field"><label htmlFor="contact-organisation">{t('organisation')}</label><input id="contact-organisation" name="organisation" maxLength={255} autoComplete="organization" placeholder="Organisation, if applicable" /><FieldError errors={formErrors} field="organisation" /></div>
                            {!context && <div className="contact-field"><label htmlFor="contact-category">{t('category')} <span aria-hidden="true">*</span></label><select id="contact-category" name="category" required defaultValue="" aria-describedby={formErrors.category ? 'contact-category-error' : undefined}><option value="" disabled>Select a category</option>{CATEGORIES.map(category => <option key={category.value} value={category.value}>{category.label}</option>)}</select><FieldError errors={formErrors} field="category" /></div>}
                            <div className="contact-field"><label htmlFor="contact-subject">{t('subject')} <span aria-hidden="true">*</span></label><input id="contact-subject" name="subject" required maxLength={200} placeholder="Briefly describe your enquiry" aria-describedby={formErrors.subject ? 'contact-subject-error' : undefined} /><FieldError errors={formErrors} field="subject" /></div>
                            <div className="contact-field contact-field-full"><label htmlFor="contact-message">{t('message')} <span aria-hidden="true">*</span></label><textarea id="contact-message" name="message" required minLength={10} maxLength={5000} rows={6} placeholder="How can we help you?" aria-describedby={formErrors.message ? 'contact-message-error' : undefined} /><FieldError errors={formErrors} field="message" /></div>
                            <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}><label htmlFor="contact-website">{t('website')}</label><input id="contact-website" name="website" tabIndex={-1} autoComplete="off" /></div>
                            <div className="contact-form-footer contact-field-full">
                                <div className="contact-field contact-consent"><input id="contact-consent" name="consent_given" type="checkbox" value="1" required /><label htmlFor="contact-consent">I understand the council will use these details to respond to my enquiry. <span aria-hidden="true">*</span></label></div>
                                <p className="contact-privacy-note">{t('enquiryHelp')}</p>
                                <button className="contact-submit" type="submit">Submit Enquiry <span aria-hidden="true">→</span></button>
                            </div>
                        </form>
                    </div>
                </section>

                {departments.length > 0 && <section className="contact-departments" aria-labelledby="contact-departments-heading">
                    <div className="contact-section-heading"><div><span className="contact-eyebrow">CONTACT THE RIGHT TEAM</span><h2 id="contact-departments-heading">Council Departments</h2><p>For a departmental matter, select the relevant team in your enquiry.</p></div><a href={`/${locale}/departments`}>View all departments <span aria-hidden="true">→</span></a></div>
                    <ul className="contact-department-grid">{departments.map(department => <li key={department.id}><span className="contact-department-mark" aria-hidden="true">{(department.public_name ?? department.name).charAt(0)}</span><strong>{department.public_name ?? department.name}</strong><span>Contact through the enquiry form <span aria-hidden="true">→</span></span></li>)}</ul>
                </section>}

                <section className="contact-quick-links" aria-labelledby="contact-links-heading">
                    <div className="contact-section-heading"><div><span className="contact-eyebrow">LOOKING FOR SOMETHING SPECIFIC?</span><h2 id="contact-links-heading">Quick Links</h2><p>Find information and services across the Council website.</p></div></div>
                    <div className="contact-link-grid"><a href={`/${locale}/services`}>Council Services <span aria-hidden="true">→</span></a><a href={`/${locale}/notices`}>Public Notices <span aria-hidden="true">→</span></a><a href={`/${locale}/documents`}>Documents and Downloads <span aria-hidden="true">→</span></a><a href={`/${locale}/wards`}>Wards Directory <span aria-hidden="true">→</span></a></div>
                </section>
            </div>
        </main>
    </PublicLayout>;
}
