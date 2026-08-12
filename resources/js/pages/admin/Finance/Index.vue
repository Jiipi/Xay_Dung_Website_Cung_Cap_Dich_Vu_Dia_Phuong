<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, useForm, router } from '@inertiajs/vue3';
import { Wallet, CheckCircle, XCircle, Clock, Banknote } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps<{
    requests: any;
    stats: {
        pending: number;
        approved: number;
        total_platform_fee: number;
    };
    filters: any;
}>();

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const filterStatus = ref(props.filters?.status || 'all');

const applyFilter = () => {
    router.get('/admin/finance', { status: filterStatus.value }, { preserveState: true });
};

const rejectForm = useForm({
    ly_do: '',
});
const selectedRequest = ref<number | null>(null);
const isRejecting = ref(false);

const approveRequest = (id: number) => {
    if (confirm('Xác nhận ĐÃ CHUYỂN KHOẢN cho yêu cầu rút tiền này?')) {
        router.post(`/admin/finance/${id}/approve`, {}, { preserveScroll: true });
    }
};

const openRejectModal = (id: number) => {
    selectedRequest.value = id;
    isRejecting.value = true;
};

const submitReject = () => {
    if (selectedRequest.value) {
        rejectForm.post(`/admin/finance/${selectedRequest.value}/reject`, {
            preserveScroll: true,
            onSuccess: () => {
                isRejecting.value = false;
                selectedRequest.value = null;
                rejectForm.reset('ly_do');
            }
        });
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
        case 'cho_xu_ly': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'da_duyet': return 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400';
        case 'tu_choi': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400';
        default: return 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>
    <Head title="Quản lý Tài chính" />

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-stone-950">Quản lý Tài chính</h2>
            <p class="mt-1 text-sm text-stone-500">Doanh thu nền tảng và yêu cầu rút tiền</p>
        </div>

        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mr-4">
                        <Banknote class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tổng Phí Nền Tảng</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(stats.total_platform_fee) }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center mr-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Yêu cầu rút tiền chờ duyệt</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pending }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mr-4">
                        <CheckCircle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Yêu cầu đã thanh toán</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.approved }}</p>
                    </div>
                </div>
            </div>

            <!-- Withdrawals List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Danh sách yêu cầu rút tiền</h3>

                    <select v-model="filterStatus" @change="applyFilter" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="cho_xu_ly">Chờ xử lý</option>
                        <option value="da_duyet">Đã duyệt (Đã chuyển khoản)</option>
                        <option value="tu_choi">Bị từ chối</option>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã / Ngày</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhà cung cấp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin Ngân hàng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="req in requests.data" :key="req.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ req.id }}</div>
                                    <div class="text-sm text-gray-500">{{ req.ngay_tao }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    {{ req.provider }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ formatCurrency(req.so_tien) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                    <template v-if="req.thong_tin_ngan_hang">
                                        <div class="font-medium">{{ req.thong_tin_ngan_hang.ten_ngan_hang }}</div>
                                        <div>{{ req.thong_tin_ngan_hang.stk_ngan_hang }}</div>
                                        <div class="text-xs text-gray-500 uppercase">{{ req.thong_tin_ngan_hang.ten_chu_tk }}</div>
                                    </template>
                                    <span v-else class="text-red-500 italic">Thiếu thông tin</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full flex items-center w-max" :class="getStatusColor(req.trang_thai)">
                                        <component :is="getStatusIcon(req.trang_thai)" class="w-3 h-3 mr-1" />
                                        {{ req.trang_thai }}
                                    </span>
                                    <div v-if="req.admin_ghi_chu" class="text-xs text-gray-500 mt-1 max-w-[150px] truncate" :title="req.admin_ghi_chu">
                                        Lý do: {{ req.admin_ghi_chu }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div v-if="req.trang_thai === 'cho_xu_ly'" class="flex justify-end space-x-2">
                                        <button @click="approveRequest(req.id)" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                            Duyệt (Đã CK)
                                        </button>
                                        <button @click="openRejectModal(req.id)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                            Từ chối
                                        </button>
                                    </div>
                                    <span v-else class="text-gray-400 text-xs italic">Không thể thao tác</span>
                                </td>
                            </tr>
                            <tr v-if="requests.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Không tìm thấy yêu cầu rút tiền nào.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reject Modal -->
            <div v-if="isRejecting" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Từ chối yêu cầu rút tiền</h3>
                    <form @submit.prevent="submitReject">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lý do từ chối (bắt buộc)</label>
                            <textarea
                                v-model="rejectForm.ly_do"
                                rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-red-500 focus:border-red-500"
                                placeholder="Nhập lý do để nhà cung cấp biết..."
                                required
                            ></textarea>
                            <div v-if="rejectForm.errors.ly_do" class="text-red-500 text-sm mt-1">{{ rejectForm.errors.ly_do }}</div>
                            <p class="text-xs text-gray-500 mt-2">Sau khi từ chối, số tiền sẽ được tự động cộng lại vào ví của nhà cung cấp.</p>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="isRejecting = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Hủy
                            </button>
                            <button type="submit" :disabled="rejectForm.processing" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                                Xác nhận từ chối
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</template>
