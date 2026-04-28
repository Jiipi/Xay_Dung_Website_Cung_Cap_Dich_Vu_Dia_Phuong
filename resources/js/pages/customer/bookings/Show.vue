<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft, Calendar, CheckCircle, Clock, MapPin, MessageSquare,
    Star, User, XCircle, AlertCircle, Package, ChevronRight,
} from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{ booking?: any }>(),
    { booking: () => ({}) },
);

const formatVND = (v: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

const statusMap: Record<string, { label: string; color: string; bgColor: string; icon: any }> = {
    cho_xac_nhan:  { label: 'Chờ xác nhận',   color: 'var(--dl-status-pending)',  bgColor: 'rgba(245,158,11,0.1)',  icon: Clock },
    da_xac_nhan:   { label: 'Đã xác nhận',    color: 'var(--dl-status-confirmed)', bgColor: 'rgba(16,185,129,0.1)', icon: CheckCircle },
    dang_thuc_hien:{ label: 'Đang thực hiện',  color: 'var(--dl-status-inprogress)', bgColor: 'rgba(59,130,246,0.1)', icon: Package },
    hoan_thanh:    { label: 'Hoàn thành',      color: 'var(--dl-status-completed)', bgColor: 'rgba(16,185,129,0.1)', icon: CheckCircle },
    da_huy:        { label: 'Đã hủy',         color: 'var(--dl-status-cancelled)', bgColor: 'rgba(239,68,68,0.1)',  icon: XCircle },
};

function getStatus(s: string) {
    return statusMap[s] || statusMap.cho_xac_nhan;
}

// Cancel modal
const showCancelModal = ref(false);
const cancelReason = ref('');
const isCancelling = ref(false);

function handleCancel() {
    isCancelling.value = true;
    router.post(`/customer/bookings/${props.booking.id}/cancel`, {
        ly_do: cancelReason.value || 'Khách hàng hủy đơn',
    }, {
        onFinish: () => { isCancelling.value = false; showCancelModal.value = false; },
    });
}

const canCancel = ['cho_xac_nhan', 'da_xac_nhan'].includes(props.booking.status);
const canReview = props.booking.status === 'hoan_thanh' && !props.booking.hasReview;
</script>

<template>
    <Head :title="`Đơn ${booking.code}`" />

    <CustomerLayout activePage="bookings">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Back -->
            <Link href="/customer/bookings" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition hover:text-stone-700">
                <ArrowLeft class="size-4" /> Quay lại danh sách
            </Link>

            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black tracking-tight text-stone-950">{{ booking.code }}</h1>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                            :style="{ background: getStatus(booking.status).bgColor, color: getStatus(booking.status).color }"
                        >
                            <component :is="getStatus(booking.status).icon" class="size-3.5" />
                            {{ getStatus(booking.status).label }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-stone-500">Đặt lúc {{ booking.createdAt }}</p>
                </div>

                <div class="flex gap-3">
                    <button
                        v-if="canCancel"
                        @click="showCancelModal = true"
                        class="rounded-full border border-red-200 bg-white px-5 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                    >
                        Hủy đơn
                    </button>
                    <Link
                        v-if="canReview"
                        :href="`/customer/reviews/create?booking_id=${booking.id}`"
                        class="rounded-full px-5 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                        style="background: var(--dl-brand);"
                    >
                        ⭐ Đánh giá
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <!-- Main Info -->
                <div class="space-y-6">
                    <!-- Service Info Card -->
                    <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-stone-900">Thông tin dịch vụ</h2>
                        <div class="flex gap-4">
                            <img :src="booking.image" class="size-20 rounded-2xl object-cover" referrerpolicy="no-referrer" />
                            <div>
                                <h3 class="text-base font-semibold text-stone-950">{{ booking.service }}</h3>
                                <p class="mt-1 text-sm text-stone-500">{{ booking.provider }}</p>
                                <Link
                                    :href="`/services/${booking.serviceId}`"
                                    class="mt-2 inline-flex items-center gap-1 text-xs font-medium transition"
                                    style="color: var(--dl-brand);"
                                >
                                    Xem dịch vụ <ChevronRight class="size-3" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-stone-900">Chi tiết đặt lịch</h2>
                        <div class="space-y-4 text-sm">
                            <div class="flex items-start gap-3">
                                <Calendar class="mt-0.5 size-5 text-stone-400" />
                                <div>
                                    <p class="font-medium text-stone-900">{{ booking.date }}</p>
                                    <p v-if="booking.time" class="text-stone-500">Lúc {{ booking.time }}</p>
                                </div>
                            </div>
                            <div v-if="booking.address" class="flex items-start gap-3">
                                <MapPin class="mt-0.5 size-5 text-stone-400" />
                                <div>
                                    <p class="font-medium text-stone-900">{{ booking.address }}</p>
                                </div>
                            </div>
                            <div v-if="booking.note" class="flex items-start gap-3">
                                <MessageSquare class="mt-0.5 size-5 text-stone-400" />
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-stone-400">Ghi chú</p>
                                    <p class="font-medium text-stone-900">{{ booking.note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel Info -->
                    <div v-if="booking.status === 'da_huy'" class="rounded-2xl border border-red-200 bg-red-50 p-5">
                        <div class="flex items-center gap-2 text-sm font-medium text-red-700">
                            <AlertCircle class="size-4" />
                            Đơn đã bị hủy {{ booking.cancelledBy === 'customer' ? 'bởi bạn' : 'bởi nhà cung cấp' }}
                        </div>
                        <p v-if="booking.cancelReason" class="mt-2 text-sm text-red-600">
                            Lý do: {{ booking.cancelReason }}
                        </p>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="space-y-6">
                    <div class="sticky top-24 rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-bold text-stone-900">Thanh toán</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-stone-600">
                                <span>Tạm tính</span>
                                <span class="font-medium text-stone-900">{{ formatVND(booking.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-stone-600">
                                <span>Phí dịch vụ</span>
                                <span class="font-medium text-stone-900">{{ booking.fee > 0 ? formatVND(booking.fee) : 'Miễn phí' }}</span>
                            </div>
                            <div v-if="booking.discount > 0" class="flex justify-between text-emerald-600">
                                <span>Giảm giá</span>
                                <span class="font-medium">-{{ formatVND(booking.discount) }}</span>
                            </div>
                            <div class="border-t border-stone-200 pt-3 flex justify-between text-base font-bold text-stone-950">
                                <span>Tổng tiền</span>
                                <span style="color: var(--dl-brand);">{{ formatVND(booking.total) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-xs text-stone-500">
                            <p><strong>Thanh toán:</strong> {{ booking.paymentMethod === 'cod' ? 'Thanh toán khi hoàn thành' : booking.paymentMethod }}</p>
                            <p class="mt-1"><strong>Trạng thái:</strong> {{ booking.paymentStatus === 'cho_thanh_toan' ? 'Chưa thanh toán' : 'Đã thanh toán' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <Teleport to="body">
            <div v-if="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showCancelModal = false">
                <div class="w-full max-w-md rounded-[2rem] bg-white p-8 shadow-2xl">
                    <h3 class="text-xl font-bold text-stone-950">Xác nhận hủy đơn</h3>
                    <p class="mt-2 text-sm text-stone-500">Bạn có chắc muốn hủy đơn <strong>{{ booking.code }}</strong>?</p>
                    <textarea
                        v-model="cancelReason"
                        rows="3"
                        class="mt-4 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100"
                        placeholder="Lý do hủy (không bắt buộc)..."
                    />
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showCancelModal = false" class="rounded-full border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50">
                            Quay lại
                        </button>
                        <button
                            @click="handleCancel"
                            :disabled="isCancelling"
                            class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ isCancelling ? 'Đang hủy...' : 'Xác nhận hủy' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </CustomerLayout>
</template>