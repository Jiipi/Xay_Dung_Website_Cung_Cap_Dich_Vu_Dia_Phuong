<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    CheckSquare,
    LayoutDashboard,
    Settings,
    Users,
} from 'lucide-vue-next';

defineProps<{ activePage?: string }>();

const page = usePage();

const navItems = [
    { key: 'dashboard', label: 'Dashboard', icon: LayoutDashboard, href: '/admin/dashboard' },
    { key: 'users', label: 'Người dùng', icon: Users, href: '/admin/users' },
    { key: 'services', label: 'Duyệt dịch vụ', icon: CheckSquare, href: '/admin/services' },
    { key: 'stats', label: 'Thống kê', icon: BarChart3, href: '/admin/stats' },
    { key: 'settings', label: 'Cài đặt', icon: Settings, href: '/settings/profile' },
];
</script>

<template>
    <nav class="admin-nav">
        <div class="admin-nav__inner">
            <Link
                v-for="item in navItems"
                :key="item.key"
                :href="item.href"
                class="admin-nav__item"
                :class="{ 'admin-nav__item--active': activePage === item.key }"
            >
                <component :is="item.icon" class="admin-nav__icon" />
                <span class="admin-nav__label">{{ item.label }}</span>
            </Link>
        </div>
    </nav>
</template>

<style scoped>
.admin-nav {
    border-bottom: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
}

.admin-nav__inner {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.admin-nav__inner::-webkit-scrollbar {
    display: none;
}

.admin-nav__item {
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

.admin-nav__item:hover {
    color: var(--dl-text);
    background: rgba(0,0,0,0.02);
}

.admin-nav__item--active {
    color: #3b82f6;
    font-weight: 600;
    border-bottom-color: #3b82f6;
}

.admin-nav__icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.admin-nav__label {
    /* Visible by default */
}

@media (max-width: 640px) {
    .admin-nav__label {
        display: none;
    }
    .admin-nav__item {
        padding: 0.75rem;
    }
    .admin-nav__icon {
        width: 1.25rem;
        height: 1.25rem;
    }
}
</style>
