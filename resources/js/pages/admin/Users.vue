<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Shield, ShieldOff, UserCheck, UserX, Users, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface UserItem {
    id: number;
    ho_ten: string;
    email: string;
    so_dien_thoai: string | null;
    anh_dai_dien: string | null;
    vai_tro: string;
    trang_thai: string;
    ngay_tao: string;
    lan_dang_nhap_cuoi?: string | null;
}

interface PaginatedUsers {
    data: UserItem[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = withDefaults(defineProps<{
    users: PaginatedUsers;
    roles: string[];
    stats: { total: number; active: number; inactive: number };
    filters: { search?: string; role?: string; status?: string };
}>(), {
    users: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }),
    roles: () => [],
    stats: () => ({ total: 0, active: 0, inactive: 0 }),
    filters: () => ({}),
});

const search = ref(props.filters.search ?? '');
const roleFilter = ref(props.filters.role ?? 'all');
const statusFilter = ref(props.filters.status ?? 'all');
const pendingUser = ref<UserItem | null>(null);

const modalTitle = computed(() =>
    pendingUser.value?.trang_thai === 'hoat_dong' ? 'Khóa tài khoản' : 'Mở khóa tài khoản',
);

function applyFilters() {
    router.get('/admin/users', {
        search: search.value || undefined,
        role: roleFilter.value !== 'all' ? roleFilter.value : undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
}

function openStatusModal(user: UserItem) {
    pendingUser.value = user;
}

function closeStatusModal() {
    pendingUser.value = null;
}

function confirmToggleStatus() {
    if (!pendingUser.value) return;

    router.post(`/admin/users/${pendingUser.value.id}/toggle-status`, {}, {
        preserveScroll: true,
        onFinish: () => {
            pendingUser.value = null;
        },
    });
}

function formatLastLogin(value?: string | null) {
    if (!value) return 'Chưa đăng nhập';

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsed);
}

const roleColors: Record<string, string> = {
    Admin: 'text-blue-700 bg-blue-50 ring-1 ring-blue-200',
    'Nhà cung cấp': 'text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200',
    'Khách hàng': 'text-amber-700 bg-amber-50 ring-1 ring-amber-200',
};

const avatarGradients: Record<string, string> = {
    Admin: 'from-blue-500 to-indigo-500',
    'Nhà cung cấp': 'from-emerald-500 to-teal-500',
    'Khách hàng': 'from-amber-500 to-orange-500',
};

// Animations
const { animateFadeUp } = useAnimations();
animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Quản lý người dùng" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
            <!-- Stats Cards -->
            <div class="animate-fade-up grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-blue-50 p-2.5 text-blue-500 transition group-hover:scale-110">
                            <Users class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.total }}</p>
                            <p class="text-xs text-stone-500">Tổng cộng</p>
                        </div>
                    </div>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-500 transition group-hover:scale-110">
                            <UserCheck class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.active }}</p>
                            <p class="text-xs text-stone-500">Đang hoạt động</p>
                        </div>
                    </div>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-red-50 p-2.5 text-red-500 transition group-hover:scale-110">
                            <UserX class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.inactive }}</p>
                            <p class="text-xs text-stone-500">Đang bị khóa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="animate-fade-up overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-stone-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700">Quản lý</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-stone-950">Người dùng</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ users.total }} người dùng trong hệ thống</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Tìm theo tên, email..."
                                class="w-52 rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition-all focus:border-sky-400 focus:bg-white focus:ring-2 focus:ring-sky-100"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <select v-model="roleFilter" class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100" @change="applyFilters">
                            <option value="all">Tất cả vai trò</option>
                            <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                        </select>
                        <select v-model="statusFilter" class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100" @change="applyFilters">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="hoat_dong">Hoạt động</option>
                            <option value="bi_khoa">Bị khóa</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Người dùng</th>
                                <th class="px-6 py-3.5">Liên hệ</th>
                                <th class="px-6 py-3.5">Vai trò</th>
                                <th class="px-6 py-3.5">Trạng thái</th>
                                <th class="px-6 py-3.5">Lần hoạt động gần đây</th>
                                <th class="px-6 py-3.5">Ngày tạo</th>
                                <th class="px-6 py-3.5 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id" class="border-b border-stone-50 transition-colors hover:bg-stone-50/80">
                                <td class="whitespace-nowrap px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div :class="['flex size-9 items-center justify-center rounded-full bg-gradient-to-br text-xs font-bold text-white', avatarGradients[user.vai_tro] ?? 'from-stone-400 to-stone-500']">
                                            {{ user.ho_ten?.charAt(0)?.toUpperCase() ?? '?' }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-stone-800">{{ user.ho_ten }}</p>
                                            <p class="text-xs text-stone-400">ID #{{ user.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="space-y-1 text-sm">
                                        <p class="text-stone-600">{{ user.email }}</p>
                                        <p class="text-xs text-stone-400">{{ user.so_dien_thoai || 'Chưa cập nhật SĐT' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="roleColors[user.vai_tro] ?? 'bg-stone-100 text-stone-500'"
                                    >
                                        {{ user.vai_tro }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span v-if="user.trang_thai === 'hoat_dong'" class="flex items-center gap-1.5 text-xs text-emerald-600">
                                        <span class="size-1.5 rounded-full bg-emerald-500" /> Hoạt động
                                    </span>
                                    <span v-else class="flex items-center gap-1.5 text-xs text-red-500">
                                        <span class="size-1.5 rounded-full bg-red-500" /> Bị khóa
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="space-y-1">
                                        <p class="text-sm text-stone-600">{{ formatLastLogin(user.lan_dang_nhap_cuoi) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-stone-500">{{ user.ngay_tao }}</td>
                                <td class="px-6 py-3.5 text-right">
                                    <button
                                        v-if="user.vai_tro !== 'Admin'"
                                        class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-xs font-medium transition-colors"
                                        :class="user.trang_thai === 'hoat_dong'
                                            ? 'text-red-600 hover:bg-red-50'
                                            : 'text-emerald-600 hover:bg-emerald-50'"
                                        @click="openStatusModal(user)"
                                    >
                                        <ShieldOff v-if="user.trang_thai === 'hoat_dong'" class="size-3.5" />
                                        <Shield v-else class="size-3.5" />
                                        {{ user.trang_thai === 'hoat_dong' ? 'Khóa' : 'Mở khóa' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="7" class="px-6 py-16 text-center text-sm text-stone-400">Không tìm thấy người dùng phù hợp</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.last_page > 1" class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                    <p class="text-xs text-stone-500">Trang {{ users.current_page }} / {{ users.last_page }} ({{ users.total }} người dùng)</p>
                    <div class="flex gap-1">
                        <template v-for="link in users.links" :key="link.label">
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

        <!-- Toggle Status Modal -->
        <Teleport to="body">
            <div v-if="pendingUser" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="w-full max-w-lg rounded-[2rem] bg-white p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Xác nhận thao tác</p>
                            <h2 class="mt-2 text-xl font-bold text-stone-900">{{ modalTitle }}</h2>
                            <p class="mt-2 text-sm leading-6 text-stone-500">
                                Bạn đang cập nhật trạng thái cho <span class="font-medium text-stone-700">{{ pendingUser.ho_ten }}</span>.
                            </p>
                        </div>
                        <button class="rounded-full p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600" @click="closeStatusModal">
                            <X class="size-4" />
                        </button>
                    </div>

                    <div class="mt-5 rounded-2xl border border-stone-200 bg-stone-50 p-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Vai trò</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.vai_tro }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trạng thái hiện tại</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Bị khóa' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Liên hệ</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.email }}</p>
                                <p class="text-xs text-stone-400">{{ pendingUser.so_dien_thoai || 'Chưa cập nhật' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Lần đăng nhập cuối</p>
                                <p class="mt-1 text-sm font-medium text-stone-700">{{ formatLastLogin(pendingUser.lan_dang_nhap_cuoi) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50" @click="closeStatusModal">
                            Quay lại
                        </button>
                        <button
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                            :class="pendingUser.trang_thai === 'hoat_dong' ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600'"
                            @click="confirmToggleStatus"
                        >
                            {{ pendingUser.trang_thai === 'hoat_dong' ? 'Xác nhận khóa tài khoản' : 'Xác nhận mở khóa' }}
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
