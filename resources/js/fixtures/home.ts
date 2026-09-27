/** DEVELOPMENT / DESIGN FIXTURE — NOT PRODUCTION COUNCIL DATA. */
export type NewsPreview = { title: string; date: string; summary: string; image?: string; href?: string };
export type EventPreview = { day: string; month: string; title: string; time: string; location: string; href?: string };

// Empty until approved, published content is available. Component states support future CMS data.
export const newsPreviews: NewsPreview[] = [];
export const eventPreviews: EventPreview[] = [];
