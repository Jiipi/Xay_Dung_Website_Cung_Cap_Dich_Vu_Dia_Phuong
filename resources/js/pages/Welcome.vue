<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Bot,
    CarFront,
    Clock3,
    Heart,
    Map,
    MapPin,
    Monitor,
    Paintbrush,
    Search,
    Shield,
    Snowflake,
    Sparkles,
    Star,
    Timer,
    TrendingUp,
    Wrench,
    Zap,
    BadgeCheck,
    MessageSquareQuote,
    ChevronRight,
} from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
import AnimatedCounter from '@/components/ui/AnimatedCounter.vue';
import { useAnimations } from '@/composables/useAnimations';

// ─── Animation System ────────────────────────────────────────────
const {
    animateHeroEntrance,
    animateStagger,
    animateParallax,
    animateScaleIn,
    animateFadeUp,
} = useAnimations();

// Template refs for hero entrance
const heroBadgeRef = ref<HTMLElement | null>(null);
const heroHeadlineRef = ref<HTMLElement | null>(null);
const heroDescRef = ref<HTMLElement | null>(null);
const heroSearchRef = ref<HTMLElement | null>(null);
const heroStatsRef = ref<HTMLElement | null>(null);
const heroIllustrationRef = ref<HTMLElement | null>(null);
const heroOrb1Ref = ref<HTMLElement | null>(null);
const heroOrb2Ref = ref<HTMLElement | null>(null);

// Section refs for scroll animations
const categorySectionRef = ref<HTMLElement | null>(null);
const serviceSectionRef = ref<HTMLElement | null>(null);
const aiBannerRef = ref<HTMLElement | null>(null);
const trustSectionRef = ref<HTMLElement | null>(null);
const reviewSectionRef = ref<HTMLElement | null>(null);
const useCaseSectionRef = ref<HTMLElement | null>(null);

// ─── Hero Entrance Animation ─────────────────────────────────────
animateHeroEntrance({
    badge: heroBadgeRef,
    headline: heroHeadlineRef,
    description: heroDescRef,
    searchBar: heroSearchRef,
    stats: heroStatsRef,
    illustration: heroIllustrationRef,
});

// ─── Parallax Effects ────────────────────────────────────────────
animateParallax(heroOrb1Ref, { speed: -0.4 });
animateParallax(heroOrb2Ref, { speed: 0.3 });

// ─── Scroll Reveals ──────────────────────────────────────────────
// Categories
animateStagger(categorySectionRef, '.cat-card', {
    stagger: 0.06,
    duration: 0.6,
    start: 'top 88%',
});

// Services
animateStagger(serviceSectionRef, '.service-card', {
    stagger: 0.1,
    duration: 0.7,
    start: 'top 85%',
});

// AI Banner
animateScaleIn(aiBannerRef, { duration: 0.9, start: 'top 88%' });

// Trust Cards
animateStagger(trustSectionRef, '.trust-card', {
    stagger: 0.1,
    duration: 0.7,
    start: 'top 85%',
});

// Review Cards
animateStagger(reviewSectionRef, '.review-card', {
    stagger: 0.12,
    duration: 0.7,
    start: 'top 85%',
});

// Use Cases
animateStagger(useCaseSectionRef, '.usecase-card', {
    stagger: 0.1,
    duration: 0.7,
    start: 'top 85%',
});

// ─── Data / Props ────────────────────────────────────────────────
const props = withDefaults(
    defineProps<{
        canRegister: boolean;
        categories?: Array<any>;
        featuredServices?: Array<any>;
        customerReviews?: Array<any>;
        stats?: Array<{ label: string; value: string }>;
    }>(),
    {
        canRegister: true,
        categories: () => [],
        featuredServices: () => [],
        customerReviews: () => [],
        stats: () => [],
    },
);

const iconMap: Record<string, any> = {
    Map, Wrench, Sparkles, CarFront, Bot, Search, Snowflake, Paintbrush, Monitor, Heart,
};

const processedCategories = computed(() => {
    if (props.categories && props.categories.length > 0) {
        return props.categories.map((c: any) => ({
            ...c,
            icon: iconMap[c.icon] || Search,
        }));
    }
    return [];
});

const processedServices = ref([...(props.featuredServices || [])]);

