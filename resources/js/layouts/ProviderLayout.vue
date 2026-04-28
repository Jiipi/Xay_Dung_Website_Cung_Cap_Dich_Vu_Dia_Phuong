<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashToast from '@/components/FlashToast.vue';
import {
    ArrowLeft,
    Bell,
    CalendarCheck,
    ChevronDown,
    ChevronRight,
    ClipboardList,
    Compass,
    ExternalLink,
    Home,
    LayoutDashboard,
    LogOut,
    Menu,
    Package,
    Search,
    Settings,
    Star,
    User,
    Wrench,
    X,
} from 'lucide-vue-next';

const props = defineProps<{ activePage?: string }>();

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Nhà cung cấp');
const userEmail = computed(() => page.props.auth?.user?.email ?? '');
const userInitial = computed(() => userName.value?.charAt(0)?.toUpperCase() ?? 'P');
const pendingCount = computed(() => (page.props as any).pendingBookingsCount ?? 0);
const unreadCount = computed(() => (page.props as any).unreadNotifications ?? 0);
const isMobileSidebarOpen = ref(false);
const isUserMenuOpen = ref(false);

const navItems = [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/provider/dashboard' },
    { key: 'services', label: 'Dịch vụ', icon: Package, href: '/provider/services' },
    { key: 'bookings', label: 'Đơn hàng', icon: ClipboardList, href: '/provider/bookings', badge: pendingCount.value },
    { key: 'reviews', label: 'Đánh giá', icon: Star, href: '/provider/reviews' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/provider/notifications', badge: unreadCount.value },
    { key: 'availability', label: 'Lịch rảnh', icon: CalendarCheck, href: '/provider/availability' },
    { key: 'profile', label: 'Hồ sơ', icon: User, href: '/provider/profile' },
    { key: 'settings', label: 'Cài đặt', icon: Settings, href: '/settings/profile' },
];

const currentPageLabel = computed(() => {
    const item = navItems.find(i => i.key === props.activePage);
    return item?.label ?? 'Tổng quan';
});
function closeUserMenuSoon() {
    setTimeout(() => {
        isUserMenuOpen.value = false;
    }, 150);
}
</script>

<template>
    <div class="provider-shell" data-role="provider">
        <!-- Sidebar (Desktop) -->
        <aside class="provider-sidebar">
            <!-- Logo -->
            <div class="provider-sidebar__header">
                <Link href="/provider/dashboard" class="flex items-center gap-2.5">
                    <div class="provider-sidebar__logo-icon">
                        <Wrench class="size-4.5" />
                    </div>
                    <div>
                        <p class="provider-sidebar__label">Provider</p>
                        <h1 class="provider-sidebar__title">Dalat Services</h1>
                    </div>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="provider-sidebar__nav">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="provider-sidebar__link"
                    :class="{ 'provider-sidebar__link--active': activePage === item.key }"
                >
                    <component :is="item.icon" class="size-4.5" />
                    {{ item.label }}
                    <span v-if="item.badge && item.badge > 0" class="provider-sidebar__badge">{{ item.badge }}</span>
                </Link>
            </nav>

            <!-- Bottom user info -->
            <div class="provider-sidebar__footer">
                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                    <span class="provider-sidebar__avatar">{{ userInitial }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium" style="color: var(--dl-text);">{{ userName }}</p>
                        <p class="truncate text-xs" style="color: var(--dl-text-muted);">{{ userEmail }}</p>
                    </div>
                </div>
                <Link href="/logout" method="post" as="button" class="provider-sidebar__logout">
                    <LogOut class="size-4" /> Đăng xuất
                </Link>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="provider-content">
            <!-- Topbar -->
            <header class="provider-topbar">
                <button class="provider-topbar__hamburger" @click="isMobileSidebarOpen = !isMobileSidebarOpen">
                    <Menu class="size-5" />
                </button>

                <!-- Breadcrumb -->
                <nav class="provider-topbar__breadcrumb" aria-label="breadcrumb">
                    <Link href="/" class="provider-topbar__crumb provider-topbar__crumb--home">
                        <Home class="size-3.5" />
                        <span>Trang chủ</span>
                    </Link>
                    <ChevronRight class="provider-topbar__crumb-sep" />
                    <span class="provider-topbar__crumb provider-topbar__crumb--current">{{ currentPageLabel }}</span>
                </nav>

                <div class="flex-1" />

                <!-- Quick links -->
                <div class="provider-topbar__quick-links">
                    <Link href="/services" class="provider-topbar__quick-btn">
                        <Search class="size-3.5" />
                        <span>Khám phá dịch vụ</span>
                    </Link>
                    <Link href="/" class="provider-topbar__back-btn">
                        <ArrowLeft class="size-3.5 provider-topbar__back-arrow" />
                        <span>Về Marketplace</span>
                        <ExternalLink class="size-3 opacity-40" />
                    </Link>
                </div>

                <!-- User dropdown (desktop) -->
                <div class="relative">
                    <button
                        class="provider-topbar__user-btn"
                        @click="isUserMenuOpen = !isUserMenuOpen"
                        @blur="closeUserMenuSoon"
                    >
                        <span class="provider-topbar__avatar">{{ userInitial }}</span>
                        <ChevronDown class="size-3.5" style="color: var(--dl-text-muted);" />
                    </button>
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="scale-95 opacity-0 translate-y-1"
                        enter-to-class="scale-100 opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="scale-100 opacity-100 translate-y-0"
                        leave-to-class="scale-95 opacity-0 translate-y-1"
                    >
                        <div v-show="isUserMenuOpen" class="provider-topbar__dropdown">
                            <div class="border-b px-4 py-3" style="border-color: var(--dl-warm-border);">
                                <p class="text-sm font-semibold" style="color: var(--dl-text);">{{ userName }}</p>
                                <p class="text-xs" style="color: var(--dl-text-muted);">{{ userEmail }}</p>
                            </div>
                            <div class="p-1.5">
                                <Link href="/settings/profile" class="provider-topbar__dropdown-item">
                                    <Settings class="size-4" style="color: var(--dl-text-faint);" /> Cài đặt
                                </Link>
                            </div>
                            <div class="border-t p-1.5" style="border-color: var(--dl-warm-border);">
                                <Link href="/logout" method="post" as="button" class="provider-topbar__dropdown-item provider-topbar__dropdown-item--danger">
                                    <LogOut class="size-4" /> Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </header>

            <!-- Page Content -->
            <main class="provider-main">
                <slot />
            </main>

            <!-- Minimal footer -->
            <footer class="provider-footer">
                <p>&copy; 2026 Dalat Services. Bảng điều khiển nhà cung cấp.</p>
                <a href="/">Về trang chủ</a>
            </footer>
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
            <div v-show="isMobileSidebarOpen" class="provider-mobile-sidebar md:hidden">
                <div class="flex items-center justify-between border-b p-4" style="border-color: var(--dl-warm-border);">
                    <span class="text-sm font-bold" style="color: var(--dl-text);">Menu</span>
                    <button @click="isMobileSidebarOpen = false" style="color: var(--dl-text-muted);"><X class="size-5" /></button>
                </div>
                <nav class="space-y-1 p-3">
                    <Link
                        v-for="item in navItems"
                        :key="item.key"
                        :href="item.href"
                        class="provider-sidebar__link"
                        :class="{ 'provider-sidebar__link--active': activePage === item.key }"
                        @click="isMobileSidebarOpen = false"
                    >
                        <component :is="item.icon" class="size-4.5" />
                        {{ item.label }}
                    </Link>
                </nav>
            </div>
        </Transition>
        <div v-show="isMobileSidebarOpen" class="fixed inset-0 z-40 bg-black/40 md:hidden" @click="isMobileSidebarOpen = false" />
        <FlashToast />
    </div>
</template>

<style scoped>
.provider-shell {
    --role-color: var(--dl-provider);
    --role-surface: var(--dl-provider-surface);
    display: grid;
    grid-template-columns: var(--dl-sidebar-provider) 1fr;
    min-height: 100dvh;
    background: var(--dl-warm-bg);
}

@media (max-width: 768px) {
    .provider-shell { grid-template-columns: 1fr; }
}

/* Sidebar */
.provider-sidebar {
    position: sticky;
    top: 0;
    height: 100dvh;
    border-right: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    display: none;
    flex-direction: column;
}
@media (min-width: 769px) {
    .provider-sidebar { display: flex; }
}

.provider-sidebar__header {
    padding: var(--dl-space-5);
    border-bottom: 1px solid var(--dl-warm-border);
}
.provider-sidebar__logo-icon {
    display: flex;
    width: 36px; height: 36px;
    align-items: center; justify-content: center;
    border-radius: var(--dl-radius-lg);
    background: linear-gradient(135deg, var(--dl-provider), #d4502e);
    color: white;
    box-shadow: var(--dl-shadow-md);
}
.provider-sidebar__label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: var(--dl-text-faint);
}
.provider-sidebar__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--dl-text);
}
.provider-sidebar__nav {
    padding: var(--dl-space-3);
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-1);
    flex: 1;
}
.provider-sidebar__link {
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
.provider-sidebar__link:hover {
    background: var(--dl-provider-surface);
    color: var(--dl-provider);
}
.provider-sidebar__link--active {
    background: var(--dl-provider-surface);
    color: var(--dl-provider);
    font-weight: 600;
    box-shadow: inset 3px 0 0 var(--dl-provider);
}
.provider-sidebar__badge {
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.25rem;
    height: 1.25rem;
    padding: 0 0.35rem;
    border-radius: 9999px;
    background: var(--dl-provider);
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    line-height: 1;
}

.provider-sidebar__footer {
    padding: var(--dl-space-3);
    border-top: 1px solid var(--dl-warm-border);
    margin-top: auto;
}
.provider-sidebar__avatar {
    display: flex;
    width: 32px; height: 32px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--dl-provider), #d4502e);
    font-size: 12px; font-weight: 700;
    color: white;
    flex-shrink: 0;
}
.provider-sidebar__logout {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    width: 100%;
    padding: var(--dl-space-2) var(--dl-space-3);
    border-radius: var(--dl-radius-lg);
    font-size: 13px;
    font-weight: 500;
    color: #dc2626;
    transition: var(--dl-transition-fast);
}
.provider-sidebar__logout:hover {
    background: #fef2f2;
}

