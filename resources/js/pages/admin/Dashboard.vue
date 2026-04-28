<script setup lang="ts">
import { ref, onMounted, nextTick, computed } from 'vue';
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
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout activePage="dashboard">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 space-y-8">
            <!-- ═══ Hero / Welcome ═══ -->
            <div class="admin-hero">
                <div class="admin-hero__bg" />
                <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <Sparkles class="size-4 text-sky-300" />
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-sky-300/80">Admin Panel</span>
                        </div>
                        <h1 class="text-2xl font-black text-white md:text-3xl">
                            {{ greeting }}, {{ adminName }}!
                        </h1>
                        <p class="mt-2 text-sm text-slate-400 max-w-md">
                            Hệ thống đang hoạt động ổn định.
                            <template v-if="kpiCards.pendingOrders > 0 || kpiCards.pendingServices > 0">
                                Có <strong class="text-amber-400">{{ kpiCards.pendingOrders + kpiCards.pendingServices }}</strong> mục cần xử lý.
                            </template>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            href="/admin/services"
                            class="flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/15"
                        >
                            <CheckSquare class="size-4" /> Duyệt dịch vụ
                        </Link>
                        <Link
                            href="/admin/users"
                            class="flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/15"
                        >
                            <Users class="size-4" /> Quản lý users
                        </Link>
                    </div>
                </div>
            </div>

            <!-- ═══ KPI Cards ═══ -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Users -->
                <div class="admin-kpi-card group">
                    <div class="admin-kpi-card__glow admin-kpi-card__glow--sky" />
                    <div class="relative">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="admin-kpi-card__icon admin-kpi-card__icon--sky">
                                <Users class="size-5" />
                            </div>
                            <span
                                class="flex items-center gap-0.5 text-xs font-semibold"
                                :class="kpiCards.usersChangePercent >= 0 ? 'text-emerald-400' : 'text-red-400'"
                            >
                                <ArrowUpRight v-if="kpiCards.usersChangePercent >= 0" class="size-3.5" />
                                <ArrowDownRight v-else class="size-3.5" />
                                {{ Math.abs(kpiCards.usersChangePercent) }}%
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-stone-800">{{ animatedValues.totalUsers }}</p>
                        <p class="mt-1 text-sm text-stone-500">Tổng người dùng</p>
                    </div>
                </div>

                <!-- Services -->
                <div class="admin-kpi-card group">
                    <div class="admin-kpi-card__glow admin-kpi-card__glow--emerald" />
                    <div class="relative">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="admin-kpi-card__icon admin-kpi-card__icon--emerald">
                                <Package class="size-5" />
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-stone-800">{{ animatedValues.totalServices }}</p>
                        <p class="mt-1 text-sm text-stone-500">Tổng dịch vụ</p>
                        <p class="mt-1.5 text-xs text-amber-400">{{ kpiCards.pendingServices }} dịch vụ chờ duyệt</p>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="admin-kpi-card group">
                    <div class="admin-kpi-card__glow admin-kpi-card__glow--violet" />
                    <div class="relative">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="admin-kpi-card__icon admin-kpi-card__icon--violet">
                                <DollarSign class="size-5" />
                            </div>
                            <span
                                class="flex items-center gap-0.5 text-xs font-semibold"
                                :class="kpiCards.revenueChangePercent >= 0 ? 'text-emerald-400' : 'text-red-400'"
                            >
                                <TrendingUp class="size-3.5" />
                                {{ kpiCards.revenueChangePercent >= 0 ? '+' : '' }}{{ kpiCards.revenueChangePercent }}%
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-stone-800">{{ formatVND(animatedValues.totalRevenue) }}</p>
                        <p class="mt-1 text-sm text-stone-500">Doanh thu</p>
                    </div>
                </div>

                <!-- Pending -->
                <div class="admin-kpi-card group">
                    <div class="admin-kpi-card__glow admin-kpi-card__glow--amber" />
                    <div class="relative">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="admin-kpi-card__icon admin-kpi-card__icon--amber">
                                <Clock class="size-5" />
                            </div>
                            <span v-if="kpiCards.pendingOrders > 0" class="admin-kpi-card__pulse">
                                <span class="admin-kpi-card__pulse-ring" />
                                <span class="admin-kpi-card__pulse-dot" />
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-stone-800">{{ animatedValues.pendingOrders }}</p>
                        <p class="mt-1 text-sm text-stone-500">Chờ phê duyệt</p>
                        <p v-if="kpiCards.pendingOrders > 0" class="mt-1.5 text-xs text-amber-400">⚡ Cần hành động</p>
                    </div>
                </div>
            </div>

            <!-- ═══ Charts Row ═══ -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
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
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
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
            <div class="admin-card">
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
    </AdminLayout>
