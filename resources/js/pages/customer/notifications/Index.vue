<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Bell, BellOff, Calendar, CheckCircle, 
    Star, XCircle, Package, CheckCheck,
} from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{
        notifications?: any[];
        unreadCount?: number;
    }>(),
    {
        notifications: () => [],
        unreadCount: 0,
    },
);

const typeIconMap: Record<string, any> = {
    booking_created:   CheckCircle,
    booking_new:       Calendar,
    booking_cancelled: XCircle,
    review_new:        Star,
};

const typeColorMap: Record<string, string> = {
    booking_created:   'bg-emerald-100 text-emerald-600',
    booking_new:       'bg-blue-100 text-blue-600',
    booking_cancelled: 'bg-red-100 text-red-600',
    review_new:        'bg-amber-100 text-amber-600',
};

function getIcon(type: string) {
    return typeIconMap[type] || Bell;
}

function getColor(type: string) {
    return typeColorMap[type] || 'bg-stone-100 text-stone-600';
}

function markRead(id: number) {
    router.post(`/notifications/${id}/read`, {}, { preserveScroll: true });
}

function markAllRead() {
    router.post('/notifications/read-all', {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Thông báo" />

    <CustomerLayout activePage="notifications">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-stone-950">Thông báo</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ unreadCount }} chưa đọc</p>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllRead"
                        class="flex items-center gap-2 rounded-full border border-stone-200 px-4 py-2 text-xs font-medium text-stone-600 transition hover:bg-stone-50"
                    >
                        <CheckCheck class="size-3.5" /> Đọc tất cả
                    </button>
                </div>

                <div v-if="notifications.length > 0" class="space-y-2">
                    <div
                        v-for="n in notifications"
                        :key="n.id"
                        :class="[
                            'flex items-start gap-4 rounded-2xl p-4 transition cursor-pointer',
                            n.read ? 'bg-white hover:bg-stone-50' : 'bg-stone-50 hover:bg-stone-100',
                        ]"
                        @click="!n.read && markRead(n.id)"
                    >
                        <div :class="['flex size-10 shrink-0 items-center justify-center rounded-xl', getColor(n.type)]">
                            <component :is="getIcon(n.type)" class="size-5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 :class="['text-sm', n.read ? 'font-medium text-stone-700' : 'font-bold text-stone-950']">
                                    {{ n.title }}
                                </h3>
                                <span class="shrink-0 text-[11px] text-stone-400">{{ n.date }}</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-500 line-clamp-2">{{ n.body }}</p>
                        </div>
                        <div v-if="!n.read" class="mt-2 size-2.5 shrink-0 rounded-full" style="background: var(--dl-brand);"></div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center py-16 text-center">
                    <BellOff class="mb-4 size-12 text-stone-300" />
                    <h3 class="text-lg font-bold text-stone-900">Chưa có thông báo</h3>
                    <p class="mt-1 text-sm text-stone-500">Bạn sẽ nhận thông báo khi có cập nhật mới.</p>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>