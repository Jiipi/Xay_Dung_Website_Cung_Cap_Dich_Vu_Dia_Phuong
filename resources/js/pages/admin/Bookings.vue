<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    CalendarDays, Search, CheckCircle, XCircle, AlertCircle, Eye, ClipboardCheck, ChevronLeft, ChevronRight, X
} from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import debounce from 'lodash/debounce';

const props = defineProps<{
    bookings: any;
    statusCounts: Record<string, number>;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search ?? '');
const currentStatus = ref(props.filters.status ?? 'all');

const doSearch = debounce(() => {
    router.get('/admin/bookings', {
        search: search.value,
        status: currentStatus.value,
    }, { preserveState: true, replace: true });
}, 500);

watch(search, doSearch);

function filterByStatus(status: string) {
    currentStatus.value = status;
    router.get('/admin/bookings', {
        search: search.value,
        status: status,
    }, { preserveState: true, replace: true });
}

const statusOptions = [
    { key: 'all', label: 'Tất cả' },
    { key: 'cho_xac_nhan', label: 'Chờ xác nhận' },
    { key: 'da_xac_nhan', label: 'Đã xác nhận' },
    { key: 'dang_thuc_hien', label: 'Đang thực hiện' },
    { key: 'hoan_thanh', label: 'Hoàn thành' },
    { key: 'da_huy', label: 'Đã hủy' },
];

function statusColor(status: string) {
    const map: Record<string, string> = {
        cho_xac_nhan: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        da_xac_nhan: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        dang_thuc_hien: 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
        hoan_thanh: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        da_huy: 'bg-red-500/10 text-red-400 border-red-500/20',
    };
    return map[status] || 'bg-slate-500/10 text-slate-400 border-slate-500/20';
}

function statusLabel(status: string) {
    return statusOptions.find(o => o.key === status)?.label || status;
}

// Action Modals
const showModal = ref(false);
const modalAction = ref('');
const selectedBooking = ref<any>(null);

function confirmAction(booking: any, action: string) {
    selectedBooking.value = booking;
    modalAction.value = action;
    showModal.value = true;
}

function executeAction() {
    if (!selectedBooking.value) return;

    let route = '';
    if (modalAction.value === 'confirm') route = `/admin/bookings/${selectedBooking.value.id}/force-confirm`;
    else if (modalAction.value === 'complete') route = `/admin/bookings/${selectedBooking.value.id}/force-complete`;
    else if (modalAction.value === 'reject') route = `/admin/bookings/${selectedBooking.value.id}/force-reject`;

    if (route) {
        router.post(route, {}, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                selectedBooking.value = null;
            }
        });
    }
}
</script>

