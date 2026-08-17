import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Salones Belleza';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        if (name.startsWith('site/')) return SiteLayout;
        if (name.startsWith('auth/')) return AuthLayout;
        if (name.startsWith('settings/')) return [AppLayout, SettingsLayout];
        return AppLayout;
    },
    progress: {
        color: '#ec4899',
    },
    // Habilitar View Transitions API
    resolve: async (name) => {
        const pages = import.meta.glob('./pages/**/*.vue');
        const page = await pages[`./pages/${name}.vue`]();

        // Habilitar transiciones de vista nativas del navegador
        if (page && typeof page === 'object' && 'default' in page) {
            (page as any).default.transition = 'native';
        }

        return page;
    },
});

initializeTheme();
initializeFlashToast();