const toggleFavorite = async (service: any) => {
    service.is_favorited = !service.is_favorited;
    
    try {
        const response = await fetch('/customer/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ dich_vu_id: service.id }),
        });
        if (!response.ok) {
            // Revert if error
            service.is_favorited = !service.is_favorited;
        }
    } catch (e) {
        service.is_favorited = !service.is_favorited;
    }
};

const activeServiceTab = ref('popular');

const filteredServices = computed(() => {
    const services = processedServices.value;
    if (activeServiceTab.value === 'popular') {
        return [...services].sort((a: any, b: any) => b.reviewCount - a.reviewCount);
    }
    if (activeServiceTab.value === 'rating') {
        return [...services].sort((a: any, b: any) => parseFloat(b.rating) - parseFloat(a.rating));
    }
    return services;
});

const serviceTabs = [
    { key: 'popular', label: 'Phổ biến nhất' },
    { key: 'nearest', label: 'Gần bạn nhất' },
    { key: 'rating', label: 'Đánh giá cao' },
];

const heroStats = computed(() =>
    props.stats && props.stats.length > 0
        ? props.stats
        : [
            { label: 'Dịch vụ nổi bật', value: '50+' },
            { label: 'Nhà cung cấp', value: '20+' },
            { label: 'Đánh giá trung bình', value: '4.5★' },
        ],
);

const whyChooseUs = [
    { icon: Shield, title: 'Bảo hiểm dịch vụ', description: 'Mọi booking đều được bảo vệ. Hoàn tiền nếu dịch vụ không đạt cam kết.' },
    { icon: Timer, title: 'Đúng giờ 100%', description: 'Nhà cung cấp cam kết đến đúng giờ hẹn hoặc giảm giá cho lần sau.' },
    { icon: BadgeCheck, title: 'Giá minh bạch', description: 'Giá hiển thị rõ ràng trước khi đặt. Không phát sinh, không ẩn phí.' },
    { icon: Zap, title: 'Hoàn tiền nếu không hài lòng', description: 'Không hài lòng? Hoàn tiền 100% trong vòng 24h, không cần lý do.' },
];

const heroLines = ['Đặt lịch dịch vụ tại nhà', 'chỉ trong 60 giây.'];

const searchQuery = ref('');
const searchCategory = ref('');

function handleSearch() {
    if (searchQuery.value.trim()) {
        router.get('/services', { q: searchQuery.value, category: searchCategory.value });
    }
}

function handleHeroSearch() {
    handleSearch();
}
</script>

