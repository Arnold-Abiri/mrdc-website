import { Head, usePage } from '@inertiajs/react';

type SeoProps = {
    title: string;
    description?: string | null;
    image?: string | null;
    type?: 'website' | 'article';
    schema?: Record<string, unknown> | null;
};

/**
 * Canonical, Open Graph, and structured-data metadata for public pages.
 * hreflang alternates are rendered server-side in app.blade.php.
 */
export function SeoHead({ title, description, image, type = 'article', schema = null }: SeoProps) {
    const { url } = usePage().props as { url?: string };
    const path = (url ?? '/').split('?')[0];
    const canonical = typeof window !== 'undefined' ? window.location.origin + path : path;
    return (
        <Head>
            <title>{title}</title>
            {description && <meta name="description" content={description} />}
            <meta property="og:title" content={title} />
            {description && <meta property="og:description" content={description} />}
            <meta property="og:url" content={canonical} />
            <meta property="og:type" content={type} />
            <meta property="og:site_name" content="Mutoko Rural District Council" />
            {image && <meta property="og:image" content={image} />}
            {schema && <script type="application/ld+json">{JSON.stringify(schema)}</script>}
        </Head>
    );
}

export function organizationSchema(): Record<string, unknown> {
    return {
        '@context': 'https://schema.org',
        '@type': 'GovernmentOrganization',
        name: 'Mutoko Rural District Council',
        areaServed: 'Mutoko District, Mashonaland East, Zimbabwe',
    };
}
