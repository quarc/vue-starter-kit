import '../css/app.css';
import { formatDate } from './lib/datetime';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createI18n } from 'vue-i18n';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Load translation files
const loadMessages = () => {
    const modules = import.meta.glob('../../lang/*.json', { eager: true });
    const messages: Record<string, any> = {};

    for (const path in modules) {
        const match = path.match(/\.\.\/\.\.\/lang\/([^/]+)\.json$/);
        if (match) {
            const locale = match[1];
            messages[locale] = (modules[path] as any).default;
        }
    }
};

const messages = loadMessages();

// Auto-discover and register plugins
const plugins = import.meta.glob('./components/modules/**/index.ts', {
    eager: true,
});
console.log('Discovered plugins:', Object.keys(plugins));

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const sharedDatetimeFormats: any = {};

        const datetimeFormats = {
            en: sharedDatetimeFormats,
            uk: sharedDatetimeFormats,
            es: sharedDatetimeFormats,
        };

        const i18n = createI18n({
            legacy: false,
            locale: (props.initialPage.props.locale as string) || 'en',
            fallbackLocale: 'en',
            messages,
            datetimeFormats,
        });

        // Update locale when navigating
        router.on('success', (event) => {
            const locale = event.detail.page.props.locale as string;
            console.log(
                'Inertia Success: New locale:',
                locale,
                'Current i18n:',
                i18n.global.locale.value,
            );
            if (locale && i18n.global.locale.value !== locale) {
                i18n.global.locale.value = locale;
                console.log('Updated i18n locale to:', locale);
            }
        });

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n);

        // Register global $date helper
        app.config.globalProperties.$date = (
            date: string | Date | number,
            format: string,
        ) => {
            return formatDate(date, format, i18n.global.locale.value as string);
        };

        app.mount(el);

        // Dynamic loading of theme based on configuration
        const theme =
            (props.initialPage.props.brand as any)?.theme || 'default';

        if (theme && theme !== 'default') {
            import(`../css/themes/${theme}.css`).catch((err) => {
                console.warn(`Failed to load theme: ${theme}`, err);
            });
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
