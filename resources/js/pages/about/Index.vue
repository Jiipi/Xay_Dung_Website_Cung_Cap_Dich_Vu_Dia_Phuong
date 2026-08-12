<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Building2, ChevronRight, Home, ShieldCheck, Sparkles, Users } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

const pillars = [
    { icon: Users, title: 'Kết nối địa phương', description: 'Kết nối khách hàng với nhà cung cấp địa phương có hồ sơ, đánh giá và lịch làm việc rõ ràng.' },
    { icon: ShieldCheck, title: 'Tăng độ tin cậy', description: 'Xây trust bằng booking flow, review, lịch sử giao dịch và xác minh nhà cung cấp.' },
    { icon: Sparkles, title: 'AI gợi ý', description: 'Tận dụng dữ liệu hành vi để gợi ý lịch trình, dịch vụ phù hợp theo nhu cầu thực tế.' },
    { icon: Building2, title: 'Sẵn sàng triển khai', description: 'Thiết kế như một marketplace có thể mở rộng thành hệ thống vận hành thật.' },
];
</script>

<template>
    <Head title="Giới thiệu" />

    <MarketplaceLayout>
        <section class="relative overflow-hidden border-b border-stone-200 bg-gradient-to-br from-stone-50 via-white to-emerald-50">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(16,185,129,0.18),transparent_30%),radial-gradient(circle_at_85%_10%,rgba(14,165,233,0.16),transparent_28%)]"></div>
            <div class="absolute -right-24 top-12 size-72 rounded-full bg-brand/10 blur-3xl"></div>
            <div class="absolute -bottom-32 left-1/4 size-80 rounded-full bg-sky-200/30 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <nav class="mb-7 flex items-center gap-2 text-sm text-stone-400">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-stone-700"><Home class="size-3.5" /> Trang chủ</Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-stone-700">Giới thiệu</span>
                </nav>

                <div class="grid gap-8 lg:grid-cols-[1fr_0.72fr] lg:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.26em] text-brand">About Dalat Services</p>
                        <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight text-stone-950 sm:text-5xl">Marketplace dịch vụ địa phương cho du lịch, sửa chữa và đặt lịch thông minh.</h1>
                        <p class="mt-5 max-w-2xl text-base leading-8 text-stone-600">Nền tảng giúp bạn tìm kiếm, so sánh và đặt lịch các dịch vụ địa phương một cách nhanh chóng, minh bạch và đáng tin cậy.</p>
                    </div>

                    <div class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-xl shadow-stone-200/60 backdrop-blur">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-stone-950 p-4 text-white">
                                <p class="text-3xl font-black">3</p>
                                <p class="mt-1 text-xs text-stone-300">Luồng chính</p>
                            </div>
                            <div class="rounded-2xl bg-brand p-4 text-white">
                                <p class="text-3xl font-black">24/7</p>
                                <p class="mt-1 text-xs text-white/80">Đặt lịch</p>
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-stone-600">Một hệ thống duy nhất cho tìm kiếm dịch vụ, quản lý nhà cung cấp và trải nghiệm khách hàng.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <article v-for="item in pillars" :key="item.title" class="group rounded-3xl border border-stone-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="inline-flex rounded-2xl bg-brand-surface p-4 text-brand transition group-hover:scale-105"><component :is="item.icon" class="size-6" /></div>
                    <h2 class="mt-5 text-lg font-semibold text-stone-950">{{ item.title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-stone-600">{{ item.description }}</p>
                </article>
            </div>
        </section>
    </MarketplaceLayout>
</template>
