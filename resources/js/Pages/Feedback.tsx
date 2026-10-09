import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import { PageHero, ConnectBanner } from '../Components/public/PageHeader';
import '../../css/contact.css';

type DepartmentOption = { id: number; name: string };

function FieldError({ errors, field }: { errors: Record<string, string>; field: string }) {
    if (!errors[field]) return null;
    return <p className="contact-field-error" role="alert">{errors[field]}</p>;
}

export default function Feedback({ csrfToken, submitted, reference, departments }: { csrfToken: string; submitted: boolean; reference: string | null; departments: DepartmentOption[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = (errors ?? {}) as Record<string, string>;
    return (
        <PublicLayout>
            <Head title={t('feedbackPage')} />
            <PageHero eyebrow="HAVE YOUR SAY" title={t('feedbackPage')} subtitle={t('feedbackDesc')} crumb={[{ label: 'Feedback' }]} />
            <main id="main" className="contact-page">
                <div className="container">
                    <section className="about-page-section" aria-labelledby="feedback-form">
                        <span className="about-accent-eyebrow">FEEDBACK FORM</span>
                        <h2 id="feedback-form">Share your feedback</h2>
                        <div className="contact-panel contact-form-col">
                            {submitted && <p className="contact-success" role="status">{t('feedbackReceived')}: <strong>{reference}</strong></p>}
                            {Object.keys(formErrors).length > 0 && <div className="contact-error-summary" role="alert"><p>{t('formErrorsNotice')}:</p><ul>{Object.entries(formErrors).map(([field, message]) => <li key={field}>{message}</li>)}</ul></div>}
                            <form className="contact-form" action={`/${locale}/feedback`} method="post">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <div className="contact-field"><label htmlFor="feedback-name">{t('name')} <span aria-hidden="true">*</span></label><input id="feedback-name" name="name" required maxLength={160} autoComplete="name" placeholder="Enter your full name" /><FieldError errors={formErrors} field="name" /></div>
                                <div className="contact-field"><label htmlFor="feedback-email">{t('email')} <span aria-hidden="true">*</span></label><input id="feedback-email" name="email" type="email" required maxLength={254} autoComplete="email" placeholder="Enter your email address" /><FieldError errors={formErrors} field="email" /></div>
                                <div className="contact-field"><label htmlFor="feedback-phone">{t('phoneOptional')}</label><input id="feedback-phone" name="phone" maxLength={40} autoComplete="tel" placeholder="Enter your phone number" /><FieldError errors={formErrors} field="phone" /></div>
                                <div className="contact-field"><label htmlFor="feedback-organisation">{t('organisation')}</label><input id="feedback-organisation" name="organisation" maxLength={255} autoComplete="organization" placeholder="Organisation, if applicable" /><FieldError errors={formErrors} field="organisation" /></div>
                                <div className="contact-field"><label htmlFor="feedback-category">{t('category')} <span aria-hidden="true">*</span></label><select id="feedback-category" name="category" required defaultValue="general"><option value="general">{t('general')}</option><option value="feedback">{t('feedback')}</option><option value="complaint">{t('complaint')}</option><option value="services">{t('services')}</option><option value="other">{t('other')}</option></select><FieldError errors={formErrors} field="category" /></div>
                                <div className="contact-field"><label htmlFor="feedback-department">{t('selectDepartment')}</label><select id="feedback-department" name="department_id" defaultValue=""><option value="">\u2014</option>{departments.map((department) => <option key={department.id} value={department.id}>{department.name}</option>)}</select><FieldError errors={formErrors} field="department_id" /></div>
                                <div className="contact-field"><label htmlFor="feedback-subject">{t('subject')} <span aria-hidden="true">*</span></label><input id="feedback-subject" name="subject" required maxLength={200} placeholder="Briefly describe your feedback" /><FieldError errors={formErrors} field="subject" /></div>
                                <div className="contact-field contact-field-full"><label htmlFor="feedback-message">{t('message')} <span aria-hidden="true">*</span></label><textarea id="feedback-message" name="message" required minLength={10} maxLength={5000} rows={6} placeholder="How can we help you?" /><FieldError errors={formErrors} field="message" /></div>
                                <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}><label htmlFor="feedback-website">{t('website')}</label><input id="feedback-website" name="website" tabIndex={-1} autoComplete="off" /></div>
                                <div className="contact-form-footer contact-field-full">
                                    <div className="contact-field contact-consent"><input id="feedback-consent" type="checkbox" name="consent_given" value="1" required /><label htmlFor="feedback-consent">{t('consentText')} <span aria-hidden="true">*</span></label></div>
                                    <button className="contact-submit" type="submit">{t('submitFeedback')} <span aria-hidden="true">\u2192</span></button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </main>
            <ConnectBanner />
        </PublicLayout>
    );
}
