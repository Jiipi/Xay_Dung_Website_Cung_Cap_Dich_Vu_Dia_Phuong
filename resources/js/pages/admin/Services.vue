<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle2, Eye, Search, Sparkles, X, XCircle } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface ServiceItem {
    id: number;
    ten_dich_vu: string;
    hinh_anh: string | null;
    gia_tien: number;
    don_vi: string;
    nha_cung_cap: string;
    danh_muc: string;
    trang_thai_duyet: string;
    trang_thai: string;
    ngay_tao: string;
}

interface PaginatedServices {
    data: ServiceItem[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = withDefaults(defineProps<{
    services: PaginatedServices;
    statusCounts: Record<string, number>;
    filters: { search?: string; status?: string };
}>(), {
    services: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }),
    statusCounts: () => ({ all: 0, cho_duyet: 0, da_duyet: 0, tu_choi: 0 }),
    filters: () => ({}),
});

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? 'all');
const pendingService = ref<ServiceItem | null>(null);
const pendingAction = ref<'approve' | 'reject' | null>(null);

const statusTabs = [
    { key: 'all', label: 'Tat ca' },
    { key: 'cho_duyet', label: 'Cho duyet' },
    { key: 'da_duyet', label: 'Da duyet' },
    { key: 'tu_choi', label: 'Tu choi' },
];

const modalTitle = computed(() => pendingAction.value === 'approve' ? 'Duyet dich vu' : 'Tu choi dich vu');

const moderationNote = computed(() => {
    if (!pendingService.value || !pendingAction.value) return '';

    return pendingAction.value === 'approve'
        ? `Sau khi duyet, dich vu ${pendingService.value.ten_dich_vu} se san sang hien thi cho khach hang.`
        : `Sau khi tu choi, dich vu ${pendingService.value.ten_dich_vu} se quay lai hang doi va nha cung cap can cap nhat them.`;
});

