<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Briefcase,
    CalendarCheck,
    Clock3,
    LayoutDashboard,
    Settings,
    ShieldCheck,
    Star,
} from 'lucide-vue-next';

defineProps<{ activePage?: string }>();

const page = usePage();
const pendingCount = (page.props as any).pendingBookingsCount ?? 0;
const unreadCount = (page.props as any).unreadNotifications ?? 0;

const navItems = [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/provider/dashboard' },
    { key: 'services', label: 'Dịch vụ', icon: Briefcase, href: '/provider/services' },
    { key: 'bookings', label: 'Đơn hàng', icon: CalendarCheck, href: '/provider/bookings', badge: pendingCount },
    { key: 'reviews', label: 'Đánh giá', icon: Star, href: '/provider/reviews' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/provider/notifications', badge: unreadCount },
    { key: 'availability', label: 'Lịch rảnh', icon: Clock3, href: '/provider/availability' },
    { key: 'profile', label: 'Hồ sơ', icon: ShieldCheck, href: '/provider/profile' },
    { key: 'settings', label: 'Cài đặt', icon: Settings, href: '/settings/profile' },
];
</script>

<template>
    <nav class="provider-nav">
        <div class="provider-nav__inner">
            <Link
                v-for="item in navItems"
                :key="item.key"
                :href="item.href"
                class="provider-nav__item"
                :class="{ 'provider-nav__item--active': activePage === item.key }"
            >
                <component :is="item.icon" class="provider-nav__icon" />
                <span class="provider-nav__label">{{ item.label }}</span>
                <span
                    v-if="item.badge && item.badge > 0"
                    class="provider-nav__badge"
                >{{ item.badge }}</span>
            </Link>
        </div>
    </nav>
</template>

<style scoped>
.provider-nav {
    border-bottom: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
}

.provider-nav__inner {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.provider-nav__inner::-webkit-scrollbar {
    display: none;
}

.provider-nav__item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.125rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--dl-text-muted);
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    position: relative;
}

.provider-nav__item:hover {
    color: var(--dl-text);
    background: rgba(0,0,0,0.02);
}

.provider-nav__item--active {
    color: var(--dl-provider, #e85d2a);
    font-weight: 600;
    border-bottom-color: var(--dl-provider, #e85d2a);
}

.provider-nav__icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.provider-nav__label {
    /* Visible by default */
}

.provider-nav__badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.125rem;
    height: 1.125rem;
    padding: 0 0.3rem;
    border-radius: 9999px;
    background: var(--dl-provider, #e85d2a);
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    line-height: 1;
}

@media (max-width: 640px) {
    .provider-nav__label {
        display: none;
    }
    .provider-nav__item {
        padding: 0.75rem;
    }
    .provider-nav__icon {
        width: 1.25rem;
        height: 1.25rem;
    }
}
</style>
