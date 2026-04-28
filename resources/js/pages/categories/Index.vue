<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Brush,
    Car,
    ChevronDown,
    ChevronRight,
    GraduationCap,
    Hammer,
    Home,
    Laptop,
    Leaf,
    MapPin,
    Music,
    Paintbrush,
    Search,
    ShieldCheck,
    ShoppingBag,
    Sparkles,
    Utensils,
    Wrench,
    Zap,
} from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

/* ── Props from controller ── */
const props = defineProps<{
    filters?: any;
    categories?: any[];
}>();

/* ── Search ── */
const searchQuery = ref('');

/* ── Icon & color mapping by slug ── */
const categoryMeta: Record<string, { icon: string; color: string; description: string }> = {
    'don-dep-ve-sinh': { icon: 'sparkles', color: 'sky', description: 'Dịch vụ vệ sinh nhà cửa, văn phòng, giặt đồ chuyên nghiệp' },
    'xay-dung-sua-chua': { icon: 'hammer', color: 'amber', description: 'Sửa chữa điện nước, xây dựng, cải tạo nhà ở' },
    'du-lich-di-chuyen': { icon: 'car', color: 'emerald', description: 'Tour du lịch, thuê xe, xe đưa đón sân bay' },
    'lam-dep-suc-khoe': { icon: 'leaf', color: 'rose', description: 'Spa, massage, làm tóc, chăm sóc da tại nhà' },
    'su-kien-giai-tri': { icon: 'music', color: 'violet', description: 'Tổ chức sự kiện, chụp ảnh, MC, ban nhạc, tiệc' },
};
const defaultIcons = ['sparkles', 'hammer', 'car', 'leaf', 'music', 'utensils', 'laptop', 'graduation'];
const defaultColors = ['sky', 'amber', 'emerald', 'rose', 'violet', 'orange', 'indigo', 'teal'];

/* ── Use real data from controller ── */
const expandedIds = ref<Set<number>>(new Set());

const categories = computed(() => {
    if (!props.categories || props.categories.length === 0) return [];
    return props.categories.map((c: any, i: number) => {
        const meta = categoryMeta[c.slug] || {};
        return {
            id: c.id,
            name: c.ten_danh_muc,
            slug: c.slug,
            icon: meta.icon || defaultIcons[i % defaultIcons.length],
            color: meta.color || defaultColors[i % defaultColors.length],
            description: c.mo_ta || meta.description || 'Khám phá các dịch vụ trong danh mục này',
            serviceCount: c.so_dich_vu ?? 0,
            subcategories: (c.danh_muc_con || []).map((sc: any) => ({
                id: sc.id,
                name: sc.ten_danh_muc,
                slug: sc.slug,
                serviceCount: sc.so_dich_vu ?? 0,
            })),
        };
    });
});

/* ── Filtered categories ── */
const filteredCategories = computed(() => {
    if (!searchQuery.value.trim()) return categories.value;
    const q = searchQuery.value.toLowerCase().trim();
    return categories.value.filter(
        (c) =>
            c.name.toLowerCase().includes(q) ||
            c.description.toLowerCase().includes(q) ||
            c.subcategories.some((sc: any) => sc.name.toLowerCase().includes(q)),
    );
});

const totalServices = computed(() => categories.value.reduce((sum, c) => sum + c.serviceCount, 0));

/* ── Toggle accordion ── */
function isExpanded(catId: number) {
    return expandedIds.value.has(catId);
}
function toggleCategory(catId: number) {
    if (expandedIds.value.has(catId)) {
        expandedIds.value.delete(catId);
    } else {
        expandedIds.value.add(catId);
    }
    // Trigger reactivity
    expandedIds.value = new Set(expandedIds.value);
}

/* ── Icon mapping ── */
const iconMap: Record<string, any> = {
    sparkles: Sparkles,
    hammer: Hammer,
    car: Car,
    leaf: Leaf,
    music: Music,
    utensils: Utensils,
    laptop: Laptop,
    graduation: GraduationCap,
};

const colorMap: Record<string, { bg: string; text: string; border: string; light: string }> = {
    sky: { bg: 'bg-brand-surface', text: 'text-brand', border: 'border-brand', light: 'bg-brand-surface' },
    amber: { bg: 'bg-amber-100', text: 'text-amber-700', border: 'border-amber-200', light: 'bg-amber-50' },
    emerald: { bg: 'bg-emerald-100', text: 'text-emerald-700', border: 'border-emerald-200', light: 'bg-emerald-50' },
    rose: { bg: 'bg-rose-100', text: 'text-rose-700', border: 'border-rose-200', light: 'bg-rose-50' },
    violet: { bg: 'bg-violet-100', text: 'text-violet-700', border: 'border-violet-200', light: 'bg-violet-50' },
    orange: { bg: 'bg-orange-100', text: 'text-orange-700', border: 'border-orange-200', light: 'bg-orange-50' },
    indigo: { bg: 'bg-indigo-100', text: 'text-indigo-700', border: 'border-indigo-200', light: 'bg-indigo-50' },
    teal: { bg: 'bg-teal-100', text: 'text-teal-700', border: 'border-teal-200', light: 'bg-teal-50' },
};

function getColor(color: string) {
    return colorMap[color] || colorMap.sky;
}
</script>

