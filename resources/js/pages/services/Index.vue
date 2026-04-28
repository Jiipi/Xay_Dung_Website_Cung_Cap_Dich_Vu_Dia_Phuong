<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowUpDown,
    BadgeCheck,
    CarFront,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
    Filter,
    Heart,
    Home,
    MapPin,
    Monitor,
    Paintbrush,
    Search,
    SlidersHorizontal,
    Snowflake,
    Star,
    TrendingUp,
    Wrench,
    X,
    Map as MapIcon,
    List
} from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
import LeafletMap from '@/components/ui/LeafletMap.vue';
import { useAnimations } from '@/composables/useAnimations';
import gsap from 'gsap'; // Added gsap import for direct usage

// ─── Animation System ────────────────────────────────────────────
const { animateStagger, createTimeline, trackTween } = useAnimations();

const serviceGridRef = ref<HTMLElement | null>(null);
const bannerRef = ref<HTMLElement | null>(null);

// Banner fade up immediately on load (no ScrollTrigger needed for topmost element)
onMounted(() => {
    if (typeof window === 'undefined') return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    
    // Slight delay to ensure DOM is ready
    setTimeout(() => {
        if (!bannerRef.value) return;
        const tw = gsap.fromTo(
            bannerRef.value,
            { opacity: 0, y: 30 },
            { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }
        );
        trackTween(tw);
    }, 100);
});

// Service cards stagger reveal on scroll
animateStagger(serviceGridRef, '.service-card-wrapper', {
    stagger: 0.05, // slightly faster stagger
    duration: 0.5,
    start: 'top 90%',
});

/* ──────────────────────────── Props from Controller ──────────────────────────── */
const props = withDefaults(
    defineProps<{
        services?: Array<any>;
        categories?: Array<{ value: string; label: string; count: number }>;
        locations?: Array<{ value: string; count: number }>;
        queryCategory?: string | null;
        querySearch?: string | null;
    }>(),
    {
        services: () => [],
        categories: () => [],
        locations: () => [],
        queryCategory: null,
        querySearch: null,
    },
);

/* ──────────────────────────── Icon mapping ──────────────────────────── */
const categoryIconMap: Record<string, any> = {
    'don-dep-ve-sinh': Paintbrush,
    'xay-dung-sua-chua': Wrench,
    'du-lich-di-chuyen': MapPin,
    'lam-dep-suc-khoe': Heart,
    'su-kien-giai-tri': Star,
};

/* ──────────────────────────── Filter State ──────────────────────────── */
const selectedCategory = ref('all');
const selectedCities = ref<string[]>([]);
const priceMax = ref(5000000);
const ratingFilter = ref(0);
const sortBy = ref('default');
const searchQuery = ref(props.querySearch ?? '');
const currentPage = ref(1);
const perPage = 12;
const showMobileFilter = ref(false);

const viewMode = ref<'grid' | 'map'>('grid'); // New state for Map vs Grid view

// Khởi tạo category từ query param
onMounted(() => {
    if (props.queryCategory) {
        selectedCategory.value = props.queryCategory;
    }
});

/* ──────────────────────────── Computed categories for sidebar ──────────────────────────── */
const sidebarCategories = computed(() => {
    const allItem = {
        value: 'all',
        label: 'Tất cả',
        icon: Search,
        count: props.services.length,
    };
    const items = props.categories.map((cat) => ({
        ...cat,
        icon: categoryIconMap[cat.value] || Search,
    }));
    return [allItem, ...items];
});

const sortOptions = [
    { value: 'default', label: 'Mặc định' },
    { value: 'price_asc', label: 'Giá: Thấp → Cao' },
    { value: 'price_desc', label: 'Giá: Cao → Thấp' },
    { value: 'rating', label: 'Đánh giá cao nhất' },
    { value: 'reviews', label: 'Nhiều đánh giá nhất' },
];

