<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from '@/lib/debounce';
import { Search, Star, Trash2, AlertCircle, MessageSquare } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps<{
    reviews: any;
    stats: {
        total: number;
        avg_rating: number;
        star_counts: Record<number, number>;
    };
    filters: { search?: string; so_sao?: string };
}>();

const search = ref(props.filters.search ?? '');
const currentStar = ref(props.filters.so_sao ?? 'all');

const doSearch = debounce(() => {
    router.get('/admin/reviews', {
        search: search.value,
        so_sao: currentStar.value,
    }, { preserveState: true, replace: true });
}, 500);

watch(search, doSearch);

function filterByStar(star: string) {
    currentStar.value = star;
    router.get('/admin/reviews', {
        search: search.value,
        so_sao: star,
    }, { preserveState: true, replace: true });
}

// Action Modal
const showModal = ref(false);
const selectedReview = ref<any>(null);

function confirmDelete(review: any) {
    selectedReview.value = review;
    showModal.value = true;
}

function executeDelete() {
    if (!selectedReview.value) return;

    router.delete(`/admin/reviews/${selectedReview.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            selectedReview.value = null;
        }
    });
}

// Animations
const { animateFadeUp } = useAnimations();
animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Quản lý Đánh giá - Admin" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="animate-fade-up overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-stone-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700">Kiểm duyệt</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-stone-950">Đánh giá</h1>
                        <p class="mt-1 text-sm text-stone-500">Kiểm duyệt và quản lý các đánh giá từ khách hàng · {{ stats.total }} đánh giá · TB {{ stats.avg_rating }}★</p>
                    </div>
                    <div class="relative">
                        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Tìm nội dung, người dùng..."
                            class="w-60 rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-9 pr-3 text-sm outline-none transition-all focus:border-sky-400 focus:bg-white focus:ring-2 focus:ring-sky-100"
                        />
                    </div>
                </div>

                <!-- Star Filter Tabs -->
                <div class="flex flex-wrap items-center gap-2 border-b border-stone-100 bg-stone-50/70 px-6 py-3">
                    <button
                        @click="filterByStar('all')"
                        :class="[
                            'whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition',
                            currentStar === 'all'
                                ? 'border-sky-500 bg-sky-50 text-sky-700 shadow-sm'
                                : 'border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-800'
                        ]"
                    >
                        Tất cả ({{ stats.total }})
                    </button>
                    <button
                        v-for="star in [5,4,3,2,1]"
                        :key="star"
                        @click="filterByStar(star.toString())"
                        :class="[
                            'inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl border px-3 py-2 text-sm font-medium transition',
                            currentStar === star.toString()
                                ? 'border-amber-500 bg-amber-50 text-amber-700 shadow-sm'
                                : 'border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-800'
                        ]"
                    >
                        {{ star }} <Star class="size-3.5 fill-current text-amber-400" />
                        <span class="ml-1 opacity-70">({{ stats.star_counts[star] || 0 }})</span>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Đánh giá</th>
                                <th class="px-6 py-3.5">Khách hàng</th>
                                <th class="px-6 py-3.5">Nhà cung cấp</th>
                                <th class="px-6 py-3.5">Dịch vụ</th>
                                <th class="px-6 py-3.5">Thời gian</th>
                                <th class="px-6 py-3.5 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="r in reviews.data" :key="r.id" class="border-b border-stone-50 transition-colors hover:bg-stone-50/80">
                                <td class="px-6 py-4 min-w-[300px]">
                                    <div class="flex items-center gap-1 mb-2 text-amber-400">
                                        <Star v-for="i in 5" :key="i" :class="['size-3.5', i <= r.so_sao ? 'fill-current' : 'text-stone-200']" />
                                    </div>
                                    <p class="text-stone-700 line-clamp-2" :title="r.noi_dung">{{ r.noi_dung || '(Không có nội dung)' }}</p>
                                    <div v-if="r.phan_hoi" class="mt-2 flex items-start gap-2 rounded-lg bg-stone-50 p-2 text-xs text-stone-500">
                                        <MessageSquare class="mt-0.5 size-3.5 shrink-0" />
                                        <p class="line-clamp-2"><span class="font-medium text-stone-700">NCC phản hồi:</span> {{ r.phan_hoi }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-stone-800">{{ r.khach_hang }}</td>
                                <td class="px-6 py-4 text-stone-600">{{ r.nha_cung_cap }}</td>
                                <td class="px-6 py-4 text-stone-600">{{ r.dich_vu }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-stone-400">{{ r.ngay_tao }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="confirmDelete(r)" title="Xóa đánh giá" class="inline-flex items-center gap-1 rounded-lg border border-stone-200 px-2.5 py-2 text-stone-500 transition-colors hover:bg-red-50 hover:text-red-600">
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="reviews.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center text-sm text-stone-400">Không tìm thấy đánh giá nào.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="reviews.last_page > 1" class="flex items-center justify-between border-t border-stone-100 px-6 py-4">
                    <p class="text-xs text-stone-500">
                        Hiển thị {{ reviews.from }} — {{ reviews.to }} / {{ reviews.total }} kết quả
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-if="reviews.prev_page_url"
                            :href="reviews.prev_page_url"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100"
                            preserve-state
                        >Trước</Link>
                        <Link
                            v-if="reviews.next_page_url"
                            :href="reviews.next_page_url"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100"
                            preserve-state
                        >Tiếp</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                <div class="mx-4 w-full max-w-md rounded-[2rem] bg-white p-6 shadow-xl">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-full bg-red-50 p-2.5">
                            <AlertCircle class="size-5 text-red-600" />
                        </div>
                        <h3 class="text-lg font-bold text-stone-950">Xóa đánh giá này?</h3>
                    </div>
                    <p class="text-sm text-stone-600">
                        Hành động này sẽ <strong class="text-red-600">xóa vĩnh viễn</strong> đánh giá khỏi hệ thống.
                        Điểm trung bình của nhà cung cấp sẽ được tính toán lại.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-stone-700 transition-colors hover:bg-stone-50">Hủy bỏ</button>
                        <button @click="executeDelete" class="rounded-xl bg-red-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-600">
                            Xóa vĩnh viễn
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
