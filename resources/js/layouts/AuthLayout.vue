<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Bot, Map, ShieldCheck, Wrench } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const highlights = [
    {
        title: 'Tìm dịch vụ đúng nhu cầu',
        description:
            'Khám phá tour, sửa chữa, thuê xe và dịch vụ địa phương trong một hệ thống duy nhất.',
        icon: Map,
    },
    {
        title: 'Quản lý đặt lịch rõ ràng',
        description:
            'Theo dõi booking, trạng thái xử lý và lịch sử sử dụng dịch vụ ngay trong dashboard.',
        icon: ShieldCheck,
    },
    {
        title: 'Sẵn sàng mở rộng AI',
        description:
            'Nền tảng được thiết kế để tiến tới gợi ý lịch trình và đề xuất dịch vụ phù hợp.',
        icon: Bot,
    },
];
</script>

<template>
    <div class="min-h-dvh bg-stone-950 text-stone-100">
        <div class="grid min-h-dvh lg:grid-cols-[1.15fr_0.85fr]">
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.26),_transparent_35%),radial-gradient(circle_at_80%_20%,_rgba(20,184,166,0.18),_transparent_30%),linear-gradient(135deg,_#0c1222_0%,_#101826_38%,_#0f172a_100%)]" />
                <div class="absolute inset-0 opacity-20 [background-image:linear-gradient(rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:52px_52px]" />

                <div class="relative flex h-full flex-col px-6 py-8 sm:px-10 lg:px-14 lg:py-12">
                    <div class="flex items-center justify-between">
                        <Link
                            :href="home()"
                            class="inline-flex items-center gap-3 text-sm font-semibold tracking-[0.24em] text-white/90 uppercase"
                        >
                            <span class="flex size-11 items-center justify-center rounded-2xl bg-cyan-400/20 ring-1 ring-white/10">
                                <Wrench class="size-5 text-cyan-200" />
                            </span>
                            Dalat Services
                        </Link>

                        <div class="hidden rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs text-white/70 backdrop-blur md:block">
                            Laravel 12 + Inertia + Vue + PostgreSQL
                        </div>
                    </div>

                    <div class="mt-14 max-w-2xl lg:mt-24">
                        <p class="mb-4 text-sm font-medium uppercase tracking-[0.3em] text-cyan-200/80">
                            Marketplace dịch vụ địa phương
                        </p>
                        <h1 class="max-w-xl text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                            Một cổng dịch vụ hiện đại cho du lịch, sửa chữa và nhu cầu tại chỗ.
                        </h1>
                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                            Đăng nhập để quản lý đặt lịch, theo dõi trạng thái dịch vụ, vận hành nhà cung cấp và mở rộng dần sang gợi ý thông minh.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 md:grid-cols-3 lg:mt-auto">
                        <article
                            v-for="item in highlights"
                            :key="item.title"
                            class="rounded-3xl border border-white/10 bg-white/6 p-5 backdrop-blur"
                        >
                            <component :is="item.icon" class="mb-4 size-5 text-cyan-200" />
                            <h2 class="text-sm font-semibold text-white">
                                {{ item.title }}
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                {{ item.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center bg-stone-50 px-6 py-10 text-stone-900 sm:px-8 lg:px-12">
                <div class="w-full max-w-md">
                    <div class="rounded-[2rem] border border-stone-200 bg-white p-7 shadow-[0_32px_80px_-42px_rgba(15,23,42,0.45)] sm:p-9">
                        <div class="mb-8">
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-sky-700/80">
                                Truy cập hệ thống
                            </p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-stone-950" v-if="title">
                                {{ title }}
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-stone-600" v-if="description">
                                {{ description }}
                            </p>
                        </div>

                        <slot />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>