/* ──────────────────────────── Computed ──────────────────────────── */
const filteredServices = computed(() => {
    let result = props.services.filter((s: any) => {
        const matchCat = selectedCategory.value === 'all' || s.category === selectedCategory.value;
        const matchCity = selectedCities.value.length === 0 || selectedCities.value.some((c: string) => s.location.includes(c));
        const matchPrice = s.price <= priceMax.value;
        const matchRating = s.rating >= ratingFilter.value;
        const matchSearch = searchQuery.value === '' ||
            s.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            s.provider.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchCat && matchCity && matchPrice && matchRating && matchSearch;
    });

    // Sorting
    if (sortBy.value === 'price_asc') result = [...result].sort((a: any, b: any) => a.price - b.price);
    else if (sortBy.value === 'price_desc') result = [...result].sort((a: any, b: any) => b.price - a.price);
    else if (sortBy.value === 'rating') result = [...result].sort((a: any, b: any) => b.rating - a.rating);
    else if (sortBy.value === 'reviews') result = [...result].sort((a: any, b: any) => b.reviews - a.reviews);

    return result;
});

const totalPages = computed(() => Math.ceil(filteredServices.value.length / perPage));

const paginatedServices = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredServices.value.slice(start, start + perPage);
});

const paginationRange = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;
    const pages: (number | string)[] = [];
    if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');
        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
        if (current < total - 2) pages.push('...');
        pages.push(total);
    }
    return pages;
});

const activeFilterCount = computed(() => {
    let count = 0;
    if (selectedCategory.value !== 'all') count++;
    if (selectedCities.value.length > 0) count++;
    if (priceMax.value < 5000000) count++;
    if (ratingFilter.value > 0) count++;
    return count;
});

// Reset page on filter change
watch([selectedCategory, selectedCities, priceMax, ratingFilter, sortBy, searchQuery], () => {
    currentPage.value = 1;
});

/* ──────────────────────────── Helpers ──────────────────────────── */
const formatVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

const formatShortVND = (value: number) => {
    if (value >= 1000000) return `${(value / 1000000).toFixed(1)}tr`;
    if (value >= 1000) return `${(value / 1000).toFixed(0)}k`;
    return `${value}đ`;
};

const resetFilters = () => {
    selectedCategory.value = 'all';
    selectedCities.value = [];
    priceMax.value = 5000000;
    ratingFilter.value = 0;
    searchQuery.value = '';
    sortBy.value = 'default';
};

const toggleCity = (city: string) => {
    const idx = selectedCities.value.indexOf(city);
    if (idx === -1) selectedCities.value.push(city);
    else selectedCities.value.splice(idx, 1);
};

const badgeColor = (badge: string | null) => {
    if (!badge) return '';
    const map: Record<string, string> = {
        'Phổ biến': 'bg-brand-surface text-brand border-brand',
        'Top đánh giá': 'bg-amber-100 text-amber-700 border-amber-200',
        'Best seller': 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Giá tốt': 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Ưu đãi': 'bg-rose-100 text-rose-700 border-rose-200',
        'Mới': 'bg-violet-100 text-violet-700 border-violet-200',
        'Chất lượng': 'bg-indigo-100 text-indigo-700 border-indigo-200',
    };
    return map[badge] ?? 'bg-stone-100 text-stone-700 border-stone-200';
};
</script>

