import { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import '../../css/contact.css';

type DepartmentOption = { id: number; name: string; public_name?: string | null };
type WardOption = { id: number; name: string; slug: string };

const FEEDBACK_CATEGORIES = [
    { value: 'fault_report', label: 'faultReport' },
    { value: 'complaint', label: 'complaint' },
    { value: 'feedback', label: 'feedback' },
    { value: 'services', label: 'services' },
    { value: 'general', label: 'general' },
    { value: 'other', label: 'other' },
] as const;

const FAULT_PRESETS = [
    { key: 'faultWater', labelKey: 'faultWater', subject: 'Water supply / Borehole issue' },
    { key: 'faultRoads', labelKey: 'faultRoads', subject: 'Road repair / Bridge washaway / Drainage issue' },
    { key: 'faultRefuse', labelKey: 'faultRefuse', subject: 'Refuse collection / Illegal dumping issue' },
    { key: 'faultHealth', labelKey: 'faultHealth', subject: 'Clinic / Healthcare facility issue' },
    { key: 'faultLighting', labelKey: 'faultLighting', subject: 'Public lighting / Market facility issue' },
    { key: 'faultOther', labelKey: 'faultOther', subject: 'Infrastructure / Service issue' },
] as const;

function FieldError({ errors, field }: { errors: Record<string, string>; field: string }) {
    if (!errors[field]) return null;
    return <p className="contact-field-error" role="alert" id={`feedback-${field}-error`}>{errors[field]}</p>;
}

function FeedbackIcon({ name }: { name: 'tracking' | 'response' | 'routing' | 'message' | 'alert' | 'wrench' }) {
    const paths: Record<string, React.ReactNode> = {
        tracking: <><circle cx="12" cy="12" r="9" /><path d="m8.5 12.5 2.5 2.5 4.5-5.5" /></>,
        response: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3.5 2" /></>,
        routing: <><circle cx="6" cy="6" r="2.5" /><circle cx="18" cy="18" r="2.5" /><path d="M8.5 6H15a3 3 0 0 1 3 3v6.5M15.5 18H9a3 3 0 0 1-3-3V8.5" /></>,
        message: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></>,
        alert: <><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></>,
        wrench: <><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" /></>,
    };
    return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>;
}

export default function Feedback({
    csrfToken,
    submitted,
    reference,
    departments = [],
    wards = [],
    initialCategory = null,
    initialWard = null,
}: {
    csrfToken: string;
    submitted: boolean;
    reference: string | null;
    departments?: DepartmentOption[];
    wards?: WardOption[];
    initialCategory?: string | null;
    initialWard?: string | null;
}) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const { errors } = usePage().props as { errors?: Record<string, string> };
    const formErrors = (errors ?? {}) as Record<string, string>;

    const defaultCategory = initialCategory ?? 'fault_report';
    const [category, setCategory] = useState<string>(defaultCategory);
    const [selectedWard, setSelectedWard] = useState<string>(initialWard ?? '');
    const [locationDetail, setLocationDetail] = useState<string>('');
    const [subject, setSubject] = useState<string>('');

    const isFault = category === 'fault_report';

    const handlePresetSelect = (presetSubject: string) => {
        setSubject(presetSubject);
    };

    return (
        <PublicLayout>
            <Head title={isFault ? `${t('faultReport')} · Mutoko RDC` : t('feedbackPage')} />
            <header className="about-page-hero contact-hero">
                <div className="about-page-hero-overlay" aria-hidden="true" />
                <div className="container">
                    <nav className="about-page-breadcrumb" aria-label={t('breadcrumb')}>
                        <a href={`/${locale}`}>{t('home')}</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">{t('feedbackPage')}</span>
                    </nav>
                    <span className="about-accent-eyebrow">{isFault ? t('reportIssueTitle') : t('haveYourSay')}</span>
                    <h1>{isFault ? t('faultReport') : t('feedbackPage')}</h1>
                    <p className="about-page-hero-subtitle">{t('feedbackDesc')}</p>
                </div>
            </header>

            <section className="contact-page">
                <div className="container">
                    <section className="contact-columns" aria-label="How feedback works and feedback form">
                        <div className="contact-panel contact-info-col">
                            <span className="contact-eyebrow">{t('howItWorks')}</span>
                            <h2 id="feedback-info-heading">{t('whatHappensNext')}</h2>
                            <p className="contact-intro">{t('feedbackIntro')}</p>
                            <ul className="contact-details-list">
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="tracking" /></span>
                                    <div><strong>{t('trackedSubmission')}</strong><span className="contact-office">{t('trackedSubmissionDesc')}</span></div>
                                </li>
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="response" /></span>
                                    <div><strong>{t('response48')}</strong><span className="contact-office">{t('response48Desc')}</span></div>
                                </li>
                                <li>
                                    <span className="contact-detail-icon"><FeedbackIcon name="routing" /></span>
                                    <div><strong>{t('directRouting')}</strong><span className="contact-office">{t('directRoutingDesc')}</span></div>
                                </li>
                            </ul>
                            <div className="contact-info-note">
                                <strong>{t('complaintsPrivate')}</strong>
                                <p>{t('complaintsPrivateDesc')}</p>
                            </div>
                        </div>

                        <div className="contact-panel contact-form-col" id="feedback-form">
                            <div className="fault-mode-tabs" role="tablist" aria-label={t('feedbackType')}>
                                <button
                                    type="button"
                                    role="tab"
                                    className={`fault-tab-btn ${isFault ? 'active' : ''}`}
                                    aria-pressed={isFault}
                                    onClick={() => setCategory('fault_report')}
                                >
                                    <FeedbackIcon name="wrench" />
                                    <span>{t('faultReport')}</span>
                                </button>
                                <button
                                    type="button"
                                    role="tab"
                                    className={`fault-tab-btn ${!isFault ? 'active' : ''}`}
                                    aria-pressed={!isFault}
                                    onClick={() => setCategory('feedback')}
                                >
                                    <FeedbackIcon name="message" />
                                    <span>{t('feedbackPage')}</span>
                                </button>
                            </div>

                            <div className="contact-form-heading">
                                <div>
                                    <span className="contact-eyebrow">
                                        {isFault ? t('citizenPill') : t('sendFeedbackEyebrow')}
                                    </span>
                                    <h2>{isFault ? t('reportIssueTitle') : t('shareFeedback')}</h2>
                                    <p className="contact-intro">
                                        {isFault ? t('reportIssueDesc') : t('shareFeedbackDesc')}
                                    </p>
                                </div>
                                <span className="contact-form-heading-icon" aria-hidden="true">
                                    <FeedbackIcon name={isFault ? 'wrench' : 'message'} />
                                </span>
                            </div>

                            {isFault && (
                                <div className="fault-emergency-banner" role="note">
                                    <FeedbackIcon name="alert" />
                                    <span>{t('faultReportingNotice')}</span>
                                </div>
                            )}

                            {submitted && (
                                <div className="fault-success-card" role="status">
                                    <div className="fault-success-header">
                                        <span className="fault-success-icon" aria-hidden="true">✓</span>
                                        <div>
                                            <h3 className="fault-success-title">{t('feedbackReceived')}</h3>
                                            <p className="fault-success-ref">
                                                <span>REF:</span> <strong>{reference}</strong>
                                            </p>
                                        </div>
                                    </div>
                                    <p className="fault-success-note">
                                        {t('trackedSubmissionDesc')} {t('response48Desc')} Quote this reference when following up via Council offices or WhatsApp.
                                    </p>
                                </div>
                            )}

                            {Object.keys(formErrors).length > 0 && (
                                <div className="contact-error-summary" role="alert">
                                    <p>{t('formErrorsNotice')}:</p>
                                    <ul>
                                        {Object.entries(formErrors).map(([field, message]) => (
                                            <li key={field}>{message}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            <form className="contact-form" action={`/${locale}/feedback`} method="post">
                                <input type="hidden" name="_token" value={csrfToken} />

                                {selectedWard ? <input type="hidden" name="context_type" value="ward" /> : null}

                                <div className="contact-field">
                                    <label htmlFor="feedback-category">
                                        {t('category')} <span aria-hidden="true">*</span>
                                    </label>
                                    <select
                                        id="feedback-category"
                                        name="category"
                                        required
                                        value={category}
                                        onChange={(e) => setCategory(e.target.value)}
                                        aria-describedby={formErrors.category ? 'feedback-category-error' : undefined}
                                    >
                                        {FEEDBACK_CATEGORIES.map((cat) => (
                                            <option key={cat.value} value={cat.value}>
                                                {t(cat.label)}
                                            </option>
                                        ))}
                                    </select>
                                    <FieldError errors={formErrors} field="category" />
                                </div>

                                <div className="contact-field">
                                    <label htmlFor="feedback-ward">
                                        {t('selectWard')} {isFault && <span aria-hidden="true">*</span>}
                                    </label>
                                    <select
                                        id="feedback-ward"
                                        name="context_reference"
                                        value={selectedWard}
                                        onChange={(e) => setSelectedWard(e.target.value)}
                                    >
                                        <option value="">{t('selectWardPlaceholder')}</option>
                                        {wards.map((ward) => (
                                            <option key={ward.slug} value={ward.slug}>
                                                {ward.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {isFault && (
                                    <div className="contact-field contact-field-full">
                                        <label>{t('faultType')}</label>
                                        <div className="fault-category-pills">
                                            {FAULT_PRESETS.map((preset) => (
                                                <button
                                                    key={preset.key}
                                                    type="button"
                                                    className={`fault-pill ${subject === preset.subject ? 'selected' : ''}`}
                                                    onClick={() => handlePresetSelect(preset.subject)}
                                                >
                                                    {t(preset.labelKey)}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                <div className="contact-field">
                                    <label htmlFor="feedback-name">
                                        {t('name')} <span aria-hidden="true">*</span>
                                    </label>
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
                                    <label htmlFor="feedback-email">
                                        {t('email')} <span aria-hidden="true">*</span>
                                    </label>
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
                                    <label htmlFor="feedback-department">{t('selectDepartment')}</label>
                                    <select
                                        id="feedback-department"
                                        name="department_id"
                                        defaultValue=""
                                        aria-describedby={formErrors.department_id ? 'feedback-department_id-error' : undefined}
                                    >
                                        <option value="">—</option>
                                        {departments.map((department) => (
                                            <option key={department.id} value={department.id}>
                                                {department.public_name ?? department.name}
                                            </option>
                                        ))}
                                    </select>
                                    <FieldError errors={formErrors} field="department_id" />
                                </div>

                                <div className="contact-field contact-field-full">
                                    <label htmlFor="feedback-location-detail">
                                        {t('specificLocation')}
                                    </label>
                                    <input
                                        id="feedback-location-detail"
                                        name="location_detail"
                                        value={locationDetail}
                                        onChange={(e) => setLocationDetail(e.target.value)}
                                        placeholder={t('enterSpecificLocation')}
                                        maxLength={160}
                                    />
                                </div>

                                <div className="contact-field contact-field-full">
                                    <label htmlFor="feedback-subject">
                                        {t('subject')} <span aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="feedback-subject"
                                        name="subject"
                                        required
                                        maxLength={200}
                                        value={subject}
                                        onChange={(e) => setSubject(e.target.value)}
                                        placeholder={t('describeEnquiry')}
                                        aria-describedby={formErrors.subject ? 'feedback-subject-error' : undefined}
                                    />
                                    <FieldError errors={formErrors} field="subject" />
                                </div>

                                <div className="contact-field contact-field-full">
                                    <label htmlFor="feedback-message">
                                        {t('message')} <span aria-hidden="true">*</span>
                                    </label>
                                    <textarea
                                        id="feedback-message"
                                        name="message"
                                        required
                                        minLength={10}
                                        maxLength={5000}
                                        rows={6}
                                        placeholder={isFault ? `${t('enterSpecificLocation')}. Describe what broke down, when it started, and who is affected...` : t('howCanWeHelp')}
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
                                        <label htmlFor="feedback-consent">
                                            {t('consentText')} <span aria-hidden="true">*</span>
                                        </label>
                                    </div>
                                    <p className="contact-privacy-note">{t('enquiryHelp')}</p>
                                    <button className="contact-submit" type="submit">
                                        {isFault ? t('reportIssueBtn') : t('submitFeedback')}{' '}
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    {departments.length > 0 && (
                        <section className="contact-departments" aria-labelledby="feedback-departments-heading">
                            <div className="contact-section-heading">
                                <div>
                                    <span className="contact-eyebrow">{t('contactRightTeam')}</span>
                                    <h2 id="feedback-departments-heading">{t('councilDepartments')}</h2>
                                    <p>{t('contactDeptDesc')}</p>
                                </div>
                                <a href={`/${locale}/departments`}>
                                    View all departments <span aria-hidden="true">→</span>
                                </a>
                            </div>
                            <ul className="contact-department-grid">
                                {departments.map((department) => (
                                    <li key={department.id}>
                                        <span className="contact-department-mark" aria-hidden="true">
                                            {(department.public_name ?? department.name).charAt(0)}
                                        </span>
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
                                <span className="contact-eyebrow">{t('contactLookingFor')}</span>
                                <h2 id="feedback-links-heading">{t('quickLinks')}</h2>
                                <p>{t('contactQuickLinksDesc')}</p>
                            </div>
                        </div>
                        <div className="contact-link-grid">
                            <a href={`/${locale}/contact`}>
                                {t('contactUs')} <span aria-hidden="true">→</span>
                            </a>
                            <a href={`/${locale}/services`}>
                                {t('councilServices')} <span aria-hidden="true">→</span>
                            </a>
                            <a href={`/${locale}/notices`}>
                                {t('publicNotices')} <span aria-hidden="true">→</span>
                            </a>
                            <a href={`/${locale}/documents`}>
                                {t('linkDocsDownloads')} <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </section>
                </div>
            </section>
        </PublicLayout>
    );
}
