<script setup lang="ts">
defineOptions({ layout: CustomerLayout });
import { Head, useForm } from '@inertiajs/vue3';
import {
    Wallet,
    ArrowUpCircle,
    ArrowDownCircle,
    CreditCard,
    CheckCircle2,
    XCircle,
    Clock,
    Banknote,
    Sparkles,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = defineProps<{
    so_du: number;
    demo_topup_enabled: boolean;
    transactions: {
        id: number;
        loai: string;
        so_tien: number;
        phuong_thuc: string;
        trang_thai: string;
        ghi_chu: string;
        ma_giao_dich: string | null;
        ngay_tao: string;
    }[];
}>();

const formatCurrency = (val: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);

const quickAmounts = [50000, 100000, 200000, 500000, 1000000, 2000000];

const form = useForm({
    so_tien: null as number | null,
});

const selectedQuick = ref<number | null>(null);

const selectQuick = (amount: number) => {
    selectedQuick.value = amount;
    form.so_tien = amount;
};

const setCustom = () => {
    selectedQuick.value = null;
};

const submitTopup = () => {
    if (!form.so_tien || form.so_tien < 10000) return;
    form.post('/customer/wallet/topup', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedQuick.value = null;
        },
    });
};

const transactionTypeMap: Record<string, { label: string; color: string; icon: any; sign: string }> = {
    nap_tien: { label: 'Nạp tiền', color: 'text-emerald-600', icon: ArrowUpCircle, sign: '+' },
    thanh_toan_coc: { label: 'Đặt cọc', color: 'text-red-500', icon: ArrowDownCircle, sign: '-' },
    thanh_toan_vi: { label: 'Thanh toán từ ví', color: 'text-red-500', icon: ArrowDownCircle, sign: '-' },
    rut_tien: { label: 'Rút tiền', color: 'text-orange-500', icon: ArrowDownCircle, sign: '-' },
    hoan_tien: { label: 'Hoàn tiền', color: 'text-blue-500', icon: ArrowUpCircle, sign: '+' },
    thu_phi_nen_tang: { label: 'Phí nền tảng', color: 'text-gray-500', icon: ArrowDownCircle, sign: '-' },
};

const statusMap: Record<string, { label: string; icon: any; color: string }> = {
    thanh_cong: { label: 'Thành công', icon: CheckCircle2, color: 'text-emerald-500' },
    cho_xu_ly: { label: 'Đang xử lý', icon: Clock, color: 'text-amber-500' },
    that_bai: { label: 'Thất bại', icon: XCircle, color: 'text-red-500' },
};

const getType = (loai: string) => transactionTypeMap[loai] ?? { label: loai, color: 'text-gray-500', icon: CreditCard, sign: '' };
const getStatus = (status: string) => statusMap[status] ?? { label: status, icon: Clock, color: 'text-gray-500' };
</script>

