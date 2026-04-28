<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    CalendarDays,
    Heart,
    LayoutDashboard,
    Settings,
    User,
} from 'lucide-vue-next';

defineProps<{ activePage?: string }>();

const page = usePage();
const unreadCount = (page.props as any).unreadNotifications ?? 0;

const navItems = [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/customer/dashboard' },
    { key: 'bookings', label: 'Booking', icon: CalendarDays, href: '/customer/bookings' },
    { key: 'favorites', label: 'Yêu thích', icon: Heart, href: '/customer/favorites' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/customer/notifications', badge: unreadCount },
    { key: 'profile', label: 'Hồ sơ', icon: User, href: '/customer/profile' },
    { key: 'settings', label: 'Cài đặt', icon: Settings, href: '/settings/profile' },
];
</script>

<template>
    <nav class="customer-nav">
        <div class="customer-nav__inner">
            <Link
                v-for="item in navItems"
                :key="item.key"
                :href="item.href"
                class="customer-nav__item"
                :class="{ 'customer-nav__item--active': activePage === item.key }"
            >
                <component :is="item.icon" class="customer-nav__icon" />
                <span class="customer-nav__label">{{ item.label }}</span>
                <span
                    v-if="item.badge && item.badge > 0"
                    class="customer-nav__badge"
                >{{ item.badge }}</span>
            </Link>
        </div>
    </nav>
</template>

<style scoped>
.customer-nav {
    border-bottom: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
}

.customer-nav__inner {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.customer-nav__inner::-webkit-scrollbar {
    display: none;
}

.customer-nav__item {
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

.customer-nav__item:hover {
    color: var(--dl-text);
    background: rgba(0,0,0,0.02);
}

.customer-nav__item--active {
    color: var(--dl-brand);
    font-weight: 600;
    border-bottom-color: var(--dl-brand);
}

.customer-nav__icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.customer-nav__label {
    /* Hide on very small screens */
}

.customer-nav__badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.125rem;
    height: 1.125rem;
    padding: 0 0.3rem;
    border-radius: 9999px;
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    line-height: 1;
}

@media (max-width: 640px) {
    .customer-nav__label {
        display: none;
    }
    .customer-nav__item {
        padding: 0.75rem;
    }
    .customer-nav__icon {
        width: 1.25rem;
        height: 1.25rem;
    }
}
</style>
