<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    Calendar,
    CheckSquare,
    Clock,
    DollarSign,
    Package,
    Sparkles,
    TrendingUp,
    Users,
    UserPlus,
} from 'lucide-vue-next';
import { ref, onMounted, nextTick, computed } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface KpiCards {
    totalUsers: number;
    usersChangePercent: number;
    totalServices: number;
    pendingServices: number;
    totalRevenue: number;
    revenueChangePercent: number;
    pendingOrders: number;
}

interface ChartData {
    month: string;
    monthFull: string;
    revenue: number;
    bookings: number;
}

interface UserDist {
    label: string;
    count: number;
}

interface RecentOrder {
    id: number;
    ma_don: string;
    khach_hang: string;
    dich_vu: string;
    ngay: string;
    trang_thai: string;
    tong_tien: number;
}

interface RecentUser {
    id: number;
    ho_ten: string;
    email: string;
    vai_tro: string;
    ngay_tao: string;
}

const props = withDefaults(defineProps<{
    kpiCards: KpiCards;
    revenueChart: ChartData[];
    userDistribution: UserDist[];
    orderStatuses: Record<string, number>;
    recentOrders: RecentOrder[];
    recentUsers: RecentUser[];
}>(), {
    kpiCards: () => ({
        totalUsers: 0, usersChangePercent: 0, totalServices: 0,
        pendingServices: 0, totalRevenue: 0, revenueChangePercent: 0, pendingOrders: 0,
    }),
    revenueChart: () => [],
    userDistribution: () => [],
    orderStatuses: () => ({}),
    recentOrders: () => [],
    recentUsers: () => [],
});

// ─── Greeting ────────────────────────────────────────────────
const page = usePage();
const adminName = computed(() => page.props.auth?.user?.name ?? 'Admin');
const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Chào buổi sáng';
    if (h < 18) return 'Chào buổi chiều';
    return 'Chào buổi tối';
});

// ─── Formatters ──────────────────────────────────────────────
const formatVND = (value: number) => {
    if (value >= 1_000_000) return (value / 1_000_000).toFixed(1).replace('.0', '') + 'M đ';
    if (value >= 1_000) return (value / 1_000).toFixed(0) + 'K đ';
    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
};

const formatFullVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

const statusLabels: Record<string, string> = {
    cho_xac_nhan: 'Chờ xác nhận',
    da_xac_nhan: 'Đã xác nhận',
    dang_thuc_hien: 'Đang thực hiện',
    hoan_thanh: 'Hoàn thành',
    da_huy: 'Đã hủy',
};

const statusColors: Record<string, string> = {
    cho_xac_nhan: 'bg-amber-500',
    da_xac_nhan: 'bg-emerald-500',
    dang_thuc_hien: 'bg-sky-500',
    hoan_thanh: 'bg-indigo-500',
    da_huy: 'bg-red-500',
};

const statusTextColors: Record<string, string> = {
    cho_xac_nhan: 'text-amber-400',
    da_xac_nhan: 'text-emerald-400',
    dang_thuc_hien: 'text-sky-400',
    hoan_thanh: 'text-indigo-400',
    da_huy: 'text-red-400',
};

const roleColors: Record<string, string> = {
    'Admin': 'text-sky-400 bg-sky-500/10',
    'Nhà cung cấp': 'text-emerald-400 bg-emerald-500/10',
    'Khách hàng': 'text-amber-400 bg-amber-500/10',
};

// ─── KPI count-up animation ─────────────────────────────────
const animatedValues = ref({
    totalUsers: 0,
    totalRevenue: 0,
    totalServices: 0,
    pendingOrders: 0,
});