<template>
    <Head title="Quản lý Booking - Admin" />

    <AdminLayout activePage="bookings">
        <div class="mx-auto max-w-7xl p-4 lg:p-8">
            <div class="mb-8 sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Quản lý Booking</h1>
                    <p class="mt-2 text-sm text-slate-400">Giám sát và can thiệp đơn đặt lịch trên hệ thống.</p>
                </div>
            </div>

            <!-- Stats/Filters -->
            <div class="mb-6 grid gap-4 lg:grid-cols-4">
                <div class="col-span-1 lg:col-span-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/50 p-1 flex items-center gap-1">
                    <button
                        v-for="opt in statusOptions"
                        :key="opt.key"
                        @click="filterByStatus(opt.key)"
                        :class="[
                            'whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition',
                            currentStatus === opt.key
                                ? 'bg-sky-500 text-white shadow-sm'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
                        ]"
                    >
                        {{ opt.label }}
                        <span
                            :class="[
                                'ml-2 rounded-full px-2 py-0.5 text-xs',
                                currentStatus === opt.key ? 'bg-sky-400/20 text-sky-100' : 'bg-slate-800 text-slate-500'
                            ]"
                        >
                            {{ statusCounts[opt.key] || 0 }}
                        </span>
                    </button>
                </div>

                <div class="relative col-span-1">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-500" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm mã đơn, tên..."
                        class="w-full rounded-xl border border-slate-800 bg-slate-900/50 py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder:text-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="border-b border-slate-800 bg-slate-800/50 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Mã đơn</th>
                                <th class="px-6 py-4">Khách hàng</th>
                                <th class="px-6 py-4">Nhà cung cấp</th>
                                <th class="px-6 py-4">Dịch vụ</th>
                                <th class="px-6 py-4">Thời gian</th>
                                <th class="px-6 py-4 text-right">Tổng tiền</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <tr v-for="b in bookings.data" :key="b.id" class="transition hover:bg-slate-800/30">
                                <td class="px-6 py-4 font-mono text-sky-400 font-medium">{{ b.ma_don }}</td>
                                <td class="px-6 py-4">{{ b.khach_hang }}</td>
                                <td class="px-6 py-4">{{ b.nha_cung_cap }}</td>
                                <td class="px-6 py-4 text-slate-200">{{ b.dich_vu }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <CalendarDays class="size-4 text-slate-500" />
                                        {{ b.thoi_gian_thuc_hien }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-medium">{{ b.tong_tien.toLocaleString('vi-VN') }}đ</td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium', statusColor(b.trang_thai)]">
                                        {{ statusLabel(b.trang_thai) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Actions depend on status -->
                                        <button v-if="b.trang_thai === 'cho_xac_nhan'" @click="confirmAction(b, 'confirm')" title="Ép xác nhận" class="rounded p-1.5 text-slate-400 transition hover:bg-blue-500/20 hover:text-blue-400">
                                            <CheckCircle class="size-4" />
                                        </button>
                                        <button v-if="b.trang_thai === 'da_xac_nhan'" @click="confirmAction(b, 'complete')" title="Ép hoàn thành" class="rounded p-1.5 text-slate-400 transition hover:bg-emerald-500/20 hover:text-emerald-400">
                                            <ClipboardCheck class="size-4" />
                                        </button>
                                        <button v-if="['cho_xac_nhan', 'da_xac_nhan'].includes(b.trang_thai)" @click="confirmAction(b, 'reject')" title="Hủy đơn" class="rounded p-1.5 text-slate-400 transition hover:bg-red-500/20 hover:text-red-400">
                                            <XCircle class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="bookings.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    Không tìm thấy đơn hàng nào.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.last_page > 1" class="border-t border-slate-800 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-400">
                            Hiển thị <span class="font-medium text-slate-200">{{ bookings.from }}</span> đến <span class="font-medium text-slate-200">{{ bookings.to }}</span> trong tổng số <span class="font-medium text-slate-200">{{ bookings.total }}</span> kết quả
                        </p>
                        <div class="flex items-center gap-2">
                            <Link
                                v-if="bookings.prev_page_url"
                                :href="bookings.prev_page_url"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-slate-700"
                            >
                                <ChevronLeft class="size-4" />
                            </Link>
                            <span v-else class="inline-flex items-center justify-center rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-medium text-slate-600 opacity-50">
                                <ChevronLeft class="size-4" />
                            </span>

                            <Link
                                v-if="bookings.next_page_url"
                                :href="bookings.next_page_url"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-slate-700"
                            >
                                <ChevronRight class="size-4" />
                            </Link>
                            <span v-else class="inline-flex items-center justify-center rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-medium text-slate-600 opacity-50">
                                <ChevronRight class="size-4" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
                    <div class="flex items-start gap-4">
                        <div :class="[
                            'flex size-10 shrink-0 items-center justify-center rounded-full',
                            modalAction === 'confirm' ? 'bg-blue-500/20 text-blue-400' :
                            modalAction === 'complete' ? 'bg-emerald-500/20 text-emerald-400' :
                            'bg-red-500/20 text-red-400'
                        ]">
                            <AlertCircle class="size-5" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-white">
                                <span v-if="modalAction === 'confirm'">Ép xác nhận đơn hàng?</span>
                                <span v-if="modalAction === 'complete'">Ép hoàn thành đơn hàng?</span>
                                <span v-if="modalAction === 'reject'">Hủy bỏ đơn hàng?</span>
                            </h3>
                            <p class="mt-2 text-sm text-slate-400">
                                Bạn đang can thiệp vào đơn hàng <span class="font-mono text-slate-200">{{ selectedBooking?.ma_don }}</span> của khách hàng <span class="text-slate-200">{{ selectedBooking?.khach_hang }}</span>.
                                Hành động này sẽ được ghi nhận lại và thông báo cho cả 2 bên.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">Hủy bỏ</button>
                        <button
                            @click="executeAction"
                            :class="[
                                'rounded-xl px-4 py-2.5 text-sm font-medium text-white transition',
                                modalAction === 'confirm' ? 'bg-blue-500 hover:bg-blue-600' :
                                modalAction === 'complete' ? 'bg-emerald-500 hover:bg-emerald-600' :
                                'bg-red-500 hover:bg-red-600'
                            ]"
                        >
                            Tiến hành
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>