/* Content */
.provider-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.provider-main {
    flex: 1;
    overflow-x: hidden;
}

/* Topbar */
.provider-topbar {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: var(--dl-space-2) var(--dl-space-6);
    border-bottom: 1px solid var(--dl-warm-border);
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(16px);
    position: sticky; top: 0; z-index: 30;
    min-height: 3.25rem;
}
.provider-topbar__hamburger {
    display: none;
    padding: var(--dl-space-2);
    color: var(--dl-text-muted);
}
@media (max-width: 768px) {
    .provider-topbar__hamburger { display: block; }
}

/* Breadcrumb */
.provider-topbar__breadcrumb {
    display: none;
    align-items: center;
    gap: 0.375rem;
    animation: fadeSlideIn 0.4s ease-out both;
}
@media (min-width: 640px) {
    .provider-topbar__breadcrumb { display: flex; }
}
.provider-topbar__crumb {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 12.5px;
    color: var(--dl-text-faint);
    transition: color 0.2s ease, transform 0.2s ease;
    text-decoration: none;
}
.provider-topbar__crumb--home:hover {
    color: var(--dl-provider);
    transform: scale(1.04);
}
.provider-topbar__crumb--current {
    color: var(--dl-text);
    font-weight: 600;
}
.provider-topbar__crumb-sep {
    width: 12px; height: 12px;
    color: var(--dl-text-faint);
    opacity: 0.5;
}

