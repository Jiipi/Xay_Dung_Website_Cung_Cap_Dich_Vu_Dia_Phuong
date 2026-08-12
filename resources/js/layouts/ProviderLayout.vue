<script setup lang="ts">
import { useRealtimeUserChannel } from '@/composables/useRealtimeUserChannel';
import {
    Bell,
    CalendarCheck,
    ClipboardList,
    LayoutDashboard,
    MessageCircle,
    Package,
    Settings,
    Star,
    User,
    Wallet,
} from 'lucide-vue-next';
import { computed } from 'vue';
import SharedDashboardLayout from './SharedDashboardLayout.vue';

const { pendingBookingsCount, unreadNotifications } = useRealtimeUserChannel();
const pendingCount = computed(() => pendingBookingsCount.value);
const unreadCount = computed(() => unreadNotifications.value);

const navItems = computed(() => [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/provider/dashboard' },
    { key: 'services', label: 'Dịch vụ', icon: Package, href: '/provider/services' },
    { key: 'bookings', label: 'Đơn hàng', icon: ClipboardList, href: '/provider/bookings', badge: pendingCount.value },
    { key: 'reviews', label: 'Đánh giá', icon: Star, href: '/provider/reviews' },
    { key: 'finance', label: 'Tài chính', icon: Wallet, href: '/provider/finance' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/provider/notifications', badge: unreadCount.value },
    { key: 'chat', label: 'Tin nhắn', icon: MessageCircle, href: '/chat' },
    { key: 'availability', label: 'Lịch rảnh', icon: CalendarCheck, href: '/provider/availability' },
    { key: 'profile', label: 'Hồ sơ', icon: User, href: '/provider/profile' },
]);
</script>

<template>
    <SharedDashboardLayout
        role="provider"
        :navItems="navItems"
        logoText="Provider"
        logoTitle="Dalat Services"
        logoUrl="/provider/dashboard"
        homeUrl="/"
        showMarketplaceLink
        showNotifications
    >
        <slot />
    </SharedDashboardLayout>
</template>
