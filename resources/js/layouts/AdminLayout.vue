<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import FlashToast from '@/components/FlashToast.vue';
import { computed, ref } from 'vue';
import {
    ArrowLeft,
    BarChart3,
    CheckSquare,
    ChevronDown,
    ChevronRight,
    ExternalLink,
    Globe,
    Home,
    LayoutDashboard,
    LogOut,
    Menu,
    Search,
    Settings,
    Users,
    Wrench,
    X,
    CalendarDays,
    Star,
} from 'lucide-vue-next';

const props = defineProps<{ activePage?: string }>();

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Admin');
const userEmail = computed(() => page.props.auth?.user?.email ?? '');
const userInitial = computed(() => userName.value?.charAt(0)?.toUpperCase() ?? 'A');
const isUserMenuOpen = ref(false);
const isMobileSidebarOpen = ref(false);

const mainNav = [
    { key: 'dashboard', label: 'Dashboard', icon: LayoutDashboard, href: '/admin/dashboard' },
    { key: 'users', label: 'Người dùng', icon: Users, href: '/admin/users' },
    { key: 'services', label: 'Duyệt dịch vụ', icon: CheckSquare, href: '/admin/services' },
    { key: 'bookings', label: 'Đơn hàng', icon: CalendarDays, href: '/admin/bookings' },
    { key: 'reviews', label: 'Đánh giá', icon: Star, href: '/admin/reviews' },
    { key: 'stats', label: 'Thống kê', icon: BarChart3, href: '/admin/stats' },
];

const utilNav = [
    { key: 'settings', label: 'Cài đặt', icon: Settings, href: '/settings/profile' },
    { key: 'home', label: 'Trang chủ', icon: Globe, href: '/' },
];
const allNav = [...mainNav, ...utilNav];
const currentPageLabel = computed(() => {
    const item = allNav.find(i => i.key === props.activePage);
    return item?.label ?? 'Dashboard';
});

function closeUserMenuSoon() {
    setTimeout(() => {
        isUserMenuOpen.value = false;
    }, 150);
}
</script>

