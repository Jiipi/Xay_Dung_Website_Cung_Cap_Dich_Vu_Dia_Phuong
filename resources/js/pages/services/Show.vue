<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, CheckCircle, Clock, MapPin, MessageSquare, Shield, Star, User, ChevronRight, Heart } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
import { toast } from 'vue-sonner';

const props = withDefaults(
    defineProps<{
        service?: any;
    }>(),
    {
        service: () => ({}),
    },
);

const localService = ref({ ...props.service });

const formatVND = (value: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

/* ──────── Booking form state ──────── */
const bookingDate = ref('');
const guestCount = ref(1);
const isSubmitting = ref(false);
const bookingSuccess = ref(false);

const subtotal = computed(() => (props.service.price ?? 0) * guestCount.value);
const total = computed(() => subtotal.value);

const handleBooking = () => {
    if (!bookingDate.value) {
        toast.error('Vui lòng chọn ngày bắt đầu');
        return;
    }
    
    // Optimistic UI: Hiển thị ngay lập tức thành công
    bookingSuccess.value = true;
    toast.success('Đặt lịch thành công!', {
        description: 'Đã gửi yêu cầu đến nhà cung cấp. Sẽ đồng bộ hệ thống.',
    });
    
    isSubmitting.value = true;
    router.post('/customer/bookings', {
        dich_vu_id: props.service.id,
        thoi_gian_thuc_hien: bookingDate.value,
        so_luong: guestCount.value,
        dia_diem_thuc_hien: props.service.location ?? '',
        ghi_chu: '',
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => { isSubmitting.value = false; },
        onError: (errors) => {
            // Revert changes on error
            bookingSuccess.value = false;
            const msg = Object.values(errors).flat().join('\n');
            toast.error(msg || 'Có lỗi xảy ra, vui lòng thử lại.');
        },
    });
};

/* ──────── Gallery ──────── */
const activeImage = ref(0);
const images = computed(() => props.service.images ?? []);

/* ──────── Favorite ──────── */
const toggleFavorite = async () => {
    localService.value.is_favorited = !localService.value.is_favorited;
    
    try {
        const response = await fetch('/customer/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ dich_vu_id: localService.value.id }),
        });
        if (!response.ok) {
            localService.value.is_favorited = !localService.value.is_favorited;
        }
    } catch (e) {
        localService.value.is_favorited = !localService.value.is_favorited;
    }
};
</script>

