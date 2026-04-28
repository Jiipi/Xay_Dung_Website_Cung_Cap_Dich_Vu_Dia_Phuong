<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarRange, CheckCircle, ChevronRight,
    Clock, Package, XCircle, Star,
} from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{ bookings?: any[] }>(),
    { bookings: () => [] },
);

const statusTabs = [
    { key: 'all',            label: 'Tất cả' },
    { key: 'cho_xac_nhan',   label: 'Chờ xác nhận' },
    { key: 'da_xac_nhan',    label: 'Đã xác nhận' },
    { key: 'dang_thuc_hien', label: 'Đang thực hiện' },
    { key: 'hoan_thanh',     label: 'Hoàn thành' },
    { key: 'da_huy',         label: 'Đã hủy' },
];

const activeTab = ref('all');

const filteredBookings = computed(() => {
    if (activeTab.value === 'all') return props.bookings;
    return props.bookings.filter(b => b.status === activeTab.value);
});

const statusMap: Record<string, { label: string; cls: string; icon: typeof CheckCircle }> = {
    cho_xac_nhan:   { label: 'Chờ xác nhận',   cls: 'bg-amber-50 text-amber-700',   icon: Clock },
    da_xac_nhan:    { label: 'Đã xác nhận',    cls: 'bg-emerald-50 text-emerald-700', icon: CheckCircle },
    dang_thuc_hien: { label: 'Đang thực hiện',  cls: 'bg-blue-50 text-blue-700',     icon: Clock },
    hoan_thanh:     { label: 'Hoàn thành',      cls: 'bg-emerald-50 text-emerald-700', icon: CheckCircle },
    da_huy:         { label: 'Đã hủy',         cls: 'bg-red-50 text-red-700',        icon: XCircle },
};

function getStatus(s: string) {
    return statusMap[s] || statusMap.cho_xac_nhan;
}

const formatVND = (v: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

function tabCount(key: string) {
    if (key === 'all') return props.bookings.length;
    return props.bookings.filter(b => b.status === key).length;
}
</script>

<template>
    <Head title="Booking của tôi" />

    <CustomerLayout activePage="bookings">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-stone-950">Booking của tôi</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ bookings.length }} đơn đặt dịch vụ</p>
                    </div>
                    <Link href="/services" class="rounded-2xl px-5 py-3 text-sm font-bold text-white transition hover:opacity-90" style="background: var(--dl-brand);">
                        Đặt dịch vụ mới
                    </Link>
                </div>

                <!-- Status Tabs -->
                <div class="mb-6 flex gap-1.5 overflow-x-auto rounded-xl border border-stone-200 bg-stone-50 p-1.5">
                    <button
                        v-for="tab in statusTabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="[
                            'shrink-0 rounded-lg px-3 py-2 text-xs font-medium transition whitespace-nowrap',
                            activeTab === tab.key
                                ? 'bg-white text-stone-900 shadow-sm'
                                : 'text-stone-500 hover:text-stone-700',
                        ]"
                    >
                        {{ tab.label }}
                        <span v-if="tabCount(tab.key) > 0" class="ml-1 rounded-full bg-stone-200 px-1.5 py-0.5 text-[10px] font-bold text-stone-600">
                            {{ tabCount(tab.key) }}
                        </span>
                    </button>
                </div>

                <!-- Booking List -->
                <div v-if="filteredBookings.length > 0" class="space-y-4">
                    <div
                        v-for="item in filteredBookings"
                        :key="item.id"
                        class="group flex flex-col gap-4 rounded-2xl border border-stone-200 p-4 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-sm sm:flex-row"
                    >
                        <img :src="item.image" :alt="item.service" class="h-28 w-full rounded-xl object-cover sm:w-28" referrerpolicy="no-referrer" />
                        <div class="flex flex-1 flex-col justify-between">
                            <div>
                                <div class="mb-1 flex items-start justify-between">
                                    <div>
                                        <p class="font-mono text-xs text-stone-400">{{ item.code }}</p>
                                        <h3 class="mt-1 text-lg font-bold text-stone-950">{{ item.service }}</h3>
                                    </div>
                                    <span class="flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium" :class="getStatus(item.status).cls">
                                        <component :is="getStatus(item.status).icon" class="size-3" />
                                        {{ getStatus(item.status).label }}
                                    </span>
                                </div>
                                <p class="text-sm text-stone-500">Cung cấp bởi: <span class="font-medium text-stone-700">{{ item.provider }}</span></p>
                            </div>
                            <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3">
                                <div class="flex flex-wrap gap-4 text-sm text-stone-600">
                                    <span class="flex items-center gap-1"><CalendarRange class="size-4 text-stone-400" /> {{ item.date }}</span>
                                    <span class="flex items-center gap-1"><Clock class="size-4 text-stone-400" /> {{ item.time }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-stone-950">{{ formatVND(item.price) }}</span>
                                    <Link
                                        v-if="item.status === 'hoan_thanh' && !item.hasReview"
                                        :href="`/customer/reviews/create?booking_id=${item.id}`"
                                        class="rounded-full px-3 py-1.5 text-xs font-bold text-white"
                                        style="background: var(--dl-accent);"
                                    >
                                        ⭐ Đánh giá
                                    </Link>
                                    <Link
                                        :href="`/customer/bookings/${item.id}`"
                                        class="flex items-center gap-1 rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50"
                                    >
                                        Chi tiết <ChevronRight class="size-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center py-16 text-center">
                    <Package class="mb-4 size-12 text-stone-300" />
                    <h3 class="text-lg font-bold text-stone-900">
                        {{ activeTab === 'all' ? 'Chưa có đơn đặt nào' : 'Không có đơn nào ở trạng thái này' }}
                    </h3>
                    <p class="mt-1 text-sm text-stone-500">Hãy khám phá dịch vụ và đặt lịch đầu tiên!</p>
                    <Link href="/services" class="mt-4 rounded-2xl px-6 py-3 text-sm font-bold text-white transition hover:opacity-90" style="background: var(--dl-brand);">
                        Khám phá dịch vụ
                    </Link>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>