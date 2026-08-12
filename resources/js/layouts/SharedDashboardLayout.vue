<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import {
    ArrowLeft,
    Bell,
    ChevronDown,
    ChevronRight,
    ExternalLink,
    Home,
    LogOut,
    Menu,
    Search,
    User,
    X,
} from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';
import FlashToast from '@/components/FlashToast.vue';
import { useRealtimeUserChannel } from '@/composables/useRealtimeUserChannel';
import { useSmoothScroll } from '@/composables/useSmoothScroll';

interface NavItem {
    key: string;
    label: string;
    icon: any;
    href: string;
    badge?: number;
}

const props = withDefaults(
    defineProps<{
        role: 'admin' | 'provider' | 'customer';
        navItems: NavItem[];
        utilNavItems?: NavItem[];
        logoText: string;
        logoTitle: string;
        logoUrl?: string;
        homeUrl?: string;
        showMarketplaceLink?: boolean;
        showNotifications?: boolean;
    }>(),
    {
        utilNavItems: () => [],
        logoUrl: '/',
        homeUrl: '/',
        showMarketplaceLink: false,
        showNotifications: false,
    },
);

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'User');
const userEmail = computed(() => page.props.auth?.user?.email ?? '');
const userAvatar = computed(() => page.props.auth?.user?.anh_dai_dien ?? null);
const userInitial = computed(
    () => userName.value?.charAt(0)?.toUpperCase() ?? 'U',
);
const { unreadNotifications } = useRealtimeUserChannel();
const unreadCount = computed(() => unreadNotifications.value);

const isMobileSidebarOpen = ref(false);
const isUserMenuOpen = ref(false);

useSmoothScroll();

onMounted(() => {
    if (typeof window !== 'undefined') {
        setTimeout(() => {
            ScrollTrigger.refresh();
        }, 300);
    }
});

const allNav = computed(() => [
    ...props.navItems,
    ...(props.utilNavItems || []),
]);

const computedActivePage = computed(() => {
    const path = page.url.split('?')[0];
    const match = allNav.value.find(
        (item) => path.startsWith(item.href) && item.href !== props.logoUrl,
    );
    // Fallback: exact match if none startsWith, else just use the first matched key
    return match?.key ?? allNav.value[0]?.key ?? 'dashboard';
});

const currentPageLabel = computed(() => {
    const item = allNav.value.find((i) => i.key === computedActivePage.value);
    return item?.label ?? 'Dashboard';
});

function closeUserMenuSoon() {
    setTimeout(() => {
        isUserMenuOpen.value = false;
    }, 150);
}

const roleBadgeLabel = computed(() => {
    if (props.role === 'admin') return 'Quản trị viên';
    if (props.role === 'provider') return 'Nhà cung cấp';
    return 'Khách hàng';
});

// Role-based CSS variables
const roleStyle = computed(() => {
    if (props.role === 'admin')
        return {
            '--role-color': 'var(--dl-admin)',
            '--role-surface': 'var(--dl-admin-surface)',
        };
    if (props.role === 'provider')
        return {
            '--role-color': 'var(--dl-brand)',
            '--role-surface': 'var(--dl-brand-surface)',
        };
    return { '--role-color': 'var(--dl-accent)', '--role-surface': '#fef2f2' }; // customer fallback
});

const notificationsUrl = computed(() => {
    if (props.role === 'provider') return '/provider/notifications';
    return '/customer/notifications';
});
</script>

