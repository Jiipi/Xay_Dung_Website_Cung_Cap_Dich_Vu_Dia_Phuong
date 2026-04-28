<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    CreditCard,
    ExternalLink,
    Facebook,
    FileText,
    Globe,
    Loader2,
    MapPin,
    Phone,
    Save,
    ShieldCheck,
    Upload,
    User,
} from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface ProfileData {
    ten_thuong_hieu: string;
    gioi_thieu: string | null;
    nam_kinh_nghiem: number;
    website: string | null;
    facebook: string | null;
    giay_phep_kinh_doanh: string | null;
    stk_ngan_hang: string | null;
    ten_ngan_hang: string | null;
    ten_chu_tk: string | null;
    diem_danh_gia: number;
}

interface UserData {
    ho_ten: string;
    email: string;
    so_dien_thoai: string | null;
    anh_dai_dien: string | null;
}

const props = withDefaults(defineProps<{
    profile: ProfileData | null;
    user: UserData;
}>(), {
    profile: null,
    user: () => ({ ho_ten: '', email: '', so_dien_thoai: null, anh_dai_dien: null }),
});

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
}));

const form = useForm({
    ho_ten: props.user?.ho_ten ?? '',
    so_dien_thoai: props.user?.so_dien_thoai ?? '',
    anh_dai_dien: null as File | null,
    ten_thuong_hieu: props.profile?.ten_thuong_hieu ?? '',
    gioi_thieu: props.profile?.gioi_thieu ?? '',
    nam_kinh_nghiem: props.profile?.nam_kinh_nghiem ?? 0,
    website: props.profile?.website ?? '',
    facebook: props.profile?.facebook ?? '',
    giay_phep_kinh_doanh: props.profile?.giay_phep_kinh_doanh ?? '',
    stk_ngan_hang: props.profile?.stk_ngan_hang ?? '',
    ten_ngan_hang: props.profile?.ten_ngan_hang ?? '',
    ten_chu_tk: props.profile?.ten_chu_tk ?? '',
});

const avatarPreview = ref<string | null>(props.user?.anh_dai_dien ?? null);

function handleAvatarChange(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        form.anh_dai_dien = input.files[0];
        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarPreview.value = ev.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function submit() {
    form.post('/provider/profile', {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Hồ sơ nhà cung cấp" />

    <ProviderLayout activePage="profile">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="space-y-6">
                    <!-- Flash -->
                    <div v-if="flash.success" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <CheckCircle2 class="size-5 shrink-0" /> {{ flash.success }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-stone-950">Hồ sơ nhà cung cấp</h1>
                                <p class="text-sm text-stone-500">Quản lý thông tin thương hiệu và tài khoản</p>
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-700 disabled:opacity-50"
                            >
                                <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                                <Save v-else class="size-4" />
                                Lưu thay đổi
                            </button>
                        </div>

                        <!-- Avatar & Personal Info -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 flex items-center gap-2 text-base font-semibold text-stone-950">
                                <User class="size-5 text-brand" /> Thông tin cá nhân
                            </h2>
                            <div class="flex flex-col gap-6 sm:flex-row">
                                <!-- Avatar -->
                                <div class="flex flex-col items-center gap-3">
                                    <div class="relative size-24 overflow-hidden rounded-full bg-stone-100 ring-4 ring-stone-200/50">
                                        <img
                                            v-if="avatarPreview"
                                            :src="avatarPreview"
                                            class="size-full object-cover"
                                            referrerpolicy="no-referrer"
                                        />
                                        <div v-else class="flex size-full items-center justify-center">
                                            <User class="size-8 text-stone-300" />
                                        </div>
                                    </div>
                                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-medium text-stone-600 transition-colors hover:bg-stone-50">
                                        <Upload class="size-3.5" /> Đổi ảnh
                                        <input type="file" accept="image/*" class="hidden" @change="handleAvatarChange" />
                                    </label>
                                </div>

                                <!-- Fields -->
                                <div class="flex-1 grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-stone-700">Họ tên <span class="text-red-500">*</span></label>
                                        <input v-model="form.ho_ten" type="text" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                        <p v-if="form.errors.ho_ten" class="mt-1 text-xs text-red-600">{{ form.errors.ho_ten }}</p>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-stone-700">Email</label>
                                        <input :value="user.email" type="email" disabled class="w-full rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-500 outline-none" />
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-stone-700">Số điện thoại</label>
                                        <div class="relative">
                                            <Phone class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                                            <input v-model="form.so_dien_thoai" type="text" placeholder="0912 345 678" class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pl-11 pr-4 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin thương hiệu -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 flex items-center gap-2 text-base font-semibold text-stone-950">
                                <Building2 class="size-5 text-brand" /> Thông tin thương hiệu
                            </h2>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Tên thương hiệu <span class="text-red-500">*</span></label>
                                    <input v-model="form.ten_thuong_hieu" type="text" placeholder="VD: Dịch Vụ Pro Đà Lạt" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" :class="{ 'border-red-300': form.errors.ten_thuong_hieu }" />
                                    <p v-if="form.errors.ten_thuong_hieu" class="mt-1 text-xs text-red-600">{{ form.errors.ten_thuong_hieu }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Năm kinh nghiệm</label>
                                    <input v-model="form.nam_kinh_nghiem" type="number" min="0" max="100" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Giới thiệu</label>
                                    <textarea v-model="form.gioi_thieu" rows="4" placeholder="Mô tả về thương hiệu, lĩnh vực chuyên môn, cam kết chất lượng..." class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Website</label>
                                    <div class="relative">
                                        <Globe class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                                        <input v-model="form.website" type="url" placeholder="https://" class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pl-11 pr-4 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Facebook</label>
                                    <div class="relative">
                                        <Facebook class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                                        <input v-model="form.facebook" type="text" placeholder="facebook.com/..." class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pl-11 pr-4 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Giấy phép kinh doanh</label>
                                    <div class="relative">
                                        <FileText class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                                        <input v-model="form.giay_phep_kinh_doanh" type="text" placeholder="Số giấy phép hoặc link file" class="w-full rounded-xl border border-stone-200 bg-stone-50 py-3 pl-11 pr-4 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin ngân hàng -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 flex items-center gap-2 text-base font-semibold text-stone-950">
                                <CreditCard class="size-5 text-brand" /> Thông tin ngân hàng
                            </h2>
                            <div class="grid gap-5 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Tên ngân hàng</label>
                                    <input v-model="form.ten_ngan_hang" type="text" placeholder="VD: Vietcombank" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Số tài khoản</label>
                                    <input v-model="form.stk_ngan_hang" type="text" placeholder="0123456789" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Tên chủ tài khoản</label>
                                    <input v-model="form.ten_chu_tk" type="text" placeholder="NGUYEN VAN A" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                            </div>
                        </div>

                        <!-- Trạng thái xác minh -->
                        <div v-if="profile" class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                            <ShieldCheck class="size-5 text-emerald-600" />
                            <div>
                                <p class="text-sm font-medium text-emerald-800">Tài khoản đã xác minh</p>
                                <p class="text-xs text-emerald-600">Điểm đánh giá hiện tại: {{ profile.diem_danh_gia }} ★</p>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </ProviderLayout>
</template>