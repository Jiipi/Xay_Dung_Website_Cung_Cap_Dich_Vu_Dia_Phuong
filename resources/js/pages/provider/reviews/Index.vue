<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Filter,
    Loader2,
    MessageSquare,
    Send,
    Star,
    User,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface Review {
    id: number;
    khach_hang: string;
    avatar: string | null;
    so_sao: number;
    noi_dung: string | null;
    phan_hoi_tu_ncc: string | null;
    ngay_phan_hoi: string | null;
    dich_vu: string;
    ngay: string;
}

interface PaginatedReviews {
    data: Review[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface ReviewStats {
    tong: number;
    trung_binh: number;
    chua_phan_hoi: number;
    phan_bo: Record<number, number>;
}

const props = withDefaults(defineProps<{
    reviews: PaginatedReviews;
    stats: ReviewStats;
    filters: { so_sao?: string; chua_phan_hoi?: string };
}>(), {
    reviews: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }),
    stats: () => ({ tong: 0, trung_binh: 0, chua_phan_hoi: 0, phan_bo: {} }),
    filters: () => ({}),
});

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
}));

// Track which review is being replied to
const replyingTo = ref<number | null>(null);
const replyText = ref('');
const replyLoading = ref(false);

function openReply(reviewId: number) {
    replyingTo.value = reviewId;
    replyText.value = '';
}

function cancelReply() {
    replyingTo.value = null;
    replyText.value = '';
}

function submitReply(reviewId: number) {
    if (!replyText.value.trim()) return;
    replyLoading.value = true;

    router.post(`/provider/reviews/${reviewId}/reply`, {
        phan_hoi: replyText.value,
    }, {
        onFinish: () => {
            replyLoading.value = false;
            replyingTo.value = null;
            replyText.value = '';
        },
    });
}

function filterByStars(stars: number | null) {
    router.get('/provider/reviews', {
        so_sao: stars ?? undefined,
    }, { preserveState: true, replace: true });
}

function filterUnreplied() {
    router.get('/provider/reviews', {
        chua_phan_hoi: '1',
    }, { preserveState: true, replace: true });
}

function getStarPercent(star: number): number {
    if (!props.stats.tong) return 0;
    return ((props.stats.phan_bo[star] ?? 0) / props.stats.tong) * 100;
}
</script>

