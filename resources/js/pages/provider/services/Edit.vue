<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Eye, ImagePlus, Loader2, Save, Tag, X } from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface Category {
    id: number;
    ten_danh_muc: string;
    children: Array<{ id: number; ten_danh_muc: string }>;
}

interface ServiceData {
    id: number;
    ten_dich_vu: string;
    danh_muc_id: number;
    mo_ta_chi_tiet: string | null;
    gia_tu: number;
    gia_den: number;
    don_vi_gia: string | null;
    dia_chi_hien_thi: string | null;
    danh_sach_anh: string[];
    the_tu_khoa: string[];
    khu_vuc_phuc_vu: string[];
    trang_thai_hoat_dong: string;
    trang_thai_duyet: string;
}

const props = defineProps<{
    service: ServiceData;
    categories: Category[];
}>();

const form = useForm({
    _method: 'PUT' as const,
    ten_dich_vu: props.service.ten_dich_vu,
    danh_muc_id: props.service.danh_muc_id,
    mo_ta_chi_tiet: props.service.mo_ta_chi_tiet ?? '',
    gia_tu: props.service.gia_tu || '',
    gia_den: props.service.gia_den || '',
    don_vi_gia: props.service.don_vi_gia ?? 'lượt',
    dia_chi_hien_thi: props.service.dia_chi_hien_thi ?? '',
    trang_thai_hoat_dong: props.service.trang_thai_hoat_dong,
    anh_dich_vu: [] as File[],
    anh_xoa: [] as string[],
    the_tu_khoa: [...(props.service.the_tu_khoa || [])],
    khu_vuc_phuc_vu: [...(props.service.khu_vuc_phuc_vu || [])],
});

const existingImages = ref<string[]>([...(props.service.danh_sach_anh || [])]);
const newImagePreviews = ref<string[]>([]);
const tagInput = ref('');
const areaInput = ref('');

const duyetLabels: Record<string, string> = {
    cho_duyet: 'Chờ duyệt',
    da_duyet: 'Đã duyệt',
    tu_choi: 'Từ chối',
};

function handleImageSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files) {
        for (const file of Array.from(input.files)) {
            form.anh_dich_vu.push(file);
            const reader = new FileReader();
            reader.onload = (ev) => {
                newImagePreviews.value.push(ev.target?.result as string);
            };
            reader.readAsDataURL(file);
        }
    }
}

function removeExistingImage(index: number) {
    const removed = existingImages.value.splice(index, 1);
    form.anh_xoa.push(...removed);
}

function removeNewImage(index: number) {
    form.anh_dich_vu.splice(index, 1);
    newImagePreviews.value.splice(index, 1);
}

function addTag() {
    const tag = tagInput.value.trim();
    if (tag && !form.the_tu_khoa.includes(tag)) {
        form.the_tu_khoa.push(tag);
    }
    tagInput.value = '';
}

function removeTag(index: number) {
    form.the_tu_khoa.splice(index, 1);
}

function addArea() {
    const area = areaInput.value.trim();
    if (area && !form.khu_vuc_phuc_vu.includes(area)) {
        form.khu_vuc_phuc_vu.push(area);
    }
    areaInput.value = '';
}

function removeArea(index: number) {
    form.khu_vuc_phuc_vu.splice(index, 1);
}

