<script setup lang="ts">
withDefaults(defineProps<{
    variant?: 'text' | 'circle' | 'card' | 'image';
    width?: string;
    height?: string;
    lines?: number;
}>(), {
    variant: 'text',
    lines: 1,
});
</script>

<template>
    <div v-if="variant === 'card'" class="skeleton skeleton--card" :style="{ width, height }">
        <div class="skeleton__shimmer skeleton--image" style="height: 160px;" />
        <div style="padding: var(--dl-space-4); display: flex; flex-direction: column; gap: var(--dl-space-3);">
            <div class="skeleton__shimmer" style="height: 14px; width: 75%;" />
            <div class="skeleton__shimmer" style="height: 12px; width: 50%;" />
            <div class="skeleton__shimmer" style="height: 12px; width: 90%;" />
        </div>
    </div>

    <div v-else-if="variant === 'circle'" class="skeleton__shimmer skeleton--circle" :style="{ width: width || '48px', height: height || '48px' }" />

    <div v-else-if="variant === 'image'" class="skeleton__shimmer skeleton--image" :style="{ width: width || '100%', height: height || '200px' }" />

    <div v-else class="skeleton--text-group" :style="{ width }">
        <div
            v-for="i in lines"
            :key="i"
            class="skeleton__shimmer skeleton--text"
            :style="{
                height: height || '14px',
                width: i === lines && lines > 1 ? '60%' : '100%',
            }"
        />
    </div>
</template>

<style scoped>
.skeleton__shimmer {
    background: linear-gradient(
        90deg,
        var(--dl-warm-border) 25%,
        color-mix(in srgb, var(--dl-warm-border) 50%, white) 50%,
        var(--dl-warm-border) 75%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: var(--dl-radius-md);
}

@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton--card {
    border-radius: var(--dl-radius-2xl);
    border: 1px solid var(--dl-warm-border);
    overflow: hidden;
    background: var(--dl-warm-surface);
}

.skeleton--circle {
    border-radius: 50%;
}

.skeleton--image {
    border-radius: var(--dl-radius-lg);
}

.skeleton--text {
    border-radius: var(--dl-radius-sm);
}

.skeleton--text-group {
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-2);
}
</style>
