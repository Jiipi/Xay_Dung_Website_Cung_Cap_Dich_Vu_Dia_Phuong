<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle2, Eye, Search, X, XCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
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
    { key: 'all', label: 'Tất cả' },
    { key: 'cho_duyet', label: 'Chờ duyệt' },
    { key: 'da_duyet', label: 'Đã duyệt' },
    { key: 'tu_choi', label: 'Từ chối' },
];

const modalTitle = computed(() => pendingAction.value === 'approve' ? 'Duyệt dịch vụ' : 'Từ chối dịch vụ');

const moderationNote = computed(() => {
    if (!pendingService.value || !pendingAction.value) return '';

    return pendingAction.value === 'approve'
        ? `Sau khi duyệt, dịch vụ "${pendingService.value.ten_dich_vu}" sẽ sẵn sàng hiển thị cho khách hàng.`
        : `Sau khi từ chối, dịch vụ "${pendingService.value.ten_dich_vu}" sẽ quay lại hàng đợi và nhà cung cấp cần cập nhật thêm.`;
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
    cho_duyet: 'Chờ duyệt',
    da_duyet: 'Đã duyệt',
    tu_choi: 'Từ chối',
};

const approvalColors: Record<string, string> = {
    cho_duyet: 'text-amber-700 bg-amber-50 ring-1 ring-amber-200',
    da_duyet: 'text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200',
    tu_choi: 'text-red-700 bg-red-50 ring-1 ring-red-200',
};

const activityLabels: Record<string, string> = {
    hoat_dong: 'Đang hiển thị',
    dang_hoat_dong: 'Đang hiển thị',
    tam_ngung: 'Tạm ngưng',
    an: 'Đang ẩn',
};

const formatVND = (v: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

// Animations
const { animateFadeUp } = useAnimations();
animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Duyệt dịch vụ" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
            <!-- Stats -->
            <div class="animate-fade-up grid gap-4 md:grid-cols-3">
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-600">Hàng đợi</p>
                    <p class="mt-2 text-2xl font-bold text-stone-900">{{ statusCounts.cho_duyet ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Dịch vụ đang cần admin xem và phân loại.</p>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-600">Đã duyệt</p>
                    <p class="mt-2 text-2xl font-bold text-stone-900">{{ statusCounts.da_duyet ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Theo dõi để đảm bảo chất lượng và tính sẵn sàng.</p>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-red-600">Từ chối</p>
                    <p class="mt-2 text-2xl font-bold text-stone-900">{{ statusCounts.tu_choi ?? 0 }}</p>
                    <p class="mt-1 text-sm text-stone-500">Nhóm này nên được xem lại khi provider cập nhật nội dung.</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="animate-fade-up overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-stone-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700">Phê duyệt</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-stone-950">Duyệt dịch vụ</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ services.total }} dịch vụ</p>
                    </div>
                    <div class="relative">
                        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm dịch vụ cần xử lý..."
                            class="w-60 rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition-all focus:border-sky-400 focus:bg-white focus:ring-2 focus:ring-sky-100"
                            @keyup.enter="applyFilters()"
                        />
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="flex flex-wrap items-center gap-2 border-b border-stone-100 bg-stone-50/70 px-6 py-3">
                    <button
                        v-for="tab in statusTabs"
                        :key="tab.key"
                        :class="[
                            'whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition',
                            statusFilter === tab.key
                                ? 'border-sky-500 bg-sky-50 text-sky-700 shadow-sm'
                                : 'border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-800'
                        ]"
                        @click="applyFilters(tab.key)"
                    >
                        {{ tab.label }}
                        <span
                            class="ml-1.5 rounded-full px-2 py-0.5 text-[10px]"
                            :class="statusFilter === tab.key ? 'bg-sky-100 text-sky-600' : 'bg-stone-100 text-stone-500'"
                        >
                            {{ statusCounts[tab.key] ?? 0 }}
                        </span>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[960px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Dịch vụ</th>
                                <th class="px-6 py-3.5">Nhà cung cấp</th>
                                <th class="px-6 py-3.5">Danh mục</th>
                                <th class="px-6 py-3.5">Giá</th>
                                <th class="px-6 py-3.5">Trạng thái duyệt</th>
                                <th class="px-6 py-3.5">Trạng thái hiển thị</th>
                                <th class="px-6 py-3.5">Ngày tạo</th>
                                <th class="px-6 py-3.5 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sv in services.data" :key="sv.id" class="border-b border-stone-50 transition-colors hover:bg-stone-50/80">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 overflow-hidden rounded-lg bg-stone-100 ring-1 ring-stone-200">
                                            <img v-if="sv.hinh_anh" :src="sv.hinh_anh" alt="" class="size-full object-cover" />
                                        </div>
                                        <div>
                                            <span class="block max-w-[240px] truncate font-medium text-stone-950">{{ sv.ten_dich_vu }}</span>
                                            <span class="text-xs text-stone-400">ID #{{ sv.id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-stone-600">{{ sv.nha_cung_cap }}</td>
                                <td class="px-6 py-4 text-stone-500">{{ sv.danh_muc }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-stone-800">{{ formatVND(sv.gia_tien) }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="approvalColors[sv.trang_thai_duyet] ?? 'bg-stone-100 text-stone-500'"
                                    >
                                        {{ approvalLabels[sv.trang_thai_duyet] ?? sv.trang_thai_duyet }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-stone-500">{{ activityLabels[sv.trang_thai] ?? sv.trang_thai }}</td>
                                <td class="px-6 py-4 text-stone-400">{{ sv.ngay_tao }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-1.5">
                                        <Link
                                            :href="`/admin/services/${sv.id}`"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-stone-50 hover:text-stone-700"
                                        >
                                            <Eye class="size-4" />
                                            <span class="hidden xl:inline">Xem</span>
                                        </Link>
                                        <button
                                            v-if="sv.trang_thai_duyet !== 'da_duyet'"
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-emerald-50 hover:text-emerald-600"
                                            @click="openModerationModal(sv, 'approve')"
                                        >
                                            <CheckCircle2 class="size-4" />
                                            <span>Duyệt</span>
                                        </button>
                                        <button
                                            v-if="sv.trang_thai_duyet !== 'tu_choi'"
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-red-50 hover:text-red-600"
                                            @click="openModerationModal(sv, 'reject')"
                                        >
                                            <XCircle class="size-4" />
                                            <span>Từ chối</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="services.data.length === 0">
                                <td colspan="8" class="px-6 py-16 text-center text-sm text-stone-400">Không có dịch vụ nào trong bộ lọc hiện tại</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="services.last_page > 1" class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                    <p class="text-xs text-stone-500">Trang {{ services.current_page }} / {{ services.last_page }}</p>
                    <div class="flex gap-1">
                        <template v-for="link in services.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                                :class="link.active ? 'bg-sky-600 text-white' : 'text-stone-600 hover:bg-stone-100'"
                                v-html="link.label"
                                preserve-state
                            />
                            <span
                                v-else
                                class="rounded-lg px-3 py-1.5 text-xs text-stone-300"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Moderation Modal -->
        <Teleport to="body">
            <div v-if="pendingService && pendingAction" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="w-full max-w-2xl rounded-[2rem] bg-white p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Xem nhanh trước khi xử lý</p>
                            <h2 class="mt-2 text-xl font-bold text-stone-900">{{ modalTitle }}</h2>
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
                                    <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Danh mục</p>
                                    <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingService.danh_muc }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Ngày tạo</p>
                                    <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingService.ngay_tao }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trạng thái duyệt</p>
                                    <p class="mt-1 text-sm font-medium text-stone-700">{{ approvalLabels[pendingService.trang_thai_duyet] ?? pendingService.trang_thai_duyet }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trạng thái hiển thị</p>
                                    <p class="mt-1 text-sm font-medium text-stone-700">{{ activityLabels[pendingService.trang_thai] ?? pendingService.trang_thai }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-stone-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Checklist nhanh</p>
                            <ul class="mt-3 space-y-3 text-sm text-stone-600">
                                <li class="rounded-xl bg-stone-50 px-3 py-2">Tên dịch vụ rõ ràng và phù hợp với danh mục.</li>
                                <li class="rounded-xl bg-stone-50 px-3 py-2">Giá và đơn vị nhất quán để khách hàng dễ so sánh.</li>
                                <li class="rounded-xl bg-stone-50 px-3 py-2">Trạng thái hiển thị hợp với quyết định duyệt hiện tại.</li>
                            </ul>

                            <Link
                                :href="`/admin/services/${pendingService.id}`"
                                target="_blank"
                                class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                            >
                                Mở trang chi tiết
                            </Link>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50" @click="closeModerationModal">
                            Quay lại
                        </button>
                        <button
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                            :class="pendingAction === 'approve' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600'"
                            @click="submitModeration"
                        >
                            {{ pendingAction === 'approve' ? 'Xác nhận duyệt' : 'Xác nhận từ chối' }}
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
