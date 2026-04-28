<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    Check,
    CheckCircle2,
    Clock,
    Filter,
    MapPin,
    MessageSquare,
    Phone,
    Search,
    User,
    X as XIcon,
    XCircle,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface Booking {
    id: number;
    ma_don: string;
    khach_hang: string;
    so_dien_thoai: string | null;
    dich_vu: string;
    ngay_dat: string;
    thoi_gian_thuc_hien: string | null;
    dia_diem: string | null;
    so_luong: number;
    don_vi: string | null;
    ghi_chu: string | null;
    tong_tien: number;
    trang_thai: string;
    trang_thai_thanh_toan: string;
}

interface PaginatedBookings {
    data: Booking[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = withDefaults(defineProps<{
    bookings: PaginatedBookings;
    statusCounts: Record<string, number>;
    filters: { trang_thai?: string; search?: string };
}>(), {
    bookings: () => ({ data: [], current_page: 1, last_page: 1, per_page: 10, total: 0, links: [] }),
    statusCounts: () => ({}),
    filters: () => ({}),
});

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
    error: page.props.flash?.error as string | undefined,
}));

const activeFilter = ref(props.filters?.trang_thai ?? 'all');
const searchQuery = ref(props.filters?.search ?? '');
const showRejectModal = ref(false);
const rejectBookingId = ref<number | null>(null);
const rejectReason = ref('');
const rejectReasonSuggestions = [
    'Khung gio nay da kin lich.',
    'Thong tin dat lich chua day du.',
    'Khu vuc nay hien tai chua ho tro.',
];

const filters = [
    { key: 'all', label: 'Tat ca' },
    { key: 'cho_xac_nhan', label: 'Cho xac nhan' },
    { key: 'da_xac_nhan', label: 'Da xac nhan' },
    { key: 'hoan_thanh', label: 'Hoan thanh' },
    { key: 'da_huy', label: 'Da huy' },
];

const statusStyles: Record<string, string> = {
    cho_xac_nhan: 'bg-amber-50 text-amber-700 ring-amber-200',
    da_xac_nhan: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    dang_thuc_hien: 'bg-brand-surface text-brand ring-brand',
    hoan_thanh: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    da_huy: 'bg-red-50 text-red-700 ring-red-200',
};

const statusLabels: Record<string, string> = {
    cho_xac_nhan: 'Cho xac nhan',
    da_xac_nhan: 'Da xac nhan',
    dang_thuc_hien: 'Dang thuc hien',
    hoan_thanh: 'Hoan thanh',
    da_huy: 'Da huy',
};

const formatVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

function submitFilters(status = activeFilter.value) {
    activeFilter.value = status;
    router.get('/provider/bookings', {
        trang_thai: status === 'all' ? undefined : status,
        search: searchQuery.value || undefined,
    }, { preserveState: true, replace: true });
}

function confirmBooking(id: number) {
    router.post(`/provider/bookings/${id}/confirm`);
}

function openRejectModal(id: number) {
    rejectBookingId.value = id;
    rejectReason.value = '';
    showRejectModal.value = true;
}

function executeReject() {
    if (!rejectBookingId.value) return;
    router.post(`/provider/bookings/${rejectBookingId.value}/reject`, {
        ly_do: rejectReason.value,
    }, {
        onFinish: () => {
            showRejectModal.value = false;
            rejectBookingId.value = null;
        },
    });
}

function completeBooking(id: number) {
    router.post(`/provider/bookings/${id}/complete`);
}

const totalAll = computed(() => Object.values(props.statusCounts).reduce((a, b) => a + b, 0));

function getCount(key: string): number {
    if (key === 'all') return totalAll.value;
    return props.statusCounts[key] ?? 0;
}
</script>