function animateCount(target: keyof typeof animatedValues.value, endVal: number, duration = 1200) {
    const start = performance.now();
    function step(now: number) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
        animatedValues.value[target] = Math.round(eased * endVal);
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// ─── Revenue Chart ──────────────────────────────────────────
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
    const data = props.revenueChart;
    if (!data.length) return;

    const padL = 70, padR = 20, padT = 20, padB = 40;
    const cW = w - padL - padR, cH = h - padT - padB;
    const maxRev = Math.max(...data.map(d => d.revenue), 1);
    const barW = cW / data.length;
    const innerW = barW * 0.45;

    ctx.clearRect(0, 0, w, h);

    // Grid
    const gridSteps = 4;
    ctx.font = '11px Inter, system-ui, sans-serif';
    ctx.textAlign = 'right';
    for (let i = 0; i <= gridSteps; i++) {
        const y = padT + cH - (cH / gridSteps) * i;
        ctx.strokeStyle = '#f5f5f4';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padL, y);
        ctx.lineTo(w - padR, y);
        ctx.stroke();
        ctx.fillStyle = '#a8a29e';
        ctx.fillText(formatVND((maxRev / gridSteps) * i), padL - 10, y + 4);
    }

    // Bars with rounded corners
    data.forEach((d, i) => {
        const barH = (d.revenue / maxRev) * cH;
        const x = padL + i * barW + (barW - innerW) / 2;
        const y = padT + cH - barH;

        const grad = ctx.createLinearGradient(x, y, x, padT + cH);
        grad.addColorStop(0, '#38bdf8');
        grad.addColorStop(0.5, '#6366f1');
        grad.addColorStop(1, '#4f46e5');
        ctx.fillStyle = grad;

        const r = 6;
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + innerW - r, y);
        ctx.quadraticCurveTo(x + innerW, y, x + innerW, y + r);
        ctx.lineTo(x + innerW, padT + cH);
        ctx.lineTo(x, padT + cH);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.fill();

        // Glow effect on top
        ctx.shadowColor = '#38bdf8';
        ctx.shadowBlur = 8;
        ctx.fillRect(x, y, innerW, 2);
        ctx.shadowBlur = 0;

        // Month label
        ctx.fillStyle = '#78716c';
        ctx.textAlign = 'center';
        ctx.font = '12px Inter, system-ui, sans-serif';
        ctx.fillText(d.month, padL + i * barW + barW / 2, h - padB + 20);
    });

    // Bookings trend line
    if (data.some(d => d.bookings > 0)) {
        const maxB = Math.max(...data.map(d => d.bookings), 1);
        ctx.strokeStyle = '#4ade80';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.setLineDash([]);
        ctx.beginPath();
        data.forEach((d, i) => {
            const x = padL + i * barW + barW / 2;
            const y = padT + cH - (d.bookings / maxB) * cH;
            if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
        });
        ctx.stroke();

        // Dots
        data.forEach((d, i) => {
            const x = padL + i * barW + barW / 2;
            const y = padT + cH - (d.bookings / maxB) * cH;
            ctx.fillStyle = 'white';
            ctx.beginPath(); ctx.arc(x, y, 5, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#4ade80';
            ctx.beginPath(); ctx.arc(x, y, 3, 0, Math.PI * 2); ctx.fill();
        });
    }
}

// ─── Donut Chart ────────────────────────────────────────────
const donutCanvas = ref<HTMLCanvasElement | null>(null);
const donutColors = ['#38bdf8', '#4ade80', '#f59e0b', '#f87171'];

function drawDonutChart() {
    const canvas = donutCanvas.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const w = rect.width, h = rect.height;
    const cx = w / 2, cy = h / 2;
    const radius = Math.min(w, h) / 2 - 10;
    const innerR = radius * 0.6;
    const data = props.userDistribution;
    const total = data.reduce((s, d) => s + d.count, 0);
    if (!total) return;

    let angle = -Math.PI / 2;
    data.forEach((d, i) => {
        const sliceAngle = (d.count / total) * Math.PI * 2;
        ctx.fillStyle = donutColors[i % donutColors.length];
        ctx.beginPath();
        ctx.arc(cx, cy, radius, angle, angle + sliceAngle);
        ctx.arc(cx, cy, innerR, angle + sliceAngle, angle, true);
        ctx.closePath();
        ctx.fill();

        // Subtle glow
        ctx.shadowColor = donutColors[i % donutColors.length];
        ctx.shadowBlur = 6;
        ctx.fill();
        ctx.shadowBlur = 0;

        angle += sliceAngle;
    });

    // Center text
    ctx.fillStyle = '#44403c';
    ctx.font = 'bold 24px Inter, system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(String(total), cx, cy - 6);
    ctx.font = '11px Inter, system-ui, sans-serif';
    ctx.fillStyle = '#78716c';
    ctx.fillText('người dùng', cx, cy + 14);
}

