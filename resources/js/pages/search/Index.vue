<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Filter, Heart, MapPin, Search, SearchX, Star } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

const props = withDefaults(
    defineProps<{
        query?: string;
        results?: any[];
        total?: number;
    }>(),
    { query: '', results: () => [], total: 0 },
);

const searchInput = ref(props.query);
const selectedCategory = ref('all');
const selectedLocation = ref('all');
const ratingFilter = ref(0);
const sortBy = ref('relevance');
const localResults = ref([...props.results]);
const syncingFavoriteIds = ref<number[]>([]);

const formatVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

let debounceTimer: ReturnType<typeof setTimeout>;
watch(searchInput, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/search', { q: val }, { preserveState: true, replace: true });
    }, 400);
});

watch(
    () => props.results,
    (newVal) => {
        localResults.value = [...newVal];
        selectedCategory.value = 'all';
        selectedLocation.value = 'all';
        ratingFilter.value = 0;
        sortBy.value = 'relevance';
    },
);

const categoryOptions = computed(() => {
    const categories = Array.from(new Set(localResults.value.map((item) => item.category).filter(Boolean)));
    return ['all', ...categories];
});

const locationOptions = computed(() => {
    const locations = Array.from(new Set(localResults.value.map((item) => item.location).filter(Boolean)));
    return ['all', ...locations];
});

const filteredResults = computed(() => {
    let results = [...localResults.value];

    if (selectedCategory.value !== 'all') {
        results = results.filter((item) => item.category === selectedCategory.value);
    }

    if (selectedLocation.value !== 'all') {
        results = results.filter((item) => item.location === selectedLocation.value);
    }

    if (ratingFilter.value > 0) {
        results = results.filter((item) => Number(item.rating || 0) >= ratingFilter.value);
    }

    if (sortBy.value === 'price_asc') {
        results.sort((a, b) => Number(a.price || 0) - Number(b.price || 0));
    } else if (sortBy.value === 'price_desc') {
        results.sort((a, b) => Number(b.price || 0) - Number(a.price || 0));
    } else if (sortBy.value === 'rating') {
        results.sort((a, b) => Number(b.rating || 0) - Number(a.rating || 0));
    }

    return results;
});

const activeFilterCount = computed(() => {
    let count = 0;
    if (selectedCategory.value !== 'all') count++;
    if (selectedLocation.value !== 'all') count++;
    if (ratingFilter.value > 0) count++;
    if (sortBy.value !== 'relevance') count++;
    return count;
});

function resetFilters() {
    selectedCategory.value = 'all';
    selectedLocation.value = 'all';
    ratingFilter.value = 0;
    sortBy.value = 'relevance';
}

const toggleFavorite = async (item: any) => {
    if (syncingFavoriteIds.value.includes(item.id)) return;

    item.is_favorited = !item.is_favorited;
    syncingFavoriteIds.value.push(item.id);

    try {
        const response = await fetch('/customer/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ dich_vu_id: item.id }),
        });

        if (!response.ok) {
            item.is_favorited = !item.is_favorited;
        }
    } catch (e) {
        item.is_favorited = !item.is_favorited;
    } finally {
        syncingFavoriteIds.value = syncingFavoriteIds.value.filter((id) => id !== item.id);
    }
};
</script>