<template>
    <Head>
        <title>{{ service.title ?? 'Chi tiết dịch vụ' }} — Dalat Services</title>
        <meta name="description" :content="service.description ? service.description.substring(0, 160).trim() + '...' : 'Chi tiết dịch vụ tại Dalat Services'" />
        <meta property="og:title" :content="service.title" />
        <meta property="og:description" :content="service.description ? service.description.substring(0, 160).trim() + '...' : 'Chi tiết dịch vụ tại Dalat Services'" />
        <meta property="og:image" :content="images[0]" />
        <meta property="og:type" content="product" />
    </Head>

    <MarketplaceLayout>
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="mb-6 flex items-center gap-2 text-sm text-stone-500">
                <Link href="/" class="brand-link transition">Trang chủ</Link>
                <ChevronRight class="size-3.5" />
                <Link :href="`/services?category=${service.category?.slug}`" class="brand-link transition">
                    {{ service.category?.name ?? 'Dịch vụ' }}
                </Link>
                <ChevronRight class="size-3.5" />
                <span class="truncate font-medium text-stone-950">{{ service.title }}</span>
            </nav>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Main Content -->
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-4">
                        <h1 class="mb-4 text-3xl font-semibold tracking-tight text-stone-950">{{ service.title }}</h1>
                        <button
                            @click.prevent="toggleFavorite"
                            class="flex size-10 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-white shadow-sm transition hover:scale-105 active:scale-95"
                            :class="localService.is_favorited ? 'border-red-200 text-red-500' : 'text-stone-400 hover:text-red-500'"
                        >
                            <Heart class="size-5" :class="localService.is_favorited ? 'fill-current text-red-500' : ''" />
                        </button>
                    </div>
                    <div class="mb-6 flex flex-wrap items-center gap-4 text-sm text-stone-600">
                        <div class="flex items-center gap-1">
                            <Star class="size-5 fill-amber-400 text-amber-400" />
                            <span class="font-semibold text-stone-950">{{ service.rating?.toFixed(1) }}</span>
                            <span class="cursor-pointer underline">({{ service.reviews }} đánh giá)</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <MapPin class="size-5 text-stone-400" />
                            {{ service.location }}
                        </div>
                    </div>

                    <!-- Image Gallery -->
                    <div class="mb-8">
                        <!-- Main image -->
                        <div class="mb-2 h-64 overflow-hidden rounded-3xl md:h-96">
                            <img
                                :src="images[activeImage] || images[0]"
                                alt="Main"
                                class="size-full object-cover transition-transform duration-500 hover:scale-105"
                                referrerpolicy="no-referrer"
                            />
                        </div>
                        <!-- Thumbnails -->
                        <div v-if="images.length > 1" class="flex gap-2 overflow-x-auto">
                            <button
                                v-for="(img, idx) in images"
                                :key="idx"
                                @click="activeImage = idx"
                                class="h-20 w-28 shrink-0 overflow-hidden rounded-xl border-2 transition"
                                :class="activeImage === idx ? 'border-brand' : 'border-transparent opacity-70 hover:opacity-100'"
                            >
                                <img :src="img" :alt="`Ảnh ${idx + 1}`" class="size-full object-cover" referrerpolicy="no-referrer" />
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <section class="mb-10">
                        <h2 class="mb-4 text-2xl font-semibold tracking-tight text-stone-950">Mô tả dịch vụ</h2>
                        <p class="whitespace-pre-line leading-8 text-stone-600">{{ service.description }}</p>
                    </section>

                    <!-- Attributes -->
                    <section v-if="service.attributes && service.attributes.length > 0" class="mb-10 rounded-[2rem] border border-stone-200 bg-stone-50 p-6">
                        <h2 class="mb-4 text-xl font-semibold text-stone-950">Thông tin chi tiết</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div v-for="(attr, idx) in service.attributes" :key="idx" class="flex items-center gap-3">
                                <CheckCircle class="size-5" style="color: var(--dl-brand);" />
                                <div>
                                    <span class="block text-xs uppercase tracking-[0.18em] text-stone-500">{{ attr.name }}</span>
                                    <span class="font-medium text-stone-950">{{ attr.value }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Customer Reviews -->
                    <section v-if="service.customerReviews && service.customerReviews.length > 0" class="mb-10 border-t border-stone-200 pt-10">
                        <h2 class="mb-6 text-2xl font-semibold tracking-tight text-stone-950">
                            Đánh giá từ khách hàng
                            <span class="ml-2 text-base font-normal text-stone-400">({{ service.reviews }})</span>
                        </h2>
                        <div class="space-y-6">
                            <div
                                v-for="(review, idx) in service.customerReviews"
                                :key="idx"
                                class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm"
                            >
                                <div class="mb-3 flex items-center gap-3">
                                    <img :src="review.avatar" :alt="review.name" class="size-10 rounded-full object-cover" referrerpolicy="no-referrer" />
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-stone-900">{{ review.name }}</h4>
                                        <span class="text-xs text-stone-400">{{ review.date }}</span>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <Star
                                            v-for="s in 5"
                                            :key="s"
                                            class="size-4"
                                            :class="s <= review.rating ? 'fill-amber-400 text-amber-400' : 'text-stone-200'"
                                        />
                                    </div>
                                </div>
                                <p class="text-sm leading-relaxed text-stone-600">{{ review.content }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Provider Info -->
                    <section class="mb-10 border-t border-stone-200 pt-10">
                        <h2 class="mb-6 text-2xl font-semibold tracking-tight text-stone-950">Thông tin nhà cung cấp</h2>
                        <div class="flex items-start gap-6">
                            <img :src="service.provider?.avatar" :alt="service.provider?.name" class="size-16 rounded-2xl border border-stone-200 object-cover" referrerpolicy="no-referrer" />
                            <div>
                                <h3 class="flex items-center gap-2 text-xl font-semibold text-stone-950">
                                    {{ service.provider?.name }}
                                    <Shield v-if="service.provider?.verified" class="size-5" style="color: var(--dl-brand); fill: var(--dl-brand-surface);" />
                                </h3>
                                <p v-if="service.provider?.description" class="mt-1 text-sm text-stone-500">
                                    {{ service.provider.description }}
                                </p>
                                <div class="mt-2 flex items-center gap-4 text-sm text-stone-600">
                                    <span class="flex items-center gap-1">
                                        <Star class="size-4 fill-amber-400 text-amber-400" /> {{ service.provider?.rating?.toFixed(1) }} ({{ service.provider?.reviews }})
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Clock class="size-4 text-stone-400" /> Kinh nghiệm: {{ service.provider?.experience }}
                                    </span>
                                </div>
                                <button class="mt-4 flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition-colors" style="border-color: var(--dl-brand); color: var(--dl-brand);">
                                    <MessageSquare class="size-4" /> Liên hệ nhà cung cấp
                                </button>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Booking Sidebar -->
                <aside class="w-full shrink-0 lg:w-96">
                    <div class="sticky top-24 rounded-[2rem] border border-stone-200 bg-white p-6 shadow-xl">
                        <!-- Price -->
                        <div class="mb-6">
                            <span class="text-3xl font-semibold" style="color: var(--dl-brand);">{{ formatVND(service.price) }}</span>
                            <span v-if="service.priceTo" class="text-lg text-stone-400"> - {{ formatVND(service.priceTo) }}</span>
                            <span class="text-sm text-stone-500"> / {{ service.priceUnit }}</span>
                        </div>

                        <!-- Success state -->
                        <div v-if="bookingSuccess" class="rounded-2xl bg-emerald-50 border border-emerald-200 px-6 py-8 text-center">
                            <CheckCircle class="mx-auto size-12 text-emerald-500" />
                            <h3 class="mt-3 text-lg font-semibold text-emerald-800">Đã gửi yêu cầu!</h3>
                            <p class="mt-1 text-sm text-emerald-600">Nhà cung cấp sẽ xác nhận trong thời gian sớm nhất.</p>
                            <button @click="bookingSuccess = false" class="mt-4 text-sm font-medium text-emerald-700 underline">Đặt thêm</button>
                        </div>

                        <!-- Booking form -->
                        <form v-else class="space-y-4" @submit.prevent="handleBooking">
                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-stone-500">Ngày bắt đầu</label>
                                <div class="relative">
                                    <Calendar class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                                    <input
                                        v-model="bookingDate"
                                        type="date"
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pl-12 pr-4 outline-none transition-all focus:border-brand focus:ring-2 focus:ring-brand/30"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-stone-500">Số lượng</label>
                                <div class="relative">
                                    <User class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                                    <select
                                        v-model.number="guestCount"
                                        class="w-full appearance-none rounded-2xl border border-stone-200 bg-stone-50 py-3 pl-12 pr-4 outline-none transition-all focus:border-brand focus:ring-2 focus:ring-brand/30"
                                    >
                                        <option v-for="n in 10" :key="n" :value="n">{{ n }} {{ service.priceUnit ?? 'người' }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-stone-100 pt-4">
                                <div class="mb-2 flex justify-between text-stone-600">
                                    <span>{{ formatVND(service.price) }} × {{ guestCount }}</span>
                                    <span>{{ formatVND(subtotal) }}</span>
                                </div>
                                <div class="mb-4 flex justify-between text-stone-600">
                                    <span>Phí dịch vụ</span>
                                    <span>Miễn phí</span>
                                </div>
                                <div class="flex justify-between text-lg font-semibold text-stone-950">
                                    <span>Tổng tiền</span>
                                    <span>{{ formatVND(total) }}</span>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full rounded-full bg-stone-950 py-4 font-semibold text-white shadow-lg transition-all active:scale-[0.98] hover:bg-stone-800 disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                                    <svg class="size-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Đang xử lý...
                                </span>
                                <span v-else>Đặt lịch ngay</span>
                            </button>

                            <p class="mt-2 text-center text-xs text-stone-400">
                                Bạn sẽ không bị trừ tiền cho đến khi nhà cung cấp xác nhận.
                            </p>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </MarketplaceLayout>
</template>