<template>
    <Head title="Ví của tôi" />

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">

        <!-- Balance Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-8 text-white shadow-xl">
            <div class="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                <Wallet class="size-48" />
            </div>
            <div class="absolute -left-10 -bottom-10 size-40 rounded-full bg-white/5" />
            <div class="absolute right-20 bottom-4 size-24 rounded-full bg-white/5" />

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <Wallet class="size-5 text-white/80" />
                    <span class="text-sm font-medium text-white/80 uppercase tracking-wider">Số dư ví</span>
                </div>
                <div class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-1">
                    {{ formatCurrency(so_du) }}
                </div>
                <p class="text-sm text-white/60 mt-2">Ví nội bộ — Sử dụng để đặt cọc và thanh toán dịch vụ</p>
            </div>
        </div>

        <!-- Top-up Section -->
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-100 px-6 py-4 bg-stone-50/50">
                <div class="flex items-center gap-2">
                    <Banknote class="size-5 text-indigo-500" />
                    <h2 class="text-lg font-bold text-stone-900">Nạp tiền vào ví</h2>
                </div>
                <p class="text-sm text-stone-500 mt-1">Chọn mệnh giá hoặc nhập số tiền tùy chỉnh</p>
            </div>

            <form v-if="demo_topup_enabled" @submit.prevent="submitTopup" class="p-6 space-y-6">
                <!-- Quick amounts -->
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-3">Chọn nhanh</label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                        <button
                            v-for="amount in quickAmounts"
                            :key="amount"
                            type="button"
                            class="relative rounded-xl border-2 px-3 py-3 text-center font-semibold transition-all duration-200 hover:shadow-md"
                            :class="selectedQuick === amount
                                ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-200 shadow-md scale-[1.03]'
                                : 'border-stone-200 bg-white text-stone-700 hover:border-indigo-300 hover:bg-indigo-50/50'"
                            @click="selectQuick(amount)"
                        >
                            <span class="text-sm sm:text-base">{{ new Intl.NumberFormat('vi-VN').format(amount) }}</span>
                            <Sparkles v-if="selectedQuick === amount" class="absolute -top-1.5 -right-1.5 size-4 text-indigo-500" />
                        </button>
                    </div>
                </div>

                <!-- Custom amount -->
                <div>
                    <label for="custom-amount" class="block text-sm font-medium text-stone-700 mb-2">Hoặc nhập số tiền (VNĐ)</label>
                    <div class="relative">
                        <input
                            id="custom-amount"
                            v-model.number="form.so_tien"
                            type="number"
                            min="10000"
                            max="10000000"
                            step="10000"
                            class="w-full rounded-xl border-stone-300 pl-4 pr-16 py-3 text-lg font-semibold text-stone-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="Nhập số tiền..."
                            @focus="setCustom"
                        />
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-stone-400">VNĐ</span>
                    </div>
                    <p v-if="form.errors.so_tien" class="text-red-500 text-sm mt-1.5">{{ form.errors.so_tien }}</p>
                    <p class="text-xs text-stone-400 mt-1.5">Tối thiểu 10.000đ — Tối đa 10.000.000đ</p>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing || !form.so_tien || form.so_tien < 10000"
                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3.5 text-base font-bold text-white shadow-lg transition-all duration-300 hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg"
                >
                    <CreditCard class="size-5" />
                    <span v-if="form.processing">Đang xử lý...</span>
                    <span v-else>
                        Nạp {{ form.so_tien ? formatCurrency(form.so_tien) : '' }} vào ví
                    </span>
                </button>

                <p class="text-center text-xs text-stone-400">
                    Đây là nạp tiền mô phỏng (Demo) — không sử dụng tiền thật
                </p>
            </form>
            <div v-else class="p-6 text-sm text-stone-600">
                Nạp tiền mô phỏng đã được tắt. Hãy kết nối cổng thanh toán có callback ký số trước khi bật tính năng này trên production.
            </div>
        </div>

        <!-- Transaction History -->
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-stone-100 px-6 py-4 bg-stone-50/50">
                <div class="flex items-center gap-2">
                    <CreditCard class="size-5 text-indigo-500" />
                    <h2 class="text-lg font-bold text-stone-900">Lịch sử giao dịch</h2>
                </div>
            </div>

            <div v-if="transactions.length === 0" class="p-12 text-center">
                <Wallet class="size-12 text-stone-300 mx-auto mb-3" />
                <p class="text-stone-500 font-medium">Chưa có giao dịch nào</p>
                <p class="text-sm text-stone-400 mt-1">Hãy nạp tiền vào ví để bắt đầu</p>
            </div>

            <ul v-else class="divide-y divide-stone-100">
                <li
                    v-for="tx in transactions"
                    :key="tx.id"
                    class="flex items-center gap-4 px-6 py-4 hover:bg-stone-50/50 transition-colors"
                >
                    <!-- Icon -->
                    <div
                        class="flex size-10 items-center justify-center rounded-xl"
                        :class="getType(tx.loai).sign === '+' ? 'bg-emerald-50' : 'bg-red-50'"
                    >
                        <component
                            :is="getType(tx.loai).icon"
                            class="size-5"
                            :class="getType(tx.loai).color"
                        />
                    </div>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-stone-900">{{ getType(tx.loai).label }}</p>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                :class="getStatus(tx.trang_thai).color"
                            >
                                <component :is="getStatus(tx.trang_thai).icon" class="size-3" />
                                {{ getStatus(tx.trang_thai).label }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-500 mt-0.5 truncate">
                            {{ tx.ghi_chu }}
                            <span v-if="tx.ma_giao_dich" class="text-stone-400"> · {{ tx.ma_giao_dich }}</span>
                        </p>
                    </div>

                    <!-- Amount & Date -->
                    <div class="text-right shrink-0">
                        <p
                            class="text-sm font-bold"
                            :class="getType(tx.loai).sign === '+' ? 'text-emerald-600' : 'text-red-500'"
                        >
                            {{ getType(tx.loai).sign }}{{ formatCurrency(tx.so_tien) }}
                        </p>
                        <p class="text-xs text-stone-400 mt-0.5">{{ tx.ngay_tao }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
