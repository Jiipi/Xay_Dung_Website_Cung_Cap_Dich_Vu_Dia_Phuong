<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head } from '@inertiajs/vue3';
import { BarChart3, DollarSign, Star, TrendingUp } from 'lucide-vue-next';
import { ref, onMounted, nextTick } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface RevenueMonth { month: string; revenue: number; orders: number; }
interface TopService { ten_dich_vu: string; so_don: number; doanh_thu: number; }
interface UserMonth { month: string; count: number; }
interface CategoryStat { ten_danh_muc: string; so_dich_vu: number; }
interface GeneralStats {
    totalRevenue: number; totalOrders: number; completedOrders: number;
    cancelledOrders: number; avgOrderValue: number; totalReviews: number; avgRating: number;
}

const props = withDefaults(defineProps<{
    revenueByMonth: RevenueMonth[];
    topServices: TopService[];
    usersByMonth: UserMonth[];
    categoryStats: CategoryStat[];
    generalStats: GeneralStats;
}>(), {
    revenueByMonth: () => [],
    topServices: () => [],
    usersByMonth: () => [],
    categoryStats: () => [],
    generalStats: () => ({ totalRevenue: 0, totalOrders: 0, completedOrders: 0, cancelledOrders: 0, avgOrderValue: 0, totalReviews: 0, avgRating: 0 }),
});

const formatVND = (v: number) => {
    if (v >= 1_000_000) return (v / 1_000_000).toFixed(1).replace('.0', '') + 'M đ';
    if (v >= 1_000) return (v / 1_000).toFixed(0) + 'K đ';
    return new Intl.NumberFormat('vi-VN').format(v) + 'đ';
};
const formatFullVND = (v: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

const barGradients = [
    'from-blue-500 to-indigo-500',
    'from-emerald-500 to-teal-500',
    'from-violet-500 to-purple-500',
    'from-amber-500 to-orange-500',
    'from-rose-500 to-pink-500',
];

const barColors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#f43f5e'];

// Revenue Chart
const revenueCanvas = ref<HTMLCanvasElement | null>(null);
function drawRevenueChart() {
    const canvas = revenueCanvas.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);
    const w = rect.width, h = rect.height;
    const data = props.revenueByMonth;
    if (!data.length) return;
    const padL = 70, padR = 20, padT = 20, padB = 50;
    const cW = w - padL - padR, cH = h - padT - padB;
    const maxRev = Math.max(...data.map(d => d.revenue), 1);
    const barW = cW / data.length;
    const innerW = barW * 0.5;
    ctx.clearRect(0, 0, w, h);

    // Grid
    for (let i = 0; i <= 4; i++) {
        const y = padT + cH - (cH / 4) * i;
        ctx.strokeStyle = '#f5f5f4';
        ctx.lineWidth = 1;
        ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(w - padR, y); ctx.stroke();
        ctx.fillStyle = '#a8a29e'; ctx.font = '11px Inter, system-ui, sans-serif'; ctx.textAlign = 'right';
        ctx.fillText(formatVND((maxRev / 4) * i), padL - 10, y + 4);
    }

    // Bars
    data.forEach((d, i) => {
        const barH = (d.revenue / maxRev) * cH;
        const x = padL + i * barW + (barW - innerW) / 2;
        const y = padT + cH - barH;
        const grad = ctx.createLinearGradient(x, y, x, padT + cH);
        grad.addColorStop(0, '#38bdf8');
        grad.addColorStop(0.5, '#6366f1');
        grad.addColorStop(1, '#4f46e5');
        ctx.fillStyle = grad;
        const r = 5;
        ctx.beginPath();
        ctx.moveTo(x + r, y); ctx.lineTo(x + innerW - r, y);
        ctx.quadraticCurveTo(x + innerW, y, x + innerW, y + r);
        ctx.lineTo(x + innerW, padT + cH); ctx.lineTo(x, padT + cH);
        ctx.lineTo(x, y + r); ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.fill();

        // Glow
        ctx.shadowColor = '#38bdf8';
        ctx.shadowBlur = 8;
        ctx.fillRect(x, y, innerW, 2);
        ctx.shadowBlur = 0;

        ctx.fillStyle = '#78716c'; ctx.textAlign = 'center'; ctx.font = '10px Inter, system-ui, sans-serif';
        ctx.fillText(d.month, padL + i * barW + barW / 2, h - padB + 15);
    });
}