<template>
    <div class="shared-shell" :data-role="role" :style="roleStyle">
        <!-- Sidebar (Desktop) -->
        <aside class="shared-sidebar">
            <!-- Logo -->
            <div class="shared-sidebar__header">
                <Link :href="logoUrl" class="flex items-center gap-2.5">
                    <img
                        src="/favicon.png"
                        alt="Dalat Services"
                        class="shared-sidebar__logo-image"
                    />
                    <div>
                        <p class="shared-sidebar__label">{{ logoText }}</p>
                        <h1 class="shared-sidebar__title">{{ logoTitle }}</h1>
                    </div>
                </Link>
            </div>

            <!-- Main Nav -->
            <nav class="shared-sidebar__nav">
                <p
                    v-if="utilNavItems.length > 0"
                    class="shared-sidebar__section-label"
                >
                    Quản lý
                </p>
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="shared-sidebar__link"
                    :class="{
                        'shared-sidebar__link--active':
                            computedActivePage === item.key,
                    }"
                >
                    <component :is="item.icon" class="size-4.5" />
                    {{ item.label }}
                    <span
                        v-if="item.badge && item.badge > 0"
                        class="shared-sidebar__badge"
                        >{{ item.badge }}</span
                    >
                </Link>

                <template v-if="utilNavItems.length > 0">
                    <div class="shared-sidebar__divider" />
                    <p class="shared-sidebar__section-label">Hệ thống</p>
                    <Link
                        v-for="item in utilNavItems"
                        :key="item.key"
                        :href="item.href"
                        class="shared-sidebar__link"
                        :class="{
                            'shared-sidebar__link--active':
                                computedActivePage === item.key,
                        }"
                    >
                        <component :is="item.icon" class="size-4.5" />
                        {{ item.label }}
                    </Link>
                </template>
            </nav>

            <!-- Bottom user info or footer -->
            <div class="shared-sidebar__footer">
                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                    <span
                        class="shared-sidebar__avatar shrink-0 overflow-hidden"
                    >
                        <img
                            v-if="userAvatar"
                            :src="userAvatar"
                            class="size-full object-cover"
                            referrerpolicy="no-referrer"
                        />
                        <span v-else>{{ userInitial }}</span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p
                            class="truncate text-sm font-medium"
                            style="color: var(--dl-text)"
                        >
                            {{ userName }}
                        </p>
                        <p
                            class="truncate text-xs"
                            style="color: var(--dl-text-muted)"
                        >
                            {{ userEmail }}
                        </p>
                    </div>
                </div>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="shared-sidebar__logout"
                >
                    <LogOut class="size-4" /> Đăng xuất
                </Link>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="shared-content">
            <!-- Topbar -->
            <header class="shared-topbar">
                <button
                    class="shared-topbar__hamburger"
                    @click="isMobileSidebarOpen = !isMobileSidebarOpen"
                >
                    <Menu class="size-5" />
                </button>

                <nav class="shared-topbar__breadcrumb" aria-label="breadcrumb">
                    <Link
                        :href="homeUrl"
                        class="shared-topbar__crumb shared-topbar__crumb--home"
                    >
                        <Home class="size-3.5" />
                        <span>Trang chủ</span>
                    </Link>
                    <ChevronRight class="shared-topbar__crumb-sep" />
                    <span
                        class="shared-topbar__crumb shared-topbar__crumb--current"
                        >{{ currentPageLabel }}</span
                    >
                </nav>

                <div class="flex-1" />

                <!-- Topbar Actions -->
                <div class="shared-topbar__quick-links">
                    <Link
                        v-if="showMarketplaceLink"
                        href="/services"
                        class="shared-topbar__quick-btn"
                    >
                        <Search class="size-3.5" />
                        <span>Marketplace</span>
                    </Link>
                    <Link
                        v-if="role === 'admin'"
                        :href="homeUrl"
                        class="shared-topbar__back-btn"
                    >
                        <ArrowLeft class="shared-topbar__back-arrow size-3.5" />
                        <span>Về Trang chủ</span>
                        <ExternalLink class="size-3 opacity-40" />
                    </Link>
                    <Link
                        v-if="showNotifications"
                        :href="notificationsUrl"
                        class="shared-topbar__bell"
                    >
                        <div class="relative">
                            <Bell class="size-4.5" />
                            <span
                                v-if="unreadCount > 0"
                                class="absolute -top-1 -right-1 flex size-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white"
                            >
                                {{ unreadCount > 9 ? '9+' : unreadCount }}
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button
                        class="shared-topbar__user-btn"
                        @click="isUserMenuOpen = !isUserMenuOpen"
                        @blur="closeUserMenuSoon"
                    >
                        <span class="shared-topbar__avatar overflow-hidden">
                            <img
                                v-if="userAvatar"
                                :src="userAvatar"
                                class="size-full object-cover"
                                referrerpolicy="no-referrer"
                            />
                            <span v-else>{{ userInitial }}</span>
                        </span>
                        <span
                            class="hidden text-sm lg:inline"
                            style="color: var(--dl-text)"
                            >{{ userName }}</span
                        >
                        <ChevronDown
                            class="size-3.5"
                            style="color: var(--dl-text-faint)"
                        />
                    </button>

                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="scale-95 opacity-0 translate-y-1"
                        enter-to-class="scale-100 opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="scale-100 opacity-100 translate-y-0"
                        leave-to-class="scale-95 opacity-0 translate-y-1"
                    >
                        <div
                            v-show="isUserMenuOpen"
                            class="shared-topbar__dropdown"
                        >
                            <div class="border-b border-stone-200 px-4 py-3">
                                <p class="text-sm font-semibold text-stone-900">
                                    {{ userName }}
                                </p>
                                <p class="text-xs text-stone-500">
                                    {{ userEmail }}
                                </p>
                                <span
                                    class="mt-1.5 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                                    style="
                                        background: var(--role-surface);
                                        color: var(--role-color);
                                    "
                                >
                                    {{ roleBadgeLabel }}
                                </span>
                            </div>
                            <div class="p-1.5">
                                <Link
                                    :href="`/${role}/profile`"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-stone-600 transition hover:bg-stone-100"
                                >
                                    <User class="size-4 text-stone-400" /> Hồ sơ
                                </Link>
                            </div>
                            <div class="border-t border-stone-200 p-1.5">
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-600 transition hover:bg-red-50"
                                >
                                    <LogOut class="size-4" /> Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </header>

            <main class="shared-main flex flex-col">
                <div class="flex-1">
                    <slot />
                </div>
                <footer
                    class="mt-12 border-t border-stone-200 py-6 text-center text-sm text-stone-500"
                >
                    <p>Dalat Services &copy; 2025 — {{ roleBadgeLabel }}</p>
                </footer>
            </main>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <div
                v-show="isMobileSidebarOpen"
                class="shared-mobile-sidebar md:hidden"
            >
                <div
                    class="flex items-center justify-between border-b border-stone-200 p-4"
                >
                    <span class="text-sm font-bold text-stone-900">Menu</span>
                    <button
                        @click="isMobileSidebarOpen = false"
                        class="text-stone-400"
                    >
                        <X class="size-5" />
                    </button>
                </div>
                <nav class="space-y-1 p-3">
                    <Link
                        v-for="item in allNav"
                        :key="item.key"
                        :href="item.href"
                        class="shared-sidebar__link"
                        :class="{
                            'shared-sidebar__link--active':
                                computedActivePage === item.key,
                        }"
                        @click="isMobileSidebarOpen = false"
                    >
                        <component :is="item.icon" class="size-4.5" />
                        {{ item.label }}
                        <span
                            v-if="item.badge && item.badge > 0"
                            class="shared-sidebar__badge"
                            >{{ item.badge }}</span
                        >
                    </Link>
                </nav>
            </div>
        </Transition>
        <div
            v-show="isMobileSidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            @click="isMobileSidebarOpen = false"
        />
        <FlashToast />
    </div>