// ─── Animations ─────────────────────────────────────────────
const { animateHeroEntrance, animateFadeUp, animateParallax } = useAnimations();
const heroBadge = ref<HTMLElement | null>(null);
const heroHeadline = ref<HTMLElement | null>(null);
const heroDesc = ref<HTMLElement | null>(null);
const heroButtons = ref<HTMLElement | null>(null);
const heroStats = ref<HTMLElement | null>(null);

const heroOrb1 = ref<HTMLElement | null>(null);
const heroOrb2 = ref<HTMLElement | null>(null);

// ─── Mount ──────────────────────────────────────────────────
onMounted(async () => {
    await nextTick();
    drawRevenueChart();
    drawDonutChart();

    // Start count-up animations
    animateCount('totalUsers', props.kpiCards.totalUsers);
    animateCount('totalRevenue', props.kpiCards.totalRevenue);
    animateCount('totalServices', props.kpiCards.totalServices);
    animateCount('pendingOrders', props.kpiCards.pendingOrders);

    window.addEventListener('resize', () => {
        drawRevenueChart();
        drawDonutChart();
    });
});

animateHeroEntrance({
    badge: heroBadge,
    headline: heroHeadline,
    description: heroDesc,
    searchBar: heroButtons,
    stats: heroStats,
});

animateParallax(heroOrb1, { speed: -0.4 });
animateParallax(heroOrb2, { speed: 0.3 });

animateFadeUp('.animate-fade-up', { duration: 0.6, y: 40 });
</script>