// User Growth Chart
const userCanvas = ref<HTMLCanvasElement | null>(null);
function drawUserChart() {
    const canvas = userCanvas.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);
    const w = rect.width, h = rect.height;
    const data = props.usersByMonth;
    if (!data.length) return;
    const padL = 40, padR = 20, padT = 20, padB = 40;
    const cW = w - padL - padR, cH = h - padT - padB;
    const max = Math.max(...data.map(d => d.count), 1);
    ctx.clearRect(0, 0, w, h);

    // Area fill
    const grad = ctx.createLinearGradient(0, padT, 0, padT + cH);
    grad.addColorStop(0, 'rgba(16,185,129,0.12)');
    grad.addColorStop(1, 'rgba(16,185,129,0)');
    ctx.fillStyle = grad;
    ctx.beginPath();
    data.forEach((d, i) => {
        const x = padL + (i / Math.max(data.length - 1, 1)) * cW;
        const y = padT + cH - (d.count / max) * cH;
        if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.lineTo(padL + cW, padT + cH);
    ctx.lineTo(padL, padT + cH);
    ctx.closePath();
    ctx.fill();

    // Line
    ctx.strokeStyle = '#10b981'; ctx.lineWidth = 2.5; ctx.lineJoin = 'round';
    ctx.beginPath();
    data.forEach((d, i) => {
        const x = padL + (i / Math.max(data.length - 1, 1)) * cW;
        const y = padT + cH - (d.count / max) * cH;
        if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.stroke();

    // Dots + labels
    data.forEach((d, i) => {
        const x = padL + (i / Math.max(data.length - 1, 1)) * cW;
        const y = padT + cH - (d.count / max) * cH;
        ctx.fillStyle = 'white'; ctx.beginPath(); ctx.arc(x, y, 5, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = '#10b981'; ctx.beginPath(); ctx.arc(x, y, 3, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = '#78716c'; ctx.textAlign = 'center'; ctx.font = '11px Inter, system-ui, sans-serif';
        ctx.fillText(d.month, x, h - padB + 15);
        ctx.fillStyle = '#44403c'; ctx.font = 'bold 11px Inter, system-ui, sans-serif';
        ctx.fillText(String(d.count), x, y - 10);
    });
}

onMounted(async () => {
    await nextTick();
    drawRevenueChart();
    drawUserChart();
    window.addEventListener('resize', () => { drawRevenueChart(); drawUserChart(); });
});

// Animations
const { animateFadeUp } = useAnimations();
animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Thống kê" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">
            <!-- Summary Cards -->
            <div class="animate-fade-up grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="mb-2 rounded-xl bg-blue-50 p-2 w-fit text-blue-500 transition group-hover:scale-110">
                        <DollarSign class="size-5" />
                    </div>
                    <p class="text-xl font-bold text-stone-800">{{ formatVND(generalStats.totalRevenue) }}</p>
                    <p class="text-xs text-stone-500">Tổng doanh thu</p>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="mb-2 rounded-xl bg-emerald-50 p-2 w-fit text-emerald-500 transition group-hover:scale-110">
                        <BarChart3 class="size-5" />
                    </div>
                    <p class="text-xl font-bold text-stone-800">{{ generalStats.totalOrders }}</p>
                    <p class="text-xs text-stone-500">Tổng đơn hàng</p>
                    <p class="mt-1 text-[10px] text-emerald-600">{{ generalStats.completedOrders }} hoàn thành · {{ generalStats.cancelledOrders }} đã hủy</p>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="mb-2 rounded-xl bg-violet-50 p-2 w-fit text-violet-500 transition group-hover:scale-110">
                        <TrendingUp class="size-5" />
                    </div>
                    <p class="text-xl font-bold text-stone-800">{{ formatVND(generalStats.avgOrderValue) }}</p>
                    <p class="text-xs text-stone-500">Giá trị TB / đơn</p>
                </div>
                <div class="group rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="mb-2 rounded-xl bg-amber-50 p-2 w-fit text-amber-500 transition group-hover:scale-110">
                        <Star class="size-5" />
                    </div>
                    <p class="text-xl font-bold text-stone-800">{{ generalStats.avgRating }} ★</p>
                    <p class="text-xs text-stone-500">{{ generalStats.totalReviews }} đánh giá</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="animate-fade-up grid grid-cols-1 gap-6 lg:grid-cols-5">
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm lg:col-span-3">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Doanh thu 12 tháng</h2>
                            <p class="mt-0.5 text-xs text-stone-400">Biểu đồ doanh thu toàn nền tảng</p>
                        </div>
                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-500">12 tháng</span>
                    </div>
                    <div class="p-4">
                        <canvas ref="revenueCanvas" class="h-72 w-full" />
                    </div>
                </div>

                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Tăng trưởng người dùng</h2>
                            <p class="mt-0.5 text-xs text-stone-400">Đăng ký mới theo tháng</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <canvas ref="userCanvas" class="h-72 w-full" />
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="animate-fade-up grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Top Services -->
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Top dịch vụ</h2>
                            <p class="mt-0.5 text-xs text-stone-400">Dịch vụ có doanh thu cao nhất</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">Dịch vụ</th>
                                    <th class="px-6 py-3">Số đơn</th>
                                    <th class="px-6 py-3 text-right">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(sv, i) in topServices"
                                    :key="sv.ten_dich_vu"
                                    class="border-b border-stone-50 transition-colors hover:bg-stone-50/80"
                                >
                                    <td class="px-6 py-2.5">
                                        <span :class="[
                                            'inline-flex size-6 items-center justify-center rounded-full text-[10px] font-bold text-white bg-gradient-to-br',
                                            barGradients[i % barGradients.length]
                                        ]">
                                            {{ i + 1 }}
                                        </span>
                                    </td>
                                    <td class="max-w-[200px] truncate px-6 py-2.5 font-medium text-stone-700">{{ sv.ten_dich_vu }}</td>
                                    <td class="px-6 py-2.5 text-blue-600 font-semibold">{{ sv.so_don }}</td>
                                    <td class="whitespace-nowrap px-6 py-2.5 text-right font-semibold text-emerald-600">{{ formatFullVND(sv.doanh_thu) }}</td>
                                </tr>
                                <tr v-if="topServices.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-400">Chưa có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Phân bố theo danh mục</h2>
                            <p class="mt-0.5 text-xs text-stone-400">Số lượng dịch vụ mỗi danh mục</p>
                        </div>
                    </div>
                    <div class="space-y-4 p-6">
                        <div v-for="(cat, i) in categoryStats" :key="cat.ten_danh_muc" class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="size-2 rounded-full" :style="{ backgroundColor: barColors[i % barColors.length] }" />
                                    <span class="text-stone-600">{{ cat.ten_danh_muc }}</span>
                                </div>
                                <span class="font-semibold text-stone-800">{{ cat.so_dich_vu }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-stone-100">
                                <div
                                    class="h-full rounded-full transition-all duration-700"
                                    :style="{
                                        width: `${Math.max((cat.so_dich_vu / Math.max(...categoryStats.map(c => c.so_dich_vu), 1)) * 100, 5)}%`,
                                        backgroundColor: barColors[i % barColors.length]
                                    }"
                                />
                            </div>
                        </div>
                        <div v-if="categoryStats.length === 0" class="py-8 text-center text-sm text-stone-400">
                            Chưa có dữ liệu
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
