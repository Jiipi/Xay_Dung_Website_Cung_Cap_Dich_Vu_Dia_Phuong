<script setup lang="ts">
import { computed } from 'vue';
import { useRealtimeUserChannel } from '@/composables/useRealtimeUserChannel';
import {
    Bell,
    CalendarDays,
    Heart,
    LayoutDashboard,
    MessageCircle,
    Settings,
    User,
    Wallet,
} from 'lucide-vue-next';
import SharedDashboardLayout from './SharedDashboardLayout.vue';

const { unreadNotifications } = useRealtimeUserChannel();
const unreadCount = computed(() => unreadNotifications.value);

const navItems = computed(() => [
    { key: 'dashboard', label: 'Tổng quan', icon: LayoutDashboard, href: '/customer/dashboard' },
    { key: 'bookings', label: 'Booking của tôi', icon: CalendarDays, href: '/customer/bookings' },
    { key: 'favorites', label: 'Yêu thích', icon: Heart, href: '/customer/favorites' },
    { key: 'wallet', label: 'Ví của tôi', icon: Wallet, href: '/customer/wallet' },
    { key: 'notifications', label: 'Thông báo', icon: Bell, href: '/customer/notifications', badge: unreadCount.value },
    { key: 'chat', label: 'Tin nhắn', icon: MessageCircle, href: '/chat' },
    { key: 'profile', label: 'Hồ sơ cá nhân', icon: User, href: '/customer/profile' },
]);
</script>

<template>
    <SharedDashboardLayout
        role="customer"
        :navItems="navItems"
        logoText="Customer"
        logoTitle="Dalat Services"
        logoUrl="/customer/dashboard"
        homeUrl="/"
        showMarketplaceLink
        showNotifications
    >
        <slot />
    </SharedDashboardLayout>
</template>
