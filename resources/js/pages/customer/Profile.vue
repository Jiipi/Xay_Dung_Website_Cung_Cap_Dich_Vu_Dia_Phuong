<script setup lang="ts">
defineOptions({ layout: CustomerLayout });
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {
    Mail,
    Phone,
    MapPin,
    Save,
    CheckCircle,
    User,
    Upload,
    Lock,
    Loader2,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';

const props = withDefaults(defineProps<{ profile?: any }>(), {
    profile: () => ({}),
});

const page = usePage();
const flash = computed(() => ({
    success: (page.props as any).flash?.success as string | undefined,
}));

const isChangingPassword = ref(false);

const form = useForm({
    name: props.profile.name ?? '',
    phone: props.profile.phone ?? '',
    address: props.profile.address ?? '',
    avatar: null as File | null,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref<string | null>(props.profile.avatar ?? null);

function handleAvatarChange(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh không được vượt quá 2MB.');
            input.value = '';
            return;
        }
        form.avatar = input.files[0];
        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarPreview.value = ev.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
        input.value = '';
    }
}

function handleSubmit() {
    if (!isChangingPassword.value) {
        form.current_password = '';
        form.password = '';
        form.password_confirmation = '';
    }

    form.transform((data) => {
        const cleaned: Record<string, any> = {};
        for (const [key, value] of Object.entries(data)) {
            if (value !== null) {
                cleaned[key] = value;
            }
        }
        return cleaned;
    }).post('/customer/profile/update', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset('current_password', 'password', 'password_confirmation');
            form.avatar = null;
        },
        onError: (errors) => {
            if (errors.avatar) {
                alert('Lỗi tải ảnh: ' + errors.avatar);
            }
            if (errors.password || errors.current_password) {
                alert(
                    'Lỗi đổi mật khẩu: Vui lòng kiểm tra lại thông tin mật khẩu ở dưới cùng.',
                );
            }
        },
    });
}
</script>

<template>
    <Head title="Hồ sơ cá nhân" />

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div
            class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm"
        >
            <h1 class="text-2xl font-black tracking-tight text-stone-950">
                Hồ sơ cá nhân
            </h1>
            <p class="mt-2 text-sm text-stone-500">
                Cập nhật thông tin để nhà cung cấp liên hệ dễ dàng hơn.
            </p>

            <!-- Flash success -->
            <div
                v-if="flash.success"
                class="mt-4 flex items-center gap-2 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
            >
                <CheckCircle class="size-4" />
                {{ flash.success }}
            </div>

            <form @submit.prevent="handleSubmit" class="mt-8 space-y-8">
                <!-- Avatar Upload -->
                <div
                    class="flex flex-col items-center gap-3 sm:flex-row sm:items-start sm:gap-6"
                >
                    <div
                        class="relative size-24 shrink-0 overflow-hidden rounded-full bg-stone-100 ring-4 ring-stone-200/50"
                    >
                        <img
                            v-if="avatarPreview"
                            :src="avatarPreview"
                            class="size-full object-cover"
                            referrerpolicy="no-referrer"
                        />
                        <div
                            v-else
                            class="flex size-full items-center justify-center"
                        >
                            <User class="size-8 text-stone-300" />
                        </div>
                    </div>
                    <div class="flex flex-col justify-center pt-2">
                        <label
                            class="flex w-fit cursor-pointer items-center gap-1.5 rounded-lg border border-stone-200 px-4 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-50"
                        >
                            <Upload class="size-4" /> Đổi ảnh đại diện
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleAvatarChange"
                            />
                        </label>
                        <p class="mt-2 text-xs text-stone-400">
                            Định dạng hỗ trợ: JPG, PNG, WEBP. Tối đa 2MB.
                        </p>
                        <p
                            v-if="form.errors.avatar"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.avatar }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-stone-700"
                            >Họ tên</label
                        >
                        <div class="relative">
                            <User
                                class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-stone-400"
                            />
                            <input
                                v-model="form.name"
                                type="text"
                                class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pr-4 pl-12 text-sm transition outline-none focus:ring-2"
                                placeholder="Nguyễn Văn A"
                            />
                        </div>
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email (read-only) -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-stone-700"
                            >Email</label
                        >
                        <div class="relative">
                            <Mail
                                class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-stone-400"
                            />
                            <input
                                :value="profile.email"
                                type="email"
                                disabled
                                class="w-full cursor-not-allowed rounded-2xl border border-stone-200 bg-stone-100 py-3 pr-4 pl-12 text-sm text-stone-500 outline-none"
                            />
                        </div>
                        <p class="mt-1 text-xs text-stone-400">
                            Email không thể thay đổi. Liên hệ hỗ trợ nếu cần.
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-stone-700"
                            >Số điện thoại</label
                        >
                        <div class="relative">
                            <Phone
                                class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-stone-400"
                            />
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pr-4 pl-12 text-sm transition outline-none focus:ring-2"
                                placeholder="0912 345 678"
                            />
                        </div>
                        <p
                            v-if="form.errors.phone"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-stone-700"
                            >Địa chỉ</label
                        >
                        <div class="relative">
                            <MapPin
                                class="pointer-events-none absolute top-3.5 left-4 size-5 text-stone-400"
                            />
                            <textarea
                                v-model="form.address"
                                rows="2"
                                class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 py-3 pr-4 pl-12 text-sm transition outline-none focus:ring-2"
                                placeholder="123 Trần Hưng Đạo, Phường 10, Đà Lạt, Lâm Đồng"
                            />
                        </div>
                        <p
                            v-if="form.errors.address"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.address }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-stone-100 pt-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2
                            class="flex items-center gap-2 text-lg font-bold text-stone-900"
                        >
                            <Lock class="text-brand size-5" /> Đổi mật khẩu
                        </h2>
                        <button
                            type="button"
                            @click="isChangingPassword = !isChangingPassword"
                            class="text-sm font-medium text-stone-600 underline transition hover:text-stone-900"
                        >
                            {{
                                isChangingPassword ? 'Hủy' : 'Thay đổi mật khẩu'
                            }}
                        </button>
                    </div>

                    <div v-if="isChangingPassword">
                        <p class="mb-6 text-sm text-stone-500">
                            Nhập mật khẩu cũ và mới để thay đổi.
                        </p>

                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-stone-700"
                                    >Mật khẩu hiện tại</label
                                >
                                <input
                                    v-model="form.current_password"
                                    type="password"
                                    class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm transition outline-none focus:ring-2"
                                />
                                <p
                                    v-if="form.errors.current_password"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.current_password }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-stone-700"
                                    >Mật khẩu mới</label
                                >
                                <input
                                    v-model="form.password"
                                    type="password"
                                    class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm transition outline-none focus:ring-2"
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-stone-700"
                                    >Xác nhận mật khẩu</label
                                >
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="focus:border-brand focus:ring-brand/30 w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm transition outline-none focus:ring-2"
                                />
                                <p
                                    v-if="form.errors.password_confirmation"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.password_confirmation }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm text-stone-500 italic">
                            Nhấn vào "Thay đổi mật khẩu" nếu bạn muốn thay đổi.
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center gap-2 rounded-full px-8 py-3 text-sm font-bold text-white transition hover:opacity-90 disabled:opacity-50"
                    style="background: var(--dl-brand)"
                >
                    <Loader2
                        v-if="form.processing"
                        class="size-4 animate-spin"
                    />
                    <Save v-else class="size-4" />
                    {{ form.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
                </button>
            </form>
        </div>
    </div>
</template>
