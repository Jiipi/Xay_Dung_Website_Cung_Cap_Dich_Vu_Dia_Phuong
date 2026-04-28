<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Heart, HeartOff, MapPin, Search, Star } from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{ favorites?: any[] }>(),
    { favorites: () => [] },
);

const localFavorites = ref([...(props.favorites || [])]);
const searchQuery = ref('');
const removingIds = ref<number[]>([]);
const lastRemoved = ref<any | null>(null);

const formatVND = (v: number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

const filteredFavorites = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    if (!query) return localFavorites.value;

    return localFavorites.value.filter((fav) =>
        [fav.title, fav.provider].some((value) => String(value || '').toLowerCase().includes(query)),
    );
});

async function sendToggle(serviceId: number) {
    const response = await fetch('/customer/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ dich_vu_id: serviceId }),
    });

    if (!response.ok) {
        throw new Error('Toggle favorite failed');
    }
}

async function removeFavorite(fav: any) {
    if (removingIds.value.includes(fav.id)) return;

    removingIds.value.push(fav.id);
    const previous = [...localFavorites.value];
    localFavorites.value = localFavorites.value.filter((item) => item.id !== fav.id);
    lastRemoved.value = fav;

    try {
        await sendToggle(fav.serviceId);
    } catch (error) {
        localFavorites.value = previous;
        lastRemoved.value = null;
    } finally {
        removingIds.value = removingIds.value.filter((id) => id !== fav.id);
    }
}

async function undoRemove() {
    if (!lastRemoved.value) return;

    const favorite = lastRemoved.value;
    lastRemoved.value = null;

    try {
        await sendToggle(favorite.serviceId);
        localFavorites.value = [favorite, ...localFavorites.value];
    } catch (error) {
        lastRemoved.value = favorite;
    }
}
</script>

<template>
    <Head title="Dịch vụ yêu thích" />

    <CustomerLayout activePage="favorites">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div v-if="lastRemoved" class="mb-5 flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold">Đã bỏ khỏi yêu thích</p>
                        <p class="text-amber-700">{{ lastRemoved.title }}</p>
                    </div>
                    <button class="rounded-full bg-white px-4 py-2 font-semibold text-amber-800 transition hover:bg-amber-100" @click="undoRemove">
                        Hoàn tác
                    </button>
                </div>

                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-stone-950">Dịch vụ yêu thích</h1>
                        <p class="mt-1 text-sm text-stone-500">{{ filteredFavorites.length }} / {{ localFavorites.length }} dịch vụ đã lưu</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
                        <div class="relative min-w-0 lg:w-80">
                            <Search class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tìm trong yêu thích..."
                                class="w-full rounded-xl border border-stone-200 bg-stone-50 py-2.5 pl-10 pr-3 text-sm text-stone-700 outline-none transition focus:border-brand focus:bg-white"
                            />
                        </div>
                        <Link href="/services" class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800">
                            Khám phá thêm
                        </Link>
                    </div>
                </div>

                <div v-if="filteredFavorites.length > 0" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="fav in filteredFavorites"
                        :key="fav.id"
                        class="group overflow-hidden rounded-2xl border border-stone-200 bg-white transition-all duration-300 hover:-translate-y-0.5 hover:border-brand hover:shadow-md"
                    >
                        <div class="relative">
                            <img :src="fav.image" :alt="fav.title" class="h-40 w-full object-cover" referrerpolicy="no-referrer" />
                            <button
                                class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/90 text-red-500 shadow-sm backdrop-blur-sm transition hover:bg-red-50 hover:text-red-600 disabled:cursor-wait disabled:opacity-60"
                                :disabled="removingIds.includes(fav.id)"
                                title="Bỏ yêu thích"
                                @click="removeFavorite(fav)"
                            >
                                <HeartOff class="size-4" />
                            </button>
                        </div>
                        <div class="p-4">
                            <Link :href="`/services/${fav.serviceId}`" class="text-base font-bold text-stone-950 transition group-hover:text-brand">
                                {{ fav.title }}
                            </Link>
                            <p class="mt-1 text-sm text-stone-500">{{ fav.provider }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-black text-brand">{{ formatVND(fav.price) }}</span>
                                <div v-if="fav.rating > 0" class="flex items-center gap-1 text-amber-500">
                                    <Star class="size-4 fill-current" />
                                    <span class="text-sm font-bold">{{ fav.rating.toFixed(1) }}</span>
                                </div>
                            </div>
                            <p class="mt-2 flex items-center gap-1 text-xs text-stone-400">
                                <MapPin class="size-3.5" /> Lưu ngày {{ fav.addedAt }}
                            </p>
                            <div class="mt-4 flex items-center gap-2">
                                <Link
                                    :href="`/services/${fav.serviceId}`"
                                    class="inline-flex flex-1 items-center justify-center rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50"
                                >
                                    Xem chi tiết
                                </Link>
                                <Link
                                    :href="`/services/${fav.serviceId}`"
                                    class="inline-flex flex-1 items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90"
                                >
                                    Đặt ngay
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-else class="flex flex-col items-center py-16 text-center">
                    <Heart class="mb-4 size-12 text-stone-300" />
                    <h3 class="text-lg font-bold text-stone-900">
                        {{ searchQuery ? 'Không tìm thấy dịch vụ phù hợp' : 'Chưa có dịch vụ yêu thích' }}
                    </h3>
                    <p class="mt-1 text-sm text-stone-500">
                        {{ searchQuery ? 'Thử từ khóa khác hoặc quay lại danh sách dịch vụ.' : 'Nhấn tim trên các dịch vụ để lưu và quay lại nhanh hơn.' }}
                    </p>
                    <Link href="/services" class="mt-4 rounded-2xl bg-brand px-6 py-3 text-sm font-bold text-white transition hover:bg-brand">
                        Khám phá dịch vụ
                    </Link>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
