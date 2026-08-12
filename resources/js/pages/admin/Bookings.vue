<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from '@/lib/debounce';
import {
    CalendarDays, Search, CheckCircle, XCircle, AlertCircle, ClipboardCheck, ChevronRight, Sparkles
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import AdminLayout from '@/layouts/AdminLayout.vue';

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
        cho_xac_nhan: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        da_xac_nhan: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        dang_thuc_hien: 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
        hoan_thanh: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        da_huy: 'bg-red-50 text-red-700 ring-1 ring-red-200',
    };
    return map[status] || 'bg-stone-50 text-stone-600 ring-1 ring-stone-200';
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

// Animations
const { animateFadeUp } = useAnimations();
animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Quản lý Booking - Admin" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="animate-fade-up overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-stone-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700">Quản lý</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-stone-950">Đơn hàng (Booking)</h1>
                        <p class="mt-1 text-sm text-stone-500">Giám sát và can thiệp đơn đặt lịch trên hệ thống.</p>
                    </div>
                    <div class="relative">
                        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm mã đơn, tên..."
                            class="w-60 rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition-all focus:border-sky-400 focus:bg-white focus:ring-2 focus:ring-sky-100"
                        />
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="flex flex-wrap items-center gap-2 border-b border-stone-100 bg-stone-50/70 px-6 py-3">
                    <button
                        v-for="opt in statusOptions"
                        :key="opt.key"
                        @click="filterByStatus(opt.key)"
                        :class="[
                            'whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition',
                            currentStatus === opt.key
                                ? 'border-sky-500 bg-sky-50 text-sky-700 shadow-sm'
                                : 'border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-800'
                        ]"
                    >
                        {{ opt.label }}
                        <span
                            :class="[
                                'ml-1.5 rounded-full px-2 py-0.5 text-[10px]',
                                currentStatus === opt.key ? 'bg-sky-100 text-sky-600' : 'bg-stone-100 text-stone-500'
                            ]"
                        >
                            {{ statusCounts[opt.key] || 0 }}
                        </span>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Mã đơn</th>
                                <th class="px-6 py-3.5">Khách hàng</th>
                                <th class="px-6 py-3.5">Nhà cung cấp</th>
                                <th class="px-6 py-3.5">Dịch vụ</th>
                                <th class="px-6 py-3.5">Thời gian</th>
                                <th class="px-6 py-3.5 text-right">Tổng tiền</th>
                                <th class="px-6 py-3.5">Trạng thái</th>
                                <th class="px-6 py-3.5 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="b in bookings.data" :key="b.id" class="border-b border-stone-50 transition-colors hover:bg-stone-50/80">
                                <td class="px-6 py-4 font-mono text-sky-600 font-medium">{{ b.ma_don }}</td>
                                <td class="px-6 py-4 text-stone-700">{{ b.khach_hang }}</td>
                                <td class="px-6 py-4 text-stone-600">{{ b.nha_cung_cap }}</td>
                                <td class="px-6 py-4 font-medium text-stone-950">{{ b.dich_vu }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-stone-500">
                                    <div class="flex items-center gap-2">
                                        <CalendarDays class="size-4 text-stone-400" />
                                        {{ b.thoi_gian_thuc_hien }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-stone-800">{{ b.tong_tien.toLocaleString('vi-VN') }}đ</td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', statusColor(b.trang_thai)]">
                                        {{ statusLabel(b.trang_thai) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button v-if="b.trang_thai === 'cho_xac_nhan'" @click="confirmAction(b, 'confirm')" title="Ép xác nhận" class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-blue-50 hover:text-blue-600">
                                            <CheckCircle class="size-4" />
                                        </button>
                                        <button v-if="b.trang_thai === 'da_xac_nhan'" @click="confirmAction(b, 'complete')" title="Ép hoàn thành" class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-emerald-50 hover:text-emerald-600">
                                            <ClipboardCheck class="size-4" />
                                        </button>
                                        <button v-if="['cho_xac_nhan', 'da_xac_nhan'].includes(b.trang_thai)" @click="confirmAction(b, 'reject')" title="Hủy đơn" class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-red-50 hover:text-red-600">
                                            <XCircle class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="bookings.data.length === 0">
                                <td colspan="8" class="px-6 py-16 text-center text-sm text-stone-400">Không tìm thấy đơn hàng nào.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.last_page > 1" class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                    <p class="text-xs text-stone-500">
                        Hiển thị {{ bookings.from }} — {{ bookings.to }} / {{ bookings.total }} kết quả
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-if="bookings.prev_page_url"
                            :href="bookings.prev_page_url"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100"
                            preserve-state
                        >Trước</Link>
                        <Link
                            v-if="bookings.next_page_url"
                            :href="bookings.next_page_url"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100"
                            preserve-state
                        >Tiếp</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div class="mx-4 w-full max-w-md rounded-[2rem] bg-white p-6 shadow-xl">
                    <div class="mb-4 flex items-center gap-3">
                        <div :class="[
                            'rounded-full p-2.5',
                            modalAction === 'confirm' ? 'bg-blue-50 text-blue-600' :
                            modalAction === 'complete' ? 'bg-emerald-50 text-emerald-600' :
                            'bg-red-50 text-red-600'
                        ]">
                            <AlertCircle class="size-5" />
                        </div>
                        <h3 class="text-lg font-bold text-stone-950">
                            <span v-if="modalAction === 'confirm'">Ép xác nhận đơn hàng?</span>
                            <span v-if="modalAction === 'complete'">Ép hoàn thành đơn hàng?</span>
                            <span v-if="modalAction === 'reject'">Hủy bỏ đơn hàng?</span>
                        </h3>
                    </div>
                    <p class="text-sm text-stone-600">
                        Bạn đang can thiệp vào đơn hàng <span class="font-mono font-semibold text-stone-900">{{ selectedBooking?.ma_don }}</span> của khách hàng <span class="font-semibold text-stone-900">{{ selectedBooking?.khach_hang }}</span>.
                        Hành động này sẽ được ghi nhận lại và thông báo cho cả 2 bên.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-50">Hủy bỏ</button>
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
        </Teleport>
    </template>

<style scoped>
.animate-fade-up {
    opacity: 0;
    transform: translateY(30px);
}
@media (prefers-reduced-motion: reduce) {
    .animate-fade-up {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
}
</style>