</template>

<style scoped>
/* Hero Section */
.admin-hero {
    position: relative;
    padding: 2rem 2.5rem;
    border-radius: 2rem;
    overflow: hidden;
}
.admin-hero__bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 40%, #1e1b4b 70%, #0f172a 100%);
    border: 1px solid rgba(56, 189, 248, 0.1);
    border-radius: inherit;
}
.admin-hero__bg::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.06) 0%, transparent 60%);
}

/* KPI Cards */
.admin-kpi-card {
    position: relative;
    overflow: hidden;
    border-radius: 1.5rem;
    border: 1px solid var(--dl-warm-border, #e7e5e4);
    background: white;
    padding: 1.25rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.admin-kpi-card:hover {
    border-color: #d6d3d1;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
}
.admin-kpi-card__glow {
    position: absolute;
    top: -2rem;
    right: -2rem;
    width: 6rem;
    height: 6rem;
    border-radius: 50%;
    opacity: 0;
    transition: all 0.4s ease;
}
.admin-kpi-card:hover .admin-kpi-card__glow {
    opacity: 1;
    transform: scale(1.3);
}
.admin-kpi-card__glow--sky { background: radial-gradient(circle, rgba(56,189,248,0.08), transparent); }
.admin-kpi-card__glow--emerald { background: radial-gradient(circle, rgba(52,211,153,0.08), transparent); }
.admin-kpi-card__glow--violet { background: radial-gradient(circle, rgba(139,92,246,0.08), transparent); }
.admin-kpi-card__glow--amber { background: radial-gradient(circle, rgba(245,158,11,0.08), transparent); }

.admin-kpi-card__icon {
    display: flex;
    width: 2.5rem;
    height: 2.5rem;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}
.admin-kpi-card__icon--sky { background: rgba(56,189,248,0.1); color: #0284c7; }
.admin-kpi-card__icon--emerald { background: rgba(52,211,153,0.1); color: #059669; }
.admin-kpi-card__icon--violet { background: rgba(139,92,246,0.1); color: #7c3aed; }
.admin-kpi-card__icon--amber { background: rgba(245,158,11,0.1); color: #d97706; }

.admin-kpi-card:hover .admin-kpi-card__icon {
    transform: scale(1.1);
}

.admin-kpi-card__pulse {
    position: relative;
    display: flex;
    width: 0.625rem;
    height: 0.625rem;
}
.admin-kpi-card__pulse-ring {
    position: absolute;
    display: inline-flex;
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    background: #f59e0b;
    opacity: 0.75;
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
.admin-kpi-card__pulse-dot {
    position: relative;
    display: inline-flex;
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 9999px;
    background: #fbbf24;
}

@keyframes ping {
    75%, 100% { transform: scale(2); opacity: 0; }
}

/* Cards */
.admin-card {
    overflow: hidden;
    border-radius: 1.5rem;
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
.admin-kpi-card :deep(.text-white),
.admin-card :deep(.text-white) {
    color: #1c1917;
}
</style>


