<script setup lang="ts">
import { Head, Link, usePage, Deferred } from '@inertiajs/vue3';
import {
    ArrowRight,
    Calendar,
    CalendarClock,
    CheckCircle2,
    ChevronRight,
    ClipboardList,
    Compass,
    Heart,
    MapPin,
    MessageSquareText,
    Search,
    Settings,
    Sparkles,
    Star,
    User,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import AppSkeleton from '@/components/ui/AppSkeleton.vue';

type Stats = { totalBookings: number; completedBookings: number; pendingBookings: number; upcomingBookings: number; reviewPendingCount: number; totalFavorites: number; unreadNotifications: number };
type Booking = { id: number; code: string; service: string; provider: string; date: string; time: string; status: string; statusLabel: string; price: number; image: string; hasReview: boolean };
type Service = { id: number; title: string; provider: string; category: string; price: number; rating: number; image: string; location: string; badge: string };
type Profile = { name?: string; email?: string; phone?: string | null; address?: string | null };

const props = withDefaults(defineProps<{ stats?: Stats; profile?: Profile; recentBookings?: Booking[]; upcomingBookings?: Booking[]; recommendedServices?: Service[] }>(), {
    stats: () => ({ totalBookings: 0, completedBookings: 0, pendingBookings: 0, upcomingBookings: 0, reviewPendingCount: 0, totalFavorites: 0, unreadNotifications: 0 }),
    profile: () => ({}),
    recentBookings: () => [],
    upcomingBookings: () => [],
    recommendedServices: () => [],
});

const page = usePage();
const userName = computed(() => props.profile.name || page.props.auth?.user?.name || 'Khách hàng');
const firstName = computed(() => userName.value.trim().split(' ').filter(Boolean).slice(-1)[0] || 'bạn');
const formatVND = (value: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

const statusMap: Record<string, { label: string; className: string; icon: any }> = {
    cho_xac_nhan: { label: 'Chờ xác nhận', className: 'bg-amber-50 text-amber-700 ring-amber-200', icon: CalendarClock },
    da_xac_nhan: { label: 'Đã xác nhận', className: 'bg-emerald-50 text-emerald-700 ring-emerald-200', icon: CheckCircle2 },
    dang_thuc_hien: { label: 'Đang thực hiện', className: 'bg-brand-surface text-brand ring-brand', icon: Sparkles },
    hoan_thanh: { label: 'Hoàn thành', className: 'bg-emerald-50 text-emerald-700 ring-emerald-200', icon: CheckCircle2 },
    da_huy: { label: 'Đã hủy', className: 'bg-red-50 text-red-700 ring-red-200', icon: XCircle },
};

function getStatus(status: string) {
    return statusMap[status] || statusMap.cho_xac_nhan;
}

const heroSummary = computed(() => {
    if (props.stats.totalBookings === 0) {
        return 'Bạn chưa có booking nào. Hãy bắt đầu từ một dịch vụ phù hợp hoặc lưu vài lựa chọn yêu thích để quay lại nhanh hơn.';
    }

    const notes: string[] = [];
    if (props.stats.upcomingBookings > 0) notes.push(`${props.stats.upcomingBookings} lịch sắp tới`);
    if (props.stats.pendingBookings > 0) notes.push(`${props.stats.pendingBookings} đơn chờ xác nhận`);
    if (props.stats.reviewPendingCount > 0) notes.push(`${props.stats.reviewPendingCount} đơn cần đánh giá`);

    return notes.length > 0
        ? `Hiện bạn có ${notes.join(', ')}. Các mục bên dưới ưu tiên cho thao tác nhanh và không bỏ lỡ lịch hẹn.`
        : `Bạn đã hoàn tất ${props.stats.completedBookings} booking. Hệ thống đang gợi ý thêm các dịch vụ phù hợp dựa trên lịch sử của bạn.`;
});

const actionItems = computed(() => {
    const items = [];
    if (props.stats.pendingBookings > 0) items.push({ title: `${props.stats.pendingBookings} đơn chờ xác nhận`, href: '/customer/bookings', cta: 'Xem booking', icon: CalendarClock });
    if (props.stats.reviewPendingCount > 0) items.push({ title: `${props.stats.reviewPendingCount} đơn nên đánh giá`, href: '/customer/bookings', cta: 'Đánh giá ngay', icon: MessageSquareText });
    if (props.stats.unreadNotifications > 0) items.push({ title: `${props.stats.unreadNotifications} thông báo chưa đọc`, href: '/customer/notifications', cta: 'Mở thông báo', icon: Star });
    if (props.stats.totalFavorites > 0) items.push({ title: `${props.stats.totalFavorites} dịch vụ đã lưu`, href: '/customer/favorites', cta: 'Mở yêu thích', icon: Heart });
    if (items.length === 0) items.push({ title: 'Khám phá dịch vụ đầu tiên', href: '/services', cta: 'Tìm dịch vụ', icon: Compass });
    return items.slice(0, 4);
});

const quickLinks = [
    { title: 'Khám phá', href: '/services', icon: Search },
    { title: 'Booking của tôi', href: '/customer/bookings', icon: ClipboardList },
    { title: 'Yêu thích', href: '/customer/favorites', icon: Heart },
    { title: 'Cài đặt', href: '/settings/profile', icon: Settings },
];

const profileChecks = computed(() => [
    { label: 'Email', value: props.profile.email || 'Chưa có', done: Boolean(props.profile.email) },
    { label: 'Số điện thoại', value: props.profile.phone || 'Nên bổ sung để liên hệ nhanh', done: Boolean(props.profile.phone) },
    { label: 'Địa chỉ', value: props.profile.address || 'Nên bổ sung để đặt dịch vụ tại nhà nhanh hơn', done: Boolean(props.profile.address) },
]);
</script>

<template>
    <Head title="Trang chủ khách hàng" />

    <CustomerLayout activePage="dashboard">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <section class="hero-section overflow-hidden rounded-[2rem] shadow-xl relative text-white">
                <div class="hero-bg absolute inset-0 z-0"></div>
                <!-- NOISE OVERLAY -->
                <div class="absolute inset-0 z-0 opacity-5 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>
                
                <div class="relative z-10 grid gap-8 px-6 py-12 sm:px-10 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="flex flex-col justify-center">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-white self-start backdrop-blur-md">
                            <Sparkles class="size-4" />
                            Trang chủ khách hàng
                        </div>
                        <h1 class="mt-6 max-w-2xl font-serif text-4xl sm:text-5xl lg:text-6xl text-white">
                            Chào {{ firstName }}, dịch vụ Đà Lạt <br><em class="opacity-85 font-serif italic">ngay trong tầm tay</em>
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-emerald-50 sm:text-lg">{{ heroSummary }}</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link href="/services" class="btn inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-emerald-950 transition hover:bg-stone-100">
                                <Compass class="size-4" />
                                Tìm dịch vụ mới
                            </Link>
                            <Link href="/customer/bookings" class="btn inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 backdrop-blur-md px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                Xem lịch hẹn
                                <ArrowRight class="size-4" />
                            </Link>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 content-center">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur-sm card">
                            <p class="text-sm font-medium text-emerald-100">Tổng booking</p>
                            <p class="mt-2 font-serif text-4xl text-white">{{ props.stats.totalBookings }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur-sm card">
                            <p class="text-sm font-medium text-emerald-100">Lịch sắp tới</p>
                            <p class="mt-2 font-serif text-4xl text-white">{{ props.stats.upcomingBookings }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur-sm card">
                            <p class="text-sm font-medium text-amber-200">Chờ xác nhận</p>
                            <p class="mt-2 font-serif text-4xl text-white">{{ props.stats.pendingBookings }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur-sm card">
                            <p class="text-sm font-medium text-emerald-100">Đã lưu</p>
                            <p class="mt-2 font-serif text-4xl text-white">{{ props.stats.totalFavorites }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Cần xử lý</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Các việc nên làm tiếp theo</h2>
                        </div>
                        <Link href="/customer/bookings" class="hidden items-center gap-1 text-sm font-semibold text-emerald-700 sm:flex">Mở lịch sử <ChevronRight class="size-4" /></Link>
                    </div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <Link v-for="item in actionItems" :key="item.title" :href="item.href" class="group rounded-[1.5rem] border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-5 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-white text-stone-700 shadow-sm">
                                <component :is="item.icon" class="size-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-bold tracking-tight text-stone-950">{{ item.title }}</h3>
                            <p class="mt-2 text-sm text-stone-500">Ưu tiên thao tác nhanh từ trang chủ để không bỏ sót lịch hẹn hoặc phản hồi cần thiết.</p>
                            <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-stone-900">{{ item.cta }} <ArrowRight class="size-4 transition group-hover:translate-x-1" /></div>
                        </Link>
                    </div>
                </section>

                <div class="space-y-6">
                    <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Đi nhanh</p>
                                <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Lối tắt thường dùng</h2>
                            </div>
                            <div class="rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-500">{{ props.stats.unreadNotifications }} mới</div>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <Link v-for="link in quickLinks" :key="link.href" :href="link.href" class="group flex items-center gap-3 rounded-[1.25rem] border border-stone-200 px-4 py-4 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                                <div class="flex size-10 items-center justify-center rounded-2xl bg-stone-100 text-stone-700"><component :is="link.icon" class="size-5" /></div>
                                <span class="min-w-0 flex-1 text-sm font-semibold text-stone-900">{{ link.title }}</span>
                                <ChevronRight class="size-4 text-stone-400 transition group-hover:text-emerald-700" />
                            </Link>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-stone-200 bg-stone-950 p-6 text-white shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-white/10"><User class="size-5" /></div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-300">Tài khoản</p>
                                <h2 class="text-2xl font-black tracking-tight">Thông tin đặt dịch vụ</h2>
                            </div>
                        </div>
                        <div class="mt-5 space-y-3">
                            <div v-for="item in profileChecks" :key="item.label" class="rounded-[1.25rem] border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-white">{{ item.label }}</p>
                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em]" :class="item.done ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200'">{{ item.done ? 'Đã có' : 'Nên bổ sung' }}</span>
                                </div>
                                <p class="mt-2 text-sm text-stone-300">{{ item.value }}</p>
                            </div>
                        </div>
                        <Link href="/settings/profile" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-stone-950 transition hover:bg-stone-100">Cập nhật hồ sơ <ArrowRight class="size-4" /></Link>
                    </section>
                </div>
            </div>

            <section class="mt-8 rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Sắp diễn ra</p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Lịch hẹn gần nhất của bạn</h2>
                    </div>
                    <Link href="/customer/bookings" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700">Xem tất cả booking <ChevronRight class="size-4" /></Link>
                </div>
                <Deferred data="upcomingBookings">
                    <template #fallback>
                        <div class="mt-6">
                            <AppSkeleton variant="card" lines="3" />
                        </div>
                    </template>
                    <div v-if="upcomingBookings && upcomingBookings.length > 0" class="mt-6 grid gap-4 lg:grid-cols-3">
                        <div v-for="booking in upcomingBookings" :key="booking.id" class="card rounded-[1.5rem] border border-stone-200 bg-gradient-to-br from-white to-stone-50 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <span class="rounded-full bg-stone-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-stone-500">{{ booking.code }}</span>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium ring-1" :class="getStatus(booking.status).className"><component :is="getStatus(booking.status).icon" class="size-3.5" />{{ booking.statusLabel }}</span>
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-stone-950">{{ booking.service }}</h3>
                            <p class="mt-1 text-sm text-stone-500">{{ booking.provider }}</p>
                            <div class="mt-4 space-y-2 text-sm text-stone-600">
                                <div class="flex items-center gap-2"><Calendar class="size-4 text-stone-400" />{{ booking.date }}</div>
                                <div class="flex items-center gap-2"><CalendarClock class="size-4 text-stone-400" />{{ booking.time }}</div>
                            </div>
                            <div class="mt-5 flex items-center justify-between">
                                <p class="font-serif text-xl border-t border-transparent">{{ formatVND(booking.price) }}</p>
                                <Link :href="`/customer/bookings/${booking.id}`" class="btn inline-flex items-center gap-1 rounded-full border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">Chi tiết <ChevronRight class="size-4" /></Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="mt-6 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-6 py-10 text-center">
                        <CalendarClock class="mx-auto size-12 text-stone-300" />
                        <h3 class="mt-4 text-lg font-bold text-stone-900">Chưa có lịch hẹn sắp tới</h3>
                        <p class="mt-2 text-sm text-stone-500">Hãy khám phá dịch vụ mới hoặc đặt lại từ danh sách yêu thích của bạn.</p>
                    </div>
                </Deferred>
            </section>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Gần đây</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Lịch sử booking mới nhất</h2>
                        </div>
                        <Link href="/customer/bookings" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700">Xem đầy đủ <ChevronRight class="size-4" /></Link>
                    </div>
                    <Deferred data="recentBookings">
                        <template #fallback>
                            <div class="mt-6 space-y-4">
                                <AppSkeleton variant="card" />
                            </div>
                        </template>
                        <div v-if="recentBookings && recentBookings.length > 0" class="mt-6 space-y-4">
                            <article v-for="booking in recentBookings" :key="booking.id" class="card group flex flex-col gap-4 rounded-[1.5rem] border border-stone-200 p-4 transition hover:border-emerald-200 hover:shadow-sm sm:flex-row">
                                <img :src="booking.image" :alt="booking.service" class="h-28 w-full rounded-[1.25rem] object-cover sm:w-32" referrerpolicy="no-referrer" />
                                <div class="flex min-w-0 flex-1 flex-col justify-between">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-stone-400">{{ booking.code }}</p>
                                            <h3 class="mt-1 truncate text-lg font-bold text-stone-950">{{ booking.service }}</h3>
                                            <p class="mt-1 text-sm text-stone-500">{{ booking.provider }}</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium ring-1" :class="getStatus(booking.status).className"><component :is="getStatus(booking.status).icon" class="size-3.5" />{{ booking.statusLabel }}</span>
                                    </div>
                                    <div class="mt-4 flex flex-col gap-3 border-t border-stone-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex flex-wrap gap-4 text-sm text-stone-500">
                                            <span class="inline-flex items-center gap-2"><Calendar class="size-4 text-stone-400" />{{ booking.date }}</span>
                                            <span class="inline-flex items-center gap-2"><CalendarClock class="size-4 text-stone-400" />{{ booking.time }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="mr-1 font-serif text-xl border-t border-transparent">{{ formatVND(booking.price) }}</span>
                                            <Link v-if="booking.status === 'hoan_thanh' && !booking.hasReview" :href="`/customer/reviews/create?booking_id=${booking.id}`" class="btn inline-flex items-center gap-1 rounded-full bg-amber-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-600">Đánh giá <Star class="size-4" /></Link>
                                            <Link :href="`/customer/bookings/${booking.id}`" class="btn inline-flex items-center gap-1 rounded-full border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">Chi tiết <ChevronRight class="size-4" /></Link>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <div v-else class="mt-6 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-6 py-10 text-center">
                            <ClipboardList class="mx-auto size-12 text-stone-300" />
                            <h3 class="mt-4 text-lg font-bold text-stone-900">Chưa có lịch sử booking</h3>
                            <p class="mt-2 text-sm text-stone-500">Sau khi đặt dịch vụ, toàn bộ trạng thái xử lý sẽ xuất hiện ở đây.</p>
                        </div>
                    </Deferred>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Gợi ý cho bạn</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-stone-950">Dựa trên lịch sử sử dụng</h2>
                        </div>
                        <Link href="/services" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700">Mở dịch vụ <ChevronRight class="size-4" /></Link>
                    </div>
                    <Deferred data="recommendedServices">
                        <template #fallback>
                            <div class="mt-6 space-y-4">
                                <AppSkeleton variant="card" lines="3" />
                            </div>
                        </template>
                        <div v-if="recommendedServices && recommendedServices.length > 0" class="mt-6 space-y-4">
                            <article v-for="service in recommendedServices" :key="service.id" class="service-card overflow-hidden rounded-[1.5rem] border border-stone-200 bg-white transition hover:shadow-md">
                                <img :src="service.image" :alt="service.title" class="h-44 w-full object-cover" referrerpolicy="no-referrer" />
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">{{ service.category }}</p>
                                            <h3 class="mt-2 text-lg font-black text-stone-950">{{ service.title }}</h3>
                                        </div>
                                        <div v-if="service.rating > 0" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700"><Star class="size-3.5 fill-current" />{{ service.rating.toFixed(1) }}</div>
                                    </div>
                                    <p class="mt-2 text-sm text-stone-500">{{ service.provider }}</p>
                                    <div class="mt-3 flex items-center gap-2 text-sm text-stone-500"><MapPin class="size-4 text-stone-400" />{{ service.location }}</div>
                                    <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">{{ service.badge }}</p>
                                            <p class="mt-1 font-serif text-2xl border-t border-transparent">{{ formatVND(service.price) }}</p>
                                        </div>
                                        <Link :href="`/services/${service.id}`" class="btn inline-flex items-center gap-2 rounded-full bg-stone-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800">Xem chi tiết <ArrowRight class="size-4" /></Link>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <div v-else class="mt-6 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-6 py-10 text-center">
                            <Compass class="mx-auto size-12 text-stone-300" />
                            <h3 class="mt-4 text-lg font-bold text-stone-900">Chưa có dữ liệu gợi ý đủ mạnh</h3>
                            <p class="mt-2 text-sm text-stone-500">Hãy lưu vài dịch vụ yêu thích hoặc tạo booking đầu tiên để hệ thống đề xuất sát hơn.</p>
                        </div>
                    </Deferred>
                </section>
            </div>
        </div>
    </CustomerLayout>
</template>

<style scoped>
.hero-section {
    min-height: 40vh;
}
.hero-bg {
    background: linear-gradient(
        135deg in oklch,
        #1b4332 0%,      /* Xanh thông đậm */
        #2d6a4f 40%,     /* Xanh thông */
        #52b788 100%     /* Xanh nhạt — sương mù */
    );
}
</style>
