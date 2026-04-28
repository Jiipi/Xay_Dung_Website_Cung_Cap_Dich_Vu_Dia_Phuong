<script setup lang="ts">
defineProps<{
    title?: string;
    subtitle?: string;
}>();
</script>

<template>
    <div class="dashboard-template">
        <!-- Page Header -->
        <header v-if="title" class="dashboard-header">
            <div>
                <h1 class="dashboard-header__title">{{ title }}</h1>
                <p v-if="subtitle" class="dashboard-header__subtitle">{{ subtitle }}</p>
            </div>
            <div class="dashboard-header__actions">
                <slot name="actions" />
            </div>
        </header>

        <!-- KPI Cards Row -->
        <section v-if="$slots.kpis" class="dashboard-kpis">
            <slot name="kpis" />
        </section>

        <!-- Main + Sidebar Layout -->
        <div class="dashboard-body">
            <div class="dashboard-main">
                <slot name="main" />
                <!-- Default slot also maps to main -->
                <slot />
            </div>
            <aside v-if="$slots.sidebar" class="dashboard-sidebar">
                <slot name="sidebar" />
            </aside>
        </div>
    </div>
</template>

<style scoped>
.dashboard-template {
    padding: var(--dl-space-8) var(--dl-space-6);
    max-width: var(--dl-content-max);
    margin-inline: auto;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}

/* Header */
.dashboard-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: var(--dl-space-4);
    flex-wrap: wrap;
}
.dashboard-header__title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--dl-text);
}
.dashboard-header__subtitle {
    margin-top: var(--dl-space-1);
    font-size: 0.875rem;
    color: var(--dl-text-muted);
}

/* KPI Grid — 2 cols mobile, 4 cols desktop */
.dashboard-kpis {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--dl-space-4);
}
@media (min-width: 768px) {
    .dashboard-kpis {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Main + Sidebar */
.dashboard-body {
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}
@media (min-width: 1024px) {
    .dashboard-body {
        flex-direction: row;
    }
}

.dashboard-main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}

.dashboard-sidebar {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-4);
}
@media (min-width: 1024px) {
    .dashboard-sidebar {
        width: 320px;
        flex-shrink: 0;
    }
}
</style>
