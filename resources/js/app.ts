import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { initializeTheme } from '@/composables/useAppearance';

// ─── Register GSAP Plugins ──────────────────────────────────────
gsap.registerPlugin(ScrollTrigger);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const shouldCheckForUpdates = ['localhost', '127.0.0.1'].includes(window.location.hostname);
let activeInertiaVersion: string | null = null;

async function checkForFreshAssets() {
    if (!shouldCheckForUpdates || !activeInertiaVersion) return;

    try {
        const separator = window.location.href.includes('?') ? '&' : '?';
        const response = await fetch(`${window.location.href}${separator}__asset_check=${Date.now()}`, {
            method: 'GET',
            headers: {
                'X-Inertia': 'true',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Inertia-Version': activeInertiaVersion,
                Accept: 'text/html, application/xhtml+xml',
                'Cache-Control': 'no-cache',
                Pragma: 'no-cache',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        if (response.status === 409 && response.headers.get('X-Inertia-Location')) {
            window.location.reload();
        }
    } catch {
        // Ignore transient local-dev network issues while checking for new assets.
    }
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // ─── Multi-Tab Auth Sync ──────────────────────────────────────
        activeInertiaVersion = props.initialPage.version ?? null;
        const pageProps = props.initialPage.props as any;
        const initialUserId = pageProps.auth?.user?.id?.toString() || 'guest';
        localStorage.setItem('auth_user_id', initialUserId);
        
        router.on('navigate', (event) => {
            activeInertiaVersion = event.detail.page.version ?? activeInertiaVersion;
            const eventProps = event.detail.page.props as any;
            const currentUserId = eventProps.auth?.user?.id?.toString() || 'guest';
            if (localStorage.getItem('auth_user_id') !== currentUserId) {
                localStorage.setItem('auth_user_id', currentUserId);
            }
        });

        window.addEventListener('storage', (event) => {
            if (event.key === 'auth_user_id') {
                // Auth state changed in another tab, reload to sync
                window.location.reload();
            }
        });

        if (shouldCheckForUpdates) {
            window.addEventListener('focus', () => {
                void checkForFreshAssets();
            });

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    void checkForFreshAssets();
                }
            });
        }

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#2d6a4f',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// ─── Enhanced Page Transitions ──────────────────────────────────
// Fade out on navigation start
router.on('start', () => {
    gsap.to('main', {
        opacity: 0,
        y: 12,
        duration: 0.2,
        ease: 'power2.in',
    });
});

// Fade in on navigation finish + refresh ScrollTriggers
router.on('finish', () => {
    gsap.fromTo(
        'main',
        { opacity: 0, y: 16 },
        {
            opacity: 1,
            y: 0,
            duration: 0.35,
            ease: 'power3.out',
            onComplete: () => {
                // Refresh after DOM is settled so new ScrollTriggers calculate correctly
                requestAnimationFrame(() => {
                    ScrollTrigger.refresh();
                });
            },
        },
    );
});