<template>
    <div class="admin-shell" data-role="admin">
        <!-- Sidebar (Desktop) -->
        <aside class="admin-sidebar">
            <!-- Logo -->
            <div class="admin-sidebar__header">
                <Link href="/admin/dashboard" class="flex items-center gap-2.5">
                    <div class="admin-sidebar__logo-icon">
                        <Wrench class="size-4.5" />
                    </div>
                    <div>
                        <p class="admin-sidebar__label">Admin Panel</p>
                        <h1 class="admin-sidebar__title">Dalat Services</h1>
                    </div>
                </Link>
            </div>

            <!-- Main Nav -->
            <nav class="admin-sidebar__nav">
                <p class="admin-sidebar__section-label">Quản lý</p>
                <Link
                    v-for="item in mainNav"
                    :key="item.key"
                    :href="item.href"
                    class="admin-sidebar__link"
                    :class="{ 'admin-sidebar__link--active': activePage === item.key }"
                >
                    <component :is="item.icon" class="size-4.5" />
                    {{ item.label }}
                </Link>

                <div class="admin-sidebar__divider" />

                <p class="admin-sidebar__section-label">Hệ thống</p>
                <Link
                    v-for="item in utilNav"
                    :key="item.key"
                    :href="item.href"
                    class="admin-sidebar__link"
                    :class="{ 'admin-sidebar__link--active': activePage === item.key }"
                >
                    <component :is="item.icon" class="size-4.5" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- Footer -->
            <div class="admin-sidebar__footer">
                <p class="text-[10px] text-slate-600">Dalat Services &copy; 2025</p>
                <p class="text-[9px] text-slate-700">v2.0 — Admin Panel</p>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="admin-content">
            <!-- Topbar -->
            <header class="admin-topbar">
                <button class="admin-topbar__hamburger" @click="isMobileSidebarOpen = !isMobileSidebarOpen">
                    <Menu class="size-5" />
                </button>

                <nav class="admin-topbar__breadcrumb" aria-label="breadcrumb">
                    <Link href="/" class="admin-topbar__crumb admin-topbar__crumb--home">
                        <Home class="size-3.5" />
                        <span>Trang chủ</span>
                    </Link>
                    <ChevronRight class="admin-topbar__crumb-sep" />
                    <span class="admin-topbar__crumb admin-topbar__crumb--current">{{ currentPageLabel }}</span>
                </nav>

                <div class="flex-1" />

                <div class="admin-topbar__quick-links">
                    <Link href="/services" class="admin-topbar__quick-btn">
                        <Search class="size-3.5" />
                        <span>Marketplace</span>
                    </Link>
                    <Link href="/" class="admin-topbar__back-btn">
                        <ArrowLeft class="size-3.5 admin-topbar__back-arrow" />
                        <span>Về Trang chủ</span>
                        <ExternalLink class="size-3 opacity-40" />
                    </Link>
                </div>

                <div class="relative">
                    <button
                        class="admin-topbar__user-btn"
                        @click="isUserMenuOpen = !isUserMenuOpen"
                        @blur="closeUserMenuSoon"
                    >
                        <span class="admin-topbar__avatar">{{ userInitial }}</span>
                        <span class="hidden text-sm text-slate-300 lg:inline">{{ userName }}</span>
                        <ChevronDown class="size-3.5 text-slate-500" />
                    </button>

                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="scale-95 opacity-0 translate-y-1"
                        enter-to-class="scale-100 opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="scale-100 opacity-100 translate-y-0"
                        leave-to-class="scale-95 opacity-0 translate-y-1"
                    >
                        <div v-show="isUserMenuOpen" class="admin-topbar__dropdown">
                            <div class="border-b border-slate-700 px-4 py-3">
                                <p class="text-sm font-semibold text-white">{{ userName }}</p>
                                <p class="text-xs text-slate-400">{{ userEmail }}</p>
                                <span class="mt-1.5 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold" style="background: color-mix(in srgb, var(--dl-admin) 15%, transparent); color: var(--dl-admin-surface);">
                                    Admin
                                </span>
                            </div>
                            <div class="p-1.5">
                                <Link href="/settings/profile" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-slate-700">
                                    <Settings class="size-4 text-slate-500" /> Cài đặt
                                </Link>
                            </div>
                            <div class="border-t border-slate-700 p-1.5">
                                <Link href="/logout" method="post" as="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-400 transition hover:bg-red-500/10">
                                    <LogOut class="size-4" /> Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </header>

            <main class="admin-main">
                <slot />
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
            <div v-show="isMobileSidebarOpen" class="admin-mobile-sidebar md:hidden">
                <div class="flex items-center justify-between border-b border-slate-700 p-4">
                    <span class="text-sm font-bold text-white">Menu</span>
                    <button @click="isMobileSidebarOpen = false" class="text-slate-400"><X class="size-5" /></button>
                </div>
                <nav class="space-y-1 p-3">
                    <Link
                        v-for="item in [...mainNav, ...utilNav]"
                        :key="item.key"
                        :href="item.href"
                        class="admin-sidebar__link"
                        :class="{ 'admin-sidebar__link--active': activePage === item.key }"
                        @click="isMobileSidebarOpen = false"
                    >
                        <component :is="item.icon" class="size-4.5" />
                        {{ item.label }}
                    </Link>
                </nav>
            </div>
        </Transition>
        <div v-show="isMobileSidebarOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="isMobileSidebarOpen = false" />
        <FlashToast />
    </div>
</template>

<style scoped>
.admin-shell {
    --role-color: var(--dl-admin);
    --role-surface: var(--dl-admin-surface);
    display: grid;
    grid-template-columns: var(--dl-sidebar-admin) 1fr;
    min-height: 100dvh;
    background: #0f172a;
    color: #e2e8f0;
}

@media (max-width: 768px) {
    .admin-shell { grid-template-columns: 1fr; }
}

/* Sidebar */
.admin-sidebar {
    position: sticky;
    top: 0;
    height: 100dvh;
    border-right: 1px solid rgba(255,255,255,0.06);
    background: #1e293b;
    display: none;
    flex-direction: column;
}
@media (min-width: 769px) {
    .admin-sidebar { display: flex; }
}

.admin-sidebar__header {
    padding: var(--dl-space-5);
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.admin-sidebar__logo-icon {
    display: flex;
    width: 36px; height: 36px;
    align-items: center; justify-content: center;
    border-radius: var(--dl-radius-lg);
    background: linear-gradient(135deg, var(--dl-admin), #2a5298);
    color: white;
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.15);
}
.admin-sidebar__label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: #64748b;
}
.admin-sidebar__title {
    font-size: 14px;
    font-weight: 700;
    color: white;
}
.admin-sidebar__nav {
    padding: var(--dl-space-3);
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}
.admin-sidebar__section-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #475569;
    padding: 12px 12px 6px;
}
.admin-sidebar__divider {
    height: 1px;
    background: rgba(255,255,255,0.04);
    margin: 8px 12px;
}
.admin-sidebar__link {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: 10px 12px;
    border-radius: var(--dl-radius-lg);
    font-size: 13px;
    font-weight: 500;
    color: #94a3b8;
    transition: all 0.2s ease;
}
.admin-sidebar__link:hover {
    background: rgba(255,255,255,0.05);
    color: #e2e8f0;
}
.admin-sidebar__link--active {
    background: linear-gradient(135deg, rgba(56,189,248,0.12), rgba(99,102,241,0.08));
    color: #38bdf8;
    box-shadow: inset 3px 0 0 #38bdf8, 0 0 20px rgba(56,189,248,0.05);
    font-weight: 600;
}