function submit() {
    form.post(`/provider/services/${props.service.id}`, {
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="`Sửa: ${service.ten_dich_vu}`" />

    <ProviderLayout activePage="services">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <Link href="/provider/services" class="rounded-lg border border-stone-200 p-2 text-stone-500 transition-colors hover:bg-stone-50 hover:text-stone-700">
                                    <ArrowLeft class="size-4" />
                                </Link>
                                <div>
                                    <h1 class="text-xl font-bold text-stone-950">Chỉnh sửa dịch vụ</h1>
                                    <p class="text-sm text-stone-500">
                                        Trạng thái duyệt:
                                        <span class="font-medium" :class="service.trang_thai_duyet === 'da_duyet' ? 'text-emerald-600' : service.trang_thai_duyet === 'tu_choi' ? 'text-red-600' : 'text-amber-600'">
                                            {{ duyetLabels[service.trang_thai_duyet] ?? service.trang_thai_duyet }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Link
                                    :href="`/services/${service.id}`"
                                    target="_blank"
                                    class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 shadow-sm transition-colors hover:bg-stone-50"
                                >
                                    <Eye class="size-4" />
                                    Xem trước
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-700 disabled:opacity-50"
                                >
                                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                                    <Save v-else class="size-4" />
                                    Cập nhật
                                </button>
                            </div>
                        </div>

                        <!-- Thông tin chính -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 text-base font-semibold text-stone-950">Thông tin chính</h2>
                            <div class="grid gap-5 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Tên dịch vụ <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.ten_dich_vu"
                                        type="text"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                        :class="{ 'border-red-300 bg-red-50': form.errors.ten_dich_vu }"
                                    />
                                    <p v-if="form.errors.ten_dich_vu" class="mt-1.5 text-xs text-red-600">{{ form.errors.ten_dich_vu }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Danh mục <span class="text-red-500">*</span></label>
                                    <select
                                        v-model="form.danh_muc_id"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                        :class="{ 'border-red-300 bg-red-50': form.errors.danh_muc_id }"
                                    >
                                        <option value="">Chọn danh mục</option>
                                        <template v-for="cat in categories" :key="cat.id">
                                            <optgroup :label="cat.ten_danh_muc">
                                                <option v-for="sub in cat.children" :key="sub.id" :value="sub.id">
                                                    {{ sub.ten_danh_muc }}
                                                </option>
                                            </optgroup>
                                        </template>
                                    </select>
                                    <p v-if="form.errors.danh_muc_id" class="mt-1.5 text-xs text-red-600">{{ form.errors.danh_muc_id }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Trạng thái hoạt động</label>
                                    <select
                                        v-model="form.trang_thai_hoat_dong"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                    >
                                        <option value="hoat_dong">Hoạt động</option>
                                        <option value="tam_ngung">Tạm ngưng</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Đơn vị giá</label>
                                    <select
                                        v-model="form.don_vi_gia"
                                        class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                    >
                                        <option value="lượt">/ lượt</option>
                                        <option value="giờ">/ giờ</option>
                                        <option value="ngày">/ ngày</option>
                                        <option value="tháng">/ tháng</option>
                                        <option value="người">/ người</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Giá từ (VNĐ)</label>
                                    <input v-model="form.gia_tu" type="number" min="0" step="1000" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                    <p v-if="form.errors.gia_tu" class="mt-1.5 text-xs text-red-600">{{ form.errors.gia_tu }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Giá đến (VNĐ)</label>
                                    <input v-model="form.gia_den" type="number" min="0" step="1000" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                    <p v-if="form.errors.gia_den" class="mt-1.5 text-xs text-red-600">{{ form.errors.gia_den }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Địa chỉ hiển thị</label>
                                    <input v-model="form.dia_chi_hien_thi" type="text" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-sm font-medium text-stone-700">Mô tả chi tiết</label>
                                    <textarea v-model="form.mo_ta_chi_tiet" rows="6" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition-all focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" />
                                </div>
                            </div>
                        </div>

                        <!-- Ảnh dịch vụ -->
                        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 text-base font-semibold text-stone-950">Ảnh dịch vụ</h2>
                            <div class="flex flex-wrap gap-3">
                                <!-- Ảnh cũ -->
                                <div
                                    v-for="(img, idx) in existingImages"
                                    :key="'existing-' + idx"
                                    class="group relative size-24 overflow-hidden rounded-xl border border-stone-200"
                                >
                                    <img :src="img" class="size-full object-cover" referrerpolicy="no-referrer" />
                                    <button
                                        type="button"
                                        class="absolute right-1 top-1 rounded-full bg-black/60 p-0.5 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        @click="removeExistingImage(idx)"
                                    >
                                        <X class="size-3.5" />
                                    </button>
                                </div>
                                <!-- Ảnh mới -->
                                <div
                                    v-for="(preview, idx) in newImagePreviews"
                                    :key="'new-' + idx"
                                    class="group relative size-24 overflow-hidden rounded-xl border-2 border-brand"
                                >
                                    <img :src="preview" class="size-full object-cover" />
                                    <button
                                        type="button"
                                        class="absolute right-1 top-1 rounded-full bg-black/60 p-0.5 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        @click="removeNewImage(idx)"
                                    >
                                        <X class="size-3.5" />
                                    </button>
                                    <span class="absolute bottom-0 left-0 right-0 bg-brand/80 py-0.5 text-center text-[9px] text-white">Mới</span>
                                </div>
                                <!-- Add button -->
                                <label class="flex size-24 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-stone-300 text-stone-400 transition-colors hover:border-brand hover:text-brand">
                                    <ImagePlus class="size-6" />
                                    <span class="mt-1 text-[10px]">Thêm ảnh</span>
                                    <input type="file" accept="image/*" multiple class="hidden" @change="handleImageSelect" />
                                </label>
                            </div>
                        </div>

                        <!-- Tags & Khu vực -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                                <h2 class="mb-4 text-base font-semibold text-stone-950">Từ khóa</h2>
                                <div class="flex gap-2">
                                    <input v-model="tagInput" type="text" placeholder="Nhập từ khóa..." class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" @keyup.enter.prevent="addTag" />
                                    <button type="button" class="rounded-xl bg-stone-100 px-3.5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-200" @click="addTag">Thêm</button>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span v-for="(tag, idx) in form.the_tu_khoa" :key="idx" class="flex items-center gap-1.5 rounded-full bg-brand-surface px-3 py-1 text-xs font-medium text-brand ring-1 ring-brand">
                                        <Tag class="size-3" /> {{ tag }}
                                        <button type="button" class="ml-0.5 text-brand hover:text-brand" @click="removeTag(idx)"><X class="size-3" /></button>
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                                <h2 class="mb-4 text-base font-semibold text-stone-950">Khu vực phục vụ</h2>
                                <div class="flex gap-2">
                                    <input v-model="areaInput" type="text" placeholder="VD: Phường 3, Đà Lạt" class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand" @keyup.enter.prevent="addArea" />
                                    <button type="button" class="rounded-xl bg-stone-100 px-3.5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-200" @click="addArea">Thêm</button>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span v-for="(area, idx) in form.khu_vuc_phuc_vu" :key="idx" class="flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                        {{ area }}
                                        <button type="button" class="ml-0.5 text-emerald-400 hover:text-emerald-600" @click="removeArea(idx)"><X class="size-3" /></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
        </div>
    </ProviderLayout>
</template>