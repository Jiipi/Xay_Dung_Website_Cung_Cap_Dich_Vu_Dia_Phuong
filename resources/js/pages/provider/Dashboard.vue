<script setup lang="ts">
import { ref, onMounted, nextTick, computed } from 'vue';
import { Head, Link, usePage, Deferred } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowRight,
    ArrowUpRight,
    Briefcase,
    Calendar,
    CalendarCheck,
    ChevronRight,
    Clock,
    ClipboardList,
    Compass,
    DollarSign,
    MapPin,
    MessageSquareText,
    Package,
    Settings,
    ShoppingBag,
    Sparkles,
    Star,
    TrendingUp,
    User,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';
import AppSkeleton from '@/components/ui/AppSkeleton.vue';
import { useAnimations } from '@/composables/useAnimations';

// ─── Props from Controller ───────────────────────────────────
interface KpiCards {
    totalRevenue: number;
    revenueChangePercent: number;
    totalOrders: number;
    ordersThisMonth: number;
    ordersChangePercent: number;
    avgRating: number;
    totalReviews: number;
    pendingOrders: number;
}

interface ChartData {
    month: string;
    monthFull: string;
    revenue: number;
    orders: number;
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

interface TopService {
    id: number;
    ten_dich_vu: string;
    so_don: number;
    rating: number;
}

interface Appointment {
    id: number;
    khach_hang: string;
    dich_vu: string;
    thoi_gian: string;
    dia_diem: string;
    trang_thai: string;
}

interface Review {
    id: number;
    khach_hang: string;
    avatar: string | null;
    so_sao: number;
    noi_dung: string;
    ngay: string;
    phan_hoi: string | null;
}

interface ProviderProfile {
    ten_thuong_hieu: string;
    diem_danh_gia: number;
}

const props = defineProps<{
    kpiCards: KpiCards;
    revenueChart: ChartData[];
    recentOrders: RecentOrder[];
    topServices: TopService[];
    maxServiceOrders: number;
    upcomingAppointments: Appointment[];
    latestReviews: Review[];
    providerProfile: ProviderProfile | null;
}>();

const page = usePage();
const userName = computed(() => props.providerProfile?.ten_thuong_hieu || page.props.auth?.user?.name || 'Nhà cung cấp');
const firstName = computed(() => (page.props.auth?.user?.name || 'bạn').trim().split(' ').filter(Boolean).slice(-1)[0] || 'bạn');

// ─── Formatters ──────────────────────────────────────────────
const formatVND = (value: number) => {
    if (value >= 1_000_000) {
        return (value / 1_000_000).toFixed(1).replace('.0', '') + 'M đ';
    }
    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
};

const formatFullVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

// ─── Status Mapping ──────────────────────────────────────────
const statusStyles: Record<string, string> = {
    cho_xac_nhan: 'bg-amber-50 text-amber-700 ring-amber-200',
    da_xac_nhan: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    dang_thuc_hien: 'bg-sky-50 text-sky-700 ring-sky-200',
    hoan_thanh: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    da_huy: 'bg-red-50 text-red-700 ring-red-200',
};

const statusLabels: Record<string, string> = {
    cho_xac_nhan: 'Chờ xác nhận',
    da_xac_nhan: 'Đã xác nhận',
    dang_thuc_hien: 'Đang thực hiện',
    hoan_thanh: 'Hoàn thành',
    da_huy: 'Đã hủy',
};

// ─── Hero Summary ────────────────────────────────────────────
const heroSummary = computed(() => {
    if (props.kpiCards.totalOrders === 0) {
        return 'Bạn chưa có đơn hàng nào. Hãy tạo dịch vụ đầu tiên để bắt đầu nhận booking từ khách hàng.';
    }
    const notes: string[] = [];
    if (props.kpiCards.pendingOrders > 0) notes.push(`${props.kpiCards.pendingOrders} đơn chờ xác nhận`);
    if (props.kpiCards.ordersThisMonth > 0) notes.push(`${props.kpiCards.ordersThisMonth} đơn tháng này`);
    return notes.length > 0
        ? `Hiện bạn có ${notes.join(', ')}. Doanh thu tháng này ${props.kpiCards.revenueChangePercent >= 0 ? 'tăng' : 'giảm'} ${Math.abs(props.kpiCards.revenueChangePercent)}% so với tháng trước.`
        : `Tổng doanh thu đạt ${formatVND(props.kpiCards.totalRevenue)} từ ${props.kpiCards.totalOrders} đơn hàng.`;
});

// ─── Action Items ────────────────────────────────────────────
const actionItems = computed(() => {
    const items = [];
    if (props.kpiCards.pendingOrders > 0) items.push({ title: `${props.kpiCards.pendingOrders} đơn chờ xác nhận`, href: '/provider/bookings', cta: 'Xử lý ngay', icon: ClipboardList });
    if (props.latestReviews.some(r => !r.phan_hoi)) items.push({ title: 'Đánh giá chưa phản hồi', href: '/provider/reviews', cta: 'Phản hồi ngay', icon: MessageSquareText });
    if (props.kpiCards.totalOrders === 0) items.push({ title: 'Tạo dịch vụ mới', href: '/provider/services/create', cta: 'Bắt đầu ngay', icon: Package });
    if (items.length === 0) items.push({ title: 'Quản lý dịch vụ', href: '/provider/services', cta: 'Xem dịch vụ', icon: Compass });
    return items.slice(0, 4);
});

// ─── Quick Links ─────────────────────────────────────────────
const quickLinks = [
    { title: 'Quản lý dịch vụ', href: '/provider/services', icon: Briefcase },
    { title: 'Đơn hàng', href: '/provider/bookings', icon: CalendarCheck },
    { title: 'Đánh giá', href: '/provider/reviews', icon: Star },
    { title: 'Cài đặt', href: '/settings/profile', icon: Settings },
];

// ─── Chart (Vanilla Canvas) ─────────────────────────────────
const chartCanvas = ref<HTMLCanvasElement | null>(null);
const chartAnimProgress = ref(0);

function drawChart() {
    const canvas = chartCanvas.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const w = rect.width;
    const h = rect.height;
    const data = props.revenueChart;
    if (data.length === 0) return;

    const padLeft = 70;
    const padRight = 20;
    const padTop = 20;
    const padBottom = 40;
    const chartW = w - padLeft - padRight;
    const chartH = h - padTop - padBottom;

    const maxRevenue = Math.max(...data.map((d) => d.revenue), 1);
    const barWidth = chartW / data.length;
    const innerBarWidth = barWidth * 0.5;

    ctx.clearRect(0, 0, w, h);

    // Grid lines
    const gridSteps = 4;
    ctx.strokeStyle = '#f0f0f0';
    ctx.lineWidth = 1;
    ctx.font = '11px Inter, system-ui, sans-serif';
    ctx.fillStyle = '#a8a29e';
    ctx.textAlign = 'right';

    for (let i = 0; i <= gridSteps; i++) {
        const y = padTop + chartH - (chartH / gridSteps) * i;
        ctx.beginPath();
        ctx.moveTo(padLeft, y);
        ctx.lineTo(w - padRight, y);
        ctx.stroke();

        const val = (maxRevenue / gridSteps) * i;
        ctx.fillText(formatVND(val), padLeft - 10, y + 4);
    }

    const progress = chartAnimProgress.value;

    // Bars
    data.forEach((d, i) => {
        const barH = (d.revenue / maxRevenue) * chartH * progress;
        const x = padLeft + i * barWidth + (barWidth - innerBarWidth) / 2;
        const y = padTop + chartH - barH;

        // Bar gradient — provider warm palette
        const grad = ctx.createLinearGradient(x, y, x, padTop + chartH);
        grad.addColorStop(0, '#f97316');
        grad.addColorStop(1, '#ea580c');
        ctx.fillStyle = grad;

        // Rounded top bar
        const radius = 6;
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + innerBarWidth - radius, y);
        ctx.quadraticCurveTo(x + innerBarWidth, y, x + innerBarWidth, y + radius);
        ctx.lineTo(x + innerBarWidth, padTop + chartH);
        ctx.lineTo(x, padTop + chartH);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.fill();

        // Month label
        ctx.fillStyle = '#78716c';
        ctx.textAlign = 'center';
        ctx.font = '12px Inter, system-ui, sans-serif';
        ctx.fillText(d.month, padLeft + i * barWidth + barWidth / 2, h - padBottom + 20);
    });