<template>
    <Head title="Admin Dashboard" />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">
            <!-- ══════ Hero Section ══════ -->
            <section class="hero-section overflow-hidden rounded-[2rem] shadow-xl relative text-white">
                <div class="hero-bg absolute inset-0 z-0"></div>
                <!-- NOISE OVERLAY -->
                <div class="absolute inset-0 z-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

                <!-- Parallax Drift Orbs -->
                <div ref="heroOrb1" class="hero-orb absolute -right-20 -top-20 size-[30rem] rounded-full bg-white/10 blur-[80px]"></div>
                <div ref="heroOrb2" class="hero-orb absolute -bottom-32 -left-20 size-[25rem] rounded-full bg-sky-400/20 blur-[100px]"></div>

                <div class="relative z-10 grid gap-8 px-6 py-12 sm:px-10 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="flex flex-col justify-center">
                        <div ref="heroBadge" class="hero-el inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-white self-start backdrop-blur-md">
                            <Sparkles class="size-4" />
                            Admin Panel
                        </div>
                        <h1 ref="heroHeadline" class="hero-el mt-6 max-w-2xl font-serif text-4xl sm:text-5xl lg:text-6xl text-white">
                            {{ greeting }}, <br><em class="opacity-85 font-serif italic">{{ adminName }}!</em>
                        </h1>
                        <p ref="heroDesc" class="hero-el mt-4 max-w-2xl text-base leading-7 text-sky-50 sm:text-lg">
                            Hệ thống đang hoạt động ổn định.
                            <template v-if="kpiCards.pendingOrders > 0 || kpiCards.pendingServices > 0">
                                Hiện có <strong class="text-amber-300 font-semibold">{{ kpiCards.pendingOrders + kpiCards.pendingServices }}</strong> mục cần xử lý.
                            </template>
                        </p>
                        <div ref="heroButtons" class="hero-el mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link href="/admin/services" class="btn inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-sky-950 transition hover:bg-stone-100">
                                <CheckSquare class="size-4" />
                                Duyệt dịch vụ
                            </Link>
                            <Link href="/admin/users" class="btn inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 backdrop-blur-md px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                Quản lý users
                                <Users class="size-4" />
                            </Link>
                        </div>
                    </div>

                    <div ref="heroStats" class="hero-el grid grid-cols-2 content-center rounded-[2rem] border border-white/15 bg-white/5 shadow-2xl backdrop-blur-md overflow-hidden">
                        <div class="border-b border-r border-white/10 p-5 sm:p-6 transition hover:bg-white/5 relative overflow-hidden group">
                            <div class="absolute -bottom-10 -right-10 size-32 rounded-full bg-sky-400/20 blur-3xl transition duration-500 group-hover:bg-sky-400/30"></div>
                            <div class="relative z-10 flex items-center gap-2 text-sky-100/70">
                                <Users class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Tổng người dùng</p>
                            </div>
                            <p class="relative z-10 mt-3 font-serif text-4xl text-white">{{ animatedValues.totalUsers }}</p>
                            <span class="relative z-10 mt-2 inline-flex items-center gap-0.5 text-xs font-semibold" :class="kpiCards.usersChangePercent >= 0 ? 'text-emerald-300' : 'text-red-300'">
                                <ArrowUpRight v-if="kpiCards.usersChangePercent >= 0" class="size-3.5" />
                                <ArrowDownRight v-else class="size-3.5" />
                                {{ Math.abs(kpiCards.usersChangePercent) }}%
                            </span>
                        </div>
                        <div class="border-b border-white/10 p-5 sm:p-6 transition hover:bg-white/5 relative overflow-hidden group">
                            <div class="absolute -bottom-10 -right-10 size-32 rounded-full bg-emerald-400/20 blur-3xl transition duration-500 group-hover:bg-emerald-400/30"></div>
                            <div class="relative z-10 flex items-center gap-2 text-sky-100/70">
                                <Package class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Tổng dịch vụ</p>
                            </div>
                            <p class="relative z-10 mt-3 font-serif text-4xl text-white">{{ animatedValues.totalServices }}</p>
                            <p v-if="kpiCards.pendingServices > 0" class="relative z-10 mt-2 text-xs font-semibold text-amber-300">{{ kpiCards.pendingServices }} chờ duyệt</p>
                            <p v-else class="relative z-10 mt-2 text-xs text-sky-200/70">Đã duyệt hết</p>
                        </div>
                        <div class="border-r border-white/10 p-5 sm:p-6 transition hover:bg-white/5 relative overflow-hidden group">
                            <div class="absolute -bottom-10 -right-10 size-32 rounded-full bg-violet-400/20 blur-3xl transition duration-500 group-hover:bg-violet-400/30"></div>
                            <div class="relative z-10 flex items-center gap-2 text-sky-100/70">
                                <DollarSign class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Doanh thu</p>
                            </div>
                            <p class="relative z-10 mt-3 font-serif text-4xl text-white">{{ formatVND(animatedValues.totalRevenue) }}</p>
                            <span class="relative z-10 mt-2 inline-flex items-center gap-0.5 text-xs font-semibold" :class="kpiCards.revenueChangePercent >= 0 ? 'text-emerald-300' : 'text-red-300'">
                                <ArrowUpRight v-if="kpiCards.revenueChangePercent >= 0" class="size-3.5" />
                                <ArrowDownRight v-else class="size-3.5" />
                                {{ Math.abs(kpiCards.revenueChangePercent) }}%
                            </span>
                        </div>
                        <div class="p-5 sm:p-6 transition hover:bg-white/5 relative overflow-hidden group">
                            <div v-if="kpiCards.pendingOrders > 0" class="absolute -bottom-10 -left-10 size-32 rounded-full bg-amber-400/20 blur-3xl transition duration-500 group-hover:bg-amber-400/30"></div>
                            <div class="relative z-10 flex items-center gap-2" :class="kpiCards.pendingOrders > 0 ? 'text-amber-300' : 'text-sky-100/70'">
                                <Clock class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Chờ phê duyệt</p>
                            </div>
                            <p class="relative z-10 mt-3 font-serif text-4xl text-white">{{ animatedValues.pendingOrders }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ Charts Row ═══ -->
            <div class="animate-fade-up grid grid-cols-1 gap-6 lg:grid-cols-5">
                <!-- Revenue Chart -->
                <div class="admin-card lg:col-span-3">
                    <div class="admin-card__header">
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Doanh thu và đơn hàng</h2>
                            <p class="mt-0.5 text-xs text-stone-500">Cột: doanh thu · Đường: số đơn</p>
                        </div>
                        <span class="admin-card__badge">6 tháng</span>
                    </div>
                    <div class="flex items-center gap-6 px-6 pt-3 text-xs text-stone-500">
                        <span class="flex items-center gap-1.5">
                            <span class="size-2.5 rounded-full bg-sky-400" /> Doanh thu
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="size-2.5 rounded-full bg-emerald-400" /> Đơn hàng
                        </span>
                    </div>
                    <div class="p-4">
                        <canvas ref="revenueCanvas" class="h-64 w-full" />
                    </div>
                </div>

                <!-- Donut: User Distribution -->
                <div class="admin-card lg:col-span-2">
                    <div class="admin-card__header">
                        <h2 class="text-base font-semibold text-stone-800">Loại người dùng</h2>
                    </div>
                    <div class="flex flex-col items-center p-6">
                        <canvas ref="donutCanvas" class="size-48" />
                        <div class="mt-5 flex flex-wrap justify-center gap-4">
                            <div
                                v-for="(item, idx) in userDistribution"
                                :key="item.label"
                                class="flex items-center gap-2 text-sm"
                            >
                                <span
                                    class="size-3 rounded-full"
                                    :style="{ backgroundColor: donutColors[idx % donutColors.length] }"
                                />
                                <span class="text-stone-500">{{ item.label }}</span>
                                <span class="font-semibold text-stone-800">{{ item.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Order Status + Recent Orders ═══ -->
            <div class="animate-fade-up grid grid-cols-1 gap-6 lg:grid-cols-5">
                <!-- Order Status Bars -->
                <div class="admin-card lg:col-span-2">
                    <div class="admin-card__header">
                        <h2 class="text-base font-semibold text-stone-800">Trạng thái đơn hàng</h2>
                    </div>
                    <div class="space-y-4 px-6 py-5">
                        <div v-for="(count, status) in orderStatuses" :key="status" class="space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span :class="statusTextColors[status] ?? 'text-slate-400'">
                                    {{ statusLabels[status] ?? status }}
                                </span>
                                <span class="font-semibold text-stone-800">{{ count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-800">
                                <div
                                    class="h-full rounded-full transition-all duration-700"
                                    :class="statusColors[status] ?? 'bg-slate-600'"
                                    :style="{ width: `${Math.max((count as number) / Math.max(...Object.values(orderStatuses).map(Number), 1) * 100, 5)}%` }"
                                />
                            </div>
                        </div>
                        <div v-if="Object.keys(orderStatuses).length === 0" class="py-8 text-center text-sm text-stone-500">
                            Chưa có đơn hàng nào
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="admin-card lg:col-span-3">
                    <div class="admin-card__header">
                        <h2 class="text-base font-semibold text-stone-800">Đơn hàng gần đây</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-stone-200 text-xs font-medium uppercase tracking-wider text-stone-500">
                                    <th class="px-6 py-3">Mã</th>
                                    <th class="px-6 py-3">Khách hàng</th>
                                    <th class="px-6 py-3">Dịch vụ</th>
                                    <th class="px-6 py-3">Trạng thái</th>
                                    <th class="px-6 py-3 text-right">Số tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id"
                                    class="admin-table-row"
                                >
                                    <td class="whitespace-nowrap px-6 py-3 font-mono text-xs text-stone-500">{{ order.ma_don }}</td>
                                    <td class="whitespace-nowrap px-6 py-3 font-medium text-stone-700">{{ order.khach_hang }}</td>
                                    <td class="max-w-[160px] truncate px-6 py-3 text-stone-500">{{ order.dich_vu }}</td>
                                    <td class="px-6 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                            :class="statusTextColors[order.trang_thai] ?? 'text-slate-400'"
                                        >
                                            {{ statusLabels[order.trang_thai] ?? order.trang_thai }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3 text-right font-semibold text-stone-800">
                                        {{ formatFullVND(order.tong_tien) }}
                                    </td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-stone-500">Chưa có đơn hàng</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ═══ Recent Users ═══ -->
            <div class="animate-fade-up admin-card">
                <div class="admin-card__header">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-sky-500/10 p-2">
                            <UserPlus class="size-4 text-sky-400" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-stone-800">Người dùng mới đăng ký</h2>
                            <p class="text-xs text-stone-500">5 tài khoản gần nhất</p>
                        </div>
                    </div>
                    <Link href="/admin/users" class="admin-card__badge hover:bg-slate-700">
                        Xem tất cả →
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-xs font-medium uppercase tracking-wider text-stone-500">
                                <th class="px-6 py-3">Người dùng</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Vai trò</th>
                                <th class="px-6 py-3">Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="u in recentUsers"
                                :key="u.id"
                                class="admin-table-row"
                            >
                                <td class="whitespace-nowrap px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-8 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-indigo-500 text-xs font-bold text-white">
                                            {{ u.ho_ten?.charAt(0)?.toUpperCase() ?? '?' }}
                                        </div>
                                        <span class="font-medium text-stone-700">{{ u.ho_ten }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-stone-500">{{ u.email }}</td>
                                <td class="px-6 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="roleColors[u.vai_tro] ?? 'text-slate-400 bg-slate-500/10'"
                                    >
                                        {{ u.vai_tro }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-stone-500">{{ u.ngay_tao }}</td>
                            </tr>
                            <tr v-if="recentUsers.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-stone-500">Chưa có người dùng mới</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

<style scoped>
/* Hero Section */
.hero-section {
    min-height: 40vh;
}
.hero-bg {
    background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 40%, #1e1b4b 70%, #0f172a 100%);
    border: 1px solid rgba(56, 189, 248, 0.1);
    border-radius: inherit;
}
.hero-bg::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.06) 0%, transparent 60%);
}

/* Cards */
.admin-card {
    overflow: hidden;
    border-radius: 2rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    transition: border-color 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.admin-card:hover {
    border-color: #d6d3d1;
}
.admin-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--dl-warm-border, #e7e5e4);
    padding: 1rem 1.5rem;
}
.admin-card__badge {
    border-radius: 0.5rem;
    background: #f5f5f4;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #78716c;
    transition: background 0.2s ease;
}

/* Table Rows */
.admin-table-row {
    border-bottom: 1px solid #f5f5f4;
    transition: background 0.2s ease;
}
.admin-table-row:hover {
    background: #fafaf9;
}

/* Override text colors for light theme */
.admin-card :deep(.text-white) {
    color: #1c1917;
}

.hero-el {
    opacity: 0;
    transform: translateY(30px);
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
@keyframes drift {
    0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
    50% { transform: translate3d(18px, -14px, 0) scale(1.05); }
}

/* === Initial State for GSAP Animations === */
.kpi-card, .animate-fade-up {
    opacity: 0;
    transform: translateY(30px);
}

@media (prefers-reduced-motion: reduce) {
    .hero-el, .hero-orb, .kpi-card, .animate-fade-up {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
}
</style>
