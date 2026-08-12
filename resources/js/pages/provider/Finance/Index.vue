<script setup lang="ts">
defineOptions({ layout: ProviderLayout });
import { Head, useForm } from '@inertiajs/vue3';
import { Wallet, ArrowUpRight, ArrowDownLeft, Clock, CheckCircle, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

const props = defineProps<{
    so_du: number;
    giaoDich: any;
    yeuCauRutTien: any;
    stk_ngan_hang: string | null;
    ten_ngan_hang: string | null;
}>();

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const form = useForm({
    so_tien: '',
});

const isWithdrawing = ref(false);

const submitWithdraw = () => {
    form.post('/provider/finance/withdraw', {
        preserveScroll: true,
        onSuccess: () => {
            isWithdrawing.value = false;
            form.reset('so_tien');
        },
    });
};

const getTransactionIcon = (type: string) => {
    switch (type) {
        case 'thu_nhap':
            return ArrowDownLeft;
        case 'rut_tien':
        case 'thu_phi_nen_tang':
            return ArrowUpRight;
        default:
            return Wallet;
    }
};

const getTransactionColor = (type: string) => {
    switch (type) {
        case 'thu_nhap':
            return 'text-green-500 bg-green-500/10';
        case 'rut_tien':
        case 'thu_phi_nen_tang':
            return 'text-red-500 bg-red-500/10';
        default:
            return 'text-blue-500 bg-blue-500/10';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'cho_xu_ly': return Clock;
        case 'da_duyet': return CheckCircle;
        case 'tu_choi': return XCircle;
        default: return Clock;
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'cho_xu_ly': return 'text-yellow-500';
        case 'da_duyet': return 'text-green-500';
        case 'tu_choi': return 'text-red-500';
        default: return 'text-gray-500';
    }
};
</script>

<template>
    <Head title="Tài chính & Ví" />

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-stone-950">Tài chính & Ví</h2>
            <p class="mt-1 text-sm text-stone-500">Quản lý số dư và giao dịch của bạn</p>
        </div>

        <div class="space-y-6">
            <!-- Balance Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                    <Wallet class="w-48 h-48 -mr-8 -mt-8" />
                </div>
                <div class="relative z-10">
                    <h3 class="text-green-100 text-sm font-medium mb-1">Số dư khả dụng</h3>
                    <div class="text-4xl font-bold mb-6 tracking-tight">{{ formatCurrency(so_du) }}</div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-green-100">
                            <template v-if="stk_ngan_hang">
                                Ngân hàng: <span class="font-semibold">{{ ten_ngan_hang }}</span><br>
                                STK: <span class="font-semibold">{{ stk_ngan_hang }}</span>
                            </template>
                            <template v-else>
                                <span class="text-yellow-200">Chưa cập nhật thông tin ngân hàng</span>
                            </template>
                        </div>
                        <button
                            @click="isWithdrawing = true"
                            class="bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition-colors shadow-sm"
                            :disabled="so_du < 50000 || !stk_ngan_hang"
                            :class="{'opacity-50 cursor-not-allowed': so_du < 50000 || !stk_ngan_hang}"
                        >
                            Rút tiền
                        </button>
                    </div>
                </div>
            </div>

            <!-- Withdraw Modal -->
            <div v-if="isWithdrawing" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Yêu cầu rút tiền</h3>
                    <form @submit.prevent="submitWithdraw">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số tiền muốn rút (VNĐ)</label>
                            <input
                                v-model="form.so_tien"
                                type="number"
                                min="50000"
                                :max="so_du"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-green-500 focus:border-green-500"
                                placeholder="Tối thiểu 50.000đ"
                                required
                            />
                            <div v-if="form.errors.so_tien" class="text-red-500 text-sm mt-1">{{ form.errors.so_tien }}</div>
                            <p class="text-sm text-gray-500 mt-2">Tiền sẽ được chuyển về tài khoản {{ ten_ngan_hang }} ({{ stk_ngan_hang }})</p>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="isWithdrawing = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Hủy
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                                Xác nhận rút
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Transaction History -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Lịch sử giao dịch</h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="gd in giaoDich.data" :key="gd.id" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4" :class="getTransactionColor(gd.loai_giao_dich)">
                                    <component :is="getTransactionIcon(gd.loai_giao_dich)" class="w-5 h-5" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ gd.ghi_chu }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ gd.ngay_tao }} • Trạng thái: {{ gd.trang_thai }}</p>
                                </div>
                                <div class="font-semibold" :class="gd.loai_giao_dich === 'thu_nhap' ? 'text-green-600' : 'text-gray-900 dark:text-white'">
                                    {{ gd.loai_giao_dich === 'thu_nhap' ? '+' : '-' }}{{ formatCurrency(gd.so_tien) }}
                                </div>
                            </li>
                            <li v-if="giaoDich.data.length === 0" class="p-8 text-center text-gray-500">
                                Chưa có giao dịch nào
                            </li>
                        </ul>
                        <!-- Pagination can be added here if needed -->
                    </div>
                </div>

                <!-- Withdrawal Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Yêu cầu rút tiền gần đây</h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="yc in yeuCauRutTien" :key="yc.id" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(yc.so_tien) }}</div>
                                    <div class="flex items-center space-x-1" :class="getStatusColor(yc.trang_thai)">
                                        <component :is="getStatusIcon(yc.trang_thai)" class="w-4 h-4" />
                                        <span class="text-xs font-medium uppercase">{{ yc.trang_thai }}</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Ngày yêu cầu: {{ yc.ngay_tao }}
                                </div>
                                <div v-if="yc.admin_ghi_chu" class="mt-2 text-xs bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-2 rounded border border-red-100 dark:border-red-800/50">
                                    Lý do: {{ yc.admin_ghi_chu }}
                                </div>
                            </li>
                            <li v-if="yeuCauRutTien.length === 0" class="p-8 text-center text-gray-500">
                                Chưa có yêu cầu nào
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