<template>
    <Head title="Danh mục dịch vụ — Dalat Services" />

    <MarketplaceLayout>
        <!-- ===== Hero Section ===== -->
        <section class="border-b border-stone-200 bg-gradient-to-br from-stone-50 via-white to-emerald-50">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-6 flex items-center gap-2 text-sm text-stone-400">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-stone-700"><Home class="size-3.5" /> Trang chủ</Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-stone-700">Danh mục dịch vụ</span>
                </nav>

                <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-black tracking-tight text-stone-900 sm:text-4xl">Danh mục dịch vụ</h1>
                        <p class="mt-3 max-w-xl text-base text-stone-500">
                            Khám phá hàng trăm dịch vụ được phân loại rõ ràng — từ sửa chữa, du lịch đến chăm sóc sức khỏe.
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-4">
                        <div class="rounded-2xl border border-stone-200 bg-white px-5 py-3 text-center shadow-sm">
                            <p class="text-2xl font-black text-brand">{{ categories.length }}</p>
                            <p class="text-xs font-medium text-stone-400">Danh mục</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-white px-5 py-3 text-center shadow-sm">
                            <p class="text-2xl font-black text-brand">{{ totalServices }}</p>
                            <p class="text-xs font-medium text-stone-400">Dịch vụ</p>
                        </div>
                    </div>
                </div>

                <!-- Search bar -->
                <div class="mt-8 flex max-w-xl gap-3">
                    <div class="relative flex-1">
                        <Search class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="w-full rounded-2xl border border-stone-300 bg-white py-3.5 pl-12 pr-4 text-sm text-stone-900 shadow-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand"
                            placeholder="Tìm kiếm danh mục..."
                        />
                    </div>
                    <button class="shrink-0 rounded-2xl bg-brand px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand">
                        Tìm
                    </button>
                </div>
            </div>
        </section>

        <!-- ===== Category Grid ===== -->
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Grid -->
            <div v-if="filteredCategories.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="cat in filteredCategories"
                    :key="cat.id"
                    class="group overflow-hidden rounded-[1.75rem] border bg-white shadow-sm transition-all duration-300 hover:shadow-md"
                    :class="isExpanded(cat.id) ? getColor(cat.color).border : 'border-stone-200'"
                >
                    <!-- Card Header -->
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <!-- Icon -->
                            <div class="flex size-14 items-center justify-center rounded-2xl" :class="[getColor(cat.color).bg]">
                                <component :is="iconMap[cat.icon] || Sparkles" class="size-7" :class="getColor(cat.color).text" />
                            </div>
                            <!-- Service count badge -->
                            <span class="rounded-full border px-3 py-1 text-xs font-bold" :class="[getColor(cat.color).border, getColor(cat.color).text]">
                                {{ cat.serviceCount }} dịch vụ
                            </span>
                        </div>

                        <h2 class="mt-4 text-lg font-bold text-stone-900 transition group-hover:text-brand">{{ cat.name }}</h2>
                        <p class="mt-1.5 text-sm leading-relaxed text-stone-500">{{ cat.description }}</p>

                        <!-- Subcategory count + toggle -->
                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-xs font-medium text-stone-400">{{ cat.subcategories.length }} danh mục con</span>
                            <button
                                @click="toggleCategory(cat.id)"
                                class="flex items-center gap-1 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
                                :class="isExpanded(cat.id)
                                    ? `${getColor(cat.color).border} ${getColor(cat.color).text} ${getColor(cat.color).light}`
                                    : 'border-stone-200 text-stone-500 hover:border-stone-300 hover:text-stone-700'"
                            >
                                <ChevronDown
                                    class="size-3.5 transition-transform"
                                    :class="isExpanded(cat.id) ? 'rotate-180' : ''"
                                />
                                {{ isExpanded(cat.id) ? 'Thu gọn' : 'Xem danh mục con' }}
                            </button>
                        </div>
                    </div>

                    <!-- Accordion: Subcategories -->
                    <Transition name="accordion">
                        <div v-if="isExpanded(cat.id)" class="border-t px-6 pb-5 pt-4" :class="[getColor(cat.color).border, getColor(cat.color).light]">
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    v-for="sub in cat.subcategories"
                                    :key="sub.id"
                                    :href="`/services?category=${cat.slug}&sub=${sub.name}`"
                                    class="inline-flex items-center gap-1.5 rounded-full border bg-white px-3 py-2 text-sm font-medium transition hover:shadow-sm"
                                    :class="[getColor(cat.color).border, 'text-stone-700 hover:' + getColor(cat.color).text]"
                                >
                                    {{ sub.name }}
                                    <span class="rounded-full bg-stone-100 px-1.5 py-0.5 text-[10px] font-bold text-stone-500">{{ sub.serviceCount }}</span>
                                </Link>
                            </div>
                            <Link
                                :href="`/services?category=${cat.slug}`"
                                class="mt-4 inline-flex items-center gap-1 text-sm font-semibold transition"
                                :class="getColor(cat.color).text"
                            >
                                Xem tất cả {{ cat.name }} <ArrowRight class="size-4" />
                            </Link>
                        </div>
                    </Transition>
                </article>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center rounded-[2rem] border border-dashed border-stone-300 bg-white py-16 text-center">
                <Search class="mb-4 size-12 text-stone-300" />
                <h3 class="text-lg font-bold text-stone-900">Không tìm thấy danh mục</h3>
                <p class="mt-2 text-sm text-stone-500">Thử tìm kiếm với từ khóa khác</p>
                <button
                    @click="searchQuery = ''"
                    class="mt-6 rounded-2xl bg-brand px-6 py-3 text-sm font-bold text-white transition hover:bg-brand"
                >
                    Xóa tìm kiếm
                </button>
            </div>
        </section>
    </MarketplaceLayout>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.3s ease;
    max-height: 300px;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 0;
}
</style>