</template>

<style scoped>
.shared-shell {
    display: grid;
    grid-template-columns: var(--dl-sidebar-admin, 260px) 1fr;
    min-height: 100dvh;
    background: var(--dl-warm-bg);
    color: var(--dl-text);
}

@media (max-width: 768px) {
    .shared-shell {
        grid-template-columns: 1fr;
    }
}

/* Sidebar */
.shared-sidebar {
    position: sticky;
    top: 0;
    height: 100dvh;
    border-right: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    display: none;
    flex-direction: column;
}
@media (min-width: 769px) {
    .shared-sidebar {
        display: flex;
    }
}

.shared-sidebar__header {
    padding: var(--dl-space-5);
    border-bottom: 1px solid var(--dl-warm-border);
}
.shared-sidebar__logo-image {
    width: 36px;
    height: 36px;
    object-fit: contain;
}
.shared-sidebar__label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: var(--dl-text-faint);
}
.shared-sidebar__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--dl-text);
}
.shared-sidebar__nav {
    padding: var(--dl-space-3);
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-1);
    flex: 1;
    overflow-y: auto;
}
.shared-sidebar__section-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: var(--dl-text-faint);
    padding: 12px 12px 6px;
}
.shared-sidebar__divider {
    height: 1px;
    background: var(--dl-warm-border);
    margin: 8px 12px;
}
.shared-sidebar__link {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: var(--dl-space-2) var(--dl-space-3);
    border-radius: var(--dl-radius-lg);
    font-size: 13px;
    font-weight: 500;
    color: var(--dl-text-muted);
    transition: var(--dl-transition-fast);
}
.shared-sidebar__link:hover {
    background: var(--role-surface);
    color: var(--role-color);
}
.shared-sidebar__link--active {
    background: var(--role-surface);
    color: var(--role-color);
    box-shadow: inset 3px 0 0 var(--role-color);
    font-weight: 600;
}
.shared-sidebar__badge {
    margin-left: auto;
    border-radius: var(--dl-radius-full);
    background: #f43f5e;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    color: white;
    box-shadow: 0 2px 8px rgba(244, 63, 94, 0.25);
}

