import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import type { ComponentType } from 'react';

const pages = import.meta.glob<{ default: ComponentType }>('./Pages/**/*.tsx');

createInertiaApp({
    progress: {
        color: '#08543c',
        showSpinner: false,
        delay: 150,
    },
    resolve: async (name) => {
        const importPage = pages[`./Pages/${name}.tsx`];
        if (!importPage) throw new Error(`Unknown page: ${name}`);
        const page = await importPage();
        return page.default;
    },
    setup({ el, App, props }) {
        if (!el) throw new Error('Inertia root element is missing');
        createRoot(el).render(<App {...props} />);

        const preloader = document.getElementById('mrdc-initial-preloader');
        if (preloader) {
            preloader.classList.add('mrdc-preloader--done');
            setTimeout(() => {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 400);
        }
    },
});