.admin-sidebar__footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,0.04);
    text-align: center;
}

/* Topbar */
.admin-topbar {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    padding: var(--dl-space-2) var(--dl-space-6);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: rgba(30,41,59,0.97);
    backdrop-filter: blur(16px);
    position: sticky; top: 0; z-index: 30;
    min-height: 3.25rem;
}
.admin-topbar__hamburger {
    display: none;
    padding: var(--dl-space-2);
    color: #94a3b8;
}
@media (max-width: 768px) {
    .admin-topbar__hamburger { display: block; }
}
.admin-topbar__breadcrumb {
    display: none;
    align-items: center;
    gap: 0.375rem;
    animation: aFadeSlideIn 0.4s ease-out both;
}
@media (min-width: 640px) {
    .admin-topbar__breadcrumb { display: flex; }
}
.admin-topbar__crumb {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 12.5px;
    color: #64748b;
    transition: color 0.2s ease, transform 0.2s ease;
    text-decoration: none;
}
.admin-topbar__crumb--home:hover {
    color: #38bdf8;
    transform: scale(1.04);
}
.admin-topbar__crumb--current {
    color: #e2e8f0;
    font-weight: 600;
}
.admin-topbar__crumb-sep {
    width: 12px; height: 12px;
    color: #475569;
    opacity: 0.7;
}
.admin-topbar__quick-links {
    display: none;
    align-items: center;
    gap: 0.5rem;
}
@media (min-width: 768px) {
    .admin-topbar__quick-links { display: flex; }
}
.admin-topbar__quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 500;
    color: #94a3b8;
    border: 1px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.admin-topbar__quick-btn:hover {
    color: #e2e8f0;
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
    transform: translateY(-1px);
}
.admin-topbar__back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: var(--dl-radius-full);
    font-size: 12px;
    font-weight: 600;
    color: #38bdf8;
    background: rgba(56,189,248,0.1);
    border: 1px solid rgba(56,189,248,0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.admin-topbar__back-btn:hover {
    background: rgba(56,189,248,0.18);
    border-color: rgba(56,189,248,0.35);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(56,189,248,0.15);
}
.admin-topbar__back-btn:hover .admin-topbar__back-arrow {
    animation: aArrowBounce 0.6s ease infinite;
}
@keyframes aArrowBounce {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-3px); }
}
.admin-page-enter-active {
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.admin-page-enter-from {
    opacity: 0;
    transform: translateY(12px);
}
.admin-page-enter-to {
    opacity: 1;
    transform: translateY(0);
}
.admin-sidebar__link {
    animation: aSidebarSlideIn 0.35s ease-out both;
}
.admin-sidebar__link:nth-child(1) { animation-delay: 0.03s; }
.admin-sidebar__link:nth-child(2) { animation-delay: 0.06s; }
.admin-sidebar__link:nth-child(3) { animation-delay: 0.09s; }
.admin-sidebar__link:nth-child(4) { animation-delay: 0.12s; }
.admin-sidebar__link:nth-child(5) { animation-delay: 0.15s; }
@keyframes aSidebarSlideIn {
    from { opacity: 0; transform: translateX(-8px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes aFadeSlideIn {
    from { opacity: 0; transform: translateX(-6px); }
    to { opacity: 1; transform: translateX(0); }
}
.admin-topbar__user-btn {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    border-radius: var(--dl-radius-full);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 6px 12px 6px 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}
.admin-topbar__user-btn:hover {
    border-color: rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.05);
}
.admin-topbar__avatar {
    display: flex;
    width: 28px; height: 28px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #38bdf8, #6366f1);
    font-size: 11px; font-weight: 700;
    color: white;
}
.admin-topbar__dropdown {
    position: absolute;
    right: 0; top: 100%; margin-top: 8px;
    width: 14rem;
    border-radius: var(--dl-radius-xl);
    border: 1px solid rgba(255,255,255,0.1);
    background: #1e293b;
    box-shadow: var(--dl-shadow-xl);
    overflow: hidden;
    transform-origin: top right;
}

/* Main */
.admin-content {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.admin-main {
    flex: 1;
    overflow-x: hidden;
}

/* Mobile sidebar */
.admin-mobile-sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: var(--dl-sidebar-admin);
    background: #1e293b;
    z-index: 50;
}
</style>
