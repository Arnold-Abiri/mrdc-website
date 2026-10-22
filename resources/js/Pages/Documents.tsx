import { useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import { usePublicTranslation, usePublicLocale } from '../usePublicTranslation';
import type { TranslationKey } from '../localization';
import { PageHero } from '../Components/public/PageHeader';
import { SourcedText } from '../Components/public/SourceButtons';

export type DocumentItem = {
    slug: string;
    title: string;
    description: string | null;
    category: string;
    published_at: string | null;
    reference_date: string | null;
    download_count: number;
    file_size?: string;
    mime_type?: string;
    department?: string | null;
};

interface DocumentsProps {
    documents: DocumentItem[];
    categories: string[];
    category_counts?: Record<string, number>;
    years: number[];
    total_count?: number;
    filters: {
        category: string | null;
        year: string | null;
        department: string | null;
        q: string;
    };
}

const CATEGORY_LABELS: Record<string, { titleKey: TranslationKey; descKey: TranslationKey }> = {
    bylaw: { titleKey: 'catBylaw', descKey: 'catBylawDesc' },
    policy: { titleKey: 'catPolicy', descKey: 'catPolicyDesc' },
    budget: { titleKey: 'catBudget', descKey: 'catBudgetDesc' },
    financial_statement: { titleKey: 'catFinStatement', descKey: 'catFinStatementDesc' },
    financial_report: { titleKey: 'catFinReport', descKey: 'catFinReportDesc' },
    plan: { titleKey: 'catPlan', descKey: 'catPlanDesc' },
    strategic_plan: { titleKey: 'catStrategic', descKey: 'catStrategicDesc' },
    procurement_plan: { titleKey: 'catProcurement', descKey: 'catProcurementDesc' },
    awards_register: { titleKey: 'catAwards', descKey: 'catAwardsDesc' },
    form: { titleKey: 'catForm', descKey: 'catFormDesc' },
    notice: { titleKey: 'catNotice', descKey: 'catNoticeDesc' },
    minutes: { titleKey: 'catMinutes', descKey: 'catMinutesDesc' },
    agenda: { titleKey: 'catAgenda', descKey: 'catAgendaDesc' },
    schedule: { titleKey: 'catSchedule', descKey: 'catScheduleDesc' },
    publication: { titleKey: 'catPublication', descKey: 'catPublicationDesc' },
    other: { titleKey: 'catOther', descKey: 'catOtherDesc' },
};

const STATUTORY_ACTS: { titleKey: TranslationKey; descKey: TranslationKey }[] = [
    { titleKey: 'statRDC', descKey: 'statRDCDesc' },
    { titleKey: 'statConstitution', descKey: 'statConstitutionDesc' },
    { titleKey: 'statProcurement', descKey: 'statProcurementDesc' },
    { titleKey: 'statFinance', descKey: 'statFinanceDesc' },
    { titleKey: 'statTraditional', descKey: 'statTraditionalDesc' },
    { titleKey: 'statMasterPlan', descKey: 'statMasterPlanDesc' },
];

function formatDate(dateStr: string | null, recentLabel = 'Recent'): string {
    if (!dateStr) return recentLabel;
    try {
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
        return dateStr;
    }
}

export default function Documents({
    documents,
    categories,
    category_counts = {},
    total_count = documents.length,
    filters,
}: DocumentsProps) {
    const t = usePublicTranslation();
    const locale = usePublicLocale();
    const L = (path: string) => path.startsWith('/#') ? `/${locale}${path.slice(1)}` : path === '/' ? `/${locale}` : path.startsWith('/') && !path.startsWith('//') ? `/${locale}${path}` : path;

    const [searchQuery, setSearchQuery] = useState(filters.q || '');
    const [selectedCategory, setSelectedCategory] = useState<string>(filters.category || 'all');
    const [selectedYear, setSelectedYear] = useState<string>(filters.year || '');

    // Filter documents in memory for instant feedback
    const filteredDocuments = useMemo(() => {
        return documents.filter((doc) => {
            if (selectedCategory !== 'all' && doc.category !== selectedCategory) {
                return false;
            }
            if (selectedYear) {
                const year = doc.reference_date?.slice(0, 4);
                if (year !== selectedYear) return false;
            }
            if (searchQuery.trim()) {
                const q = searchQuery.toLowerCase().trim();
                const titleMatch = doc.title.toLowerCase().includes(q);
                const descMatch = doc.description?.toLowerCase().includes(q) ?? false;
                const catMatch = doc.category.toLowerCase().includes(q);
                const deptMatch = doc.department?.toLowerCase().includes(q) ?? false;
                if (!titleMatch && !descMatch && !catMatch && !deptMatch) {
                    return false;
                }
            }
            return true;
        });
    }, [documents, selectedCategory, selectedYear, searchQuery]);

    // Group filtered documents by category
    const groupedDocuments = useMemo(() => {
        const groups: Record<string, DocumentItem[]> = {};
        for (const doc of filteredDocuments) {
            if (!groups[doc.category]) {
                groups[doc.category] = [];
            }
            groups[doc.category].push(doc);
        }
        return groups;
    }, [filteredDocuments]);

    const activeCategoriesWithDocs = Object.keys(groupedDocuments);

    // Compute key quick stats
    const bylawCount = category_counts['bylaw'] ?? documents.filter((d) => d.category === 'bylaw').length;
    const budgetCount = (category_counts['budget'] ?? 0) + (category_counts['financial_statement'] ?? 0) + (category_counts['financial_report'] ?? 0) || documents.filter((d) => ['budget', 'financial_statement', 'financial_report'].includes(d.category)).length;
    const minutesCount = (category_counts['minutes'] ?? 0) + (category_counts['agenda'] ?? 0) || documents.filter((d) => ['minutes', 'agenda'].includes(d.category)).length;
    const plansCount = (category_counts['plan'] ?? 0) + (category_counts['strategic_plan'] ?? 0) || documents.filter((d) => ['plan', 'strategic_plan'].includes(d.category)).length;

    // Available categories that actually have at least 1 document OR top priority ones
    const displayedFilterCategories = useMemo(() => {
        const priority = ['bylaw', 'budget', 'plan', 'policy', 'minutes', 'form', 'notice', 'financial_statement', 'procurement_plan', 'publication'];
        const existingWithDocs = Object.keys(category_counts).filter((cat) => (category_counts[cat] ?? 0) > 0);
        const combined = Array.from(new Set([...priority, ...existingWithDocs]));
        return combined.filter((c) => categories.includes(c));
    }, [categories, category_counts]);

    return (
        <PublicLayout>
            <Head title={`${t('councilDocuments')} | Mutoko Rural District Council`} />

            {/* Standard MRDC Hero Header matching other views */}
            <PageHero
                eyebrow={t('docEyebrow')}
                title={t('councilDocuments')}
                subtitle={t('docHeroDesc')}
                crumb={[{ label: t('documents') }]}
            />

            {/* Stats Ribbon matching Services and Projects views */}
            <section className="about-stats-ribbon" aria-label="Document repository statistics">
                <div className="container">
                    <div className="about-stats-flex">
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">100%</div>
                                <div className="about-stat-label">{t('statPublicAccess')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{budgetCount} {t('statFilesUnit')}</div>
                                <div className="about-stat-label">{t('statBudgetsFinance')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{bylawCount} {t('statBylawsUnit')}</div>
                                <div className="about-stat-label">{t('statStatutesRules')}</div>
                            </div>
                        </div>
                        <div className="about-stat-item">
                            <div>
                                <div className="about-stat-num">{plansCount + minutesCount} {t('statRecordsUnit')}</div>
                                <div className="about-stat-label">{t('statPlansMinutes')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Interactive Search & Category Filter Bar */}
            <div className="resources-filter-section" role="region" aria-label={t('docFilterRegion')}>
                <div className="container">
                    <div className="resources-filter-bar">
                        <div className="resources-search-wrapper">
                            <svg className="resources-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.3" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <line x1="16.5" y1="16.5" x2="21" y2="21" />
                            </svg>
                            <input
                                type="text"
                                className="resources-search-input"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder={t('docSearchPlaceholder')}
                                aria-label={t('docSearchAria')}
                            />
                            {searchQuery && (
                                <button
                                    type="button"
                                    className="resources-search-clear"
                                    onClick={() => setSearchQuery('')}
                                    aria-label={t('docClearSearch')}
                                >
                                    &times;
                                </button>
                            )}
                        </div>

                        {/* Category filter pills */}
                        <div className="resources-pills-wrap" role="tablist" aria-label={t('sideDocCats')}>
                            <button
                                type="button"
                                role="tab"
                                aria-selected={selectedCategory === 'all'}
                                className={`resources-filter-pill${selectedCategory === 'all' ? ' active' : ''}`}
                                onClick={() => setSelectedCategory('all')}
                            >
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                                <span>{t('docAllCategories')}</span>
                                <span className="pill-count">{total_count}</span>
                            </button>

                            {displayedFilterCategories.map((catKey) => {
                                const meta = CATEGORY_LABELS[catKey] || { titleKey: 'catOther' as const, descKey: 'docFallbackDesc' as const };
                                const count = category_counts[catKey] ?? documents.filter((d) => d.category === catKey).length;
                                return (
                                    <button
                                        key={catKey}
                                        type="button"
                                        role="tab"
                                        aria-selected={selectedCategory === catKey}
                                        className={`resources-filter-pill${selectedCategory === catKey ? ' active' : ''}`}
                                        onClick={() => setSelectedCategory(catKey)}
                                    >
                                        <span>{t(meta.titleKey)}</span>
                                        {count > 0 && <span className="pill-count">{count}</span>}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content Area: Document List + Civic Sticky Sidebar */}
            <section className="resources-main-section">
                <div className="container">
                    <div className="resources-portal-layout">
                        {/* Left Main Column: Categorized Document Cards */}
                        <div className="resources-content-col">
                            {/* Filter Summary / Active Status */}
                            {(searchQuery || selectedCategory !== 'all' || selectedYear) && (
                                <div className="resources-active-filters-bar">
                                    <span>
                                        {t('docShowing')} <strong>{filteredDocuments.length}</strong> {filteredDocuments.length === 1 ? t('docOne') : t('docMany')}
                                        {selectedCategory !== 'all' && (
                                            <> {t('docIn')} <strong>{selectedCategory !== 'all' && CATEGORY_LABELS[selectedCategory] ? t(CATEGORY_LABELS[selectedCategory].titleKey) : selectedCategory}</strong></>
                                        )}
                                        {searchQuery && (
                                            <> {t('docMatching')} &ldquo;<strong>{searchQuery}</strong>&rdquo;</>
                                        )}
                                    </span>
                                    <button
                                        type="button"
                                        className="resources-reset-filter-link"
                                        onClick={() => {
                                            setSearchQuery('');
                                            setSelectedCategory('all');
                                            setSelectedYear('');
                                        }}
                                    >
                                        {t('docResetFilters')}
                                    </button>
                                </div>
                            )}

                            {/* Empty Results State */}
                            {filteredDocuments.length === 0 ? (
                                <div className="resources-empty-state">
                                    <div className="resources-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="9" y1="15" x2="15" y2="15" />
                                        </svg>
                                    </div>
                                    <h3 className="resources-empty-title">{t('docNoMatch')}</h3>
                                    <p className="resources-empty-desc">
                                        {t('docEmptyDesc')}
                                    </p>
                                    <button
                                        type="button"
                                        className="btn-resource-reset"
                                        onClick={() => {
                                            setSearchQuery('');
                                            setSelectedCategory('all');
                                            setSelectedYear('');
                                        }}
                                    >
                                        {t('docClearFilters')}
                                    </button>
                                </div>
                            ) : (
                                <div className="resources-categories-list">
                                    {activeCategoriesWithDocs.map((categoryKey) => {
                                        const docsInCat = groupedDocuments[categoryKey] || [];
                                        const meta = CATEGORY_LABELS[categoryKey] || {
                                            titleKey: 'catOther' as const,
                                            descKey: 'docFallbackDesc' as const,
                                        };

                                        return (
                                            <div key={categoryKey} className="resource-category-card" data-category={categoryKey}>
                                                <div className="resource-category-header">
                                                    <div className="resource-category-title-wrap">
                                                        <div className="resource-category-icon" aria-hidden="true">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h2 className="resource-category-title">{t(meta.titleKey)}</h2>
                                                            <p className="resource-category-desc">{t(meta.descKey)}</p>
                                                        </div>
                                                    </div>
                                                    <span className="resource-category-badge">
                                                        {docsInCat.length} {docsInCat.length === 1 ? t('catOne') : t('catMany')}
                                                    </span>
                                                </div>

                                                <div className="resource-item-list" role="list">
                                                    {docsInCat.map((doc) => (
                                                        <div key={doc.slug} className="resource-doc-item" role="listitem">
                                                            <div className="resource-file-icon" aria-hidden="true">
                                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                                                </svg>
                                                                <span className="resource-file-ext">PDF</span>
                                                            </div>

                                                            <div className="resource-doc-info">
                                                                <div className="resource-doc-header">
                                                                    <h3 className="resource-doc-title">
                                                                        <Link href={L(`/documents/${doc.slug}`)}>
                                                                            {doc.title}
                                                                        </Link>
                                                                    </h3>
                                                                    {doc.file_size && (
                                                                        <span className="resource-doc-size">{doc.file_size}</span>
                                                                    )}
                                                                </div>

                                                                {doc.description && (
                                                                    <div className="resource-doc-desc"><SourcedText text={doc.description} /></div>
                                                                )}

                                                                <div className="resource-doc-meta">
                                                                    <div className="resource-meta-item">
                                                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                                            <line x1="16" y1="2" x2="16" y2="6" />
                                                                            <line x1="8" y1="2" x2="8" y2="6" />
                                                                            <line x1="3" y1="10" x2="21" y2="10" />
                                                                        </svg>
                                                                        <span>{t('publishedOn')}: {formatDate(doc.published_at || doc.reference_date, t('docRecent'))}</span>
                                                                    </div>

                                                                    {doc.department && (
                                                                        <div className="resource-meta-item">
                                                                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                                                <path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4" />
                                                                            </svg>
                                                                            <span>{doc.department}</span>
                                                                        </div>
                                                                    )}

                                                                    <div className="resource-meta-item">
                                                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                            <polyline points="7 10 12 15 17 10" />
                                                                            <line x1="12" y1="15" x2="12" y2="3" />
                                                                        </svg>
                                                                        <span>{doc.download_count} {t('downloadCount')}</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div className="resource-actions-col">
                                                                <a
                                                                    href={L(`/documents/${doc.slug}/download`)}
                                                                    className="resource-download-btn"
                                                                    aria-label={`${t('docDownloadFor')} ${doc.title}`}
                                                                >
                                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                        <polyline points="7 10 12 15 17 10" />
                                                                        <line x1="12" y1="15" x2="12" y2="3" />
                                                                    </svg>
                                                                    <span>{t('docDownload')}</span>
                                                                </a>
                                                                <Link
                                                                    href={L(`/documents/${doc.slug}`)}
                                                                    className="resource-view-btn"
                                                                    aria-label={`${t('docViewDetailsFor')} ${doc.title}`}
                                                                >
                                                                    {t('docDetails')}
                                                                </Link>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}

                            {/* Statutory Acts & Constitutional Baseline Section */}
                            <div className="statutory-baseline-card">
                                <div className="statutory-card-header">
                                    <div className="statutory-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M12 2v20" />
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 className="statutory-title">{t('statuteTitle')}</h3>
                                        <p className="statutory-desc">{t('statuteDesc')}</p>
                                    </div>
                                </div>
                                <div className="statutory-grid">
                                    {STATUTORY_ACTS.map((act) => (
                                        <div key={act.titleKey} className="statutory-item">
                                            <strong className="statutory-item-title">{t(act.titleKey)}</strong>
                                            <p className="statutory-item-desc">{t(act.descKey)}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Right Column: Harare-inspired Civic Sticky Sidebar */}
                        <aside className="resources-sidebar-col" aria-label="Council shortcuts and tourism information">
                            <div className="resources-sticky-wrapper">
                                {/* 1. Mutoko District & Tourism Spotlight Card */}
                                <div className="sidebar-card tourism-spotlight-card">
                                    <div className="tourism-card-body">
                                        <div className="tourism-badge-row">
                                            <span className="tourism-badge">
                                                {t('spotBadge')}
                                            </span>
                                            <svg className="tourism-compass-icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10" />
                                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" />
                                            </svg>
                                        </div>
                                        <h4 className="tourism-title">{t('spotTitle')}</h4>
                                        <p className="tourism-desc">
                                            {t('spotDesc')}
                                        </p>

                                        <div className="tourism-meta-box">
                                            <div className="tourism-meta-row">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                                    <circle cx="12" cy="9" r="2.5" />
                                                </svg>
                                                <span>Stand 114, Mutoko Growth Point</span>
                                            </div>
                                            <div className="tourism-meta-row">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3" />
                                                </svg>
                                                <span>+263 77 240 5683 / +263 71 280 5683</span>
                                            </div>
                                        </div>

                                        <a href={L('/tourism')} className="btn-tourism-explore">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                                                <line x1="8" y1="2" x2="8" y2="18" />
                                                <line x1="16" y1="6" x2="16" y2="22" />
                                            </svg>
                                            <span>{t('spotExplore')}</span>
                                        </a>
                                    </div>
                                </div>

                                {/* 2. Council Quick Links Card */}
                                <div className="sidebar-card">
                                    <div className="sidebar-card-header">
                                        <div className="sidebar-header-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                                                <circle cx="12" cy="12" r="10" />
                                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" />
                                            </svg>
                                        </div>
                                        <h4 className="sidebar-card-title">{t('sideQuickLinks')}</h4>
                                    </div>
                                    <div className="sidebar-links-list">
                                        <a href={L('/rates')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                                    <line x1="2" y1="10" x2="22" y2="10" />
                                                </svg>
                                                <span>{t('sideRates')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>

                                        <a href={L('/services')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <path d="M19 12h-2V8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2v4H5a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z" />
                                                </svg>
                                                <span>{t('sideServices')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>

                                        <a href={L('/tenders')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2z" />
                                                </svg>
                                                <span>{t('sideTenders')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>

                                        <a href={L('/wards')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                                                </svg>
                                                <span>{t('sideWards')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>

                                        <a href={L('/notices')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                                                </svg>
                                                <span>{t('sideNotices')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>

                                        <a href={L('/feedback')} className="sidebar-link-item">
                                            <div className="sidebar-link-content">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                                </svg>
                                                <span>{t('sideFeedback')}</span>
                                            </div>
                                            <span className="sidebar-link-arrow" aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>

                                {/* 3. Document Category Directory Shortcuts */}
                                <div className="sidebar-card">
                                    <div className="sidebar-card-header">
                                        <div className="sidebar-header-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                            </svg>
                                        </div>
                                        <h4 className="sidebar-card-title">{t('sideDocCats')}</h4>
                                    </div>
                                    <div className="sidebar-links-list">
                                        {[
                                            { key: 'bylaw', labelKey: 'catBylaw' as TranslationKey },
                                            { key: 'budget', labelKey: 'catBudget' as TranslationKey },
                                            { key: 'financial_statement', labelKey: 'catFinStatement' as TranslationKey },
                                            { key: 'plan', labelKey: 'catPlan' as TranslationKey },
                                            { key: 'minutes', labelKey: 'catMinutes' as TranslationKey },
                                            { key: 'policy', labelKey: 'catPolicy' as TranslationKey },
                                            { key: 'form', labelKey: 'catForm' as TranslationKey },
                                            { key: 'procurement_plan', labelKey: 'catProcurement' as TranslationKey },
                                        ].map((item) => (
                                            <button
                                                key={item.key}
                                                type="button"
                                                className={`sidebar-link-item w-full text-left${selectedCategory === item.key ? ' active-filter' : ''}`}
                                                onClick={() => {
                                                    setSelectedCategory(item.key);
                                                    window.scrollTo({ top: 400, behavior: 'smooth' });
                                                }}
                                            >
                                                <div className="sidebar-link-content">
                                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--color-council-green)' }} aria-hidden="true">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                        <polyline points="14 2 14 8 20 8" />
                                                    </svg>
                                                    <span>{t(item.labelKey)}</span>
                                                </div>
                                                <span className="sidebar-category-count">
                                                    {category_counts[item.key] ?? documents.filter((d) => d.category === item.key).length}
                                                </span>
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
