<script setup lang="ts">
defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="map-template">
        <!-- Header -->
        <header v-if="title || $slots.header" class="map-header">
            <h1 v-if="title" class="map-header__title">{{ title }}</h1>
            <slot name="header" />
        </header>

        <!-- Split Layout -->
        <div class="map-body">
            <!-- List Panel -->
            <aside class="map-list">
                <slot name="list" />
            </aside>

            <!-- Map Area -->
            <div class="map-area">
                <slot name="map" />
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.map-template {
    display: flex;
    flex-direction: column;
    height: calc(100dvh - 60px);
    overflow: hidden;
}

.map-header {
    padding: var(--dl-space-4) var(--dl-space-6);
    border-bottom: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    flex-shrink: 0;
}
.map-header__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dl-text);
}

.map-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}
@media (min-width: 768px) {
    .map-body {
        flex-direction: row;
    }
}

.map-list {
    width: 100%;
    overflow-y: auto;
    background: var(--dl-warm-surface);
    border-right: 1px solid var(--dl-warm-border);
}
@media (min-width: 768px) {
    .map-list {
        width: 380px;
        flex-shrink: 0;
    }
}
@media (max-width: 767px) {
    .map-list {
        max-height: 40vh;
        border-right: none;
        border-bottom: 1px solid var(--dl-warm-border);
    }
}

.map-area {
    flex: 1;
    min-height: 300px;
    background: #e5e7eb;
    position: relative;
}
</style>
