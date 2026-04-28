<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Mail, MapPin, Phone } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
</script>

<template>
    <Head title="Liên hệ" />

    <MarketplaceLayout>
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-[2.2rem] bg-[linear-gradient(135deg,#0f172a_0%,#082f49_55%,#164e63_100%)] p-8 text-white">
                    <p class="text-xs font-medium uppercase tracking-[0.22em] text-brand/80">Contact</p>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight">Liên hệ đội ngũ vận hành</h1>
                    <div class="mt-8 space-y-5 text-sm text-slate-200">
                        <p class="flex items-center gap-3"><Phone class="size-5 text-brand" /> 0900 000 001</p>
                        <p class="flex items-center gap-3"><Mail class="size-5 text-brand" /> support@dalatservices.local</p>
                        <p class="flex items-center gap-3"><MapPin class="size-5 text-brand" /> 12 Trần Phú, Đà Lạt, Lâm Đồng</p>
                    </div>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Gửi yêu cầu hỗ trợ</h2>
                    <div class="mt-6 grid gap-4">
                        <input type="text" placeholder="Họ tên" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-brand focus:ring-2 focus:ring-brand/30" />
                        <input type="email" placeholder="Email" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-brand focus:ring-2 focus:ring-brand/30" />
                        <textarea rows="5" placeholder="Nội dung cần hỗ trợ" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-brand focus:ring-2 focus:ring-brand/30" />
                        <a href="mailto:support@dalatservices.local?subject=Yêu cầu hỗ trợ" class="inline-flex justify-center rounded-full bg-brand px-5 py-3 text-sm font-medium text-white transition hover:bg-brand">
                            Gửi liên hệ qua Email
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </MarketplaceLayout>
</template>