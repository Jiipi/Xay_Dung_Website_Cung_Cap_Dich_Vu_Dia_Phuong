<script setup lang="ts">
import { onMounted } from 'vue';
import SiteHeader from '@/components/SiteHeader.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import FlashToast from '@/components/FlashToast.vue';
import { useSmoothScroll } from '@/composables/useSmoothScroll';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

withDefaults(defineProps<{ role?: 'customer' | 'provider' | 'admin' }>(), { role: 'customer' });

// Initialize Lenis smooth scroll globally for all marketplace pages
useSmoothScroll();

onMounted(() => {
    if (typeof window !== 'undefined') {
        // Force GSAP ScrollTrigger to recalculate positions after layout is fully rendered
        setTimeout(() => {
            ScrollTrigger.refresh();
        }, 300);
    }
});
</script>

<template>
    <div class="marketplace-shell" :class="`marketplace-shell--${role}`" :data-role="role">
        <SiteHeader />

        <!-- Breadcrumb slot (optional) -->
        <slot name="breadcrumb" />

        <!-- Main content area -->
        <main class="marketplace-main">
            <slot />
        </main>

        <SiteFooter />
        <FlashToast />
    </div>
</template>

<style scoped>
.marketplace-shell {
    --role-color: var(--dl-customer);
    --role-surface: var(--dl-customer-surface);
    display: flex;
    flex-direction: column;
    min-height: 100dvh;
    background: var(--dl-warm-bg);
    color: var(--dl-text);
}

.marketplace-shell--provider {
    --role-color: var(--dl-provider, #e85d2a);
    --role-surface: var(--dl-provider-surface, rgba(232, 93, 42, 0.08));
}

.marketplace-shell--admin {
    --role-color: var(--dl-admin, #3b82f6);
    --role-surface: var(--dl-admin-surface, rgba(59, 130, 246, 0.08));
}

.marketplace-main {
    flex: 1;
}
</style>

