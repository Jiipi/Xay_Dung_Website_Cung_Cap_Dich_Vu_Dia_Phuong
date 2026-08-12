<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, useForm, router } from '@inertiajs/vue3';
import { Layers, Plus, Pencil, Trash2, Search, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps<{
    categories: any;
    parentCategories: Array<{ id: number; ten_danh_muc: string }>;
    filters: any;
}>();

const search = ref(props.filters?.search || '');
const showModal = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null as number | null,
    ten_danh_muc: '',
    mo_ta: '',
    icon: '',
    hinh_anh: '',
    danh_muc_cha_id: '' as number | string,
    thu_tu: 0,
    trang_thai: 'hoat_dong',
});

const handleSearch = () => {
    router.get(
        '/admin/categories',
        { search: search.value },
        { preserveState: true },
    );
};

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.id = null;
    form.danh_muc_cha_id = '';
    form.thu_tu = 0;
    form.trang_thai = 'hoat_dong';
    showModal.value = true;
};

const openEditModal = (category: any) => {
    isEditing.value = true;
    form.id = category.id;
    form.ten_danh_muc = category.ten_danh_muc;
    form.mo_ta = category.mo_ta || '';
    form.icon = category.icon || '';
    form.hinh_anh = category.hinh_anh || '';
    form.danh_muc_cha_id = category.danh_muc_cha_id || '';
    form.thu_tu = category.thu_tu || 0;
    form.trang_thai = category.trang_thai;
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value && form.id) {
        form.put(`/admin/categories/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin/categories', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteCategory = (id: number) => {
    if (
        confirm(
            'Bạn có chắc chắn muốn xóa danh mục này? Mọi dữ liệu liên quan có thể bị ảnh hưởng.',
        )
    ) {
        router.delete(`/admin/categories/${id}`, {
            preserveScroll: true,
        });
    }
};

const getStatusColor = (status: string) => {
    return status === 'hoat_dong'
        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
};

const getSafeIconText = (icon?: string | null) => {
    if (!icon) return '';

    return icon
        .replace(/<[^>]*>/g, '')
        .trim()
        .slice(0, 2);
};
</script>

<template>
    <Head title="Quản lý Danh mục" />

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <h2 class="text-xl font-bold text-stone-950">Quản lý Danh mục</h2>
            <p class="mt-1 text-sm text-stone-500">
                Quản lý các danh mục dịch vụ trên hệ thống
            </p>
        </div>

        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row">
                <div class="relative w-full max-w-md">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                    >
                        <Search class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        v-model="search"
                        @keyup.enter="handleSearch"
                        type="text"
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2 pr-3 pl-10 leading-5 placeholder-gray-500 transition duration-150 ease-in-out focus:border-green-500 focus:ring-1 focus:ring-green-500 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        placeholder="Tìm kiếm danh mục..."
                    />
                </div>

                <button
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none"
                >
                    <Plus class="mr-2 h-5 w-5" />
                    Thêm Danh mục
                </button>
            </div>

            <!-- Categories Table -->
            <div
                class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Danh mục
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Danh mục cha
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Thứ tự
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Trạng thái
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Dịch vụ
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Hành động
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"
                        >
                            <tr
                                v-for="cat in categories.data"
                                :key="cat.id"
                                class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-700"
                                        >
                                            <span
                                                v-if="getSafeIconText(cat.icon)"
                                                class="text-base font-semibold"
                                                >{{
                                                    getSafeIconText(cat.icon)
                                                }}</span
                                            >
                                            <Layers v-else class="h-5 w-5" />
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ cat.ten_danh_muc }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ cat.slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ cat.danh_muc_cha_ten || '—' }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ cat.thu_tu }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs leading-5 font-semibold"
                                        :class="getStatusColor(cat.trang_thai)"
                                    >
                                        {{
                                            cat.trang_thai === 'hoat_dong'
                                                ? 'Hoạt động'
                                                : 'Tạm ẩn'
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900 dark:text-gray-300"
                                >
                                    {{ cat.so_luong_dich_vu }} dịch vụ
                                </td>
                                <td
                                    class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                                >
                                    <div class="flex justify-end space-x-3">
                                        <button
                                            @click="openEditModal(cat)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            <Pencil class="h-5 w-5" />
                                        </button>
                                        <button
                                            @click="deleteCategory(cat.id)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <Trash2 class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-6 py-8 text-center text-gray-500"
                                >
                                    Không tìm thấy danh mục nào.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form -->
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div
                    class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0"
                >
                    <div
                        class="fixed inset-0 transition-opacity"
                        aria-hidden="true"
                    >
                        <div
                            class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-80"
                            @click="showModal = false"
                        ></div>
                    </div>

                    <span
                        class="hidden sm:inline-block sm:h-screen sm:align-middle"
                        aria-hidden="true"
                        >&#8203;</span
                    >

                    <div
                        class="inline-block w-full transform overflow-hidden rounded-xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:max-w-lg sm:align-middle dark:bg-gray-800"
                    >
                        <div
                            class="border-b border-gray-100 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:border-gray-700"
                        >
                            <div class="flex items-center justify-between">
                                <h3
                                    class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        isEditing
                                            ? 'Cập nhật Danh mục'
                                            : 'Thêm Danh mục mới'
                                    }}
                                </h3>
                                <button
                                    @click="showModal = false"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none"
                                >
                                    <X class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="space-y-4 px-4 py-5 sm:p-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >Tên danh mục
                                        <span class="text-red-500"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.ten_danh_muc"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    />
                                    <p
                                        v-if="form.errors.ten_danh_muc"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ form.errors.ten_danh_muc }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >Danh mục cha</label
                                    >
                                    <select
                                        v-model="form.danh_muc_cha_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="">
                                            -- Không có (Danh mục gốc) --
                                        </option>
                                        <option
                                            v-for="parent in parentCategories"
                                            :key="parent.id"
                                            :value="parent.id"
                                            :disabled="
                                                isEditing &&
                                                form.id === parent.id
                                            "
                                        >
                                            {{ parent.ten_danh_muc }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="form.errors.danh_muc_cha_id"
                                        class="mt-2 text-sm text-red-600"
                                    >
                                        {{ form.errors.danh_muc_cha_id }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >Mô tả</label
                                    >
                                    <textarea
                                        v-model="form.mo_ta"
                                        rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >Thứ tự hiển thị</label
                                        >
                                        <input
                                            v-model="form.thu_tu"
                                            type="number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >Trạng thái</label
                                        >
                                        <select
                                            v-model="form.trang_thai"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                        >
                                            <option value="hoat_dong">
                                                Hoạt động
                                            </option>
                                            <option value="tam_an">
                                                Tạm ẩn
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="rounded-b-xl border-t border-gray-100 bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:border-gray-700 dark:bg-gray-800/80"
                            >
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex w-full justify-center rounded-lg border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    {{ isEditing ? 'Cập nhật' : 'Thêm mới' }}
                                </button>
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                >
                                    Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