.shared-sidebar__footer {
    padding: 16px 20px;
    border-top: 1px solid var(--dl-warm-border);
    text-align: center;
}
.shared-sidebar__avatar {
    display: flex;
    width: 32px;
    height: 32px;
    align-items: center;
    justify-content: center;
    border-radius: var(--dl-radius-lg);
    background: linear-gradient(135deg, var(--role-color), #6366f1);
    font-size: 13px;
    font-weight: 700;
    color: white;
}
.shared-sidebar__logout {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: var(--dl-radius-lg);
    border: 1px solid #fecaca;
    background: #fef2f2;
    padding: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #ef4444;
    transition: var(--dl-transition-fast);
    margin-top: var(--dl-space-3);
}
.shared-sidebar__logout:hover {
    background: #fee2e2;
    border-color: #fca5a5;
}

/* Topbar */
.shared-topbar {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: var(--dl-space-2) var(--dl-space-6);
    border-bottom: 1px solid var(--dl-warm-border);
    background: rgba(255, 255, 255, 0.97);
    backdrop-filter: blur(16px);
    position: sticky;
    top: 0;
    z-index: 30;
    min-height: 3.25rem;
}
.shared-topbar__hamburger {
    display: none;
    padding: var(--dl-space-2);
    color: var(--dl-text-muted);
}
@media (max-width: 768px) {
    .shared-topbar__hamburger {
        display: block;
    }
}
.shared-topbar__breadcrumb {
    display: none;
    align-items: center;
    gap: 0.375rem;
    animation: aFadeSlideIn 0.4s ease-out both;
}
@media (min-width: 640px) {
    .shared-topbar__breadcrumb {
        display: flex;
    }
}
.shared-topbar__crumb {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 12.5px;
    color: var(--dl-text-faint);
    transition:
        color 0.2s ease,
        transform 0.2s ease;
    text-decoration: none;
}
.shared-topbar__crumb--home:hover {
    color: var(--role-color);
    transform: scale(1.04);
}
.shared-topbar__crumb--current {
    color: var(--dl-text);
    font-weight: 600;
}
.shared-topbar__crumb-sep {
    width: 12px;
    height: 12px;
    color: var(--dl-text-faint);
    opacity: 0.7;
}

.shared-topbar__quick-links {
    display: none;
    align-items: center;
    gap: 0.5rem;
}
@media (min-width: 768px) {
    .shared-topbar__quick-links {
        display: flex;
    }
}
.shared-topbar__quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 500;
    color: var(--dl-text-muted);
    border: 1px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.shared-topbar__quick-btn:hover {
    color: var(--dl-text);
    background: var(--dl-warm-bg);
    border-color: var(--dl-warm-border);
    transform: translateY(-1px);
}
.shared-topbar__bell {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border-radius: var(--dl-radius-full);
    color: var(--dl-text-muted);
    transition: var(--dl-transition-fast);
}
.shared-topbar__bell:hover {
    color: var(--dl-text);
    background: var(--dl-warm-border);
}

.shared-topbar__back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 600;
    color: var(--role-color);
    background: var(--role-surface);
    border: 1px solid color-mix(in srgb, var(--role-color) 20%, transparent);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.shared-topbar__back-btn:hover {
    background: color-mix(in srgb, var(--role-color) 15%, transparent);
    border-color: color-mix(in srgb, var(--role-color) 30%, transparent);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px
        color-mix(in srgb, var(--role-color) 10%, transparent);
}

.shared-topbar__user-btn {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    padding: 6px 12px 6px 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}
.shared-topbar__user-btn:hover {
    border-color: #d6d3d1;
    background: var(--dl-warm-bg);
}
.shared-topbar__avatar {
    display: flex;
    width: 28px;
    height: 28px;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--role-color), #6366f1);
    font-size: 11px;
    font-weight: 700;
    color: white;
}
.shared-topbar__dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 8px;
    width: 14rem;
    border-radius: var(--dl-radius-xl);
    border: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    box-shadow: var(--dl-shadow-xl);
    overflow: hidden;
    transform-origin: top right;
}

/* Main */
.shared-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.shared-main {
    flex: 1;
    overflow-x: hidden;
}

/* Mobile sidebar */
.shared-mobile-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: var(--dl-sidebar-admin, 260px);
    background: var(--dl-warm-surface);
    z-index: 50;
}
@keyframes aFadeSlideIn {
    from {
        opacity: 0;
        transform: translateX(-6px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
