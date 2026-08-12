<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, useForm } from '@inertiajs/vue3';
import { Settings, Percent } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps<{
    settings: {
        platform_fee_percent?: string;
    };
}>();

const form = useForm({
    platform_fee_percent: props.settings.platform_fee_percent || '10',
});

const submitForm = () => {
    form.post('/admin/settings', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Cấu hình hệ thống" />

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-stone-950">Cấu hình hệ thống</h2>
            <p class="mt-1 text-sm text-stone-500">Thiết lập các thông số chung của nền tảng</p>
        </div>

        <div class="max-w-4xl space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center">
                    <Settings class="w-6 h-6 text-gray-500 mr-3" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Cài đặt Tài chính & Hoa hồng</h3>
                </div>

                <div class="p-6">
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Phí nền tảng (Platform Fee)
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Percent class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <input
                                        v-model="form.platform_fee_percent"
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        max="100"
                                        class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md"
                                        placeholder="10"
                                    >
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Tỷ lệ % hệ thống sẽ thu trên tổng giá trị mỗi đơn hàng hoàn thành.
                                    Số tiền cọc dư sẽ được cộng vào ví của nhà cung cấp.
                                </p>
                                <div v-if="form.errors.platform_fee_percent" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.platform_fee_percent }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-colors"
                            >
                                Lưu cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
