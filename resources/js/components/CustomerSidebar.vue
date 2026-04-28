<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Bell,
    CalendarDays,
    Heart,
    LayoutDashboard,
    LogOut,
    Settings,
    User,
} from 'lucide-vue-next';

defineProps<{
    activePage?: string;
    unreadCount?: number;
}>();

const navItems = [
    { key: 'dashboard', href: '/customer/dashboard', icon: LayoutDashboard, label: 'Dashboard' },
    { key: 'bookings', href: '/customer/bookings', icon: CalendarDays, label: 'Booking của tôi' },
    { key: 'favorites', href: '/customer/favorites', icon: Heart, label: 'Yêu thích' },
    { key: 'notifications', href: '/customer/notifications', icon: Bell, label: 'Thông báo' },
    { key: 'profile', href: '/customer/profile', icon: User, label: 'Hồ sơ cá nhân' },
    { key: 'settings', href: '/settings/profile', icon: Settings, label: 'Cài đặt tài khoản' },
];
</script>

<template>
    <aside class="w-full shrink-0 lg:w-72">
        <div class="sticky top-24 overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
            <nav class="space-y-1 p-4">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition"
                    :class="activePage === item.key
                        ? 'text-brand'
                        : 'text-stone-600 hover:bg-stone-50'"
                    :style="activePage === item.key ? 'background: var(--dl-brand-surface); color: var(--dl-brand);' : ''"
                >
                    <component :is="item.icon" class="size-5" />
                    {{ item.label }}
                    <span
                        v-if="item.key === 'notifications' && (unreadCount ?? 0) > 0"
                        class="ml-auto rounded-full px-2 py-0.5 text-[10px] font-bold text-white"
                        style="background: var(--dl-accent);"
                    >
                        {{ unreadCount }}
                    </span>
                </Link>

                <div class="my-2 border-t border-stone-100 pt-2" />

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                    <LogOut class="size-5" /> Đăng xuất
                </Link>
            </nav>
        </div>
    </aside>
</template>
