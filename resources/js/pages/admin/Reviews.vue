<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Star, Trash2, AlertCircle, MessageSquare } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import debounce from 'lodash/debounce';

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
</script>

<template>
    <Head title="Quản lý Đánh giá - Admin" />

    <AdminLayout activePage="reviews">
        <div class="mx-auto max-w-7xl p-4 lg:p-8">
            <div class="mb-8 sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Quản lý Đánh giá</h1>
                    <p class="mt-2 text-sm text-slate-400">Kiểm duyệt và quản lý các đánh giá từ khách hàng.</p>
                </div>
            </div>

            <!-- Stats/Filters -->
            <div class="mb-6 grid gap-4 lg:grid-cols-4">
                <div class="col-span-1 lg:col-span-3 flex flex-wrap items-center gap-2">
                    <button
                        @click="filterByStar('all')"
                        :class="[
                            'rounded-xl border px-4 py-2 text-sm font-medium transition',
                            currentStar === 'all'
                                ? 'bg-sky-500 border-sky-500 text-white shadow-sm'
                                : 'bg-slate-900/50 border-slate-800 text-slate-400 hover:bg-slate-800 hover:text-slate-200'
                        ]"
                    >
                        Tất cả ({{ stats.total }})
                    </button>
                    
                    <button
                        v-for="star in [5,4,3,2,1]"
                        :key="star"
                        @click="filterByStar(star.toString())"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-xl border px-3 py-2 text-sm font-medium transition',
                            currentStar === star.toString()
                                ? 'bg-amber-500 border-amber-500 text-white shadow-sm'
                                : 'bg-slate-900/50 border-slate-800 text-slate-400 hover:bg-slate-800 hover:text-slate-200'
                        ]"
                    >
                        {{ star }} <Star class="size-3.5 fill-current" />
                        <span class="ml-1 opacity-70">({{ stats.star_counts[star] || 0 }})</span>
                    </button>
                </div>

                <div class="relative col-span-1">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-500" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm nội dung, người dùng..."
                        class="w-full rounded-xl border border-slate-800 bg-slate-900/50 py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder:text-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/50 backdrop-blur-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="border-b border-slate-800 bg-slate-800/50 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Đánh giá</th>
                                <th class="px-6 py-4">Khách hàng</th>
                                <th class="px-6 py-4">Nhà cung cấp</th>
                                <th class="px-6 py-4">Dịch vụ</th>
                                <th class="px-6 py-4">Thời gian</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <tr v-for="r in reviews.data" :key="r.id" class="transition hover:bg-slate-800/30">
                                <td class="px-6 py-4 min-w-[300px]">
                                    <div class="flex items-center gap-1 mb-2 text-amber-400">
                                        <Star v-for="i in 5" :key="i" :class="['size-3.5', i <= r.so_sao ? 'fill-current' : 'text-slate-700']" />
                                    </div>
                                    <p class="text-slate-200 line-clamp-2" :title="r.noi_dung">{{ r.noi_dung || '(Không có nội dung)' }}</p>
                                    <div v-if="r.phan_hoi" class="mt-2 flex items-start gap-2 rounded-lg bg-slate-800/50 p-2 text-xs text-slate-400">
                                        <MessageSquare class="mt-0.5 size-3.5 shrink-0" />
                                        <p class="line-clamp-2"><span class="font-medium text-slate-300">NCC phản hồi:</span> {{ r.phan_hoi }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-200">{{ r.khach_hang }}</td>
                                <td class="px-6 py-4">{{ r.nha_cung_cap }}</td>
                                <td class="px-6 py-4">{{ r.dich_vu }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-400">{{ r.ngay_tao }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="confirmDelete(r)" title="Xóa đánh giá" class="rounded p-2 text-slate-400 transition hover:bg-red-500/20 hover:text-red-400">
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="reviews.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Không tìm thấy đánh giá nào.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (Reused logic from Bookings) -->
                <div v-if="reviews.last_page > 1" class="border-t border-slate-800 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-400">
                            Hiển thị <span class="font-medium text-slate-200">{{ reviews.from }}</span> đến <span class="font-medium text-slate-200">{{ reviews.to }}</span> trong tổng số <span class="font-medium text-slate-200">{{ reviews.total }}</span> kết quả
                        </p>
                        <div class="flex items-center gap-2">
                            <Link
                                v-if="reviews.prev_page_url"
                                :href="reviews.prev_page_url"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-slate-700"
                            >
                                Trước
                            </Link>
                            <Link
                                v-if="reviews.next_page_url"
                                :href="reviews.next_page_url"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-slate-700"
                            >
                                Tiếp
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showModal = false"></div>
                <div class="relative w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
                    <div class="flex items-start gap-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-500/20 text-red-400">
                            <AlertCircle class="size-5" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-white">Xóa đánh giá này?</h3>
                            <p class="mt-2 text-sm text-slate-400">
                                Hành động này sẽ <strong class="text-red-400">xóa vĩnh viễn</strong> đánh giá khỏi hệ thống.
                                Điểm trung bình của nhà cung cấp sẽ được tính toán lại.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">Hủy bỏ</button>
                        <button @click="executeDelete" class="rounded-xl bg-red-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-600">
                            Xóa vĩnh viễn
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>
