<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Shield, ShieldOff, UserCheck, UserX, Users, Sparkles, X } from 'lucide-vue-next';
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
    pendingUser.value?.trang_thai === 'hoat_dong' ? 'Khoa tai khoan' : 'Mo khoa tai khoan',
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
    if (!value) return 'Chua dang nhap';

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
    Admin: 'text-blue-600 bg-blue-50',
    'Nha cung cap': 'text-emerald-600 bg-emerald-50',
    'Khach hang': 'text-amber-600 bg-amber-50',
};

const avatarGradients: Record<string, string> = {
    Admin: 'from-blue-500 to-indigo-500',
    'Nha cung cap': 'from-emerald-500 to-teal-500',
    'Khach hang': 'from-amber-500 to-orange-500',
};
</script>

<template>
    <Head title="Quan ly nguoi dung" />

    <AdminLayout activePage="users">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
            <div class="admin-page-header">
                <div class="admin-page-header__accent" />
                <div>
                    <div class="mb-1 flex items-center gap-2">
                        <Sparkles class="size-3.5 text-blue-500" />
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-500/70">Quan ly</span>
                    </div>
                    <h1 class="text-2xl font-bold text-stone-800">Nguoi dung</h1>
                    <p class="mt-1 text-sm text-stone-500">Them ngu canh truoc khi khoa tai khoan va theo doi muc do hoat dong gan day.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="admin-stat-card group">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-blue-50 p-2.5 text-blue-500 transition group-hover:scale-110">
                            <Users class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.total }}</p>
                            <p class="text-xs text-stone-500">Tong cong</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card group">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-500 transition group-hover:scale-110">
                            <UserCheck class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.active }}</p>
                            <p class="text-xs text-stone-500">Dang hoat dong</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card group">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-red-50 p-2.5 text-red-500 transition group-hover:scale-110">
                            <UserX class="size-5" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-stone-800">{{ stats.inactive }}</p>
                            <p class="text-xs text-stone-500">Dang bi khoa</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Tim kiem</p>
                        <p class="mt-1 text-sm text-stone-600">Co the tim theo ten, email hoac so dien thoai de xu ly nhanh.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Kiem tra nhanh</p>
                        <p class="mt-1 text-sm text-stone-600">Moc dang nhap gan nhat giup nhin ra tai khoan bo trong hay co dau hieu bat thuong.</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Hanh dong</p>
                        <p class="mt-1 text-sm text-stone-600">Khoa va mo khoa duoc xac nhan lai de tranh thao tac nham tren role nhay cam.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[240px] flex-1">
                    <Search class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tim theo ten, email, so dien thoai..."
                        class="admin-input pl-10"
                        @keyup.enter="applyFilters"
                    />
                </div>
                <select v-model="roleFilter" class="admin-select" @change="applyFilters">
                    <option value="all">Tat ca vai tro</option>
                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                </select>
                <select v-model="statusFilter" class="admin-select" @change="applyFilters">
                    <option value="all">Tat ca trang thai</option>
                    <option value="hoat_dong">Hoat dong</option>
                    <option value="bi_khoa">Bi khoa</option>
                </select>
            </div>

            <div class="admin-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-xs font-medium uppercase tracking-wider text-stone-500">
                                <th class="px-6 py-3">Nguoi dung</th>
                                <th class="px-6 py-3">Lien he</th>
                                <th class="px-6 py-3">Vai tro</th>
                                <th class="px-6 py-3">Trang thai</th>
                                <th class="px-6 py-3">Lan hoat dong gan day</th>
                                <th class="px-6 py-3">Ngay tao</th>
                                <th class="px-6 py-3 text-right">Hanh dong</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id" class="admin-table-row">
                                <td class="whitespace-nowrap px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div :class="['flex size-9 items-center justify-center rounded-full bg-gradient-to-br text-xs font-bold text-white', avatarGradients[user.vai_tro] ?? 'from-stone-400 to-stone-500']">
                                            {{ user.ho_ten?.charAt(0)?.toUpperCase() ?? '?' }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-stone-700">{{ user.ho_ten }}</p>
                                            <p class="text-xs text-stone-400">ID #{{ user.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="space-y-1 text-sm">
                                        <p class="text-stone-600">{{ user.email }}</p>
                                        <p class="text-xs text-stone-400">{{ user.so_dien_thoai || 'Chua cap nhat so dien thoai' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="roleColors[user.vai_tro] ?? 'bg-stone-100 text-stone-500'"
                                    >
                                        {{ user.vai_tro }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <span v-if="user.trang_thai === 'hoat_dong'" class="flex items-center gap-1.5 text-xs text-emerald-600">
                                        <span class="size-1.5 rounded-full bg-emerald-500" /> Hoat dong
                                    </span>
                                    <span v-else class="flex items-center gap-1.5 text-xs text-red-500">
                                        <span class="size-1.5 rounded-full bg-red-500" /> Bi khoa
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="space-y-1">
                                        <p class="text-sm text-stone-600">{{ formatLastLogin(user.lan_dang_nhap_cuoi) }}</p>
                                        <p class="text-xs text-stone-400">
                                            {{ user.lan_dang_nhap_cuoi ? 'Da co hoat dong gan day' : 'Can theo doi them' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-stone-500">{{ user.ngay_tao }}</td>
                                <td class="px-6 py-3 text-right">
                                    <button
                                        v-if="user.vai_tro !== 'Admin'"
                                        class="admin-action-btn"
                                        :class="user.trang_thai === 'hoat_dong'
                                            ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                            : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'"
                                        @click="openStatusModal(user)"
                                    >
                                        <ShieldOff v-if="user.trang_thai === 'hoat_dong'" class="mr-1 inline size-3.5" />
                                        <Shield v-else class="mr-1 inline size-3.5" />
                                        {{ user.trang_thai === 'hoat_dong' ? 'Khoa' : 'Mo khoa' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-stone-400">Khong tim thay nguoi dung phu hop</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.last_page > 1" class="flex items-center justify-between border-t border-stone-200 px-6 py-3">
                    <p class="text-xs text-stone-500">Trang {{ users.current_page }} / {{ users.last_page }} ({{ users.total }} nguoi dung)</p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url ?? ''"
                            class="admin-pagination-btn"
                            :class="link.active
                                ? 'bg-blue-500 text-white'
                                : link.url ? 'bg-stone-100 text-stone-600 hover:bg-stone-200' : 'cursor-not-allowed bg-stone-50 text-stone-300'"
                            v-html="link.label"
                            :preserve-scroll="true"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="pendingUser" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/50 px-4">
            <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-400">Xac nhan thao tac</p>
                        <h2 class="mt-2 text-xl font-semibold text-stone-900">{{ modalTitle }}</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-500">
                            Ban dang cap nhat trang thai cho <span class="font-medium text-stone-700">{{ pendingUser.ho_ten }}</span>.
                            Hay kiem tra nhanh vai tro, lan dang nhap cuoi va thong tin lien he truoc khi tiep tuc.
                        </p>
                    </div>
                    <button class="rounded-full p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600" @click="closeStatusModal">
                        <X class="size-4" />
                    </button>
                </div>

                <div class="mt-5 rounded-2xl border border-stone-200 bg-stone-50 p-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Vai tro</p>
                            <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.vai_tro }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Trang thai hien tai</p>
                            <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.trang_thai === 'hoat_dong' ? 'Hoat dong' : 'Bi khoa' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Lien he</p>
                            <p class="mt-1 text-sm font-medium text-stone-700">{{ pendingUser.email }}</p>
                            <p class="text-xs text-stone-400">{{ pendingUser.so_dien_thoai || 'Chua cap nhat' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.14em] text-stone-400">Lan dang nhap cuoi</p>
                            <p class="mt-1 text-sm font-medium text-stone-700">{{ formatLastLogin(pendingUser.lan_dang_nhap_cuoi) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50" @click="closeStatusModal">
                        Quay lai
                    </button>
                    <button
                        class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                        :class="pendingUser.trang_thai === 'hoat_dong' ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600'"
                        @click="confirmToggleStatus"
                    >
                        {{ pendingUser.trang_thai === 'hoat_dong' ? 'Xac nhan khoa tai khoan' : 'Xac nhan mo khoa' }}
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

.admin-stat-card {
    border-radius: 1.25rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    padding: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.admin-stat-card:hover {
    border-color: #d6d3d1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
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
    transition: border-color 0.2s ease;
}

.admin-input::placeholder {
    color: #a8a29e;
}

.admin-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.admin-select {
    border-radius: 0.75rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    color: #57534e;
    outline: none;
}

.admin-select:focus {
    border-color: #3b82f6;
}

.admin-table-row {
    border-bottom: 1px solid #f5f5f4;
    transition: background 0.2s ease;
}

.admin-table-row:hover {
    background: #fafaf9;
}

.admin-action-btn {
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
