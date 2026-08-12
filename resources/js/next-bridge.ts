import type { Page } from '@inertiajs/core';
import { createInertiaApp, router } from '@inertiajs/vue3';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import type { App as VueApp, DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';

type PageModule = { default: DefineComponent };
type PageLoader = () => Promise<PageModule>;

const pageLoaders: Record<string, PageLoader> = {
    'about/Index': () => import('./pages/about/Index.vue'),
    'admin/Bookings': () => import('./pages/admin/Bookings.vue'),
    'admin/Categories': () => import('./pages/admin/Categories.vue'),
    'admin/Dashboard': () => import('./pages/admin/Dashboard.vue'),
    'admin/Finance/Index': () => import('./pages/admin/Finance/Index.vue'),
    'admin/Profile': () => import('./pages/admin/Profile.vue'),
    'admin/Reviews': () => import('./pages/admin/Reviews.vue'),
    'admin/Services': () => import('./pages/admin/Services.vue'),
    'admin/Settings': () => import('./pages/admin/Settings.vue'),
    'admin/Stats': () => import('./pages/admin/Stats.vue'),
    'admin/Users': () => import('./pages/admin/Users.vue'),
    'ai-planner/Index': () => import('./pages/ai-planner/Index.vue'),
    'auth/ConfirmPassword': () => import('./pages/auth/ConfirmPassword.vue'),
    'auth/ForgotPassword': () => import('./pages/auth/ForgotPassword.vue'),
    'auth/Login': () => import('./pages/auth/Login.vue'),
    'auth/Register': () => import('./pages/auth/Register.vue'),
    'auth/ResetPassword': () => import('./pages/auth/ResetPassword.vue'),
    'auth/TwoFactorChallenge': () => import('./pages/auth/TwoFactorChallenge.vue'),
    'auth/VerifyEmail': () => import('./pages/auth/VerifyEmail.vue'),
    'categories/Index': () => import('./pages/categories/Index.vue'),
    'chat/Index': () => import('./pages/chat/Index.vue'),
    'contact/Index': () => import('./pages/contact/Index.vue'),
    'customer/bookings/Index': () => import('./pages/customer/bookings/Index.vue'),
    'customer/bookings/Show': () => import('./pages/customer/bookings/Show.vue'),
    'customer/bookings/Success': () => import('./pages/customer/bookings/Success.vue'),
    'customer/Dashboard': () => import('./pages/customer/Dashboard.vue'),
    'customer/favorites/Index': () => import('./pages/customer/favorites/Index.vue'),
    'customer/notifications/Index': () => import('./pages/customer/notifications/Index.vue'),
    'customer/payment/Checkout': () => import('./pages/customer/payment/Checkout.vue'),
    'customer/Profile': () => import('./pages/customer/Profile.vue'),
    'customer/reviews/Create': () => import('./pages/customer/reviews/Create.vue'),
    'customer/Wallet/Index': () => import('./pages/customer/Wallet/Index.vue'),
    'errors/Error': () => import('./pages/errors/Error.vue'),
    'policy/Index': () => import('./pages/policy/Index.vue'),
    'provider/Availability': () => import('./pages/provider/Availability.vue'),
    'provider/Bookings': () => import('./pages/provider/Bookings.vue'),
    'provider/bookings/Show': () => import('./pages/provider/bookings/Show.vue'),
    'provider/Dashboard': () => import('./pages/provider/Dashboard.vue'),
    'provider/Finance/Index': () => import('./pages/provider/Finance/Index.vue'),
    'provider/notifications/Index': () => import('./pages/provider/notifications/Index.vue'),
    'provider/Profile': () => import('./pages/provider/Profile.vue'),
    'provider/reviews/Index': () => import('./pages/provider/reviews/Index.vue'),
    'provider/Services': () => import('./pages/provider/Services.vue'),
    'provider/services/Create': () => import('./pages/provider/services/Create.vue'),
    'provider/services/Edit': () => import('./pages/provider/services/Edit.vue'),
    'search/Index': () => import('./pages/search/Index.vue'),
    'services/Index': () => import('./pages/services/Index.vue'),
    'services/Show': () => import('./pages/services/Show.vue'),
    'settings/Appearance': () => import('./pages/settings/Appearance.vue'),
    'settings/Password': () => import('./pages/settings/Password.vue'),
    'settings/Profile': () => import('./pages/settings/Profile.vue'),
    'settings/TwoFactor': () => import('./pages/settings/TwoFactor.vue'),
    Welcome: () => import('./pages/Welcome.vue'),
};

let echoConfigured = false;

function configureRealtime(): void {
    if (echoConfigured) return;
    echoConfigured = true;

    const enabled = process.env.NEXT_PUBLIC_REVERB_ENABLED === 'true';
    const key = process.env.NEXT_PUBLIC_REVERB_APP_KEY;
    const host = process.env.NEXT_PUBLIC_REVERB_HOST;

    if (!enabled || !key || !host) return;

    const port = Number(process.env.NEXT_PUBLIC_REVERB_PORT ?? 443);
    const forceTLS =
        (process.env.NEXT_PUBLIC_REVERB_SCHEME ?? 'https') === 'https';

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS,
        authEndpoint: '/broadcasting/auth',
        enabledTransports: ['ws', 'wss'],
    });
}

function resolvePage(name: string): Promise<DefineComponent> {
    const loader = pageLoaders[name];

    if (!loader) {
        throw new Error(`No migrated Vue page is registered for "${name}".`);
    }

    return loader().then((module) => module.default);
}

export async function mountInertiaVueApp(
    id: string,
    initialPage: Page,
): Promise<() => void> {
    let app: VueApp<Element> | null = null;

    initializeTheme();
    configureRealtime();
    gsap.registerPlugin(ScrollTrigger);

    const initialUserId = String(
        ((initialPage.props.auth as { user?: { id?: number | string } })?.user
            ?.id ?? 'guest'),
    );
    localStorage.setItem('auth_user_id', initialUserId);

    const onStorage = (event: StorageEvent) => {
        if (event.key === 'auth_user_id') window.location.reload();
    };
    window.addEventListener('storage', onStorage);

    const stopNavigate = router.on('navigate', (event) => {
        const props = event.detail.page.props as {
            auth?: { user?: { id?: number | string } };
        };
        const userId = String(props.auth?.user?.id ?? 'guest');
        if (localStorage.getItem('auth_user_id') !== userId) {
            localStorage.setItem('auth_user_id', userId);
        }
    });

    await createInertiaApp({
        id,
        page: initialPage,
        title: (title) =>
            title ? `${title} - Dalat Services` : 'Dalat Services',
        resolve: resolvePage,
        setup({ el, App, props, plugin }) {
            if (!el) throw new Error(`Missing Vue mount element #${id}.`);
            const vueApp = createApp({ render: () => h(App, props) });
            vueApp.use(plugin);
            vueApp.mount(el);
            app = vueApp;
        },
        progress: { color: '#2d6a4f' },
    });

    return () => {
        stopNavigate();
        window.removeEventListener('storage', onStorage);
        window.Echo?.disconnect();
        app?.unmount();
    };
}
