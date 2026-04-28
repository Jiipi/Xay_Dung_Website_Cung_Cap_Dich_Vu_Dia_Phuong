<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle2,
    Edit2,
    Eye,
    EyeOff,
    Package,
    Plus,
    Power,
    Search,
    Trash2,
    XCircle,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface Service {
    id: number;
    ten_dich_vu: string;
    slug: string;
    danh_muc: string;
    gia_tu: number;
    gia_den: number;
    don_vi_gia: string | null;
    anh_dai_dien: string | null;
    trang_thai_duyet: string;
    trang_thai_hoat_dong: string;
    so_booking: number;
    ngay_tao: string;
}

interface PaginatedServices {
    data: Service[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = withDefaults(defineProps<{
    services: PaginatedServices;
    filters: { search?: string; trang_thai?: string };
}>(), {
    services: () => ({ data: [], current_page: 1, last_page: 1, per_page: 10, total: 0, links: [] }),
    filters: () => ({}),
});

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
    error: page.props.flash?.error as string | undefined,
}));

const searchQuery = ref(props.filters?.search ?? '');
const statusFilter = ref(props.filters?.trang_thai ?? '');
const showDeleteModal = ref(false);
const deleteTargetId = ref<number | null>(null);
const deleteTargetName = ref('');

const formatVND = (value: number) => {
    if (!value) return '—';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const duyetStyles: Record<string, string> = {
    cho_duyet: 'bg-amber-50 text-amber-700 ring-amber-200',
    da_duyet: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    tu_choi: 'bg-red-50 text-red-700 ring-red-200',
};

const duyetLabels: Record<string, string> = {
    cho_duyet: 'Chờ duyệt',
    da_duyet: 'Đã duyệt',
    tu_choi: 'Từ chối',
};

const hoatDongLabels: Record<string, string> = {
    hoat_dong: 'Hoạt động',
    tam_ngung: 'Tạm ngưng',
    da_xoa: 'Đã xóa',
};

function applyFilters() {
    router.get('/provider/services', {
        search: searchQuery.value || undefined,
        trang_thai: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
}

function confirmDelete(svc: Service) {
    deleteTargetId.value = svc.id;
    deleteTargetName.value = svc.ten_dich_vu;
    showDeleteModal.value = true;
}

function executeDelete() {
    if (!deleteTargetId.value) return;
    router.delete(`/provider/services/${deleteTargetId.value}`, {
        onFinish: () => {
            showDeleteModal.value = false;
            deleteTargetId.value = null;
        },
    });
}

function toggleStatus(id: number) {
    router.post(`/provider/services/${id}/toggle-status`);
}
</script>

<template>
    <Head title="Quản lý dịch vụ" />

    <ProviderLayout activePage="services">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            <div v-if="flash.success" class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <CheckCircle2 class="size-5 shrink-0" />
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <XCircle class="size-5 shrink-0" />
                {{ flash.error }}
            </div>

            <!-- Header Card -->
            <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-stone-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Quản lý</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-stone-950">Dịch vụ của bạn</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ services.total }} dịch vụ</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tìm dịch vụ..."
                                class="w-52 rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition-all focus:border-orange-400 focus:bg-white focus:ring-2 focus:ring-orange-100"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                            @change="applyFilters"
                        >
                            <option value="">Tất cả</option>
                            <option value="hoat_dong">Hoạt động</option>
                            <option value="tam_ngung">Tạm ngưng</option>
                        </select>
                        <Link
                            href="/provider/services/create"
                            class="flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-700"
                        >
                            <Plus class="size-4" /> Thêm dịch vụ
                        </Link>
                    </div>
                </div>

                <div class="grid gap-3 border-b border-stone-100 bg-stone-50/70 px-6 py-4 md:grid-cols-3">
                    <div class="rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-600">
                        <p class="font-semibold text-stone-900">Trang thai duyet</p>
                        <p class="mt-1">Dich vu moi se vao muc <span class="font-medium text-amber-700">Cho duyet</span> truoc khi hien thi rong rai.</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-600">
                        <p class="font-semibold text-stone-900">Trang thai hoat dong</p>
                        <p class="mt-1">Ban co the tam ngung de an tren marketplace ma khong can xoa han.</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-600">
                        <p class="font-semibold text-stone-900">Muc tieu uu tien</p>
                        <p class="mt-1">Tap trung vao dich vu da duyet nhung booking thap de cai thien chuyen doi.</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Dịch vụ</th>
                                <th class="px-6 py-3.5">Danh mục</th>
                                <th class="px-6 py-3.5">Giá</th>
                                <th class="px-6 py-3.5">Booking</th>
                                <th class="px-6 py-3.5">Duyệt</th>
                                <th class="px-6 py-3.5">Trạng thái</th>
                                <th class="px-6 py-3.5 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="svc in services.data"
                                :key="svc.id"
                                class="border-b border-stone-50 transition-colors hover:bg-stone-50/80"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-stone-100">
                                            <img
                                                v-if="svc.anh_dai_dien"
                                                :src="svc.anh_dai_dien"
                                                :alt="svc.ten_dich_vu"
                                                class="size-full object-cover"
                                                referrerpolicy="no-referrer"
                                            />
                                            <Package v-else class="size-4 text-stone-400" />
                                        </div>
                                        <span class="max-w-[200px] truncate font-medium text-stone-950">{{ svc.ten_dich_vu }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ svc.danh_muc }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="font-medium text-stone-950">{{ formatVND(svc.gia_tu) }}</span>
                                    <span v-if="svc.gia_den && svc.gia_den !== svc.gia_tu" class="text-stone-400"> — {{ formatVND(svc.gia_den) }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ svc.so_booking }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                        :class="duyetStyles[svc.trang_thai_duyet] ?? 'bg-stone-50 text-stone-600 ring-stone-200'"
                                    >
                                        {{ duyetLabels[svc.trang_thai_duyet] ?? svc.trang_thai_duyet }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium"
                                        :class="svc.trang_thai_hoat_dong === 'hoat_dong' ? 'text-emerald-600' : 'text-stone-400'"
                                    >
                                        <span
                                            class="size-1.5 rounded-full"
                                            :class="svc.trang_thai_hoat_dong === 'hoat_dong' ? 'bg-emerald-500' : 'bg-stone-300'"
                                        />
                                        {{ hoatDongLabels[svc.trang_thai_hoat_dong] ?? svc.trang_thai_hoat_dong }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Link
                                            :href="`/services/${svc.id}`"
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-stone-50 hover:text-stone-700"
                                            title="Xem trang công khai"
                                        >
                                            <Eye class="size-4" />
                                            <span class="hidden xl:inline">Xem</span>
                                        </Link>
                                        <Link
                                            :href="`/provider/services/${svc.id}/edit`"
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-stone-50 hover:text-orange-600"
                                            title="Sửa"
                                        >
                                            <Edit2 class="size-4" />
                                            <span class="hidden xl:inline">Sua</span>
                                        </Link>
                                        <button
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-stone-50"
                                            :class="svc.trang_thai_hoat_dong === 'hoat_dong' ? 'hover:text-amber-600' : 'hover:text-emerald-600'"
                                            :title="svc.trang_thai_hoat_dong === 'hoat_dong' ? 'Tạm ngưng' : 'Kích hoạt'"
                                            @click="toggleStatus(svc.id)"
                                        >
                                            <Power v-if="svc.trang_thai_hoat_dong === 'hoat_dong'" class="size-4" />
                                            <EyeOff v-else class="size-4" />
                                            <span class="hidden xl:inline">{{ svc.trang_thai_hoat_dong === 'hoat_dong' ? 'Tam ngung' : 'Bat lai' }}</span>
                                        </button>
                                        <button
                                            class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-red-50 hover:text-red-600"
                                            title="Xóa"
                                            @click="confirmDelete(svc)"
                                        >
                                            <Trash2 class="size-4" />
                                            <span class="hidden xl:inline">Xoa</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-if="services.data.length === 0" class="flex flex-col items-center justify-center py-16">
                    <Package class="mb-3 size-10 text-stone-300" />
                    <p class="text-sm font-medium text-stone-600">Chưa có dịch vụ nào</p>
                    <p class="mt-1 text-xs text-stone-400">Tạo dịch vụ đầu tiên để bắt đầu nhận booking</p>
                    <Link
                        href="/provider/services/create"
                        class="mt-4 flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-orange-700"
                    >
                        <Plus class="size-4" /> Tạo dịch vụ mới
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="services.last_page > 1" class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                    <p class="text-xs text-stone-500">
                        Hiển thị {{ (services.current_page - 1) * services.per_page + 1 }}
                        — {{ Math.min(services.current_page * services.per_page, services.total) }}
                        / {{ services.total }} dịch vụ
                    </p>
                    <div class="flex gap-1">
                        <template v-for="link in services.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                                :class="link.active ? 'bg-orange-600 text-white' : 'text-stone-600 hover:bg-stone-100'"
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

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div class="mx-4 w-full max-w-md rounded-[2rem] bg-white p-6 shadow-xl">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-full bg-red-50 p-2.5">
                            <AlertTriangle class="size-5 text-red-600" />
                        </div>
                        <h3 class="text-lg font-bold text-stone-950">Xác nhận xóa</h3>
                    </div>
                    <p class="text-sm text-stone-600">
                        Bạn có chắc muốn xóa dịch vụ <strong>{{ deleteTargetName }}</strong>? Hành động này không thể hoàn tác.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-50"
                            @click="showDeleteModal = false"
                        >
                            Hủy
                        </button>
                        <button
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-700"
                            @click="executeDelete"
                        >
                            Xóa dịch vụ
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </ProviderLayout>
</template>
