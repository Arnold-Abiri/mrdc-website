import PublicLayout from '../Layouts/PublicLayout';
import { usePage } from '@inertiajs/react';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';

type DepartmentOption = { id: number; name: string };

export default function Feedback({ csrfToken, submitted, reference, departments }: { csrfToken: string; submitted: boolean; reference: string | null; departments: DepartmentOption[] }) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = Object.entries(errors ?? {});
    return <PublicLayout>
        <div className="container coming-soon">
            <h1>{t('feedbackPage')}</h1>
            <p>{t('feedbackDesc')}</p>
            {submitted && <p role="status">{t('feedbackReceived')}: <strong>{reference}</strong></p>}
            {formErrors.length > 0 && <div role="alert"><p>{t('formErrorsNotice')}:</p><ul>{formErrors.map(([field, message]) => <li key={field}>{message}</li>)}</ul></div>}
            <form action={`/${locale}/feedback`} method="post">
                <input type="hidden" name="_token" value={csrfToken} />
                <label htmlFor="feedback-name">{t('name')}</label><input id="feedback-name" name="name" required maxLength={160} />
                <label htmlFor="feedback-email">{t('email')}</label><input id="feedback-email" name="email" type="email" required maxLength={254} />
                <label htmlFor="feedback-phone">{t('phoneOptional')}</label><input id="feedback-phone" name="phone" maxLength={40} />
                <label htmlFor="feedback-organisation">{t('organisation')}</label><input id="feedback-organisation" name="organisation" maxLength={255} />
                <label htmlFor="feedback-category">{t('category')}</label><select id="feedback-category" name="category" required>
                    <option value="general">{t('general')}</option><option value="feedback">{t('feedback')}</option>
                    <option value="complaint">{t('complaint')}</option><option value="services">{t('services')}</option>
                    <option value="other">{t('other')}</option>
                </select>
                <label htmlFor="feedback-department">{t('selectDepartment')}</label><select id="feedback-department" name="department_id" defaultValue="">
                    <option value="">—</option>{departments.map(department => <option key={department.id} value={department.id}>{department.name}</option>)}
                </select>
                <label htmlFor="feedback-subject">{t('subject')}</label><input id="feedback-subject" name="subject" required maxLength={200} />
                <label htmlFor="feedback-message">{t('message')}</label><textarea id="feedback-message" name="message" required minLength={10} maxLength={5000} />
                <div aria-hidden="true" style={{ position: 'absolute', left: '-10000px' }}>
                    <label htmlFor="feedback-website">{t('website')}</label><input id="feedback-website" name="website" tabIndex={-1} autoComplete="off" />
                </div>
                <label htmlFor="feedback-consent"><input id="feedback-consent" type="checkbox" name="consent_given" value="1" required /> {t('consentText')}</label>
                <button type="submit">{t('submitFeedback')}</button>
            </form>
        </div>
    </PublicLayout>;
}
