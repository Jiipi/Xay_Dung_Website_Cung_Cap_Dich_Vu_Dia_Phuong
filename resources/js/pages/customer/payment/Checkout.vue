<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, CreditCard, ShieldCheck, Wallet } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

interface Booking {
    id: number;
    code: string;
    total: number;
    deposit: number;
    service: string;
}

const props = defineProps<{
    booking: Booking;
    wallet_balance: number;
    demo_payment_enabled: boolean;
}>();

const form = useForm({
    action: 'success', // 'success', 'fail', or 'wallet'
});

function processPayment(action: 'success' | 'fail' | 'wallet') {
    form.action = action;
    form.post(`/customer/bookings/${props.booking.id}/payment/process`);
}

function formatMoney(amount: number) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

const canPayFromWallet = props.wallet_balance >= props.booking.deposit;
</script>

<template>
    <Head title="Thanh toán Đặt cọc" />

    <MarketplaceLayout>
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center gap-3">
                <Link :href="`/customer/bookings/${booking.id}`" class="rounded-lg border border-stone-200 p-2 text-stone-500 hover:bg-stone-50">
                    <ArrowLeft class="size-4" />
                </Link>
                <h1 class="text-xl font-bold text-stone-950">Thanh toán Đặt cọc</h1>
            </div>

            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                <div class="bg-blue-50 p-6 text-center border-b border-blue-100">
                    <div class="mx-auto mb-3 flex size-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <CreditCard class="size-6" />
                    </div>
                    <h2 class="text-lg font-semibold text-blue-900">Cổng thanh toán (Mô phỏng)</h2>
                    <p class="text-sm text-blue-700 mt-1">Đơn hàng: <strong>{{ booking.code }}</strong></p>
                </div>

                <div class="p-6">
                    <div class="mb-6 space-y-4 rounded-xl bg-stone-50 p-5 border border-stone-100">
                        <div class="flex justify-between">
                            <span class="text-stone-500 text-sm">Dịch vụ</span>
                            <span class="font-medium text-stone-900">{{ booking.service }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500 text-sm">Tổng tiền đơn</span>
                            <span class="font-medium text-stone-900">{{ formatMoney(booking.total) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-stone-200 pt-4">
                            <span class="text-stone-900 font-medium">Số tiền cần cọc (30%)</span>
                            <span class="text-xl font-bold text-brand">{{ formatMoney(booking.deposit) }}</span>
                        </div>
                    </div>

                    <!-- Wallet Payment Option -->
                    <div class="mb-4 rounded-xl border-2 p-4 transition-all" :class="canPayFromWallet ? 'border-indigo-200 bg-indigo-50/50' : 'border-stone-200 bg-stone-50 opacity-70'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <Wallet class="size-5 text-indigo-500" />
                                <span class="font-semibold text-stone-900">Thanh toán từ Ví</span>
                            </div>
                            <span class="text-sm font-medium" :class="canPayFromWallet ? 'text-emerald-600' : 'text-red-500'">
                                Số dư: {{ formatMoney(wallet_balance) }}
                            </span>
                        </div>
                        <button
                            @click="processPayment('wallet')"
                            :disabled="form.processing || !canPayFromWallet"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3.5 text-sm font-medium text-white shadow-sm hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            <Wallet class="size-5" />
                            <span v-if="canPayFromWallet">Thanh toán {{ formatMoney(booking.deposit) }} từ ví</span>
                            <span v-else>Số dư không đủ — <Link href="/customer/wallet" class="underline">Nạp thêm</Link></span>
                        </button>
                    </div>

                    <template v-if="demo_payment_enabled">
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-stone-200" /></div>
                            <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-stone-400 uppercase tracking-wider">Hoặc</span></div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button
                                @click="processPayment('success')"
                                :disabled="form.processing"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50 transition-colors"
                            >
                                <ShieldCheck class="size-5" />
                                Mô phỏng: Thanh toán VNPay THÀNH CÔNG
                            </button>

                            <button
                                @click="processPayment('fail')"
                                :disabled="form.processing"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3.5 text-sm font-medium text-white shadow-sm hover:bg-red-600 disabled:opacity-50 transition-colors"
                            >
                                Mô phỏng: Khách hủy giao dịch
                            </button>
                        </div>

                        <p class="mt-4 text-center text-xs text-stone-400">Đây là giao diện mô phỏng thanh toán Sandbox. Không có giao dịch tiền thật nào diễn ra.</p>
                    </template>
                    <p v-else class="mt-4 text-center text-xs text-stone-500">
                        Cổng thanh toán ngoài đang chờ cấu hình callback ký số; thanh toán ví nội bộ vẫn khả dụng.
                    </p>
                </div>
            </div>
        </div>
    </MarketplaceLayout>
</template>
