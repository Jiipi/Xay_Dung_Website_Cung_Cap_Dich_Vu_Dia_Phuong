<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, CheckIcon, CheckCheckIcon } from 'lucide-vue-next';
import axios from 'axios';

const page = usePage();
const unreadCount = computed(() => (page.props as any).unreadNotifications ?? 0);

const isOpen = ref(false);
const notifications = ref<any[]>([]);
const isLoading = ref(false);

const dropdownRef = ref<HTMLElement | null>(null);

function toggleDropdown() {
    isOpen.value = !isOpen.value;
    if (isOpen.value && notifications.value.length === 0) {
        fetchRecent();
    }
}

async function fetchRecent() {
    isLoading.value = true;
    try {
        const { data } = await axios.get('/customer/notifications/recent');
        notifications.value = data;
    } catch (e) {
        console.error('Failed to load notifications', e);
    } finally {
        isLoading.value = false;
    }
}

// Clicks outside
function handleClickOutside(event: MouseEvent) {
    if (isOpen.value && dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});
onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="toggleDropdown"
            class="relative flex size-10 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 transition hover:bg-stone-50 hover:text-stone-900"
            aria-label="Thông báo"
        >
            <Bell class="size-5" />
            <span
                v-if="unreadCount > 0"
                class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full text-[10px] font-bold text-white"
                style="background: var(--dl-accent);"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 top-full mt-2 w-80 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl z-50"
            >
                <div class="flex items-center justify-between border-b border-stone-100 bg-stone-50/50 px-4 py-3">
                    <h3 class="font-bold text-stone-900">Thông báo</h3>
                </div>

                <div class="max-h-80 overflow-y-auto">
                    <div v-if="isLoading" class="p-6 text-center text-sm text-stone-500">
                        Đang tải...
                    </div>
                    <div v-else-if="notifications.length === 0" class="p-6 text-center text-sm text-stone-500">
                        Bạn chưa có thông báo nào.
                    </div>
                    <div v-else class="divide-y divide-stone-100">
                        <Link
                            v-for="n in notifications"
                            :key="n.id"
                            href="/customer/notifications"
                            class="block p-4 transition hover:bg-stone-50"
                            :class="n.read ? 'opacity-70' : 'bg-brand/5'"
                            @click="isOpen = false"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-stone-900">{{ n.title }}</p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-stone-500">{{ n.body }}</p>
                                    <p class="mt-1 text-[10px] font-medium text-stone-400">{{ n.date }}</p>
                                </div>
                                <div v-if="!n.read" class="mt-1 size-2 shrink-0 rounded-full bg-brand"></div>
                            </div>
                        </Link>
                    </div>
                </div>

                <div class="border-t border-stone-100 p-2">
                    <Link
                        href="/customer/notifications"
                        @click="isOpen = false"
                        class="block rounded-xl py-2 text-center text-sm font-medium text-stone-600 transition hover:bg-stone-50 hover:text-stone-900"
                    >
                        Xem tất cả thông báo
                    </Link>
                </div>
            </div>
        </Transition>
    </div>
</template>

