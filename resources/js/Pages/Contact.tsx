import { Head } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';

type PublicContact = { office: string; type: 'phone' | 'email' | 'physical_address' | 'postal_address'; value: string };

export default function Contact({ csrfToken, submitted, contacts }: { csrfToken: string; submitted: boolean; contacts: PublicContact[] }) {
    return <PublicLayout>
        <Head title="Contact" />
        <div className="container coming-soon">
            <h1>Contact the council</h1>
            {submitted && <p role="status">Your enquiry was received. Thank you.</p>}
            <p>Use this form for a general enquiry. Do not include sensitive personal information.</p>
            {contacts.length > 0 && <section aria-labelledby="public-contacts-heading"><h2 id="public-contacts-heading">Council contact details</h2><ul>{contacts.map((contact, index) => <li key={`${contact.office}-${contact.type}-${index}`}><strong>{contact.office}:</strong> {contact.type === 'email' ? <a href={`mailto:${contact.value}`}>{contact.value}</a> : contact.type === 'phone' ? <a href={`tel:${contact.value}`}>{contact.value}</a> : <span>{contact.value}</span>}</li>)}</ul></section>}
            <form action="/contact" method="post">
                <input type="hidden" name="_token" value={csrfToken} />
                <label htmlFor="contact-name">Name</label><input id="contact-name" name="name" required maxLength={160} />
                <label htmlFor="contact-email">Email</label><input id="contact-email" name="email" type="email" required maxLength={254} />
                <label htmlFor="contact-phone">Phone (optional)</label><input id="contact-phone" name="phone" maxLength={40} />
                <label htmlFor="contact-category">Category</label><select id="contact-category" name="category" required>
                    <option value="general">General</option><option value="services">Services</option>
                    <option value="feedback">Feedback</option><option value="other">Other</option>
                </select>
                <label htmlFor="contact-subject">Subject</label><input id="contact-subject" name="subject" required maxLength={200} />
                <label htmlFor="contact-message">Message</label><textarea id="contact-message" name="message" required minLength={10} maxLength={5000} />
                <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}>
                    <label htmlFor="contact-website">Website</label><input id="contact-website" name="website" tabIndex={-1} autoComplete="off" />
                </div>
                <button type="submit">Send enquiry</button>
            </form>
        </div>
    </PublicLayout>;
}