<template>
    <Head :title="`${query ? 'Kết quả: ' + query : 'Tìm kiếm'} — Dalat Services`" />

    <MarketplaceLayout>
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <label class="mb-2 block text-xs font-medium uppercase tracking-[0.22em] text-stone-500">Tìm dịch vụ</label>
                <div class="relative">
                    <Search class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                    <input
                        v-model="searchInput"
                        type="text"
                        placeholder="Nhập tên dịch vụ, nhà cung cấp, từ khóa..."
                        class="w-full rounded-2xl border border-stone-200 bg-stone-50 py-3.5 pl-12 pr-4 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                    />
                </div>
            </div>

            <div class="mt-8 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-stone-950">
                        {{ query ? `Kết quả cho "${query}"` : 'Tìm kiếm dịch vụ' }}
                    </h1>
                    <p class="mt-1 text-sm text-stone-500">{{ filteredResults.length }} / {{ total }} dịch vụ phù hợp</p>
                </div>
                <span v-if="query" class="inline-flex items-center gap-2 rounded-full bg-brand-surface px-4 py-2 text-sm font-medium text-brand">
                    <Filter class="size-4" /> {{ activeFilterCount > 0 ? `${activeFilterCount} bộ lọc` : 'Kết quả tìm kiếm' }}
                </span>
            </div>

            <div class="mt-5 grid gap-3 rounded-[1.5rem] border border-stone-200 bg-white p-4 shadow-sm md:grid-cols-4">
                <label class="space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Danh mục</span>
                    <select v-model="selectedCategory" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-700 outline-none transition focus:border-brand focus:bg-white">
                        <option value="all">Tất cả danh mục</option>
                        <option v-for="category in categoryOptions.filter((value) => value !== 'all')" :key="category" :value="category">{{ category }}</option>
                    </select>
                </label>
                <label class="space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Khu vực</span>
                    <select v-model="selectedLocation" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-700 outline-none transition focus:border-brand focus:bg-white">
                        <option value="all">Tất cả khu vực</option>
                        <option v-for="location in locationOptions.filter((value) => value !== 'all')" :key="location" :value="location">{{ location }}</option>
                    </select>
                </label>
                <label class="space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Đánh giá</span>
                    <select v-model.number="ratingFilter" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-700 outline-none transition focus:border-brand focus:bg-white">
                        <option :value="0">Không lọc</option>
                        <option :value="4">Từ 4 sao</option>
                        <option :value="4.5">Từ 4.5 sao</option>
                    </select>
                </label>
                <label class="space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Sắp xếp</span>
                    <div class="flex gap-2">
                        <select v-model="sortBy" class="min-w-0 flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-700 outline-none transition focus:border-brand focus:bg-white">
                            <option value="relevance">Liên quan</option>
                            <option value="rating">Đánh giá cao nhất</option>
                            <option value="price_asc">Giá thấp đến cao</option>
                            <option value="price_desc">Giá cao đến thấp</option>
                        </select>
                        <button
                            type="button"
                            class="shrink-0 rounded-xl border border-stone-200 px-3 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50"
                            @click="resetFilters"
                        >
                            Đặt lại
                        </button>
                    </div>
                </label>
            </div>

            <div v-if="filteredResults.length > 0" class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <Link
                    v-for="item in filteredResults"
                    :key="item.id"
                    :href="`/services/${item.id}`"
                    class="group overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="relative h-44 w-full bg-stone-100">
                        <img v-if="item.image" :src="item.image" :alt="item.title" class="h-full w-full object-cover" referrerpolicy="no-referrer" />
                        <button
                            class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur-sm transition hover:scale-110 active:scale-95 disabled:cursor-wait disabled:opacity-60"
                            :class="item.is_favorited ? 'text-red-500' : 'text-stone-400 hover:text-red-500'"
                            :disabled="syncingFavoriteIds.includes(item.id)"
                            @click.prevent="toggleFavorite(item)"
                        >
                            <Heart class="size-4" :class="item.is_favorited ? 'fill-current text-red-500' : ''" />
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span v-if="item.category" class="text-xs font-bold uppercase tracking-[0.18em]" style="color: var(--dl-brand);">{{ item.category }}</span>
                            <span v-if="item.rating > 0" class="inline-flex items-center gap-1 text-sm font-semibold text-amber-600">
                                <Star class="size-4 fill-amber-400 text-amber-400" /> {{ item.rating }}
                                <span class="text-stone-400">({{ item.reviews }})</span>
                            </span>
                        </div>
                        <h2 class="mt-3 text-lg font-bold text-stone-950 transition-colors group-hover:text-brand">{{ item.title }}</h2>
                        <p v-if="item.provider" class="mt-1.5 text-sm text-stone-500">{{ item.provider }}</p>
                        <div v-if="item.location" class="mt-2 flex items-center gap-1.5 text-sm text-stone-400">
                            <MapPin class="size-3.5" /> {{ item.location }}
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4">
                            <span class="text-lg font-black" style="color: var(--dl-brand);">{{ formatVND(item.price) }}</span>
                            <span class="rounded-full bg-stone-950 px-4 py-2 text-xs font-semibold text-white transition group-hover:bg-stone-800">Xem chi tiết</span>
                        </div>
                    </div>
                </Link>
            </div>

            <div v-else class="mt-10 flex flex-col items-center justify-center rounded-[2rem] border border-dashed border-stone-300 bg-stone-50 px-8 py-16 text-center">
                <SearchX class="size-16 text-stone-300" />
                <h2 class="mt-6 text-xl font-bold text-stone-900">
                    {{ query ? 'Không tìm thấy kết quả phù hợp' : 'Bắt đầu tìm kiếm' }}
                </h2>
                <p class="mt-2 max-w-md text-sm text-stone-500">
                    {{ query
                        ? `Không có dịch vụ nào phù hợp với "${query}" trong bộ lọc hiện tại. Thử nới lỏng tiêu chí hoặc dùng từ khóa khác.`
                        : 'Nhập tên dịch vụ, nhà cung cấp hoặc từ khóa để tìm kiếm nhanh trong hệ thống.'
                    }}
                </p>
                <button
                    v-if="activeFilterCount > 0"
                    type="button"
                    class="mt-4 rounded-full border border-stone-200 px-5 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-100"
                    @click="resetFilters"
                >
                    Xóa bộ lọc
                </button>
                <Link href="/services" class="mt-6 inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-bold text-white" style="background: var(--dl-brand);">
                    Khám phá dịch vụ
                </Link>
            </div>
        </div>
    </MarketplaceLayout>
</template>
