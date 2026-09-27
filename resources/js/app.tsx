import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import type { ComponentType } from 'react';

const pages = import.meta.glob<{ default: ComponentType }>('./Pages/**/*.tsx', { eager: true });

createInertiaApp({
    resolve: (name) => {
        const page = pages[`./Pages/${name}.tsx`];
        if (!page) throw new Error(`Unknown page: ${name}`);
        return page;
    },
    setup({ el, App, props }) {
        if (!el) throw new Error('Inertia root element is missing');
        createRoot(el).render(<App {...props} />);
    },
});