<template>
    <Head title="Đánh giá nhận được" />

    <ProviderLayout activePage="reviews">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="space-y-6">
                    <!-- Flash -->
                    <div v-if="flash.success" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <CheckCircle2 class="size-5 shrink-0" /> {{ flash.success }}
                    </div>

                    <h1 class="text-xl font-bold text-stone-950">Đánh giá nhận được</h1>

                    <!-- Stats Overview -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-stone-200 bg-white p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold text-stone-950">{{ stats.trung_binh }} <span class="text-lg text-amber-500">★</span></p>
                            <p class="mt-1 text-sm text-stone-500">Điểm trung bình</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-white p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold text-stone-950">{{ stats.tong }}</p>
                            <p class="mt-1 text-sm text-stone-500">Tổng đánh giá</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-white p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold" :class="stats.chua_phan_hoi > 0 ? 'text-amber-600' : 'text-emerald-600'">{{ stats.chua_phan_hoi }}</p>
                            <p class="mt-1 text-sm text-stone-500">Chờ phản hồi</p>
                        </div>
                    </div>

                    <!-- Star Distribution -->
                    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-3 text-sm font-semibold text-stone-950">Phân bố đánh giá</h2>
                        <div class="space-y-2">
                            <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3">
                                <button
                                    class="flex w-14 items-center gap-1 text-xs font-medium text-stone-600 transition-colors hover:text-brand"
                                    @click="filterByStars(star)"
                                >
                                    {{ star }} <Star class="size-3 fill-amber-400 text-amber-400" />
                                </button>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-stone-100">
                                    <div
                                        class="h-full rounded-full bg-amber-400 transition-all duration-500"
                                        :style="{ width: `${getStarPercent(star)}%` }"
                                    />
                                </div>
                                <span class="w-8 text-right text-xs text-stone-500">{{ stats.phan_bo[star] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter buttons -->
                    <div class="flex items-center gap-3 overflow-x-auto pb-1">
                        <Filter class="size-4 shrink-0 text-stone-400" />
                        <button
                            class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-medium transition-colors"
                            :class="!filters.so_sao && !filters.chua_phan_hoi ? 'bg-orange-600 text-white' : 'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                            @click="filterByStars(null)"
                        >
                            Tất cả
                        </button>
                        <button
                            class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-medium transition-colors"
                            :class="filters.chua_phan_hoi ? 'bg-amber-500 text-white' : 'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                            @click="filterUnreplied"
                        >
                            Chờ phản hồi ({{ stats.chua_phan_hoi }})
                        </button>
                    </div>

                    <!-- Review List -->
                    <div class="space-y-4">
                        <article
                            v-for="review in reviews.data"
                            :key="review.id"
                            class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm"
                        >
                            <div class="p-6">
                                <div class="flex items-start gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-stone-100">
                                        <img v-if="review.avatar" :src="review.avatar" :alt="review.khach_hang" class="size-full object-cover" referrerpolicy="no-referrer" />
                                        <User v-else class="size-4 text-stone-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-stone-950">{{ review.khach_hang }}</p>
                                            <span class="text-xs text-stone-400">{{ review.ngay }}</span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            <div class="flex gap-0.5">
                                                <Star v-for="s in 5" :key="s" class="size-3.5" :class="s <= review.so_sao ? 'fill-amber-400 text-amber-400' : 'text-stone-200'" />
                                            </div>
                                            <span class="text-xs text-stone-400">· {{ review.dich_vu }}</span>
                                        </div>
                                        <p v-if="review.noi_dung" class="mt-2 text-sm leading-relaxed text-stone-600">{{ review.noi_dung }}</p>

                                        <!-- Provider reply -->
                                        <div v-if="review.phan_hoi_tu_ncc" class="mt-3 rounded-xl bg-brand-surface px-4 py-3">
                                            <p class="text-xs font-medium text-brand">Phản hồi của bạn <span class="font-normal text-brand">· {{ review.ngay_phan_hoi }}</span></p>
                                            <p class="mt-1 text-sm text-brand">{{ review.phan_hoi_tu_ncc }}</p>
                                        </div>

                                        <!-- Reply form -->
                                        <div v-else-if="replyingTo === review.id" class="mt-3">
                                            <div class="flex gap-2">
                                                <textarea
                                                    v-model="replyText"
                                                    rows="2"
                                                    placeholder="Nhập phản hồi..."
                                                    class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                                />
                                            </div>
                                            <div class="mt-2 flex justify-end gap-2">
                                                <button
                                                    type="button"
                                                    class="rounded-lg px-3 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-100"
                                                    @click="cancelReply"
                                                >
                                                    Hủy
                                                </button>
                                                <button
                                                    type="button"
                                                    :disabled="replyLoading || !replyText.trim()"
                                                    class="flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-orange-700 disabled:opacity-50"
                                                    @click="submitReply(review.id)"
                                                >
                                                    <Loader2 v-if="replyLoading" class="size-3 animate-spin" />
                                                    <Send v-else class="size-3" />
                                                    Gửi
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Reply button -->
                                        <button
                                            v-else-if="!review.phan_hoi_tu_ncc"
                                            class="mt-3 flex items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition-colors hover:bg-stone-50 hover:text-brand"
                                            @click="openReply(review.id)"
                                        >
                                            <MessageSquare class="size-3.5" /> Phản hồi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Empty state -->
                    <div v-if="reviews.data.length === 0" class="flex flex-col items-center rounded-2xl border border-stone-200 bg-white py-16 shadow-sm">
                        <Star class="mb-3 size-10 text-stone-300" />
                        <p class="text-sm font-medium text-stone-600">Chưa có đánh giá nào</p>
                        <p class="mt-1 text-xs text-stone-400">Đánh giá từ khách hàng sẽ hiển thị tại đây</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="reviews.last_page > 1" class="flex items-center justify-center gap-1">
                        <template v-for="link in reviews.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                                :class="link.active ? 'bg-orange-600 text-white' : 'text-stone-600 hover:bg-stone-100'"
                                v-html="link.label"
                                preserve-state
                            />
                            <span v-else class="rounded-lg px-3 py-1.5 text-xs text-stone-300" v-html="link.label" />
                        </template>
                    </div>
            </div>
        </div>
    </ProviderLayout>
</template>