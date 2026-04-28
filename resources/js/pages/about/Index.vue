<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Building2, ShieldCheck, Sparkles, Users } from 'lucide-vue-next';
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
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="rounded-[2.2rem] bg-[linear-gradient(135deg,#0f172a_0%,#082f49_55%,#164e63_100%)] px-8 py-12 text-white">
                <p class="text-xs font-medium uppercase tracking-[0.24em] text-brand/80">About Dalat Services</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight">Marketplace dịch vụ địa phương cho du lịch, sửa chữa và đặt lịch thông minh.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-slate-200">Nền tảng giúp bạn tìm kiếm, so sánh và đặt lịch các dịch vụ địa phương một cách nhanh chóng, minh bạch và đáng tin cậy.</p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <article v-for="item in pillars" :key="item.title" class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="inline-flex rounded-2xl bg-brand-surface p-4 text-brand"><component :is="item.icon" class="size-6" /></div>
                    <h2 class="mt-5 text-lg font-semibold text-stone-950">{{ item.title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-stone-600">{{ item.description }}</p>
                </article>
            </div>
        </div>
    </MarketplaceLayout>
</template>