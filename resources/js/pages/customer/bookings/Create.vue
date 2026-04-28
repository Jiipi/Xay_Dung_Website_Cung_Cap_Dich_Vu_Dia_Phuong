<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Calendar, MapPin, NotebookPen, UserRound } from 'lucide-vue-next';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
</script>

<template>
    <Head title="Đặt dịch vụ" />

    <CustomerLayout activePage="bookings">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                <section class="rounded-3xl border border-gray-100 bg-white p-8 shadow-sm">
                    <h1 class="text-3xl font-black tracking-tight text-gray-900">Booking form</h1>
                    <p class="mt-3 text-sm leading-7 text-gray-600">Khung này xử lý đúng bài toán marketplace: chọn ngày giờ, địa điểm, số lượng khách, ghi chú và xác nhận booking.</p>
                    <div class="mt-8 grid gap-5">
                        <label class="grid gap-2"><span class="text-sm font-medium text-gray-700">Ngày thực hiện</span><div class="relative"><Calendar class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-400" /><input type="date" class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 outline-none" /></div></label>
                        <label class="grid gap-2"><span class="text-sm font-medium text-gray-700">Khung giờ</span><select class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 outline-none"><option>08:00 - 10:00</option><option>13:00 - 15:00</option><option>18:00 - 20:00</option></select></label>
                        <label class="grid gap-2"><span class="text-sm font-medium text-gray-700">Địa điểm thực hiện</span><div class="relative"><MapPin class="pointer-events-none absolute left-4 top-4 size-4 text-gray-400" /><textarea rows="4" class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 outline-none" placeholder="Số nhà, phường/xã, quận/huyện..." /></div></label>
                        <label class="grid gap-2"><span class="text-sm font-medium text-gray-700">Số lượng</span><div class="relative"><UserRound class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-400" /><input type="number" min="1" value="2" class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 outline-none" /></div></label>
                        <label class="grid gap-2"><span class="text-sm font-medium text-gray-700">Ghi chú cho nhà cung cấp</span><div class="relative"><NotebookPen class="pointer-events-none absolute left-4 top-4 size-4 text-gray-400" /><textarea rows="5" class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 outline-none" placeholder="Yêu cầu chi tiết, thời gian linh hoạt, lưu ý khi đến nơi..." /></div></label>
                    </div>
                </section>

                <aside class="rounded-3xl border border-gray-100 bg-white p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900">Xác nhận booking</h2>
                    <div class="mt-6 space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between"><span>Dịch vụ</span><span class="font-medium text-gray-900">Tour săn mây Cầu Đất</span></div>
                        <div class="flex justify-between"><span>Tạm tính</span><span class="font-medium text-gray-900">900.000đ</span></div>
                        <div class="flex justify-between"><span>Phí nền tảng</span><span class="font-medium text-gray-900">0đ</span></div>
                        <div class="flex justify-between border-t border-gray-100 pt-3 text-base font-bold text-gray-900"><span>Tổng tiền</span><span>900.000đ</span></div>
                    </div>
                    <button type="button" class="mt-6 w-full rounded-2xl bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">Gửi yêu cầu đặt dịch vụ</button>
                </aside>
            </div>
        </div>
    </CustomerLayout>
</template>