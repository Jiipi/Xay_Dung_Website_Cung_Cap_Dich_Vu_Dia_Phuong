<script setup lang="ts">
defineProps<{
    title?: string;
    subtitle?: string;
    count?: number;
}>();
</script>

<template>
    <div class="list-template">
        <!-- Page Header -->
        <header class="list-header">
            <div>
                <h1 class="list-header__title">
                    {{ title }}
                    <span v-if="count !== undefined" class="list-header__count">{{ count }}</span>
                </h1>
                <p v-if="subtitle" class="list-header__subtitle">{{ subtitle }}</p>
            </div>
            <div class="list-header__actions">
                <slot name="actions" />
            </div>
        </header>

        <!-- Filters Bar -->
        <section v-if="$slots.filters" class="list-filters">
            <slot name="filters" />
        </section>

        <!-- List / Table Content -->
        <section class="list-content">
            <slot name="list" />
            <slot />
        </section>

        <!-- Pagination -->
        <footer v-if="$slots.pagination" class="list-pagination">
            <slot name="pagination" />
        </footer>
    </div>
</template>

<style scoped>
.list-template {
    padding: var(--dl-space-8) var(--dl-space-6);
    max-width: var(--dl-content-max);
    margin-inline: auto;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}

.list-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: var(--dl-space-4);
    flex-wrap: wrap;
}
.list-header__title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--dl-text);
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
}
.list-header__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    padding: 2px 10px;
    border-radius: var(--dl-radius-full);
    background: var(--dl-brand-surface);
    color: var(--dl-brand);
    font-size: 0.8125rem;
    font-weight: 700;
}
.list-header__subtitle {
    margin-top: var(--dl-space-1);
    font-size: 0.875rem;
    color: var(--dl-text-muted);
}

.list-filters {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    flex-wrap: wrap;
    padding: var(--dl-space-4);
    border-radius: var(--dl-radius-xl);
    background: var(--dl-warm-surface);
    border: 1px solid var(--dl-warm-border);
    box-shadow: var(--dl-shadow-sm);
}

.list-content {
    border-radius: var(--dl-radius-2xl);
    background: var(--dl-warm-surface);
    border: 1px solid var(--dl-warm-border);
    box-shadow: var(--dl-shadow-sm);
    overflow: hidden;
}

.list-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
