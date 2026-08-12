<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Home, Mail, MapPin, Phone } from 'lucide-vue-next';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
</script>

<template>
    <Head title="Liên hệ" />

    <MarketplaceLayout>
        <section class="relative overflow-hidden border-b border-stone-200 bg-gradient-to-br from-stone-50 via-white to-sky-50">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(14,165,233,0.18),transparent_30%),radial-gradient(circle_at_90%_35%,rgba(16,185,129,0.16),transparent_28%)]"></div>
            <div class="absolute -left-28 top-16 size-72 rounded-full bg-brand/10 blur-3xl"></div>
            <div class="absolute -bottom-28 right-20 size-80 rounded-full bg-sky-200/35 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <nav class="mb-7 flex items-center gap-2 text-sm text-stone-400">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-stone-700"><Home class="size-3.5" /> Trang chủ</Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-stone-700">Liên hệ</span>
                </nav>

                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-stretch">
                    <section class="rounded-[2rem] bg-[linear-gradient(135deg,#0f172a_0%,#082f49_55%,#164e63_100%)] p-8 text-white shadow-xl shadow-stone-300/50">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-brand/80">Contact</p>
                        <h1 class="mt-4 text-4xl font-black tracking-tight">Liên hệ đội ngũ vận hành</h1>
                        <p class="mt-4 text-sm leading-7 text-slate-200">Gửi phản hồi, yêu cầu hỗ trợ hoặc trao đổi hợp tác để đội ngũ Dalat Services xử lý nhanh hơn.</p>

                        <div class="mt-8 space-y-4 text-sm text-slate-200">
                            <p class="flex items-center gap-3 rounded-2xl bg-white/10 p-3"><Phone class="size-5 text-brand" /> 0900 000 001</p>
                            <p class="flex items-center gap-3 rounded-2xl bg-white/10 p-3"><Mail class="size-5 text-brand" /> support@dalatservices.local</p>
                            <p class="flex items-center gap-3 rounded-2xl bg-white/10 p-3"><MapPin class="size-5 text-brand" /> 12 Trần Phú, Đà Lạt, Lâm Đồng</p>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-xl shadow-stone-200/70 backdrop-blur">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-brand">Support request</p>
                        <h2 class="mt-3 text-2xl font-bold text-stone-950">Gửi yêu cầu hỗ trợ</h2>
                        <div class="mt-6 grid gap-4">
                            <input type="text" placeholder="Họ tên" class="rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30" />
                            <input type="email" placeholder="Email" class="rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30" />
                            <textarea rows="5" placeholder="Nội dung cần hỗ trợ" class="rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30" />
                            <a href="mailto:support@dalatservices.local?subject=Yêu cầu hỗ trợ" class="inline-flex justify-center rounded-full bg-stone-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand">
                                Gửi liên hệ qua Email
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </MarketplaceLayout>
</template>
