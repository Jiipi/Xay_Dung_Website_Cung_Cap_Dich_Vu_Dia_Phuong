<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Mail, Phone, MapPin, Save, CheckCircle, User,
} from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(
    defineProps<{ profile?: any }>(),
    { profile: () => ({}) },
);

const page = usePage();
const flash = (page.props as any).flash;

const form = ref({
    name: props.profile.name ?? '',
    phone: props.profile.phone ?? '',
    address: props.profile.address ?? '',
});

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});

function handleSubmit() {
    isSubmitting.value = true;
    errors.value = {};
    router.put('/customer/profile', form.value, {
        onFinish: () => { isSubmitting.value = false; },
        onError: (e) => { errors.value = e; },
    });
}
</script>

<template>
    <Head title="Hồ sơ cá nhân" />

    <CustomerLayout activePage="profile">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <h1 class="text-2xl font-black tracking-tight text-stone-950">Hồ sơ cá nhân</h1>
                <p class="mt-2 text-sm text-stone-500">Cập nhật thông tin để nhà cung cấp liên hệ dễ dàng hơn.</p>

                <!-- Flash success -->
                <div v-if="flash?.success" class="mt-4 flex items-center gap-2 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    <CheckCircle class="size-4" />
                    {{ flash.success }}
                </div>

                <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Họ tên</label>
                        <div class="relative">
                            <User class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                                placeholder="Nguyễn Văn A"
                            />
                        </div>
                        <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                    </div>

                    <!-- Email (read-only) -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Email</label>
                        <div class="relative">
                            <Mail class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                            <input
                                :value="profile.email"
                                type="email"
                                disabled
                                class="w-full cursor-not-allowed rounded-2xl border border-stone-200 bg-stone-100 py-3 pl-12 pr-4 text-sm text-stone-500 outline-none"
                            />
                        </div>
                        <p class="mt-1 text-xs text-stone-400">Email không thể thay đổi. Liên hệ hỗ trợ nếu cần.</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Số điện thoại</label>
                        <div class="relative">
                            <Phone class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                                placeholder="0912 345 678"
                            />
                        </div>
                        <p v-if="errors.phone" class="mt-1 text-xs text-red-600">{{ errors.phone }}</p>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Địa chỉ</label>
                        <div class="relative">
                            <MapPin class="pointer-events-none absolute left-4 top-3.5 size-5 text-stone-400" />
                            <textarea
                                v-model="form.address"
                                rows="3"
                                class="w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                                placeholder="123 Trần Hưng Đạo, Phường 10, Đà Lạt, Lâm Đồng"
                            />
                        </div>
                        <p v-if="errors.address" class="mt-1 text-xs text-red-600">{{ errors.address }}</p>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="flex items-center gap-2 rounded-full px-8 py-3 text-sm font-bold text-white transition hover:opacity-90 disabled:opacity-50"
                        style="background: var(--dl-brand);"
                    >
                        <Save v-if="!isSubmitting" class="size-4" />
                        {{ isSubmitting ? 'Đang lưu...' : 'Lưu thay đổi' }}
                    </button>
                </form>
            </div>
        </div>
    </CustomerLayout>
</template>