<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
    title?: string;
    tabs?: Array<{ key: string; label: string }>;
}>();

const activeTab = ref('');

function setTab(key: string) {
    activeTab.value = key;
}

defineExpose({ activeTab, setTab });
</script>

<template>
    <div class="detail-template">
        <!-- Hero Section -->
        <section v-if="$slots.hero" class="detail-hero">
            <slot name="hero" />
        </section>

        <!-- Info Section -->
        <section v-if="$slots.info" class="detail-info">
            <slot name="info" />
        </section>

        <!-- Tabs -->
        <div v-if="tabs && tabs.length" class="detail-tabs-wrapper">
            <nav class="detail-tabs">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    class="detail-tab"
                    :class="{ 'detail-tab--active': activeTab === tab.key }"
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <section class="detail-content">
            <slot name="tabs" :active-tab="activeTab" />
            <slot />
        </section>
    </div>
</template>

<style scoped>
.detail-template {
    max-width: var(--dl-content-max);
    margin-inline: auto;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}

.detail-hero {
    border-radius: 0 0 var(--dl-radius-2xl) var(--dl-radius-2xl);
    overflow: hidden;
}

.detail-info {
    padding: 0 var(--dl-space-6);
}

.detail-tabs-wrapper {
    padding: 0 var(--dl-space-6);
    border-bottom: 1px solid var(--dl-warm-border);
}
.detail-tabs {
    display: flex;
    gap: var(--dl-space-1);
    overflow-x: auto;
}
.detail-tab {
    padding: var(--dl-space-3) var(--dl-space-4);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--dl-text-muted);
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    transition: var(--dl-transition-fast);
}
.detail-tab:hover {
    color: var(--dl-text);
}
.detail-tab--active {
    color: var(--role-color, var(--dl-brand));
    border-bottom-color: var(--role-color, var(--dl-brand));
    font-weight: 600;
}

.detail-content {
    padding: var(--dl-space-6);
}
</style>