function applyFilters(status?: string) {
    if (status) statusFilter.value = status;
    router.get('/admin/services', {
        search: search.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
}

function openModerationModal(service: ServiceItem, action: 'approve' | 'reject') {
    pendingService.value = service;
    pendingAction.value = action;
}

function closeModerationModal() {
    pendingService.value = null;
    pendingAction.value = null;
}

function submitModeration() {
    if (!pendingService.value || !pendingAction.value) return;

    const route = pendingAction.value === 'approve'
        ? `/admin/services/${pendingService.value.id}/approve`
        : `/admin/services/${pendingService.value.id}/reject`;

    router.post(route, {}, {
        preserveScroll: true,
        onFinish: () => {
            pendingService.value = null;
            pendingAction.value = null;
        },
    });
}

const approvalLabels: Record<string, string> = {
    cho_duyet: 'Cho duyet',
    da_duyet: 'Da duyet',
    tu_choi: 'Tu choi',
};

const approvalColors: Record<string, string> = {
    cho_duyet: 'text-amber-600 bg-amber-50',
    da_duyet: 'text-emerald-600 bg-emerald-50',
    tu_choi: 'text-red-600 bg-red-50',
};

const activityLabels: Record<string, string> = {
    dang_hoat_dong: 'Dang hien thi',
    tam_ngung: 'Tam ngung',
    an: 'Dang an',
};

const formatVND = (v: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);
</script>

<template>
    <Head title="Duyet dich vu" />

    <AdminLayout activePage="services">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
            <div class="admin-page-header">
                <div class="admin-page-header__accent" />
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <Sparkles class="size-3.5 text-blue-500" />
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-500/70">Phe duyet</span>
                    </div>
                    <h1 class="text-2xl font-bold text-stone-800">Duyet dich vu</h1>
                    <p class="mt-1 text-sm text-stone-500">Bo sung buoc xem nhanh va xac nhan de admin co them ngu canh truoc khi ra quyet dinh.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-stone-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Hang doi</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">{{ statusCounts.cho_duyet ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Dich vu dang can admin xem va phan loai.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Da duyet</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">{{ statusCounts.da_duyet ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Theo doi de dam bao chat luong va tinh san sang.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Tu choi</p>
                    <p class="mt-2 text-2xl font-semibold text-stone-900">{{ statusCounts.tu_choi ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Nhom nay nen duoc xem lai khi provider cap nhat noi dung.</p>
                </div>
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.key"
                    class="admin-tab"
                    :class="statusFilter === tab.key ? 'admin-tab--active' : ''"
                    @click="applyFilters(tab.key)"
                >
                    {{ tab.label }}
                    <span
                        class="ml-1.5 rounded-full px-2 py-0.5 text-[10px]"
                        :class="statusFilter === tab.key ? 'bg-blue-100 text-blue-600' : 'bg-stone-100 text-stone-500'"
                    >
                        {{ statusCounts[tab.key] ?? 0 }}
                    </span>
                </button>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Xem nhanh</p>
                        <p class="mt-1 text-sm text-stone-600">Ngay trong modal da co ten dich vu, nha cung cap, gia, danh muc va trang thai hien tai.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Quyet dinh ro rang</p>
                        <p class="mt-1 text-sm text-stone-600">Nut duyet va tu choi khong chay ngay, giup giam sai sot khi xu ly hang doi lon.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Chuyen tiep</p>
                        <p class="mt-1 text-sm text-stone-600">Neu can xem day du hon, admin van mo nhanh trang dich vu cong khai o tab moi.</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <Search class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Tim dich vu can xu ly..."
                    class="admin-input pl-10"
                    @keyup.enter="applyFilters()"
                />
            </div>

            <div class="admin-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[960px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-xs font-medium uppercase tracking-wider text-stone-500">
                                <th class="px-6 py-3">Dich vu</th>
                                <th class="px-6 py-3">Nha cung cap</th>
                                <th class="px-6 py-3">Danh muc</th>
                                <th class="px-6 py-3">Gia</th>
                                <th class="px-6 py-3">Trang thai duyet</th>
                                <th class="px-6 py-3">Trang thai hien thi</th>
                                <th class="px-6 py-3">Ngay tao</th>
                                <th class="px-6 py-3 text-right">Hanh dong</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sv in services.data" :key="sv.id" class="admin-table-row">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 overflow-hidden rounded-lg bg-stone-100 ring-1 ring-stone-200">
                                            <img v-if="sv.hinh_anh" :src="sv.hinh_anh" alt="" class="size-full object-cover" />
                                        </div>
                                        <div>
                                            <span class="block max-w-[240px] truncate font-medium text-stone-700">{{ sv.ten_dich_vu }}</span>
                                            <span class="text-xs text-stone-400">ID #{{ sv.id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-stone-500">{{ sv.nha_cung_cap }}</td>
                                <td class="px-6 py-3 text-stone-400">{{ sv.danh_muc }}</td>
                                <td class="whitespace-nowrap px-6 py-3 font-semibold text-stone-800">{{ formatVND(sv.gia_tien) }}</td>
                                <td class="px-6 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="approvalColors[sv.trang_thai_duyet] ?? 'bg-stone-100 text-stone-500'"
                                    >
                                        {{ approvalLabels[sv.trang_thai_duyet] ?? sv.trang_thai_duyet }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-stone-500">{{ activityLabels[sv.trang_thai] ?? sv.trang_thai }}</td>
                                <td class="px-6 py-3 text-stone-400">{{ sv.ngay_tao }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/services/${sv.id}`"
                                            target="_blank"
                                            class="admin-action-btn bg-stone-100 text-stone-600 hover:bg-stone-200"
                                        >
                                            <Eye class="size-3.5" />
                                            <span class="hidden xl:inline">Xem</span>
                                        </Link>
                                        <button
                                            v-if="sv.trang_thai_duyet !== 'da_duyet'"
                                            class="admin-action-btn bg-emerald-50 text-emerald-600 hover:bg-emerald-100"
                                            @click="openModerationModal(sv, 'approve')"
                                        >
                                            <CheckCircle2 class="size-3.5" />
                                            <span>Duyet</span>
                                        </button>
                                        <button
                                            v-if="sv.trang_thai_duyet !== 'tu_choi'"
                                            class="admin-action-btn bg-red-50 text-red-600 hover:bg-red-100"
                                            @click="openModerationModal(sv, 'reject')"
                                        >
                                            <XCircle class="size-3.5" />
                                            <span>Tu choi</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="services.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-stone-400">Khong co dich vu nao trong bo loc hien tai</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="services.last_page > 1" class="flex items-center justify-between border-t border-stone-200 px-6 py-3">
                    <p class="text-xs text-stone-500">Trang {{ services.current_page }} / {{ services.last_page }}</p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in services.links"
                            :key="link.label"
                            :href="link.url ?? ''"
                            class="admin-pagination-btn"
                            :class="link.active ? 'bg-blue-500 text-white' : link.url ? 'bg-stone-100 text-stone-600 hover:bg-stone-200' : 'cursor-not-allowed bg-stone-50 text-stone-300'"
                            v-html="link.label"
                            :preserve-scroll="true"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="pendingService && pendingAction" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/50 px-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Xem nhanh truoc khi xu ly</p>
                        <h2 class="mt-2 text-xl font-semibold text-stone-900">{{ modalTitle }}</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-500">{{ moderationNote }}</p>
                    </div>
                    <button class="rounded-full p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600" @click="closeModerationModal">
                        <X class="size-4" />
                    </button>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-[minmax(0,1fr),280px]">
                    <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4">
                        <div class="flex items-start gap-4">
                            <div class="size-16 overflow-hidden rounded-2xl bg-stone-100 ring-1 ring-stone-200">
                                <img v-if="pendingService.hinh_anh" :src="pendingService.hinh_anh" alt="" class="size-full object-cover" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-lg font-semibold text-stone-900">{{ pendingService.ten_dich_vu }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ pendingService.nha_cung_cap }}</p>
                                <p class="mt-3 text-sm font-medium text-stone-700">{{ formatVND(pendingService.gia_tien) }} / {{ pendingService.don_vi }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Danh muc</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingService.danh_muc }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Ngay tao</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingService.ngay_tao }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trang thai duyet</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ approvalLabels[pendingService.trang_thai_duyet] ?? pendingService.trang_thai_duyet }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trang thai hien thi</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ activityLabels[pendingService.trang_thai] ?? pendingService.trang_thai }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Checklist nhanh</p>
                        <ul class="mt-3 space-y-3 text-sm text-stone-600">
                            <li class="rounded-xl bg-stone-50 px-3 py-2">Ten dich vu ro rang va phu hop voi danh muc.</li>
                            <li class="rounded-xl bg-stone-50 px-3 py-2">Gia va don vi nhat quan de khach hang de so sanh.</li>
                            <li class="rounded-xl bg-stone-50 px-3 py-2">Trang thai hien thi hop voi quyet dinh duyet hien tai.</li>
                        </ul>

                        <Link
                            :href="`/services/${pendingService.id}`"
                            target="_blank"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                        >
                            Mo trang chi tiet
                        </Link>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50" @click="closeModerationModal">
                        Quay lai
                    </button>
                    <button
                        class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                        :class="pendingAction === 'approve' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600'"
                        @click="submitModeration"
                    >
                        {{ pendingAction === 'approve' ? 'Xac nhan duyet' : 'Xac nhan tu choi' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-page-header {
    position: relative;
    padding: 0.5rem 0 0.5rem 1rem;
}

.admin-page-header__accent {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    border-radius: 3px;
    background: linear-gradient(to bottom, #3b82f6, #6366f1);
    box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
}

.admin-tab {
    white-space: nowrap;
    border-radius: 0.75rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #78716c;
    transition: all 0.2s ease;
}

.admin-tab:hover {
    border-color: #d6d3d1;
    color: #1c1917;
}

.admin-tab--active {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.04);
    color: #3b82f6;
    font-weight: 600;
}

.admin-card {
    overflow: hidden;
    border-radius: 1.5rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.admin-input {
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    color: #1c1917;
    outline: none;
}

.admin-input::placeholder {
    color: #a8a29e;
}

.admin-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.admin-table-row {
    border-bottom: 1px solid #f5f5f4;
    transition: background 0.2s ease;
}

.admin-table-row:hover {
    background: #fafaf9;
}

.admin-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-radius: 0.5rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.admin-pagination-btn {
    border-radius: 0.5rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    transition: all 0.2s ease;
}
</style>