<template>
    <Head title="Quan ly Booking" />

    <ProviderLayout activePage="bookings">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div v-if="flash.success" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <CheckCircle2 class="size-5 shrink-0" /> {{ flash.success }}
                </div>
                <div v-if="flash.error" class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <XCircle class="size-5 shrink-0" /> {{ flash.error }}
                </div>

                <div>
                    <h2 class="text-xl font-bold text-stone-950">Quan ly booking</h2>
                    <p class="mt-1 text-sm text-stone-500">{{ totalAll }} booking tong cong</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Cho xac nhan</p>
                        <p class="mt-2 text-2xl font-black text-stone-950">{{ getCount('cho_xac_nhan') }}</p>
                        <p class="mt-1 text-xs text-amber-700">Can xu ly som</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Da xac nhan</p>
                        <p class="mt-2 text-2xl font-black text-stone-950">{{ getCount('da_xac_nhan') }}</p>
                        <p class="mt-1 text-xs text-emerald-700">Sap den lich thuc hien</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Hoan thanh</p>
                        <p class="mt-2 text-2xl font-black text-stone-950">{{ getCount('hoan_thanh') }}</p>
                        <p class="mt-1 text-xs text-stone-500">Da phuc vu xong</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Da huy</p>
                        <p class="mt-2 text-2xl font-black text-stone-950">{{ getCount('da_huy') }}</p>
                        <p class="mt-1 text-xs text-red-600">Can theo doi ly do</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex items-center gap-3 overflow-x-auto pb-2 xl:pb-0">
                            <Filter class="size-5 shrink-0 text-stone-400" />
                            <button
                                v-for="f in filters"
                                :key="f.key"
                                class="flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition-colors"
                                :class="activeFilter === f.key ? 'bg-orange-600 text-white shadow-sm' : 'border border-stone-200 bg-white text-stone-600 hover:bg-stone-50'"
                                @click="submitFilters(f.key)"
                            >
                                {{ f.label }}
                                <span
                                    class="rounded-full px-1.5 py-0.5 text-[10px] font-bold"
                                    :class="activeFilter === f.key ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-500'"
                                >
                                    {{ getCount(f.key) }}
                                </span>
                            </button>
                        </div>
                        <div class="relative w-full xl:w-80">
                            <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tim ma don, khach hang, dich vu..."
                                class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-3 text-sm text-stone-700 outline-none transition focus:border-orange-400 focus:bg-white focus:ring-2 focus:ring-orange-100"
                                @keyup.enter="submitFilters()"
                            />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="b in bookings.data"
                        :key="b.id"
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition-all hover:shadow-md"
                    >
                        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex-1">
                                <div class="mb-3 flex flex-wrap items-center gap-3">
                                    <span class="font-mono text-xs text-stone-400">{{ b.ma_don }}</span>
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                        :class="statusStyles[b.trang_thai] ?? 'bg-stone-50 text-stone-600 ring-stone-200'"
                                    >
                                        {{ statusLabels[b.trang_thai] ?? b.trang_thai }}
                                    </span>
                                </div>
                                <h3 class="mb-1 text-lg font-bold text-stone-950">{{ b.dich_vu }}</h3>
                                <p class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-stone-500">
                                    <span class="flex items-center gap-1"><User class="size-3.5" /> {{ b.khach_hang }}</span>
                                    <span v-if="b.so_dien_thoai" class="flex items-center gap-1"><Phone class="size-3.5" /> {{ b.so_dien_thoai }}</span>
                                </p>
                                <div class="flex flex-wrap gap-4 text-sm text-stone-600">
                                    <span class="flex items-center gap-1.5"><Calendar class="size-4 text-stone-400" /> {{ b.ngay_dat }}</span>
                                    <span v-if="b.thoi_gian_thuc_hien" class="flex items-center gap-1.5"><Clock class="size-4 text-stone-400" /> {{ b.thoi_gian_thuc_hien }}</span>
                                    <span v-if="b.dia_diem" class="flex items-center gap-1.5"><MapPin class="size-4 text-stone-400" /> {{ b.dia_diem }}</span>
                                </div>
                                <p v-if="b.ghi_chu" class="mt-2 flex items-start gap-1.5 text-sm text-stone-500">
                                    <MessageSquare class="mt-0.5 size-3.5 shrink-0 text-stone-400" />
                                    <span class="line-clamp-2">{{ b.ghi_chu }}</span>
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-3">
                                <span class="text-xl font-bold text-stone-950">{{ formatVND(b.tong_tien) }}</span>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <template v-if="b.trang_thai === 'cho_xac_nhan'">
                                        <button
                                            class="flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                                            @click="confirmBooking(b.id)"
                                        >
                                            <Check class="size-4" /> Xac nhan
                                        </button>
                                        <button
                                            class="flex items-center gap-1.5 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                                            @click="openRejectModal(b.id)"
                                        >
                                            <XIcon class="size-4" /> Tu choi
                                        </button>
                                    </template>
                                    <button
                                        v-if="b.trang_thai === 'da_xac_nhan'"
                                        class="flex items-center gap-1.5 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-orange-700"
                                        @click="completeBooking(b.id)"
                                    >
                                        <Check class="size-4" /> Hoan thanh
                                    </button>
                                    <Link
                                        :href="`/provider/bookings/${b.id}`"
                                        class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-50"
                                    >
                                        Chi tiet
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="bookings.data.length === 0" class="flex flex-col items-center rounded-2xl border border-stone-200 bg-white py-16 shadow-sm">
                    <Calendar class="mb-3 size-10 text-stone-300" />
                    <p class="text-sm font-medium text-stone-600">Khong co booking nao</p>
                    <p class="mt-1 text-xs text-stone-400">Thu doi bo loc hoac tim kiem khac.</p>
                </div>

                <div v-if="bookings.last_page > 1" class="flex items-center justify-center gap-1">
                    <template v-for="link in bookings.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                            :class="link.active ? 'bg-orange-600 text-white' : 'text-stone-600 hover:bg-stone-100'"
                            v-html="link.label"
                            preserve-state
                        />
                        <span v-else class="rounded-lg px-3 py-1.5 text-xs text-stone-300" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-stone-950">Tu choi booking</h3>
                    <p class="mt-1 text-sm text-stone-500">Them ly do ro rang de khach hang de xu ly tiep.</p>
                    <textarea
                        v-model="rejectReason"
                        rows="3"
                        placeholder="Ly do tu choi (khong bat buoc)..."
                        class="mt-4 w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                    />
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            v-for="reason in rejectReasonSuggestions"
                            :key="reason"
                            type="button"
                            class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50"
                            @click="rejectReason = reason"
                        >
                            {{ reason }}
                        </button>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <button class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50" @click="showRejectModal = false">Huy</button>
                        <button class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700" @click="executeReject">Xac nhan tu choi</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </ProviderLayout>
</template>
