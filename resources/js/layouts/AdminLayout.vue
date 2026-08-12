<script setup lang="ts">
import {
    BarChart3,
    CheckSquare,
    Globe,
    LayoutDashboard,
    Settings,
    Users,
    CalendarDays,
    Star,
    Package,
    ClipboardList,
    Banknote,
    Layers,
} from 'lucide-vue-next';
import SharedDashboardLayout from './SharedDashboardLayout.vue';

const mainNav = [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/admin/dashboard' },
    { key: 'users', label: 'Người dùng', icon: Users, href: '/admin/users' },
    { key: 'categories', label: 'Danh mục', icon: Layers, href: '/admin/categories' },
    { key: 'services', label: 'Dịch vụ', icon: Package, href: '/admin/services' },
    { key: 'bookings', label: 'Đơn hàng', icon: ClipboardList, href: '/admin/bookings' },
    { key: 'finance', label: 'Tài chính', icon: Banknote, href: '/admin/finance' },
    { key: 'reviews', label: 'Đánh giá', icon: Star, href: '/admin/reviews' },
    { key: 'stats', label: 'Thống kê', icon: BarChart3, href: '/admin/stats' },
    { key: 'settings', label: 'Cấu hình', icon: Settings, href: '/admin/settings' },
];
</script>

<template>
    <SharedDashboardLayout
        role="admin"
        :navItems="mainNav"
        logoText="Admin Panel"
        logoTitle="Dalat Services"
        logoUrl="/admin/dashboard"
        homeUrl="/"
    >
        <slot />
    </SharedDashboardLayout>
</template>
