<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Star, Send } from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { toast } from 'vue-sonner';

const props = withDefaults(
    defineProps<{ booking?: any }>(),
    { booking: () => ({}) },
);

const rating = ref(0);
const hoverRating = ref(0);
const content = ref('');
const anonymous = ref(false);
const isSubmitting = ref(false);

const formatVND = (v: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

function handleSubmit() {
    if (rating.value < 1) {
        toast.warning('Vui lòng chọn số sao đánh giá.');
        return;
    }
    isSubmitting.value = true;
    router.post('/customer/reviews', {
        don_dat_lich_id: props.booking.id,
        so_sao: rating.value,
        noi_dung: content.value,
        an_danh: anonymous.value,
    }, {
        onFinish: () => { isSubmitting.value = false; },
        onError: (errors) => {
            const msg = Object.values(errors).flat().join('\n');
            toast.error(msg || 'Có lỗi xảy ra.');
        },
    });
}

const ratingLabels = ['', 'Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Tuyệt vời'];
</script>

<template>
    <Head title="Đánh giá dịch vụ" />

    <CustomerLayout activePage="bookings">
        <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
            <Link href="/customer/bookings" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition hover:text-stone-700">
                <ArrowLeft class="size-4" /> Quay lại
            </Link>

            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <h1 class="text-2xl font-black tracking-tight text-stone-950">Đánh giá dịch vụ</h1>
                <p class="mt-2 text-sm text-stone-500">Chia sẻ trải nghiệm của bạn để giúp cộng đồng lựa chọn tốt hơn.</p>

                <!-- Booking Info -->
                <div class="mt-6 rounded-2xl bg-stone-50 p-5">
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-mono text-xs text-stone-400">{{ booking.code }}</p>
                            <p class="mt-1 font-semibold text-stone-900">{{ booking.service }}</p>
                            <p class="text-stone-500">{{ booking.provider }} · {{ booking.date }}</p>
                        </div>
                        <span class="text-lg font-bold" style="color: var(--dl-brand);">{{ formatVND(booking.total) }}</span>
                    </div>
                </div>

                <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
                    <!-- Star Rating -->
                    <div>
                        <label class="mb-3 block text-sm font-medium text-stone-700">Bạn đánh giá dịch vụ này thế nào?</label>
                        <div class="flex items-center gap-1">
                            <button
                                v-for="s in 5"
                                :key="s"
                                type="button"
                                @click="rating = s"
                                @mouseenter="hoverRating = s"
                                @mouseleave="hoverRating = 0"
                                class="transition-transform hover:scale-110"
                            >
                                <Star
                                    class="size-10 transition-colors"
                                    :class="s <= (hoverRating || rating)
                                        ? 'fill-amber-400 text-amber-400'
                                        : 'text-stone-200'"
                                />
                            </button>
                            <span v-if="rating > 0" class="ml-3 text-sm font-medium text-stone-600">
                                {{ ratingLabels[rating] }}
                            </span>
                        </div>
                    </div>

                    <!-- Comment -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Nhận xét chi tiết</label>
                        <textarea
                            v-model="content"
                            rows="5"
                            class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-stone-300 focus:ring-2 focus:ring-stone-100"
                            placeholder="Mô tả trải nghiệm của bạn: chất lượng dịch vụ, thái độ nhà cung cấp, đúng giờ..."
                        />
                    </div>

                    <!-- Anonymous -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input v-model="anonymous" type="checkbox" class="size-4 rounded border-stone-300 accent-stone-900" />
                        <span class="text-sm text-stone-600">Đánh giá ẩn danh</span>
                    </label>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="isSubmitting || rating < 1"
                        class="flex w-full items-center justify-center gap-2 rounded-full py-4 text-sm font-bold text-white transition hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background: var(--dl-brand);"
                    >
                        <Send v-if="!isSubmitting" class="size-4" />
                        {{ isSubmitting ? 'Đang gửi...' : 'Gửi đánh giá' }}
                    </button>
                </form>
            </div>
        </div>
    </CustomerLayout>
</template>