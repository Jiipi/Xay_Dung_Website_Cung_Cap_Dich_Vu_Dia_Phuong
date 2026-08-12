<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, FileCheck2, Home, LockKeyhole, MessageSquareText } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

const sections = [
    { icon: FileCheck2, title: 'Chính sách booking', body: 'Khách hàng chỉ được xác nhận khi nhà cung cấp chấp thuận và hệ thống lưu đầy đủ trạng thái xử lý.' },
    { icon: MessageSquareText, title: 'Chính sách đánh giá', body: 'Chỉ booking hoàn thành mới được phép đánh giá để tránh review giả và tăng trust.' },
    { icon: LockKeyhole, title: 'Chính sách dữ liệu', body: 'Thông tin hồ sơ, lịch sử booking và thông báo được lưu nhằm hỗ trợ vận hành và recommendation.' },
];
</script>

<template>
    <Head title="Chính sách" />

    <MarketplaceLayout>
        <section class="relative overflow-hidden border-b border-stone-200 bg-gradient-to-br from-stone-50 via-white to-amber-50">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(245,158,11,0.16),transparent_30%),radial-gradient(circle_at_88%_20%,rgba(16,185,129,0.14),transparent_28%)]"></div>
            <div class="absolute -right-24 top-8 size-72 rounded-full bg-amber-200/35 blur-3xl"></div>
            <div class="absolute -bottom-32 left-20 size-80 rounded-full bg-brand/10 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <nav class="mb-7 flex items-center gap-2 text-sm text-stone-400">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-stone-700"><Home class="size-3.5" /> Trang chủ</Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-stone-700">Chính sách</span>
                </nav>

                <div class="grid gap-8 lg:grid-cols-[1fr_0.6fr] lg:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.26em] text-brand">Policy & Terms</p>
                        <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight text-stone-950 sm:text-5xl">Chính sách & điều khoản sử dụng</h1>
                        <p class="mt-5 max-w-2xl text-base leading-8 text-stone-600">Trang này làm rõ nguyên tắc booking, review và xử lý dữ liệu để đảm bảo trải nghiệm minh bạch và đáng tin cậy.</p>
                    </div>

                    <div class="rounded-[2rem] border border-white/70 bg-white/80 p-6 shadow-xl shadow-stone-200/70 backdrop-blur">
                        <p class="text-sm font-semibold text-stone-950">Minh bạch vận hành</p>
                        <p class="mt-2 text-sm leading-6 text-stone-600">Mọi thao tác quan trọng đều được lưu trạng thái để khách hàng, nhà cung cấp và quản trị viên cùng theo dõi.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-5 md:grid-cols-3">
                <article v-for="section in sections" :key="section.title" class="group rounded-3xl border border-stone-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="inline-flex rounded-2xl bg-brand-surface p-4 text-brand transition group-hover:scale-105">
                        <component :is="section.icon" class="size-6" />
                    </div>
                    <h2 class="mt-5 text-xl font-semibold text-stone-950">{{ section.title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-stone-600">{{ section.body }}</p>
                </article>
            </div>
        </section>
    </MarketplaceLayout>
</template>
