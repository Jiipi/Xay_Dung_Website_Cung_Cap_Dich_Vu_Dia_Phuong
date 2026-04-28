<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart3, Briefcase, CalendarCheck, Clock3, ShieldCheck, Star, SquarePen } from 'lucide-vue-next';

const props = defineProps<{
    activePage: 'dashboard' | 'profile' | 'services' | 'create-service' | 'bookings' | 'availability' | 'reviews';
}>();

const page = usePage();
const providerProfile = page.props.providerProfile as { ten_thuong_hieu: string; diem_danh_gia: number } | null;
const userName = page.props.auth?.user?.name ?? 'Nhà cung cấp';

const navItems = [
    { key: 'dashboard', label: 'Tổng quan', icon: BarChart3, href: '/provider/dashboard' },
    { key: 'profile', label: 'Hồ sơ nhà cung cấp', icon: ShieldCheck, href: '/provider/profile' },
    { key: 'services', label: 'Quản lý dịch vụ', icon: Briefcase, href: '/provider/services' },
    { key: 'create-service', label: 'Tạo dịch vụ mới', icon: SquarePen, href: '/provider/services/create' },
    { key: 'bookings', label: 'Quản lý Booking', icon: CalendarCheck, href: '/provider/bookings' },
    { key: 'availability', label: 'Lịch làm việc', icon: Clock3, href: '/provider/availability' },
    { key: 'reviews', label: 'Đánh giá nhận được', icon: Star, href: '/provider/reviews' },
] as const;
</script>

<template>
    <aside class="w-full shrink-0 md:w-60">
        <div class="sticky top-20 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <!-- Provider Profile -->
            <div class="border-b border-stone-100 p-5 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-indigo-100 text-sky-700 ring-2 ring-sky-200/50">
                    <span class="text-xl font-bold">{{ (providerProfile?.ten_thuong_hieu ?? userName)?.charAt(0)?.toUpperCase() }}</span>
                </div>
                <h2 class="mt-3 text-sm font-bold text-stone-950">{{ providerProfile?.ten_thuong_hieu ?? userName }}</h2>
                <p v-if="providerProfile?.diem_danh_gia" class="mt-1 flex items-center justify-center gap-1 text-xs text-stone-500">
                    <Star class="size-3.5 fill-amber-400 text-amber-400" />
                    {{ providerProfile.diem_danh_gia }}
                </p>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1 p-3">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-[13px] font-medium transition-all duration-200"
                    :class="activePage === item.key
                        ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-100'
                        : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900'"
                >
                    <component :is="item.icon" class="size-4.5 shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>
        </div>
    </aside>
</template>
