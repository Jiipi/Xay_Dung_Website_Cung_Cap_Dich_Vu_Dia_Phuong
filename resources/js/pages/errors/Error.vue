<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, MapPin, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    status: number;
}>();

const metadata = computed(() => {
    return {
        503: {
            title: 'Hệ thống bảo trì',
            description: 'Dalat Services đang trong quá trình bảo trì để nâng cấp trải nghiệm. Vui lòng quay lại sau ít phút.',
            icon: AlertTriangle,
            button: 'Tải lại trang',
            action: () => window.location.reload(),
        },
        500: {
            title: 'Lỗi máy chủ',
            description: 'Úi! Đã có lỗi xảy ra từ phía chúng tôi. Đội ngũ kỹ thuật đang khắc phục sự cố này.',
            icon: XCircle,
            button: 'Trở về trang chủ',
            action: null,
        },
        404: {
            title: 'Không tìm thấy trang',
            description: 'Rất tiếc, trang bạn đang tìm kiếm không tồn tại hoặc đã bị gỡ bỏ.',
            icon: MapPin,
            button: 'Về trang chủ',
            action: null,
        },
        403: {
            title: 'Truy cập bị từ chối',
            description: 'Bạn không có quyền truy cập vào trang này.',
            icon: XCircle,
            button: 'Quay lại',
            action: () => window.history.back(),
        },
    }[props.status] || {
        title: 'Lỗi hệ thống',
        description: 'Đã có lỗi không xác định xảy ra.',
        icon: AlertTriangle,
        button: 'Trở về trang chủ',
        action: null,
    };
});
</script>

<template>
    <Head :title="metadata.title" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-stone-50 px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full max-w-md text-center">
            <!-- Icon -->
            <div class="mx-auto mb-8 flex size-24 items-center justify-center rounded-full bg-stone-100 shadow-inner">
                <component :is="metadata.icon" class="size-10 text-stone-400" />
            </div>

            <!-- Error Code -->
            <h1 class="text-7xl font-black text-stone-200">{{ status }}</h1>

            <!-- Title -->
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-stone-900 sm:text-3xl">
                {{ metadata.title }}
            </h2>

            <!-- Description -->
            <p class="mt-4 text-base text-stone-500">
                {{ metadata.description }}
            </p>

            <!-- Action Button -->
            <div class="mt-10">
                <button
                    v-if="metadata.action"
                    @click="metadata.action"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand/90 sm:w-auto"
                >
                    {{ metadata.button }}
                </button>
                <Link
                    v-else
                    href="/"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand/90 sm:w-auto"
                >
                    {{ metadata.button }}
                </Link>
            </div>
        </div>
    </div>
</template>
