<script setup lang="ts">
defineOptions({ layout: AdminLayout });
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Lock,
    Mail,
    Phone,
    Save,
    Upload,
    User,
    Loader2,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface UserData {
    ho_ten: string;
    email: string;
    so_dien_thoai: string | null;
    anh_dai_dien: string | null;
}

const props = withDefaults(
    defineProps<{
        user: UserData;
    }>(),
    {
        user: () => ({
            ho_ten: '',
            email: '',
            so_dien_thoai: null,
            anh_dai_dien: null,
        }),
    },
);

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
}));

const isChangingPassword = ref(false);

const form = useForm({
    ho_ten: props.user?.ho_ten ?? '',
    so_dien_thoai: props.user?.so_dien_thoai ?? '',
    anh_dai_dien: null as File | null,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref<string | null>(props.user?.anh_dai_dien ?? null);

function handleAvatarChange(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh không được vượt quá 2MB.');
            input.value = '';
            return;
        }
        form.anh_dai_dien = input.files[0];
        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarPreview.value = ev.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
        input.value = ''; // Clean input
    }
}

function submit() {
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
    }).post('/admin/profile/update', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('current_password', 'password', 'password_confirmation');
            form.anh_dai_dien = null;
        },
        onError: (errors) => {
            if (errors.anh_dai_dien) {
                alert('Lỗi tải ảnh: ' + errors.anh_dai_dien);
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
    <Head title="Hồ sơ quản trị viên" />

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <!-- Flash -->
            <div
                v-if="flash.success"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            >
                <CheckCircle2 class="size-5 shrink-0" /> {{ flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-stone-950">
                            Hồ sơ cá nhân
                        </h1>
                        <p class="text-sm text-stone-500">
                            Quản lý thông tin và bảo mật tài khoản
                        </p>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:opacity-90 disabled:opacity-50"
                        style="background: var(--dl-admin)"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="size-4 animate-spin"
                        />
                        <Save v-else class="size-4" />
                        Lưu thay đổi
                    </button>
                </div>

                <!-- Thông tin cá nhân & Avatar -->
                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"
                >
                    <h2
                        class="mb-5 flex items-center gap-2 text-base font-semibold text-stone-950"
                    >
                        <User class="size-5" style="color: var(--dl-admin)" />
                        Thông tin cá nhân
                    </h2>
                    <div class="flex flex-col gap-6 sm:flex-row">
                        <!-- Avatar -->
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="relative size-24 overflow-hidden rounded-full bg-stone-100 ring-4 ring-stone-200/50"
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
                            <label
                                class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition-colors hover:bg-stone-50"
                            >
                                <Upload class="size-3.5" /> Đổi ảnh
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleAvatarChange"
                                />
                            </label>
                            <p
                                v-if="form.errors.anh_dai_dien"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.anh_dai_dien }}
                            </p>
                        </div>

                        <!-- Fields -->
                        <div class="grid flex-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Họ tên
                                    <span class="text-red-500">*</span></label
                                >
                                <div class="relative">
                                    <User
                                        class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-stone-400"
                                    />
                                    <input
                                        v-model="form.ho_ten"
                                        type="text"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pr-4 pl-11 text-sm outline-none focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.ho_ten"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.ho_ten }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Email</label
                                >
                                <div class="relative">
                                    <Mail
                                        class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-stone-400"
                                    />
                                    <input
                                        :value="user.email"
                                        type="email"
                                        disabled
                                        class="w-full cursor-not-allowed rounded-xl border border-stone-200 bg-stone-100 py-3 pr-4 pl-11 text-sm text-stone-500 outline-none"
                                    />
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Số điện thoại</label
                                >
                                <div class="relative">
                                    <Phone
                                        class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-stone-400"
                                    />
                                    <input
                                        v-model="form.so_dien_thoai"
                                        type="text"
                                        placeholder="0912 345 678"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pr-4 pl-11 text-sm outline-none focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.so_dien_thoai"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.so_dien_thoai }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Đổi mật khẩu -->
                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm"
                >
                    <div class="mb-5 flex items-center justify-between">
                        <h2
                            class="flex items-center gap-2 text-base font-semibold text-stone-950"
                        >
                            <Lock
                                class="size-5"
                                style="color: var(--dl-admin)"
                            />
                            Đổi mật khẩu
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
                        <p class="mb-5 text-sm text-stone-500">
                            Nhập mật khẩu cũ và mới để thay đổi.
                        </p>
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Mật khẩu hiện tại</label
                                >
                                <input
                                    v-model="form.current_password"
                                    type="password"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
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
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Mật khẩu mới</label
                                >
                                <input
                                    v-model="form.password"
                                    type="password"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
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
                                    class="mb-1.5 block text-sm font-medium text-stone-700"
                                    >Xác nhận mật khẩu</label
                                >
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-200"
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
            </form>
        </div>
    </div>
</template>
