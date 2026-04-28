<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

const sections = [
    { title: 'Chính sách booking', body: 'Khách hàng chỉ được xác nhận khi nhà cung cấp chấp thuận và hệ thống lưu đầy đủ trạng thái xử lý.' },
    { title: 'Chính sách đánh giá', body: 'Chỉ booking hoàn thành mới được phép đánh giá để tránh review giả và tăng trust.' },
    { title: 'Chính sách dữ liệu', body: 'Thông tin hồ sơ, lịch sử booking và thông báo được lưu nhằm hỗ trợ vận hành và recommendation.' },
];
</script>

<template>
    <Head title="Chính sách" />

    <MarketplaceLayout>
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-semibold tracking-tight text-stone-950">Chính sách & điều khoản sử dụng</h1>
            <p class="mt-4 text-base leading-8 text-stone-600">Trang này làm rõ nguyên tắc booking, review và xử lý dữ liệu để đảm bảo trải nghiệm minh bạch và đáng tin cậy.</p>

            <div class="mt-8 space-y-5">
                <section v-for="section in sections" :key="section.title" class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">{{ section.title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-stone-600">{{ section.body }}</p>
                </section>
            </div>
        </div>
    </MarketplaceLayout>
</template>