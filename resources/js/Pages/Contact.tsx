import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation } from '../usePublicTranslation';

type PublicContact = { office: string; type: 'phone' | 'email' | 'physical_address' | 'postal_address'; value: string };

export default function Contact({ csrfToken, submitted, contacts }: { csrfToken: string; submitted: boolean; contacts: PublicContact[] }) {
    const t = usePublicTranslation();
    return <PublicLayout>
        <Head title={t('contact')} />
        <div className="container coming-soon">
            <h1>{t('contactCouncil')}</h1>
            {submitted && <p role="status">{t('enquiryReceived')}</p>}
            <p>{t('enquiryHelp')}</p>
            {contacts.length > 0 && <section aria-labelledby="public-contacts-heading"><h2 id="public-contacts-heading">{t('contactDetails')}</h2><ul>{contacts.map((contact, index) => <li key={`${contact.office}-${contact.type}-${index}`}><strong>{contact.office}:</strong> {contact.type === 'email' ? <a href={`mailto:${contact.value}`}>{contact.value}</a> : contact.type === 'phone' ? <a href={`tel:${contact.value}`}>{contact.value}</a> : <span>{contact.value}</span>}</li>)}</ul></section>}
            <form action="/contact" method="post">
                <input type="hidden" name="_token" value={csrfToken} />
                <label htmlFor="contact-name">{t('name')}</label><input id="contact-name" name="name" required maxLength={160} />
                <label htmlFor="contact-email">{t('email')}</label><input id="contact-email" name="email" type="email" required maxLength={254} />
                <label htmlFor="contact-phone">{t('phoneOptional')}</label><input id="contact-phone" name="phone" maxLength={40} />
                <label htmlFor="contact-category">{t('category')}</label><select id="contact-category" name="category" required>
                    <option value="general">{t('general')}</option><option value="services">{t('services')}</option>
                    <option value="feedback">{t('feedback')}</option><option value="other">{t('other')}</option>
                </select>
                <label htmlFor="contact-subject">{t('subject')}</label><input id="contact-subject" name="subject" required maxLength={200} />
                <label htmlFor="contact-message">{t('message')}</label><textarea id="contact-message" name="message" required minLength={10} maxLength={5000} />
                <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}>
                    <label htmlFor="contact-website">{t('website')}</label><input id="contact-website" name="website" tabIndex={-1} autoComplete="off" />
                </div>
                <button type="submit">{t('sendEnquiry')}</button>
            </form>
        </div>
    </PublicLayout>;
}
