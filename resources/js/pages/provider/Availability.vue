<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Loader2, Save } from 'lucide-vue-next';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface ScheduleItem {
    thu: string;
    bat_dau: string;
    ket_thuc: string;
    hoat_dong: boolean;
}

const props = withDefaults(defineProps<{
    schedule: ScheduleItem[];
}>(), {
    schedule: () => [],
});

const page = usePage();
const flash = computed(() => ({
    success: page.props.flash?.success as string | undefined,
}));

const form = useForm({
    schedule: (props.schedule ?? []).map(item => ({ ...item })),
});

function submit() {
    form.put('/provider/availability');
}
</script>

<template>
    <Head title="Lịch làm việc" />

    <ProviderLayout activePage="availability">
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
                                <h1 class="text-xl font-bold text-stone-950">Lịch làm việc</h1>
                                <p class="text-sm text-stone-500">Thiết lập giờ hoạt động trong tuần</p>
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-700 disabled:opacity-50"
                            >
                                <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                                <Save v-else class="size-4" />
                                Lưu lịch làm việc
                            </button>
                        </div>

                        <!-- Schedule Table -->
                        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                            <div class="border-b border-stone-100 px-6 py-4">
                                <h2 class="flex items-center gap-2 text-base font-semibold text-stone-950">
                                    <Clock class="size-5 text-brand" /> Khung giờ làm việc
                                </h2>
                                <p class="mt-1 text-xs text-stone-400">Tắt ngày nghỉ, bật ngày làm việc. Thời gian theo 24h.</p>
                            </div>

                            <div class="divide-y divide-stone-100">
                                <div
                                    v-for="(item, idx) in form.schedule"
                                    :key="idx"
                                    class="flex flex-col gap-3 px-6 py-4 transition-colors sm:flex-row sm:items-center sm:gap-6"
                                    :class="item.hoat_dong ? 'bg-white' : 'bg-stone-50/50'"
                                >
                                    <!-- Toggle + Day -->
                                    <div class="flex items-center gap-4 sm:w-40">
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input
                                                type="checkbox"
                                                v-model="item.hoat_dong"
                                                class="peer sr-only"
                                            />
                                            <div class="h-6 w-11 rounded-full bg-stone-200 after:absolute after:left-[2px] after:top-[2px] after:size-5 after:rounded-full after:border after:border-stone-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-orange-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-orange-300" />
                                        </label>
                                        <span
                                            class="text-sm font-medium"
                                            :class="item.hoat_dong ? 'text-stone-950' : 'text-stone-400'"
                                        >
                                            {{ item.thu }}
                                        </span>
                                    </div>

                                    <!-- Time inputs -->
                                    <div v-if="item.hoat_dong" class="flex flex-1 items-center gap-3">
                                        <div>
                                            <label class="mb-1 block text-xs text-stone-500">Bắt đầu</label>
                                            <input
                                                v-model="item.bat_dau"
                                                type="time"
                                                class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                            />
                                        </div>
                                        <span class="mt-5 text-stone-300">→</span>
                                        <div>
                                            <label class="mb-1 block text-xs text-stone-500">Kết thúc</label>
                                            <input
                                                v-model="item.ket_thuc"
                                                type="time"
                                                class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm outline-none focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand"
                                            />
                                        </div>
                                    </div>

                                    <div v-else class="flex-1">
                                        <span class="text-sm text-stone-400">Nghỉ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </ProviderLayout>
</template>