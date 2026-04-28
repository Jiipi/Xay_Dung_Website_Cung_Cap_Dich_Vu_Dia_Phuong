<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Check,
    CheckCircle2,
    Clock,
    CreditCard,
    DollarSign,
    MapPin,
    MessageSquare,
    Phone,
    Star,
    User,
    X as XIcon,
    XCircle,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface BookingDetail {
    id: number;
    ma_don: string;
    khach_hang: {
        ho_ten: string;
        email: string | null;
        so_dien_thoai: string | null;
        anh_dai_dien: string | null;
    };
    dich_vu: {
        id: number;
        ten_dich_vu: string;
        gia_tu: number;
    };
    thoi_gian_thuc_hien: string | null;
    dia_diem_thuc_hien: string | null;
    so_luong: number;
    don_vi: string | null;
    ghi_chu: string | null;
    tam_tinh: number;
    phi_dich_vu: number;
    giam_gia: number;
    tong_tien: number;
    trang_thai: string;
    trang_thai_thanh_toan: string;
    phuong_thuc_thanh_toan: string | null;
    ngay_dat: string;
    danh_gia: {
        so_sao: number;
        noi_dung: string | null;
        phan_hoi_tu_ncc: string | null;
    } | null;
}

const props = defineProps<{
    booking: BookingDetail;
}>();

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
    error: page.props.flash?.error as string | undefined,
}));

const statusStyles: Record<string, string> = {
    cho_xac_nhan: 'bg-amber-50 text-amber-700 ring-amber-200',
    da_xac_nhan: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    hoan_thanh: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    da_huy: 'bg-red-50 text-red-700 ring-red-200',
};

const statusLabels: Record<string, string> = {
    cho_xac_nhan: 'Chờ xác nhận',
    da_xac_nhan: 'Đã xác nhận',
    hoan_thanh: 'Hoàn thành',
    da_huy: 'Đã hủy',
};

const paymentLabels: Record<string, string> = {
    cho_thanh_toan: 'Chờ thanh toán',
    da_thanh_toan: 'Đã thanh toán',
    hoan_tien: 'Đã hoàn tiền',
};

const formatVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

function confirmBooking() {
    router.post(`/provider/bookings/${props.booking.id}/confirm`);
}

function rejectBooking() {
    if (confirm('Bạn có chắc muốn từ chối booking này?')) {
        router.post(`/provider/bookings/${props.booking.id}/reject`);
    }
}

function completeBooking() {
    router.post(`/provider/bookings/${props.booking.id}/complete`);
}
</script>