<template>
    <Head>
        <title>Dalat Services — Nền tảng kết nối dịch vụ địa phương</title>
        <meta name="description" content="Khám phá và đặt ngay +500 dịch vụ địa phương tại Đà Lạt: Sửa chữa, Du lịch, Thợ điện, Làm đẹp và hơn thế nữa. Nhanh chóng, minh bạch và chuyên nghiệp." />
        <meta property="og:title" content="Dalat Services — Nền tảng kết nối dịch vụ địa phương" />
        <meta property="og:description" content="Khám phá và đặt ngay +500 dịch vụ địa phương tại Đà Lạt: Sửa chữa, Du lịch, Thợ điện, Làm đẹp và hơn thế nữa." />
        <meta property="og:type" content="website" />
    </Head>

    <MarketplaceLayout>
        <main>
            <!-- ===== HERO SECTION ===== -->
            <section class="relative overflow-hidden border-b" style="background: linear-gradient(to bottom, white, rgba(216,243,220,0.3), var(--dl-warm-bg)); border-color: var(--dl-warm-border);">
                <!-- Orbs — now with parallax -->
                <div ref="heroOrb1Ref" class="hero-orb absolute top-20 -left-20 size-72 rounded-full blur-3xl" style="background: rgba(45,106,79,0.12);" />
                <div ref="heroOrb2Ref" class="hero-orb absolute -right-16 bottom-12 size-80 rounded-full blur-3xl" style="background: rgba(82,183,136,0.10);" />

                <div class="relative z-10 mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:px-8 lg:py-24">
                    <!-- Cột Trái -->
                    <div>
                        <!-- Badge -->
                        <div ref="heroBadgeRef" class="hero-el mb-6 inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-sm font-medium shadow-sm" style="border: 1px solid var(--dl-brand-surface); color: var(--dl-brand);">
                            <Sparkles class="size-4" />
                            Dịch vụ địa phương · chọn nhanh · đặt rõ ràng
                        </div>

                        <!-- Headline -->
                        <h1 ref="heroHeadlineRef" class="hero-el text-stone-950" style="font-family: 'Instrument Serif', serif; font-size: clamp(2.5rem, 6vw, 5rem); line-height: 1.1;">
                            Dịch vụ tại Đà Lạt<br>
                            <em style="font-style: italic; opacity: 0.85;">ngay trong tầm tay</em>
                        </h1>

                        <p ref="heroDescRef" class="hero-el mt-5 max-w-xl text-lg leading-relaxed text-stone-600">
                            Hơn <strong>500+</strong> thợ lành nghề, giá minh bạch, bảo hành tận tâm. Đặt lịch online, xác nhận ngay.
                        </p>

                        <!-- Hero Search Bar -->
                        <form ref="heroSearchRef" @submit.prevent="handleHeroSearch" class="hero-el mt-8 flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-3 shadow-lg shadow-stone-200/50 sm:flex-row sm:items-center sm:rounded-full sm:p-2">
                            <select v-model="searchCategory" class="hidden rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700 outline-none sm:block sm:w-40 sm:rounded-full sm:border-0 sm:bg-transparent sm:py-2">
                                <option value="">Tất cả</option>
                                <option v-for="cat in processedCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <div class="hidden h-6 w-px bg-stone-200 sm:block"></div>
                            <div class="flex flex-1 items-center gap-2 rounded-xl bg-stone-50 px-4 py-3 sm:rounded-full sm:bg-transparent sm:py-0">
                                <Search class="size-5 text-stone-400" />
                                <input v-model="searchQuery" type="text" placeholder="Nhập: sửa điện, thuê xe, tour..." class="flex-1 bg-transparent text-sm text-stone-700 placeholder-stone-400 outline-none" />
                            </div>
                            <button type="submit" class="btn rounded-xl px-8 py-3 text-sm font-bold text-white shadow-md transition hover:shadow-lg active:scale-[0.97] sm:rounded-full" style="background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light)); box-shadow: 0 4px 12px rgba(45,106,79,0.3);">
                                Tìm ngay
                            </button>
                        </form>

                        <!-- Stats -->
                        <div ref="heroStatsRef" class="hero-el mt-14 flex flex-wrap gap-10">
                            <AnimatedCounter
                                v-for="item in heroStats"
                                :key="item.label"
                                :target="item.value"
                                :label="item.label"
                                class="text-stone-950"
                            >
                                <template #suffix><span class="text-brand">{{ item.value.replace(/[0-9.]/g, '') }}</span></template>
                            </AnimatedCounter>
                        </div>
                    </div>

                    <!-- Cột Phải: AI Planner Illustration -->
                    <div ref="heroIllustrationRef" class="hero-el relative hidden lg:block">
                        <!-- Nền trang trí -->
                        <div class="absolute inset-0 -z-10 scale-105 rounded-[2.5rem] bg-gradient-to-tr from-emerald-100 to-indigo-50 opacity-60 rotate-3"></div>
                        <div class="absolute inset-0 -z-20 rounded-[2.5rem] bg-gradient-to-tr from-emerald-200 to-teal-100 opacity-40 -rotate-2"></div>
                        
                        <!-- Panel chính -->
                        <div class="relative overflow-hidden rounded-[2rem] border border-stone-200/60 bg-white/80 p-6 shadow-2xl shadow-emerald-900/10 backdrop-blur-xl">
                            <!-- Tiêu đề Panel -->
                            <div class="mb-5 flex items-center justify-between border-b border-stone-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 text-white shadow-md shadow-indigo-200">
                                        <Bot class="size-5" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-stone-900">AI Trợ Lý Booking</h3>
                                        <p class="text-xs font-medium text-emerald-600">Đang trực tuyến</p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <div class="size-2.5 rounded-full bg-stone-200"></div>
                                    <div class="size-2.5 rounded-full bg-stone-200"></div>
                                    <div class="size-2.5 rounded-full bg-stone-200"></div>
                                </div>
                            </div>

                            <!-- Chat Items -->
                            <div class="space-y-4">
                                <!-- User Message -->
                                <div class="chat-msg chat-msg--1 flex justify-end">
                                    <div class="rounded-2xl rounded-tr-sm bg-stone-100 px-4 py-3 text-[13px] text-stone-700 shadow-sm leading-relaxed max-w-[90%]">
                                        Tôi muốn thuê xe máy và tìm thợ sửa vòi nước quận 1 hôm nay. Ngân sách dưới 500k.
                                    </div>
                                </div>

                                <!-- Bot Process -->
                                <div class="chat-msg chat-msg--2 flex gap-3 px-1">
                                    <div class="mt-1 flex size-6 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                                        <Sparkles class="size-3 text-indigo-600" />
                                    </div>
                                    <div class="flex w-full flex-col gap-2">
                                        <p class="text-[13px] font-medium text-stone-500">Đang phân tích yêu cầu...</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-brand-surface/50 p-2.5">
                                                <CarFront class="size-4 text-brand" />
                                                <span class="text-xs font-medium text-stone-700">Thuê xe máy</span>
                                            </div>
                                            <div class="flex items-center gap-2 rounded-xl border border-amber-100 bg-amber-50/50 p-2.5">
                                                <Wrench class="size-4 text-amber-600" />
                                                <span class="text-xs font-medium text-stone-700">Sửa nước Q1</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bot Result -->
                                <div class="chat-msg chat-msg--3 flex gap-3 px-1 pt-2">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 text-white shadow-sm">
                                        <Bot class="size-4" />
                                    </div>
                                    <div class="w-full">
                                        <div class="rounded-2xl rounded-tl-sm border border-indigo-100/50 bg-gradient-to-br from-emerald-50 to-indigo-50 px-4 py-3 text-[13px] leading-relaxed text-stone-700 shadow-sm">
                                            Tôi tìm thấy <strong>2 dịch vụ</strong> hoàn toàn phù hợp có thể đáp ứng ngay lúc này:
                                            <div class="mt-3 space-y-2">
                                                <div class="flex items-center justify-between rounded-lg bg-white p-2 shadow-[0_2px_8px_-4px_rgba(0,0,0,0.1)]">
                                                    <div class="flex items-center gap-2">
                                                        <img src="https://picsum.photos/seed/motor/40/40" class="size-8 rounded object-cover" referrerpolicy="no-referrer" />
                                                        <div>
                                                            <p class="text-[11px] font-bold text-stone-800">Xe Máy Đà Lạt</p>
                                                            <p class="text-[10px] text-stone-500">120.000đ/ngày</p>
                                                        </div>
                                                    </div>
                                                    <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Còn xe</span>
                                                </div>
                                                <div class="flex items-center justify-between rounded-lg bg-white p-2 shadow-[0_2px_8px_-4px_rgba(0,0,0,0.1)]">
                                                    <div class="flex items-center gap-2">
                                                        <img src="https://picsum.photos/seed/plumb/40/40" class="size-8 rounded object-cover" referrerpolicy="no-referrer" />
                                                        <div>
                                                            <p class="text-[11px] font-bold text-stone-800">Thợ Nước 24H</p>
                                                            <p class="text-[10px] text-stone-500">200.000đ</p>
                                                        </div>
                                                    </div>
                                                    <span class="rounded bg-brand-surface px-2 py-0.5 text-[10px] font-bold text-brand">Tới trong 20p</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== DANH MỤC DỊCH VỤ - 8 Ô ===== -->
            <section id="danh-muc" ref="categorySectionRef" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em]" style="color: var(--dl-brand);">Danh mục dịch vụ</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-stone-950 sm:text-3xl">
                            Chọn nhanh nhóm dịch vụ bạn cần
                        </h3>
                    </div>
                    <Link href="/categories" class="hidden items-center gap-1 text-sm font-medium transition sm:flex" style="color: var(--dl-brand);">
                        Xem tất cả <ChevronRight class="size-4" />
                    </Link>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-4 xl:grid-cols-8">
                    <Link
                        v-for="category in processedCategories"
                        :key="category.id"
                        :href="`/services?category=${category.id}`"
                        class="cat-card group flex flex-col items-center rounded-2xl border border-stone-200 bg-white p-5 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-stone-300 hover:shadow-md"
                    >
                        <div :class="['flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br transition-transform duration-300 group-hover:scale-110', category.accent]">
                            <component :is="category.icon" class="size-6" />
                        </div>
                        <p class="mt-3 text-sm font-semibold text-stone-800">{{ category.name }}</p>
                        <p v-if="category.service_count" class="mt-1 text-xs text-stone-400">{{ category.service_count }} dịch vụ</p>
                    </Link>
                </div>

                <div class="mt-6 text-center sm:hidden">
                    <Link href="/categories" class="inline-flex items-center gap-1 text-sm font-medium" style="color: var(--dl-brand);">
                        Xem tất cả danh mục <ChevronRight class="size-4" />
                    </Link>
                </div>
            </section>

            <!-- ===== DỊCH VỤ NỔI BẬT - VERTICAL CARDS ===== -->
            <section id="dich-vu" ref="serviceSectionRef" class="border-y border-stone-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em]" style="color: var(--dl-brand);">Dịch vụ nổi bật</p>
                            <h3 class="mt-2 text-2xl font-bold tracking-tight text-stone-950 sm:text-3xl">
                                Được đặt nhiều nhất gần đây
                            </h3>
                        </div>
                        <!-- Tabs -->
                        <div class="flex gap-1 rounded-full border border-stone-200 bg-stone-50 p-1">
                            <button
                                v-for="tab in serviceTabs"
                                :key="tab.key"
                                @click="activeServiceTab = tab.key"
                                :class="[
                                    'rounded-full px-4 py-2 text-sm font-medium transition',
                                    activeServiceTab === tab.key
                                        ? 'bg-white text-stone-900 shadow-sm'
                                        : 'text-stone-500 hover:text-stone-700',
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Service Cards Grid -->
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <article
                            v-for="service in filteredServices"
                            :key="service.id"
                            class="service-card group overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-stone-200/50"
                        >
                            <!-- Image -->
                            <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                                <img
                                    :src="service.image"
                                    :alt="service.title"
                                    class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                    referrerpolicy="no-referrer"
                                />
                                <!-- Badge -->
                                <span v-if="service.badge" class="absolute left-3 top-3 rounded-lg bg-white/90 px-2.5 py-1 text-xs font-bold shadow-sm backdrop-blur-sm" style="color: var(--dl-brand);">
                                    {{ service.badge }}
                                </span>
                                <!-- Favorite Toggle -->
                                <button
                                    @click.prevent="toggleFavorite(service)"
                                    class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur-sm transition hover:scale-110 active:scale-95"
                                    :class="service.is_favorited ? 'text-red-500' : 'text-stone-400 hover:text-red-500'"
                                >
                                    <Heart class="size-4" :class="service.is_favorited ? 'fill-current text-red-500' : ''" />
                                </button>
                            </div>
                            <!-- Content -->
                            <div class="p-4">
                                <!-- Rating -->
                                <div class="flex items-center gap-1.5">
                                    <Star class="size-4 fill-amber-400 text-amber-400" />
                                    <span class="text-sm font-semibold text-stone-900">{{ service.rating }}</span>
                                    <span class="text-xs text-stone-400">({{ service.reviewCount }} đánh giá)</span>
                                </div>
                                <!-- Title -->
                                <h4 class="mt-2 text-base font-semibold text-stone-900 transition-colors" style="--hover-color: var(--dl-brand);">
                                    {{ service.title }}
                                </h4>
                                <!-- Provider + Location -->
                                <p class="mt-1 text-sm text-stone-500">{{ service.provider }}</p>
                                <div class="mt-2 flex items-center gap-3 text-xs text-stone-400">
                                    <span class="flex items-center gap-1"><MapPin class="size-3" /> {{ service.location }}</span>
                                    <span class="flex items-center gap-1"><Clock3 class="size-3" /> {{ service.duration }}</span>
                                </div>
                                <!-- Price + CTA -->
                                <div class="mt-4 flex flex-col border-t border-stone-100 pt-3">
                                    <div class="mb-3 flex flex-wrap items-baseline gap-1">
                                        <p class="text-lg font-bold" style="color: var(--dl-brand);">{{ service.price }}</p>
                                        <span v-if="service.unit" class="text-xs font-medium text-stone-500">/ {{ service.unit }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-auto">
                                        <p class="text-[11px] text-stone-400">Hủy miễn phí trước 2h</p>
                                        <Link
                                            :href="`/services/${service.id}`"
                                            class="rounded-full px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:shadow-md hover:scale-105 active:scale-95"
                                            style="background: var(--dl-brand);"
                                        >
                                            Đặt ngay
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="mt-10 text-center">
                        <Link href="/services" class="inline-flex items-center gap-2 rounded-full border border-stone-300 bg-white px-6 py-3 text-sm font-medium text-stone-700 shadow-sm transition hover:bg-stone-50">
                            Xem tất cả dịch vụ <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ===== AI TRIP PLANNER BANNER ===== -->
            <section id="ai-planner" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div ref="aiBannerRef" class="relative overflow-hidden rounded-[2.5rem] bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-900 via-emerald-900 to-stone-900 px-8 py-14 shadow-2xl sm:px-12 lg:px-16 lg:py-20">
                    <!-- Background Effects -->
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wOCkiLz48L3N2Zz4=')] opacity-20"></div>
                    <div class="pointer-events-none absolute -right-40 -top-40 size-[30rem] rounded-full bg-violet-500/20 blur-[100px]"></div>
                    <div class="pointer-events-none absolute -bottom-20 -left-20 size-[20rem] rounded-full bg-brand-surface0/20 blur-[80px]"></div>

                    <div class="relative grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                        <div class="text-white">
                            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-900/30 px-4 py-1.5 text-xs font-bold tracking-wide text-emerald-300 backdrop-blur-md">
                                <Bot class="size-4" />
                                TÍNH NĂNG AI TRỢ LÝ
                            </div>
                            <h3 class="text-4xl font-black tracking-tight sm:text-5xl lg:leading-[1.15]">
                                Đừng tốn hàng giờ tìm kiếm.<br />
                                <span class="bg-gradient-to-r from-emerald-300 to-indigo-300 bg-clip-text text-transparent">Để AI lo tất cả.</span>
                            </h3>
                            <p class="mt-5 max-w-lg text-lg leading-relaxed text-stone-300">
                                Nhập yêu cầu bằng tiếng Việt tự nhiên nhất. AI của chúng tôi sẽ quét +500 dịch vụ, so sánh giá, lộ trình và đề xuất ngay lựa chọn tối ưu trong tích tắc.
                            </p>
                            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                                <Link
                                    href="/ai-planner"
                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-4 text-sm font-bold text-indigo-900 shadow-[0_0_40px_-10px_rgba(255,255,255,0.4)] transition-all hover:scale-105 hover:shadow-[0_0_60px_-10px_rgba(255,255,255,0.6)] active:scale-95"
                                >
                                    <Sparkles class="size-5" />
                                    Mở AI Planner Ngay
                                </Link>
                            </div>
                        </div>

                        <!-- 3D Interactive Mockup -->
                        <div class="relative hidden lg:block" style="perspective: 1000px;">
                            <!-- Main Floating Card -->
                            <div class="relative z-10 -rotate-y-6 rotate-x-6 transform rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur-xl transition-transform duration-700 hover:-translate-y-2 hover:rotate-0">
                                <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                                    <div class="size-3 rounded-full bg-red-400"></div>
                                    <div class="size-3 rounded-full bg-amber-400"></div>
                                    <div class="size-3 rounded-full bg-emerald-400"></div>
                                </div>
                                <div class="mt-6 flex flex-col gap-4">
                                    <!-- Prompt -->
                                    <div class="max-w-[85%] self-end rounded-2xl rounded-tr-sm bg-gradient-to-br from-indigo-500 to-purple-600 px-4 py-3 text-sm text-white shadow-md">
                                        Tìm tour săn mây nào rẻ nhất sáng mai nha! ☁️
                                    </div>
                                    <!-- Parsing -->
                                    <div class="flex items-center gap-3 self-start opacity-70">
                                        <Bot class="size-4 animate-bounce text-emerald-300" />
                                        <span class="flex items-center gap-1 text-[11px] font-semibold uppercase tracking-widest text-emerald-200">
                                            Đang phân tích
                                            <span class="flex gap-0.5"><span class="size-1 animate-ping rounded-full bg-emerald-200"></span><span class="size-1 animate-ping rounded-full bg-emerald-200" style="animation-delay: 0.1s"></span></span>
                                        </span>
                                    </div>
                                    <!-- Result Card -->
                                    <div class="max-w-[90%] self-start rounded-2xl rounded-tl-sm border border-white/10 bg-white/10 p-4 shadow-md backdrop-blur">
                                        <div class="mb-3 flex items-center gap-2 text-sm text-white">
                                            <Sparkles class="size-4 text-amber-300" />
                                            Đã tìm thấy <strong class="text-amber-300">1 tour</strong> tối ưu nhất:
                                        </div>
                                        <div class="flex items-center gap-3 rounded-xl bg-black/20 p-3">
                                            <img src="https://picsum.photos/seed/taxua/80/80" class="size-12 rounded-lg object-cover" referrerpolicy="no-referrer" />
                                            <div>
                                                <p class="text-xs font-bold text-white">Săn Mây Tà Xùa</p>
                                                <p class="text-[10px] text-stone-300">Chỉ 350.000đ/người • Đón 4:30 AM</p>
                                            </div>
                                            <button class="ml-auto rounded-lg bg-brand-surface0 px-3 py-1.5 text-[10px] font-bold text-white">Chốt</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== TẠI SAO CHỌN CHÚNG TÔI ===== -->
            <section id="tai-sao" ref="trustSectionRef" class="border-y border-stone-200 bg-stone-50">
                <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em]" style="color: var(--dl-brand);">Tại sao chọn chúng tôi</p>
                        <h3 class="mt-2 text-2xl font-bold tracking-tight text-stone-950 sm:text-3xl">
                            Đặt dịch vụ yên tâm, không lo rủi ro
                        </h3>
                    </div>

                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="item in whyChooseUs"
                            :key="item.title"
                            class="trust-card group relative overflow-hidden rounded-[2rem] bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgb(0,0,0,0.08)]"
                        >
                            <!-- Decorative bg -->
                            <div class="absolute -right-6 -top-6 size-24 rounded-full opacity-0 transition-opacity group-hover:opacity-100" style="background: var(--dl-brand-surface);"></div>
                            
                            <div class="trust-icon relative z-10 flex size-14 items-center justify-center rounded-2xl shadow-inner transition-colors duration-500">
                                <component :is="item.icon" class="size-6" />
                            </div>
                            <h4 class="mt-6 text-lg font-bold text-stone-900">{{ item.title }}</h4>
                            <p class="mt-3 text-sm leading-relaxed text-stone-500">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== ĐÁNH GIÁ KHÁCH HÀNG THẬT ===== -->
            <section id="danh-gia" ref="reviewSectionRef" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center text-center">
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-widest text-amber-700">
                        Khách hàng nói gì?
                    </div>
                    <h3 class="mt-4 text-3xl font-black tracking-tight text-stone-900 sm:text-4xl">
                        Trải nghiệm thực tế từ cộng đồng
                    </h3>
                </div>

                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="review in customerReviews"
                        :key="review.name"
                        class="review-card group relative overflow-hidden rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-stone-300 hover:shadow-xl"
                    >
                        <MessageSquareQuote class="absolute -bottom-4 -right-4 size-32 text-stone-50 opacity-50 transition-transform group-hover:rotate-0 -rotate-12" />
                        <div class="relative z-10">
                            <!-- Stars -->
                            <div class="mb-6 flex gap-1">
                                <Star v-for="s in review.rating" :key="s" class="size-5 fill-amber-400 text-amber-400 drop-shadow-sm" />
                                <Star v-for="s in (5 - review.rating)" :key="'e'+s" class="size-5 text-stone-200" />
                            </div>
                            
                            <p class="text-base font-medium italic leading-relaxed text-stone-700">
                                "{{ review.content }}"
                            </p>
                            
                            <div class="mt-8 flex items-center justify-between border-t border-stone-100 pt-6">
                                <div class="flex items-center gap-3">
                                    <img :src="review.avatar" :alt="review.name" class="size-12 rounded-full object-cover shadow-sm ring-2 ring-white" referrerpolicy="no-referrer" />
                                    <div>
                                        <p class="text-sm font-bold text-stone-900">{{ review.name }}</p>
                                        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400">{{ review.role }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="absolute right-0 top-0 rounded-bl-2xl px-3 py-1.5 text-[10px] font-bold" style="background: var(--dl-brand-surface); color: var(--dl-brand); border-bottom: 1px solid var(--dl-brand-surface); border-left: 1px solid var(--dl-brand-surface);">
                                {{ review.service }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===== USE CASES ===== -->
            <section id="kien-truc" ref="useCaseSectionRef" class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-[2.5rem] bg-stone-950 px-8 py-14 text-white shadow-2xl sm:px-12 lg:px-16 lg:py-20">
                    <!-- Glow effects -->
                    <div class="pointer-events-none absolute right-0 top-0 size-96 rounded-full bg-violet-600/20 blur-[100px]"></div>
                    <div class="pointer-events-none absolute bottom-0 left-0 size-96 rounded-full bg-brand/20 blur-[100px]"></div>

                    <div class="relative grid gap-12 lg:grid-cols-[1fr_1.2fr] lg:items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-stone-800 bg-stone-900 px-3 py-1 text-xs font-bold uppercase tracking-widest text-stone-300">
                                Hệ sinh thái toàn diện
                            </div>
                            <h3 class="mt-6 text-3xl font-black leading-tight tracking-tight sm:text-4xl">
                                Một nền tảng.<br />Mọi nhu cầu dịch vụ.
                            </h3>
                            <p class="mt-4 max-w-md text-base leading-relaxed text-stone-400">
                                Khách du lịch chốt lịch trình chớp nhoáng. Cư dân bản địa gọi thợ sửa chữa trong vài phút. Nhà cung cấp quản lý hàng trăm bookings mỗi ngày.
                            </p>
                        </div>
                        
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div v-for="(story, i) in [
                                { icon: 'Map', title: 'Du Khách', desc: 'Tìm tour, thuê xe, lên lịch trình & đặt mọi thứ dễ dàng.' },
                                { icon: 'Wrench', title: 'Cư Dân', desc: 'Đặt thợ nội khu với giá minh bạch, thợ đến đúng giờ.' },
                                { icon: 'TrendingUp', title: 'Đối Tác', desc: 'Quản lý lịch rảnh, tối ưu công suất và nhận đánh giá.' },
                            ]" :key="i" class="usecase-card group relative rounded-[1.5rem] border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition-all hover:-translate-y-1 hover:bg-white/10 hover:shadow-xl hover:shadow-emerald-900/20">
                                <component :is="story.icon === 'Map' ? Map : story.icon === 'Wrench' ? Wrench : TrendingUp" class="mb-4 size-8 text-brand transition-transform group-hover:scale-110" />
                                <p class="text-base font-bold text-white">{{ story.title }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-stone-400">{{ story.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </MarketplaceLayout>
</template>

<style scoped>
/* === Brand-colored Trust Icons === */
.trust-icon {
    background: linear-gradient(135deg, var(--dl-brand-surface), rgba(216,243,220,0.5));
    color: var(--dl-brand);
}
.group:hover .trust-icon {
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    color: white;
}

/* Service card hover */
.service-card:hover h4 {
    color: var(--dl-brand) !important;
}

/* === Hero Orbs Drift (CSS — always running, parallax via GSAP) === */
.hero-orb {
    animation: drift 10s ease-in-out infinite;
    will-change: transform;
}
.hero-orb:last-of-type {
    animation-direction: reverse;
    animation-duration: 12s;
}

/* === Hero Elements — initial hidden state (GSAP handles reveal) === */
.hero-el {
    opacity: 0;
    transform: translateY(30px);
}

/* === Chat Messages — staggered entrance via CSS === */
.chat-msg {
    opacity: 0;
    transform: scale(0.95) translateY(12px);
    animation: chatMsg 0.5s ease forwards;
    transform-origin: left bottom;
}
.chat-msg--1 {
    animation-delay: 0.8s;
    transform-origin: right bottom;
}
.chat-msg--2 {
    animation-delay: 1.6s;
}
.chat-msg--3 {
    animation-delay: 2.8s;
}

@keyframes chatMsg {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(12px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes drift {
    0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
    50% { transform: translate3d(18px, -14px, 0) scale(1.05); }
}

/* === Reduced Motion === */
@media (prefers-reduced-motion: reduce) {
    .hero-orb,
    .hero-el,
    .chat-msg,
    .cat-card,
    .service-card,
    .trust-card,
    .review-card,
    .usecase-card {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
}
</style>