    // Trend line (orders)
    if (data.some((d) => d.orders > 0)) {
        const maxOrders = Math.max(...data.map((d) => d.orders), 1);
        ctx.strokeStyle = '#0ea5e9';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.setLineDash([]);
        ctx.beginPath();

        data.forEach((d, i) => {
            const x = padLeft + i * barWidth + barWidth / 2;
            const y = padTop + chartH - (d.orders / maxOrders) * chartH * progress;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();

        // Dots
        data.forEach((d, i) => {
            const x = padLeft + i * barWidth + barWidth / 2;
            const y = padTop + chartH - (d.orders / maxOrders) * chartH * progress;
            ctx.fillStyle = '#fff';
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = '#0ea5e9';
            ctx.beginPath();
            ctx.arc(x, y, 3, 0, Math.PI * 2);
            ctx.fill();
        });
    }
}

function animateChart() {
    const start = performance.now();
    const duration = 800;

    function step(timestamp: number) {
        const elapsed = timestamp - start;
        chartAnimProgress.value = Math.min(elapsed / duration, 1);
        drawChart();
        if (elapsed < duration) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

// ─── Animations ──────────────────────────────────────────────
const { animateHeroEntrance, animateStagger, animateFadeUp } = useAnimations();
const heroBadge = ref<HTMLElement | null>(null);
const heroHeadline = ref<HTMLElement | null>(null);
const heroDesc = ref<HTMLElement | null>(null);
const heroButtons = ref<HTMLElement | null>(null);
const heroStats = ref<HTMLElement | null>(null);
const contentSections = ref<HTMLElement[]>([]);

onMounted(async () => {
    await nextTick();
    animateChart();
    window.addEventListener('resize', drawChart);

    animateHeroEntrance({
        badge: heroBadge,
        headline: heroHeadline,
        description: heroDesc,
        searchBar: heroButtons,
        stats: heroStats,
    });
    
    animateStagger('.action-grid', '.action-card');
    animateStagger('.quick-links', '.quick-link');
    contentSections.value.forEach(el => {
        if(el) animateFadeUp(el, { duration: 0.6, y: 40 });
    });
});
</script>

<template>
    <Head title="Nhà cung cấp - Dashboard" />

    <ProviderLayout activePage="dashboard">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- ══════ Hero Section ══════ -->
            <section class="hero-section overflow-hidden rounded-[2rem] shadow-xl relative text-white">
                <div class="hero-bg absolute inset-0 z-0"></div>
                <!-- NOISE OVERLAY -->
                <div class="absolute inset-0 z-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

                <div class="relative z-10 grid gap-8 px-6 py-12 sm:px-10 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="flex flex-col justify-center">
                        <div ref="heroBadge" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-white self-start backdrop-blur-md">
                            <Sparkles class="size-4" />
                            Bảng điều khiển nhà cung cấp
                        </div>
                        <h1 ref="heroHeadline" class="mt-6 max-w-2xl font-serif text-4xl sm:text-5xl lg:text-6xl text-white">
                            Chào {{ firstName }}, <br><em class="opacity-85 font-serif italic">kinh doanh tại Đà Lạt</em>
                        </h1>
                        <p ref="heroDesc" class="mt-4 max-w-2xl text-base leading-7 text-amber-50 sm:text-lg">{{ heroSummary }}</p>
                        <div ref="heroButtons" class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link href="/provider/bookings" class="btn inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-orange-950 transition hover:bg-stone-100">
                                <ClipboardList class="size-4" />
                                Xem đơn hàng
                            </Link>
                            <Link href="/provider/services/create" class="btn inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 backdrop-blur-md px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                Tạo dịch vụ mới
                                <ArrowRight class="size-4" />
                            </Link>
                        </div>
                    </div>

                    <div ref="heroStats" class="grid grid-cols-2 content-center rounded-[2rem] border border-white/15 bg-white/5 shadow-2xl backdrop-blur-md overflow-hidden">
                        <div class="border-b border-r border-white/10 p-5 sm:p-6 transition hover:bg-white/5">
                            <div class="flex items-center gap-2 text-amber-100/70">
                                <DollarSign class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Tổng doanh thu</p>
                            </div>
                            <p class="mt-3 font-serif text-4xl text-white">{{ formatVND(kpiCards.totalRevenue) }}</p>
                            <span class="mt-2 inline-flex items-center gap-0.5 text-xs font-semibold" :class="kpiCards.revenueChangePercent >= 0 ? 'text-emerald-300' : 'text-red-300'">
                                <ArrowUpRight v-if="kpiCards.revenueChangePercent >= 0" class="size-3.5" />
                                <ArrowDownRight v-else class="size-3.5" />
                                {{ Math.abs(kpiCards.revenueChangePercent) }}%
                            </span>
                        </div>
                        <div class="border-b border-white/10 p-5 sm:p-6 transition hover:bg-white/5">
                            <div class="flex items-center gap-2 text-amber-100/70">
                                <Package class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Đơn hàng</p>
                            </div>
                            <p class="mt-3 font-serif text-4xl text-white">{{ kpiCards.totalOrders }}</p>
                            <p class="mt-2 text-xs text-amber-200/70">+{{ kpiCards.ordersThisMonth }} tháng này</p>
                        </div>
                        <div class="group relative overflow-hidden border-r border-white/10 p-5 sm:p-6 transition hover:bg-white/5">
                            <div v-if="kpiCards.pendingOrders > 0" class="absolute -bottom-10 -left-10 size-32 rounded-full bg-amber-400/20 blur-3xl transition duration-500 group-hover:bg-amber-400/30"></div>
                            <div class="relative z-10 flex items-center gap-2" :class="kpiCards.pendingOrders > 0 ? 'text-amber-300' : 'text-amber-100/70'">
                                <Clock class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Chờ xác nhận</p>
                            </div>
                            <p class="relative z-10 mt-3 font-serif text-4xl text-white">{{ kpiCards.pendingOrders }}</p>
                        </div>
                        <div class="p-5 sm:p-6 transition hover:bg-white/5">
                            <div class="flex items-center gap-2 text-amber-100/70">
                                <Star class="size-4" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Đánh giá</p>
                            </div>
                            <p class="mt-3 font-serif text-4xl text-white">{{ kpiCards.avgRating }} <span class="text-lg text-amber-300">★</span></p>
                            <p class="mt-2 text-xs text-amber-200/70">{{ kpiCards.totalReviews }} đánh giá</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ══════ Action Items + Quick Links ══════ -->
            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section ref="contentSections" class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Cần xử lý</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Các việc nên làm tiếp theo</h2>
                        </div>
                        <Link href="/provider/bookings" class="hidden items-center gap-1 text-sm font-semibold text-orange-700 sm:flex">Mở đơn hàng <ChevronRight class="size-4" /></Link>
                    </div>
                    <div class="action-grid mt-6 grid gap-4 md:grid-cols-2">
                        <Link v-for="item in actionItems" :key="item.title" :href="item.href" class="action-card group rounded-[1.5rem] border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-5 transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-md">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-white text-stone-700 shadow-sm">
                                <component :is="item.icon" class="size-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-bold tracking-tight text-stone-950">{{ item.title }}</h3>
                            <p class="mt-2 text-sm text-stone-500">Ưu tiên xử lý nhanh từ dashboard để không bỏ sót đơn hàng hoặc phản hồi quan trọng.</p>
                            <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-stone-900">{{ item.cta }} <ArrowRight class="size-4 transition group-hover:translate-x-1" /></div>
                        </Link>
                    </div>
                </section>

                <div class="space-y-6">
                    <section ref="contentSections" class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Đi nhanh</p>
                                <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Lối tắt thường dùng</h2>
                            </div>
                            <div class="rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-500">{{ kpiCards.pendingOrders }} chờ</div>
                        </div>
                        <div class="quick-links mt-5 grid gap-3 sm:grid-cols-2">
                            <Link v-for="link in quickLinks" :key="link.href" :href="link.href" class="quick-link group flex items-center gap-3 rounded-[1.25rem] border border-stone-200 px-4 py-4 transition hover:border-orange-200 hover:bg-orange-50/50">
                                <div class="flex size-10 items-center justify-center rounded-2xl bg-stone-100 text-stone-700"><component :is="link.icon" class="size-5" /></div>
                                <span class="min-w-0 flex-1 text-sm font-semibold text-stone-900">{{ link.title }}</span>
                                <ChevronRight class="size-4 text-stone-400 transition group-hover:text-orange-700" />
                            </Link>
                        </div>
                    </section>

                    <!-- Provider Profile Summary -->
                    <section class="rounded-[2rem] border border-stone-200 bg-stone-950 p-6 text-white shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-white/10"><User class="size-5" /></div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-300">Hồ sơ</p>
                                <h2 class="text-2xl font-black tracking-tight">{{ userName }}</h2>
                            </div>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-[1.25rem] border border-white/10 bg-white/5 p-4 text-center">
                                <p class="font-serif text-2xl text-white">{{ kpiCards.avgRating }}</p>
                                <p class="mt-1 text-xs text-stone-400">Điểm đánh giá</p>
                            </div>
                            <div class="rounded-[1.25rem] border border-white/10 bg-white/5 p-4 text-center">
                                <p class="font-serif text-2xl text-white">{{ kpiCards.totalOrders }}</p>
                                <p class="mt-1 text-xs text-stone-400">Tổng đơn</p>
                            </div>
                            <div class="rounded-[1.25rem] border border-white/10 bg-white/5 p-4 text-center">
                                <p class="font-serif text-2xl text-white">{{ kpiCards.totalReviews }}</p>
                                <p class="mt-1 text-xs text-stone-400">Đánh giá</p>
                            </div>
                        </div>
                        <Link href="/provider/profile" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-stone-950 transition hover:bg-stone-100">Cập nhật hồ sơ <ArrowRight class="size-4" /></Link>
                    </section>
                </div>
            </div>

            <!-- ══════ Revenue Chart + Upcoming Appointments ══════ -->
            <div class="mt-8 grid gap-6 lg:grid-cols-5">
                <!-- Revenue Chart -->
                <section ref="contentSections" class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm lg:col-span-3">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Biểu đồ</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Doanh thu 6 tháng</h2>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-stone-500">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2.5 rounded-full bg-orange-400" /> Doanh thu
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="size-2.5 rounded-full bg-sky-400" /> Đơn hàng
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <canvas ref="chartCanvas" class="h-56 w-full" />
                    </div>
                </section>

                <!-- Upcoming Appointments -->
                <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Sắp tới</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Lịch hẹn</h2>
                        </div>
                        <Link href="/provider/bookings" class="text-xs font-medium text-orange-700 transition hover:text-orange-800">Xem tất cả</Link>
                    </div>
                    <div class="divide-y divide-stone-50 px-4 py-2">
                        <div v-if="upcomingAppointments.length === 0" class="flex flex-col items-center justify-center py-10 text-stone-400">
                            <Calendar class="mb-2 size-8 text-stone-300" />
                            <p class="text-sm">Không có lịch hẹn</p>
                        </div>
                        <div
                            v-for="apt in upcomingAppointments"
                            :key="apt.id"
                            class="flex items-start gap-3 rounded-xl px-2 py-3 transition-colors hover:bg-stone-50"
                        >
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                                <Calendar class="size-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-stone-950">{{ apt.dich_vu }}</p>
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-stone-500">
                                    <User class="size-3" /> {{ apt.khach_hang }}
                                </p>
                                <div class="mt-1 flex items-center gap-3 text-xs text-stone-400">
                                    <span class="flex items-center gap-1">
                                        <Clock class="size-3" /> {{ apt.thoi_gian }}
                                    </span>
                                    <span v-if="apt.dia_diem" class="flex items-center gap-1 truncate">
                                        <MapPin class="size-3" /> {{ apt.dia_diem }}
                                    </span>
                                </div>
                            </div>
                            <span
                                class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-medium ring-1"
                                :class="statusStyles[apt.trang_thai] ?? 'bg-stone-50 text-stone-600 ring-stone-200'"
                            >
                                {{ statusLabels[apt.trang_thai] ?? apt.trang_thai }}
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ══════ Recent Orders ══════ -->
            <section ref="contentSections" class="mt-8 overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Gần đây</p>
                        <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Đơn hàng gần đây</h2>
                    </div>
                    <Link href="/provider/bookings" class="text-xs font-medium text-orange-700 transition hover:text-orange-800">Xem tất cả</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <th class="px-6 py-3.5">Mã ĐH</th>
                                <th class="px-6 py-3.5">Khách hàng</th>
                                <th class="px-6 py-3.5">Dịch vụ</th>
                                <th class="px-6 py-3.5">Ngày</th>
                                <th class="px-6 py-3.5">Trạng thái</th>
                                <th class="px-6 py-3.5 text-right">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="order in recentOrders"
                                :key="order.id"
                                class="border-b border-stone-50 transition-colors hover:bg-stone-50/80"
                            >
                                <td class="whitespace-nowrap px-6 py-3.5 font-mono text-xs text-stone-400">{{ order.ma_don }}</td>
                                <td class="whitespace-nowrap px-6 py-3.5 font-medium text-stone-950">{{ order.khach_hang }}</td>
                                <td class="max-w-[180px] truncate px-6 py-3.5 text-stone-600">{{ order.dich_vu }}</td>
                                <td class="whitespace-nowrap px-6 py-3.5 text-stone-500">{{ order.ngay }}</td>
                                <td class="px-6 py-3.5">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1"
                                        :class="statusStyles[order.trang_thai] ?? 'bg-stone-50 text-stone-600 ring-stone-200'"
                                    >
                                        {{ statusLabels[order.trang_thai] ?? order.trang_thai }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-3.5 text-right font-semibold text-stone-950">{{ formatFullVND(order.tong_tien) }}</td>
                            </tr>
                            <tr v-if="recentOrders.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-stone-400">Chưa có đơn hàng nào</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ══════ Top Services + Latest Reviews ══════ -->
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <!-- Top Services -->
                <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Nổi bật</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Dịch vụ hàng đầu</h2>
                        </div>
                        <Link href="/provider/services" class="text-xs font-medium text-orange-700 transition hover:text-orange-800">Quản lý</Link>
                    </div>
                    <div class="divide-y divide-stone-50 px-6 py-2">
                        <div v-if="topServices.length === 0" class="py-10 text-center text-sm text-stone-400">Chưa có dịch vụ</div>
                        <div
                            v-for="(svc, idx) in topServices"
                            :key="svc.id"
                            class="flex items-center gap-4 py-3.5"
                        >
                            <span
                                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold"
                                :class="idx === 0 ? 'bg-amber-100 text-amber-700' : idx === 1 ? 'bg-stone-100 text-stone-600' : idx === 2 ? 'bg-orange-50 text-orange-600' : 'bg-stone-50 text-stone-400'"
                            >
                                {{ idx + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-stone-950">{{ svc.ten_dich_vu }}</p>
                                <div class="mt-1.5 flex items-center gap-3">
                                    <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-stone-100">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-orange-400 to-amber-400 transition-all duration-700"
                                            :style="{ width: `${Math.max((svc.so_don / maxServiceOrders) * 100, 5)}%` }"
                                        />
                                    </div>
                                    <span class="shrink-0 text-xs text-stone-500">{{ svc.so_don }} đơn</span>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1 text-sm">
                                <Star class="size-3.5 fill-amber-400 text-amber-400" />
                                <span class="font-semibold text-stone-800">{{ svc.rating || '—' }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Latest Reviews -->
                <section class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-stone-100 px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700">Phản hồi</p>
                            <h2 class="mt-1 text-xl font-black tracking-tight text-stone-950">Đánh giá mới nhất</h2>
                        </div>
                        <Link href="/provider/reviews" class="text-xs font-medium text-orange-700 transition hover:text-orange-800">Xem tất cả</Link>
                    </div>
                    <div class="divide-y divide-stone-50 px-6 py-2">
                        <div v-if="latestReviews.length === 0" class="py-10 text-center text-sm text-stone-400">Chưa có đánh giá</div>
                        <div
                            v-for="review in latestReviews"
                            :key="review.id"
                            class="py-4"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-stone-100">
                                    <img
                                        v-if="review.avatar"
                                        :src="review.avatar"
                                        :alt="review.khach_hang"
                                        class="size-full object-cover"
                                        referrerpolicy="no-referrer"
                                    />
                                    <User v-else class="size-4 text-stone-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-stone-950">{{ review.khach_hang }}</p>
                                        <span class="text-xs text-stone-400">{{ review.ngay }}</span>
                                    </div>
                                    <div class="mt-1 flex gap-0.5">
                                        <Star
                                            v-for="s in 5"
                                            :key="s"
                                            class="size-3"
                                            :class="s <= review.so_sao ? 'fill-amber-400 text-amber-400' : 'text-stone-200'"
                                        />
                                    </div>
                                    <p v-if="review.noi_dung" class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-stone-600">
                                        {{ review.noi_dung }}
                                    </p>
                                    <div v-if="review.phan_hoi" class="mt-2 rounded-lg bg-orange-50 px-3 py-2 text-xs text-orange-700">
                                        <span class="font-medium">Phản hồi:</span> {{ review.phan_hoi }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </ProviderLayout>
</template>

<style scoped>
.hero-section {
    min-height: 40vh;
}
.hero-bg {
    background: linear-gradient(
        135deg in oklch,
        #7c2d12 0%,      /* Terracotta đậm */
        #c2410c 40%,     /* Cam cháy */
        #f97316 100%     /* Cam sáng */
    );
}
</style>