<template>
    <Head :title="`Booking #${booking.ma_don}`" />

    <ProviderLayout activePage="bookings">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="space-y-6">
                    <!-- Flash -->
                    <div v-if="flash.success" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <CheckCircle2 class="size-5 shrink-0" /> {{ flash.success }}
                    </div>

                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <Link href="/provider/bookings" class="rounded-lg border border-stone-200 p-2 text-stone-500 transition-colors hover:bg-stone-50 hover:text-stone-700">
                                <ArrowLeft class="size-4" />
                            </Link>
                            <div>
                                <h1 class="text-xl font-bold text-stone-950">Booking #{{ booking.ma_don }}</h1>
                                <p class="text-sm text-stone-500">Đặt lúc {{ booking.ngay_dat }}</p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1"
                            :class="statusStyles[booking.trang_thai] ?? 'bg-stone-50 text-stone-600 ring-stone-200'"
                        >
                            {{ statusLabels[booking.trang_thai] ?? booking.trang_thai }}
                        </span>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <!-- Thông tin khách hàng -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-stone-950">Thông tin khách hàng</h2>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-stone-100">
                                        <img v-if="booking.khach_hang.anh_dai_dien" :src="booking.khach_hang.anh_dai_dien" class="size-full object-cover" referrerpolicy="no-referrer" />
                                        <User v-else class="size-4 text-stone-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-stone-950">{{ booking.khach_hang.ho_ten }}</p>
                                        <p v-if="booking.khach_hang.email" class="text-xs text-stone-500">{{ booking.khach_hang.email }}</p>
                                    </div>
                                </div>
                                <p v-if="booking.khach_hang.so_dien_thoai" class="flex items-center gap-2 text-sm text-stone-600">
                                    <Phone class="size-4 text-stone-400" /> {{ booking.khach_hang.so_dien_thoai }}
                                </p>
                            </div>
                        </div>

                        <!-- Thông tin dịch vụ -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-stone-950">Dịch vụ</h2>
                            <div class="space-y-3">
                                <p class="text-sm font-medium text-stone-950">{{ booking.dich_vu.ten_dich_vu }}</p>
                                <p v-if="booking.thoi_gian_thuc_hien" class="flex items-center gap-2 text-sm text-stone-600">
                                    <Clock class="size-4 text-stone-400" /> {{ booking.thoi_gian_thuc_hien }}
                                </p>
                                <p v-if="booking.dia_diem_thuc_hien" class="flex items-center gap-2 text-sm text-stone-600">
                                    <MapPin class="size-4 text-stone-400" /> {{ booking.dia_diem_thuc_hien }}
                                </p>
                                <p class="flex items-center gap-2 text-sm text-stone-600">
                                    <Calendar class="size-4 text-stone-400" /> Số lượng: {{ booking.so_luong }} {{ booking.don_vi ?? '' }}
                                </p>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-stone-950">Ghi chú</h2>
                            <p v-if="booking.ghi_chu" class="flex items-start gap-2 text-sm leading-relaxed text-stone-600">
                                <MessageSquare class="mt-0.5 size-4 shrink-0 text-stone-400" />
                                {{ booking.ghi_chu }}
                            </p>
                            <p v-else class="text-sm text-stone-400">Không có ghi chú</p>
                        </div>

                        <!-- Thanh toán -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-semibold text-stone-950">Thanh toán</h2>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-stone-500">Tạm tính</span>
                                    <span class="text-stone-700">{{ formatVND(booking.tam_tinh) }}</span>
                                </div>
                                <div v-if="booking.phi_dich_vu" class="flex items-center justify-between text-sm">
                                    <span class="text-stone-500">Phí dịch vụ</span>
                                    <span class="text-stone-700">{{ formatVND(booking.phi_dich_vu) }}</span>
                                </div>
                                <div v-if="booking.giam_gia" class="flex items-center justify-between text-sm">
                                    <span class="text-stone-500">Giảm giá</span>
                                    <span class="text-emerald-600">-{{ formatVND(booking.giam_gia) }}</span>
                                </div>
                                <div class="border-t border-stone-100 pt-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-stone-950">Tổng cộng</span>
                                        <span class="text-lg font-bold text-stone-950">{{ formatVND(booking.tong_tien) }}</span>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-stone-500">
                                    <CreditCard class="size-3.5" />
                                    {{ paymentLabels[booking.trang_thai_thanh_toan] ?? booking.trang_thai_thanh_toan }}
                                    <span v-if="booking.phuong_thuc_thanh_toan" class="text-stone-400">· {{ booking.phuong_thuc_thanh_toan }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Đánh giá -->
                        <div v-if="booking.danh_gia" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm lg:col-span-2">
                            <h2 class="mb-4 text-base font-semibold text-stone-950">Đánh giá từ khách hàng</h2>
                            <div class="flex gap-1">
                                <Star v-for="s in 5" :key="s" class="size-4" :class="s <= booking.danh_gia.so_sao ? 'fill-amber-400 text-amber-400' : 'text-stone-200'" />
                            </div>
                            <p v-if="booking.danh_gia.noi_dung" class="mt-2 text-sm leading-relaxed text-stone-600">{{ booking.danh_gia.noi_dung }}</p>
                            <div v-if="booking.danh_gia.phan_hoi_tu_ncc" class="mt-3 rounded-xl bg-brand-surface px-4 py-3">
                                <p class="text-xs font-medium text-brand">Phản hồi của bạn:</p>
                                <p class="mt-1 text-sm text-brand">{{ booking.danh_gia.phan_hoi_tu_ncc }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="booking.trang_thai === 'cho_xac_nhan' || booking.trang_thai === 'da_xac_nhan'" class="flex justify-end gap-3">
                        <template v-if="booking.trang_thai === 'cho_xac_nhan'">
                            <button
                                class="flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                                @click="confirmBooking"
                            >
                                <Check class="size-4" /> Xác nhận booking
                            </button>
                            <button
                                class="flex items-center gap-2 rounded-xl border border-red-200 px-5 py-3 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                                @click="rejectBooking"
                            >
                                <XIcon class="size-4" /> Từ chối
                            </button>
                        </template>
                        <button
                            v-if="booking.trang_thai === 'da_xac_nhan'"
                            class="flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-orange-700"
                            @click="completeBooking"
                        >
                            <Check class="size-4" /> Đánh dấu hoàn thành
                        </button>
                    </div>
            </div>
        </div>
    </ProviderLayout>
</template>