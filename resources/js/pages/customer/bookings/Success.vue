<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, Calendar, MapPin, Clock, Copy, ArrowRight, Home } from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{ booking?: any }>(),
    { booking: () => ({}) },
);

const formatVND = (v: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

function copyCode() {
    navigator.clipboard.writeText(props.booking.code ?? '');
}
</script>

<template>
    <Head title="Đặt lịch thành công!" />

    <CustomerLayout activePage="bookings">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
            <!-- Success Card -->
            <div class="rounded-[2rem] border bg-white p-8 shadow-sm text-center" style="border-color: var(--dl-warm-border);">
                <!-- Icon -->
                <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full" style="background: var(--dl-brand-surface);">
                    <CheckCircle class="size-10" style="color: var(--dl-brand);" />
                </div>

                <h1 class="text-3xl font-black tracking-tight text-stone-950">Đặt lịch thành công!</h1>
                <p class="mt-2 text-stone-500">Nhà cung cấp sẽ xác nhận đơn của bạn trong thời gian sớm nhất.</p>

                <!-- Booking Code -->
                <div class="mt-8 inline-flex items-center gap-3 rounded-2xl border border-dashed px-6 py-4" style="border-color: var(--dl-brand); background: var(--dl-brand-surface);">
                    <span class="text-xs font-medium uppercase tracking-widest text-stone-500">Mã đơn</span>
                    <span class="text-2xl font-black tracking-wider" style="color: var(--dl-brand);">{{ booking.code }}</span>
                    <button @click="copyCode" class="rounded-lg p-1.5 transition hover:bg-white/80" title="Sao chép">
                        <Copy class="size-4 text-stone-400" />
                    </button>
                </div>

                <!-- Booking Details -->
                <div class="mt-8 space-y-3 rounded-2xl bg-stone-50 p-6 text-left text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-500">
                            <Calendar class="size-4" /> Dịch vụ
                        </span>
                        <span class="font-semibold text-stone-900">{{ booking.service }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-500">
                            <Clock class="size-4" /> Nhà cung cấp
                        </span>
                        <span class="font-semibold text-stone-900">{{ booking.provider }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-500">
                            <Calendar class="size-4" /> Ngày thực hiện
                        </span>
                        <span class="font-semibold text-stone-900">{{ booking.date }} {{ booking.time ? `lúc ${booking.time}` : '' }}</span>
                    </div>
                    <div v-if="booking.address" class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-stone-500">
                            <MapPin class="size-4" /> Địa điểm
                        </span>
                        <span class="font-semibold text-stone-900 text-right max-w-[60%]">{{ booking.address }}</span>
                    </div>
                    <div class="border-t border-stone-200 pt-3 flex items-center justify-between">
                        <span class="font-bold text-stone-900">Tổng tiền</span>
                        <span class="text-xl font-black" style="color: var(--dl-brand);">{{ formatVND(booking.total) }}</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="mt-6 flex items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    <Clock class="size-4" />
                    <span class="font-medium">Đang chờ nhà cung cấp xác nhận</span>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <Link
                        :href="`/customer/bookings/${booking.id}`"
                        class="inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-bold text-white transition hover:opacity-90"
                        style="background: var(--dl-brand);"
                    >
                        Xem chi tiết đơn <ArrowRight class="size-4" />
                    </Link>
                    <Link
                        href="/"
                        class="inline-flex items-center justify-center gap-2 rounded-full border border-stone-300 bg-white px-6 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                    >
                        <Home class="size-4" /> Về trang chủ
                    </Link>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