/* Quick links */
.provider-topbar__quick-links {
    display: none;
    align-items: center;
    gap: 0.5rem;
}
@media (min-width: 768px) {
    .provider-topbar__quick-links { display: flex; }
}
.provider-topbar__quick-btn {
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
.provider-topbar__quick-btn:hover {
    color: var(--dl-text);
    background: var(--dl-warm-surface);
    border-color: var(--dl-warm-border);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.provider-topbar__back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 600;
    color: var(--dl-provider);
    background: var(--dl-provider-surface);
    border: 1px solid color-mix(in srgb, var(--dl-provider) 20%, transparent);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.provider-topbar__back-btn:hover {
    background: color-mix(in srgb, var(--dl-provider) 15%, white);
    border-color: color-mix(in srgb, var(--dl-provider) 35%, transparent);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--dl-provider) 15%, transparent);
}
.provider-topbar__back-btn:hover .provider-topbar__back-arrow {
    animation: arrowBounce 0.6s ease infinite;
}

@keyframes arrowBounce {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-3px); }
}

/* Page entrance animation */
.provider-page-enter-active {
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.provider-page-enter-from {
    opacity: 0;
    transform: translateY(12px);
}
.provider-page-enter-to {
    opacity: 1;
    transform: translateY(0);
}

/* Sidebar link stagger */
.provider-sidebar__link {
    animation: sidebarSlideIn 0.35s ease-out both;
}
.provider-sidebar__link:nth-child(1) { animation-delay: 0.03s; }
.provider-sidebar__link:nth-child(2) { animation-delay: 0.06s; }
.provider-sidebar__link:nth-child(3) { animation-delay: 0.09s; }
.provider-sidebar__link:nth-child(4) { animation-delay: 0.12s; }
.provider-sidebar__link:nth-child(5) { animation-delay: 0.15s; }
.provider-sidebar__link:nth-child(6) { animation-delay: 0.18s; }
.provider-sidebar__link:nth-child(7) { animation-delay: 0.21s; }
.provider-sidebar__link:nth-child(8) { animation-delay: 0.24s; }

@keyframes sidebarSlideIn {
    from { opacity: 0; transform: translateX(-8px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateX(-6px); }
    to { opacity: 1; transform: translateX(0); }
}
.provider-topbar__user-btn {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    padding: 4px 10px 4px 4px;
    transition: var(--dl-transition-fast);
}
.provider-topbar__user-btn:hover {
    background: var(--dl-provider-surface);
}
.provider-topbar__avatar {
    display: flex;
    width: 28px; height: 28px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--dl-provider), #d4502e);
    font-size: 11px; font-weight: 700;
    color: white;
}
.provider-topbar__dropdown {
    position: absolute;
    right: 0; top: 100%; margin-top: 8px;
    width: 14rem;
    border-radius: var(--dl-radius-xl);
    border: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    box-shadow: var(--dl-shadow-lg);
    overflow: hidden;
    transform-origin: top right;
    z-index: 50;
}
.provider-topbar__dropdown-item {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: var(--dl-space-2) var(--dl-space-3);
    border-radius: var(--dl-radius-lg);
    font-size: 13px;
    color: var(--dl-text-muted);
    transition: var(--dl-transition-fast);
    width: 100%;
}
.provider-topbar__dropdown-item:hover {
    background: var(--dl-provider-surface);
}
.provider-topbar__dropdown-item--danger {
    color: #dc2626;
}
.provider-topbar__dropdown-item--danger:hover {
    background: #fef2f2;
}

/* Footer */
.provider-footer {
    border-top: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    padding: var(--dl-space-5) var(--dl-space-6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: var(--dl-text-faint);
}
.provider-footer a {
    color: var(--dl-text-faint);
    transition: var(--dl-transition-fast);
}
.provider-footer a:hover {
    color: var(--dl-text);
}

/* Mobile sidebar */
.provider-mobile-sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: var(--dl-sidebar-provider);
    background: var(--dl-warm-surface);
    z-index: 50;
}
</style>