<template>
    <Head title="Khám phá dịch vụ — Dalat Services" />

    <MarketplaceLayout>
        <!-- ===== Banner ===== -->
        <section ref="bannerRef" class="relative overflow-hidden bg-gradient-to-br from-stone-900 via-emerald-950 to-stone-900">
            <div class="absolute inset-0 bg-[url('https://picsum.photos/seed/dalat-banner/1600/400')] bg-cover bg-center opacity-20"></div>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-900/80 via-transparent to-stone-900/40"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-6 flex items-center gap-2 text-sm text-white/60">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-white">
                        <Home class="size-3.5" /> Trang chủ
                    </Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-white">Dịch vụ</span>
                </nav>

                <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Dịch vụ tại <span class="bg-gradient-to-r from-emerald-300 to-cyan-300 bg-clip-text text-transparent">Đà Lạt</span>
                </h1>
                <p class="mt-3 max-w-xl text-base leading-relaxed text-stone-300">
                    Khám phá hàng trăm dịch vụ uy tín — từ du lịch, sửa chữa đến chăm sóc nhà cửa — với giá minh bạch và đánh giá thật từ cộng đồng.
                </p>

                <!-- Quick search bar in banner -->
                <div class="mt-6 max-w-lg">
                    <div class="flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2.5 backdrop-blur-md transition focus-within:border-white/40 focus-within:bg-white/15">
                        <Search class="size-5 shrink-0 text-white/50" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Tìm kiếm dịch vụ, thợ, tour..."
                            class="w-full bg-transparent text-sm text-white placeholder-white/40 outline-none"
                        />
                        <button v-if="searchQuery" @click="searchQuery = ''" class="shrink-0 text-white/40 transition hover:text-white">
                            <X class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Main Content ===== -->
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 relative">

            <!-- Mobile: Filter toggle button -->
            <div class="mb-4 flex items-center justify-between md:hidden">
                <button
                    @click="showMobileFilter = true"
                    class="inline-flex items-center gap-2 rounded-2xl border border-stone-300 bg-white px-5 py-3 text-sm font-semibold text-stone-700 shadow-sm transition active:scale-95"
                >
                    <SlidersHorizontal class="size-4" />
                    Bộ lọc
                    <span v-if="activeFilterCount > 0" class="flex size-5 items-center justify-center rounded-full bg-brand text-[10px] font-bold text-white">{{ activeFilterCount }}</span>
                </button>
                <select v-model="sortBy" class="rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-700 outline-none focus:border-brand">
                    <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
            </div>

            <div class="flex gap-8">

                <!-- ===== Sidebar (Desktop) ===== -->
                <aside class="hidden w-72 shrink-0 md:block">
                    <div class="sticky top-24 space-y-1">
                        <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                                    <Filter class="size-5 text-brand" /> Bộ lọc
                                </h2>
                                <button
                                    v-if="activeFilterCount > 0"
                                    @click="resetFilters"
                                    class="text-xs font-medium text-brand transition hover:text-brand"
                                >
                                    Đặt lại
                                </button>
                            </div>

                            <!-- Category -->
                            <div class="mb-6">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Danh mục</h3>
                                <div class="space-y-1">
                                    <label
                                        v-for="cat in sidebarCategories"
                                        :key="cat.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 transition"
                                        :class="selectedCategory === cat.value ? 'bg-brand-surface text-brand' : 'text-stone-600 hover:bg-stone-50'"
                                    >
                                        <input v-model="selectedCategory" type="radio" name="category" :value="cat.value" class="sr-only" />
                                        <component :is="cat.icon" class="size-4 shrink-0" />
                                        <span class="flex-1 text-sm font-medium">{{ cat.label }}</span>
                                        <span class="text-xs text-stone-400">({{ cat.count }})</span>
                                    </label>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="mb-6">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Khu vực</h3>
                                <div class="space-y-1">
                                    <label
                                        v-for="city in locations"
                                        :key="city.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-stone-50"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="selectedCities.includes(city.value)"
                                            @change="toggleCity(city.value)"
                                            class="size-4 rounded border-stone-300 text-brand focus:ring-brand/30"
                                        />
                                        <span class="flex-1 text-sm text-stone-700">{{ city.value }}</span>
                                        <span class="text-xs text-stone-400">({{ city.count }})</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-6">
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Khoảng giá</h3>
                                <input
                                    v-model.number="priceMax"
                                    type="range"
                                    min="0"
                                    max="5000000"
                                    step="50000"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-stone-200 accent-emerald-600"
                                />
                                <div class="mt-2 flex items-center justify-between text-xs text-stone-500">
                                    <span>0đ</span>
                                    <span class="rounded-lg bg-brand-surface px-2 py-1 font-bold text-brand">{{ formatShortVND(priceMax) }}</span>
                                    <span>5tr</span>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div>
                                <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Đánh giá</h3>
                                <div class="space-y-1">
                                    <label
                                        v-for="r in [0, 3, 4, 4.5]"
                                        :key="r"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 transition"
                                        :class="ratingFilter === r ? 'bg-amber-50 text-amber-700' : 'text-stone-600 hover:bg-stone-50'"
                                    >
                                        <input v-model.number="ratingFilter" type="radio" name="rating" :value="r" class="sr-only" />
                                        <template v-if="r === 0">
                                            <span class="text-sm font-medium">Tất cả</span>
                                        </template>
                                        <template v-else>
                                            <span class="flex items-center gap-1 text-sm font-medium">
                                                Từ {{ r }}
                                                <Star class="size-3.5 fill-amber-400 text-amber-400" />
                                                trở lên
                                            </span>
                                        </template>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- ===== Mobile Sidebar Drawer ===== -->
                <Teleport to="body">
                    <Transition name="drawer">
                        <div v-if="showMobileFilter" class="fixed inset-0 z-[100] md:hidden">
                            <!-- Backdrop -->
                            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showMobileFilter = false"></div>

                            <!-- Drawer Panel -->
                            <div class="absolute bottom-0 left-0 right-0 max-h-[85vh] overflow-y-auto rounded-t-[2rem] bg-white px-6 pb-8 pt-4 shadow-2xl">
                                <!-- Handle bar -->
                                <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-stone-300"></div>

                                <div class="mb-6 flex items-center justify-between">
                                    <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                                        <Filter class="size-5 text-brand" /> Bộ lọc
                                    </h2>
                                    <button @click="showMobileFilter = false" class="rounded-full p-2 text-stone-400 transition hover:bg-stone-100 hover:text-stone-600">
                                        <X class="size-5" />
                                    </button>
                                </div>

                                <!-- Category -->
                                <div class="mb-6">
                                    <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Danh mục</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="cat in sidebarCategories"
                                            :key="cat.value"
                                            @click="selectedCategory = cat.value"
                                            class="rounded-full border px-4 py-2 text-sm font-medium transition"
                                            :class="selectedCategory === cat.value
                                                ? 'border-brand bg-brand-surface text-brand'
                                                : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'"
                                        >
                                            {{ cat.label }}
                                        </button>
                                    </div>
                                </div>

                                <!-- City -->
                                <div class="mb-6">
                                    <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Khu vực</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="city in locations"
                                            :key="city.value"
                                            @click="toggleCity(city.value)"
                                            class="rounded-full border px-4 py-2 text-sm font-medium transition"
                                            :class="selectedCities.includes(city.value)
                                                ? 'border-brand bg-brand-surface text-brand'
                                                : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'"
                                        >
                                            {{ city.value }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="mb-6">
                                    <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Khoảng giá</h3>
                                    <input v-model.number="priceMax" type="range" min="0" max="5000000" step="50000" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-stone-200 accent-emerald-600" />
                                    <div class="mt-2 flex items-center justify-between text-xs text-stone-500">
                                        <span>0đ</span>
                                        <span class="rounded-lg bg-brand-surface px-2 py-1 font-bold text-brand">{{ formatShortVND(priceMax) }}</span>
                                        <span>5tr</span>
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="mb-8">
                                    <h3 class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Đánh giá</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="r in [0, 3, 4, 4.5]"
                                            :key="r"
                                            @click="ratingFilter = r"
                                            class="rounded-full border px-4 py-2 text-sm font-medium transition"
                                            :class="ratingFilter === r
                                                ? 'border-amber-300 bg-amber-50 text-amber-700'
                                                : 'border-stone-200 bg-white text-stone-600'"
                                        >
                                            <template v-if="r === 0">Tất cả</template>
                                            <template v-else>{{ r }}★+</template>
                                        </button>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex gap-3">
                                    <button
                                        @click="resetFilters"
                                        class="flex-1 rounded-2xl border border-stone-300 px-5 py-3.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50"
                                    >
                                        Đặt lại
                                    </button>
                                    <button
                                        @click="showMobileFilter = false"
                                        class="flex-1 rounded-2xl bg-brand px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-brand"
                                    >
                                        Áp dụng ({{ filteredServices.length }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </Teleport>

                <!-- ===== Main Content Area ===== -->
                <main class="min-w-0 flex-1 relative pb-20">
                    <!-- Toolbar -->
                    <div class="mb-6 hidden items-center justify-between md:flex">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold tracking-tight text-stone-900">
                                <template v-if="selectedCategory === 'all'">Tất cả dịch vụ</template>
                                <template v-else>{{ sidebarCategories.find(c => c.value === selectedCategory)?.label }}</template>
                            </h2>
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-500">{{ filteredServices.length }} kết quả</span>
                        </div>

                        <!-- Sort -->
                        <div class="flex items-center gap-2">
                            <ArrowUpDown class="size-4 text-stone-400" />
                            <span class="text-sm text-stone-500">Sắp xếp:</span>
                            <select v-model="sortBy" class="rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm font-medium text-stone-700 outline-none transition focus:border-brand">
                                <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active filter tags -->
                    <div v-if="activeFilterCount > 0" class="mb-5 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-stone-400">Đang lọc:</span>
                        <span v-if="selectedCategory !== 'all'" class="inline-flex items-center gap-1 rounded-full border border-brand bg-brand-surface px-3 py-1 text-xs font-medium text-brand">
                            {{ sidebarCategories.find(c => c.value === selectedCategory)?.label }}
                            <button @click="selectedCategory = 'all'" class="ml-0.5 text-brand hover:text-brand"><X class="size-3" /></button>
                        </span>
                        <span v-for="city in selectedCities" :key="city" class="inline-flex items-center gap-1 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-medium text-teal-700">
                            {{ city }}
                            <button @click="toggleCity(city)" class="ml-0.5 text-teal-400 hover:text-teal-600"><X class="size-3" /></button>
                        </span>
                        <span v-if="priceMax < 5000000" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                            ≤ {{ formatShortVND(priceMax) }}
                            <button @click="priceMax = 5000000" class="ml-0.5 text-emerald-400 hover:text-emerald-600"><X class="size-3" /></button>
                        </span>
                        <span v-if="ratingFilter > 0" class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                            {{ ratingFilter }}★+
                            <button @click="ratingFilter = 0" class="ml-0.5 text-amber-400 hover:text-amber-600"><X class="size-3" /></button>
                        </span>
                        <button @click="resetFilters" class="text-xs font-medium text-stone-400 transition hover:text-stone-600">Xóa tất cả</button>
                    </div>

                    <!-- Toggle Map/Grid Button (Fixed at Bottom Center) -->
                    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
                        <button @click="viewMode = viewMode === 'grid' ? 'map' : 'grid'"
                            class="btn flex items-center gap-2 justify-center bg-stone-900 text-white font-semibold text-sm px-6 py-3 rounded-full shadow-2xl"
                            style="box-shadow: 0 8px 30px rgba(0,0,0,0.3);">
                            <component :is="viewMode === 'grid' ? MapIcon : List" class="size-4" />
                            {{ viewMode === 'grid' ? 'Hiển thị bản đồ' : 'Hiển thị danh sách' }}
                        </button>
                    </div>

                    <!-- Map vs Grid View toggle wrapper -->
                    <div>
                        <div v-show="viewMode === 'grid'">
                            <!-- ===== Service Grid ===== -->
                            <div v-if="paginatedServices.length > 0" ref="serviceGridRef" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                                <div
                                    v-for="service in paginatedServices"
                                    :key="service.id"
                                    class="service-card-wrapper"
                                >
                                    <Link
                                        :href="`/services/${service.id}`"
                                        class="service-card group flex h-full flex-col overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-stone-200/50"
                                    >
                                        <!-- Image -->
                                        <div class="relative h-48 overflow-hidden sm:h-44">
                                            <img
                                                :src="service.image"
                                                :alt="service.title"
                                                class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                loading="lazy"
                                                referrerpolicy="no-referrer"
                                            />
                                            <!-- Gradient overlay -->
                                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

                                            <!-- Badge -->
                                            <div
                                                v-if="service.badge"
                                                class="absolute left-3 top-3 rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider backdrop-blur-md"
                                                :class="badgeColor(service.badge)"
                                            >
                                                {{ service.badge }}
                                            </div>

                                            <!-- Rating overlay -->
                                            <div class="absolute bottom-3 right-3 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1.5 text-xs font-bold text-stone-900 shadow-sm backdrop-blur">
                                                <Star class="size-3.5 fill-amber-400 text-amber-400" />
                                                {{ service.rating }}
                                                <span class="font-normal text-stone-400">({{ service.reviews }})</span>
                                            </div>

                                            <!-- Favorite -->
                                            <button class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/80 text-stone-400 shadow-sm backdrop-blur transition hover:bg-white hover:text-rose-500" @click.prevent>
                                                <Heart class="size-4" />
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex flex-1 flex-col p-5">
                                            <div class="mb-2 flex items-center gap-1.5 text-xs text-stone-500">
                                                <MapPin class="size-3.5 text-stone-400" />
                                                {{ service.location }}
                                            </div>
                                            <h3 class="mb-1 line-clamp-2 text-sm font-bold leading-snug text-stone-900 transition-colors group-hover:text-brand">
                                                {{ service.title }}
                                            </h3>
                                            <p class="mb-4 flex items-center gap-1.5 text-xs text-stone-400">
                                                <BadgeCheck class="size-3.5 text-brand" />
                                                {{ service.provider }}
                                            </p>

                                            <!-- Price & CTA -->
                                            <div class="mt-auto flex flex-col border-t border-stone-100 pt-4">
                                                <div class="mb-3 flex flex-wrap items-baseline gap-1">
                                                    <span class="text-lg font-black text-brand">{{ formatVND(service.price) }}</span>
                                                    <span v-if="service.unit" class="text-xs font-medium text-stone-500">/ {{ service.unit }}</span>
                                                </div>
                                                <div class="flex items-center justify-between mt-auto">
                                                    <p class="text-[11px] text-stone-400">Hủy miễn phí trước 2h</p>
                                                    <button class="btn rounded-full bg-brand px-5 py-2 text-xs font-bold text-white shadow-sm transition group-hover:bg-brand group-hover:shadow-md hover:scale-105 active:scale-95">
                                                        ĐẶT NGAY
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                            </div>

                            <!-- ===== Empty State ===== -->
                            <div v-else class="flex flex-col items-center justify-center rounded-[2rem] border border-stone-200 bg-white py-20">
                                <div class="flex size-20 items-center justify-center rounded-full bg-stone-100">
                                    <Search class="size-8 text-stone-300" />
                                </div>
                                <h3 class="mt-5 text-lg font-bold text-stone-900">Không tìm thấy dịch vụ nào</h3>
                                <p class="mt-2 text-sm text-stone-500">Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                                <button @click="resetFilters" class="mt-6 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand">
                                    Đặt lại bộ lọc
                                </button>
                            </div>

                            <!-- ===== Pagination ===== -->
                            <div v-if="totalPages > 1" class="mt-10 flex items-center justify-center gap-1.5">
                                <!-- First -->
                                <button
                                    @click="currentPage = 1"
                                    :disabled="currentPage === 1"
                                    class="flex size-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-500 transition hover:bg-stone-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <ChevronsLeft class="size-4" />
                                </button>
                                <!-- Prev -->
                                <button
                                    @click="currentPage = Math.max(1, currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="flex size-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-500 transition hover:bg-stone-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <ChevronLeft class="size-4" />
                                </button>

                                <!-- Page numbers -->
                                <template v-for="page in paginationRange" :key="page">
                                    <span v-if="page === '...'" class="flex size-10 items-center justify-center text-sm text-stone-400">…</span>
                                    <button
                                        v-else
                                        @click="currentPage = page as number"
                                        class="flex size-10 items-center justify-center rounded-xl border text-sm font-semibold transition"
                                        :class="currentPage === page
                                            ? 'border-brand bg-brand text-white shadow-sm shadow-brand'
                                            : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'"
                                    >
                                        {{ page }}
                                    </button>
                                </template>

                                <!-- Next -->
                                <button
                                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="flex size-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-500 transition hover:bg-stone-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <ChevronRight class="size-4" />
                                </button>
                                <!-- Last -->
                                <button
                                    @click="currentPage = totalPages"
                                    :disabled="currentPage === totalPages"
                                    class="flex size-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-500 transition hover:bg-stone-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <ChevronsRight class="size-4" />
                                </button>
                            </div>

                            <!-- Page info -->
                            <div v-if="totalPages > 1" class="mt-3 text-center text-xs text-stone-400">
                                Trang {{ currentPage }} / {{ totalPages }} — Hiển thị {{ paginatedServices.length }} / {{ filteredServices.length }} dịch vụ
                            </div>
                        </div>

                        <!-- Full height map view -->
                        <div v-if="viewMode === 'map'" class="h-[650px] w-full rounded-[2rem] overflow-hidden border border-stone-200 shadow-sm relative z-0">
                            <LeafletMap :services="filteredServices" />
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </MarketplaceLayout>
</template>

<style scoped>
/* Drawer animation */
.drawer-enter-active,
.drawer-leave-active {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-enter-active > div:last-child,
.drawer-leave-active > div:last-child {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}
.drawer-enter-from > div:last-child,
.drawer-leave-to > div:last-child {
    transform: translateY(100%);
}

/* Service card animation handled by GSAP ScrollTrigger in useAnimations */

/* Range input thumb styling */
input[type='range']::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #0284c7;
    cursor: pointer;
    box-shadow: 0 1px 4px rgb(0 0 0 / 0.2);
}
input[type='range']::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #0284c7;
    cursor: pointer;
    border: none;
    box-shadow: 0 1px 4px rgb(0 0 0 / 0.2);
}
</style>
