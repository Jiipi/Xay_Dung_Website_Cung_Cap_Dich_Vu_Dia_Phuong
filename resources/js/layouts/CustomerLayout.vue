<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashToast from '@/components/FlashToast.vue';
import {
    ArrowLeft,
    Bell,
    CalendarDays,
    ChevronDown,
    ChevronRight,
    ExternalLink,
    Heart,
    Home,
    LayoutDashboard,
    LogOut,
    Menu,
    Search,
    Settings,
    User,
    Wrench,
    X,
} from 'lucide-vue-next';

const props = defineProps<{ activePage?: string }>();

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Khách hàng');
const userEmail = computed(() => page.props.auth?.user?.email ?? '');
const userInitial = computed(() => userName.value?.charAt(0)?.toUpperCase() ?? 'K');
const isMobileSidebarOpen = ref(false);
const isUserMenuOpen = ref(false);

const navItems = [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/customer/dashboard' },
    { key: 'bookings', label: 'Booking của tôi', icon: CalendarDays, href: '/customer/bookings' },
    { key: 'favorites', label: 'Yêu thích', icon: Heart, href: '/customer/favorites' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/customer/notifications' },
    { key: 'profile', label: 'Hồ sơ cá nhân', icon: User, href: '/customer/profile' },
    { key: 'settings', label: 'Cài đặt tài khoản', icon: Settings, href: '/settings/profile' },
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
    <div class="customer-shell" data-role="customer">
        <!-- Sidebar (Desktop) -->
        <aside class="customer-sidebar">
            <!-- Logo -->
            <div class="customer-sidebar__header">
                <Link href="/" class="flex items-center gap-2.5">
                    <div class="customer-sidebar__logo-icon">
                        <Wrench class="size-4.5" />
                    </div>
                    <div>
                        <p class="customer-sidebar__label">Customer</p>
                        <h1 class="customer-sidebar__title">Dalat Services</h1>
                    </div>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="customer-sidebar__nav">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="customer-sidebar__link"
                    :class="{ 'customer-sidebar__link--active': activePage === item.key }"
                >
                    <component :is="item.icon" class="size-4.5" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- Bottom user info -->
            <div class="customer-sidebar__footer">
                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                    <span class="customer-sidebar__avatar">{{ userInitial }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium" style="color: var(--dl-text);">{{ userName }}</p>
                        <p class="truncate text-xs" style="color: var(--dl-text-muted);">{{ userEmail }}</p>
                    </div>
                </div>
                <Link href="/logout" method="post" as="button" class="customer-sidebar__logout">
                    <LogOut class="size-4" /> Đăng xuất
                </Link>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="customer-content">
            <!-- Topbar -->
            <header class="customer-topbar">
                <button class="customer-topbar__hamburger" @click="isMobileSidebarOpen = !isMobileSidebarOpen">
                    <Menu class="size-5" />
                </button>

                <nav class="customer-topbar__breadcrumb" aria-label="breadcrumb">
                    <Link href="/" class="customer-topbar__crumb customer-topbar__crumb--home">
                        <Home class="size-3.5" />
                        <span>Trang chủ</span>
                    </Link>
                    <ChevronRight class="customer-topbar__crumb-sep" />
                    <span class="customer-topbar__crumb customer-topbar__crumb--current">{{ currentPageLabel }}</span>
                </nav>

                <div class="flex-1" />

                <div class="customer-topbar__quick-links">
                    <Link href="/services" class="customer-topbar__quick-btn">
                        <Search class="size-3.5" />
                        <span>Khám phá dịch vụ</span>
                    </Link>
                    <Link href="/" class="customer-topbar__back-btn">
                        <ArrowLeft class="size-3.5 customer-topbar__back-arrow" />
                        <span>Về Marketplace</span>
                        <ExternalLink class="size-3 opacity-40" />
                    </Link>
                </div>

                <div class="relative">
                    <button
                        class="customer-topbar__user-btn"
                        @click="isUserMenuOpen = !isUserMenuOpen"
                        @blur="closeUserMenuSoon"
                    >
                        <span class="customer-topbar__avatar">{{ userInitial }}</span>
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
                        <div v-show="isUserMenuOpen" class="customer-topbar__dropdown">
                            <div class="border-b px-4 py-3" style="border-color: var(--dl-warm-border);">
                                <p class="text-sm font-semibold" style="color: var(--dl-text);">{{ userName }}</p>
                                <p class="text-xs" style="color: var(--dl-text-muted);">{{ userEmail }}</p>
                            </div>
                            <div class="p-1.5">
                                <Link href="/settings/profile" class="customer-topbar__dropdown-item">
                                    <Settings class="size-4" style="color: var(--dl-text-faint);" /> Cài đặt
                                </Link>
                            </div>
                            <div class="border-t p-1.5" style="border-color: var(--dl-warm-border);">
                                <Link href="/logout" method="post" as="button" class="customer-topbar__dropdown-item customer-topbar__dropdown-item--danger">
                                    <LogOut class="size-4" /> Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </header>

            <main class="customer-main">
                <slot />
            </main>

            <footer class="customer-footer">
                <p>&copy; 2026 Dalat Services. Bảng điều khiển khách hàng.</p>
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
            <div v-show="isMobileSidebarOpen" class="customer-mobile-sidebar md:hidden">
                <div class="flex items-center justify-between border-b p-4" style="border-color: var(--dl-warm-border);">
                    <span class="text-sm font-bold" style="color: var(--dl-text);">Menu</span>
                    <button @click="isMobileSidebarOpen = false" style="color: var(--dl-text-muted);"><X class="size-5" /></button>
                </div>
                <nav class="space-y-1 p-3">
                    <Link
                        v-for="item in navItems"
                        :key="item.key"
                        :href="item.href"
                        class="customer-sidebar__link"
                        :class="{ 'customer-sidebar__link--active': activePage === item.key }"
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
.customer-shell {
    --role-color: var(--dl-brand);
    --role-surface: var(--dl-brand-surface);
    display: grid;
    grid-template-columns: 260px 1fr;
    min-height: 100dvh;
    background: var(--dl-warm-bg);
}

@media (max-width: 768px) {
    .customer-shell { grid-template-columns: 1fr; }
}

/* Sidebar */
.customer-sidebar {
    position: sticky;
    top: 0;
    height: 100dvh;
    border-right: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    display: none;
    flex-direction: column;
}
@media (min-width: 769px) {
    .customer-sidebar { display: flex; }
}

.customer-sidebar__header {
    padding: var(--dl-space-5);
    border-bottom: 1px solid var(--dl-warm-border);
}
.customer-sidebar__logo-icon {
    display: flex;
    width: 36px; height: 36px;
    align-items: center; justify-content: center;
    border-radius: var(--dl-radius-lg);
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    color: white;
    box-shadow: var(--dl-shadow-md);
}
.customer-sidebar__label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: var(--dl-text-faint);
}
.customer-sidebar__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--dl-text);
}
.customer-sidebar__nav {
    padding: var(--dl-space-3);
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-1);
    flex: 1;
}
.customer-sidebar__link {
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
.customer-sidebar__link:hover {
    background: var(--dl-brand-surface);
    color: var(--dl-brand);
}
.customer-sidebar__link--active {
    background: var(--dl-brand-surface);
    color: var(--dl-brand);
    font-weight: 600;
    box-shadow: inset 3px 0 0 var(--dl-brand);
}

.customer-sidebar__footer {
    padding: var(--dl-space-3);
    border-top: 1px solid var(--dl-warm-border);
    margin-top: auto;
}
.customer-sidebar__avatar {
    display: flex;
    width: 32px; height: 32px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    font-size: 12px; font-weight: 700;
    color: white;
    flex-shrink: 0;
}
.customer-sidebar__logout {
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
.customer-sidebar__logout:hover {
    background: #fef2f2;
}

/* Content */
.customer-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.customer-main {
    flex: 1;
    overflow-x: hidden;
}

/* Topbar */
.customer-topbar {
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
.customer-topbar__hamburger {
    display: none;
    padding: var(--dl-space-2);
    color: var(--dl-text-muted);
}
@media (max-width: 768px) {
    .customer-topbar__hamburger { display: block; }
}
.customer-topbar__breadcrumb {
    display: none;
    align-items: center;
    gap: 0.375rem;
    animation: cFadeSlideIn 0.4s ease-out both;
}
@media (min-width: 640px) {
    .customer-topbar__breadcrumb { display: flex; }
}
.customer-topbar__crumb {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 12.5px;
    color: var(--dl-text-faint);
    transition: color 0.2s ease, transform 0.2s ease;
    text-decoration: none;
}
.customer-topbar__crumb--home:hover {
    color: var(--dl-brand);
    transform: scale(1.04);
}
.customer-topbar__crumb--current {
    color: var(--dl-text);
    font-weight: 600;
}
.customer-topbar__crumb-sep {
    width: 12px; height: 12px;
    color: var(--dl-text-faint);
    opacity: 0.5;
}
.customer-topbar__quick-links {
    display: none;
    align-items: center;
    gap: 0.5rem;
}
@media (min-width: 768px) {
    .customer-topbar__quick-links { display: flex; }
}
.customer-topbar__quick-btn {
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
.customer-topbar__quick-btn:hover {
    color: var(--dl-text);
    background: var(--dl-warm-surface);
    border-color: var(--dl-warm-border);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.customer-topbar__back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 600;
    color: var(--dl-brand);
    background: var(--dl-brand-surface);
    border: 1px solid color-mix(in srgb, var(--dl-brand) 20%, transparent);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.customer-topbar__back-btn:hover {
    background: color-mix(in srgb, var(--dl-brand) 15%, white);
    border-color: color-mix(in srgb, var(--dl-brand) 35%, transparent);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--dl-brand) 15%, transparent);
}
.customer-topbar__back-btn:hover .customer-topbar__back-arrow {
    animation: cArrowBounce 0.6s ease infinite;
}
@keyframes cArrowBounce {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-3px); }
}
.customer-page-enter-active {
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.customer-page-enter-from {
    opacity: 0;
    transform: translateY(12px);
}
.customer-page-enter-to {
    opacity: 1;
    transform: translateY(0);
}
.customer-sidebar__link {
    animation: cSidebarSlideIn 0.35s ease-out both;
}
.customer-sidebar__link:nth-child(1) { animation-delay: 0.03s; }
.customer-sidebar__link:nth-child(2) { animation-delay: 0.06s; }
.customer-sidebar__link:nth-child(3) { animation-delay: 0.09s; }
.customer-sidebar__link:nth-child(4) { animation-delay: 0.12s; }
.customer-sidebar__link:nth-child(5) { animation-delay: 0.15s; }
.customer-sidebar__link:nth-child(6) { animation-delay: 0.18s; }
@keyframes cSidebarSlideIn {
    from { opacity: 0; transform: translateX(-8px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes cFadeSlideIn {
    from { opacity: 0; transform: translateX(-6px); }
    to { opacity: 1; transform: translateX(0); }
}
.customer-topbar__user-btn {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    padding: 4px 10px 4px 4px;
    transition: var(--dl-transition-fast);
}
.customer-topbar__user-btn:hover {
    background: var(--dl-brand-surface);
}
.customer-topbar__avatar {
    display: flex;
    width: 28px; height: 28px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    font-size: 11px; font-weight: 700;
    color: white;
}
.customer-topbar__dropdown {
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
.customer-topbar__dropdown-item {
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
.customer-topbar__dropdown-item:hover {
    background: var(--dl-brand-surface);
}
.customer-topbar__dropdown-item--danger {
    color: #dc2626;
}
.customer-topbar__dropdown-item--danger:hover {
    background: #fef2f2;
}

/* Footer */
.customer-footer {
    border-top: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    padding: var(--dl-space-5) var(--dl-space-6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: var(--dl-text-faint);
}
.customer-footer a {
    color: var(--dl-text-faint);
    transition: var(--dl-transition-fast);
}
.customer-footer a:hover {
    color: var(--dl-text);
}

/* Mobile sidebar */
.customer-mobile-sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: 260px;
    background: var(--dl-warm-surface);
    z-index: 50;
}
</style>
