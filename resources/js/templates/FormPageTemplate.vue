<script setup lang="ts">
import { ref, computed } from 'vue';
import { ChevronLeft, ChevronRight, Check } from 'lucide-vue-next';

const props = defineProps<{
    title?: string;
    steps?: Array<{ key: string; label: string }>;
}>();

const currentStep = ref(0);
const totalSteps = computed(() => props.steps?.length ?? 1);

function next() {
    if (currentStep.value < totalSteps.value - 1) currentStep.value++;
}
function prev() {
    if (currentStep.value > 0) currentStep.value--;
}
function goTo(idx: number) {
    currentStep.value = idx;
}

defineExpose({ currentStep, next, prev, goTo });
</script>

<template>
    <div class="form-template">
        <div class="form-template__body">
            <!-- Form Side -->
            <div class="form-template__form">
                <!-- Header -->
                <header v-if="title" class="form-template__header">
                    <h1 class="form-template__title">{{ title }}</h1>
                </header>

                <!-- Step Indicator -->
                <nav v-if="steps && steps.length > 1" class="form-steps">
                    <div
                        v-for="(step, idx) in steps"
                        :key="step.key"
                        class="form-step"
                        :class="{
                            'form-step--completed': idx < currentStep,
                            'form-step--active': idx === currentStep,
                        }"
                        @click="goTo(idx)"
                    >
                        <span class="form-step__indicator">
                            <Check v-if="idx < currentStep" class="size-3.5" />
                            <span v-else>{{ idx + 1 }}</span>
                        </span>
                        <span class="form-step__label">{{ step.label }}</span>
                        <div v-if="idx < steps.length - 1" class="form-step__line" />
                    </div>
                </nav>

                <!-- Form Content -->
                <div class="form-template__content">
                    <slot name="form" :step="currentStep" :step-key="steps?.[currentStep]?.key" />
                    <slot :step="currentStep" />
                </div>

                <!-- Navigation Buttons -->
                <footer v-if="steps && steps.length > 1" class="form-template__nav">
                    <button
                        v-if="currentStep > 0"
                        class="form-nav-btn form-nav-btn--secondary"
                        @click="prev"
                    >
                        <ChevronLeft class="size-4" /> Quay lại
                    </button>
                    <div class="flex-1" />
                    <slot name="submit" :step="currentStep" :is-last="currentStep === totalSteps - 1">
                        <button
                            v-if="currentStep < totalSteps - 1"
                            class="form-nav-btn form-nav-btn--primary"
                            @click="next"
                        >
                            Tiếp theo <ChevronRight class="size-4" />
                        </button>
                    </slot>
                </footer>
            </div>

            <!-- Preview Side -->
            <aside v-if="$slots.preview" class="form-template__preview">
                <slot name="preview" :step="currentStep" />
            </aside>
        </div>
    </div>
</template>

<style scoped>
.form-template {
    padding: var(--dl-space-8) var(--dl-space-6);
    max-width: var(--dl-content-max);
    margin-inline: auto;
}

.form-template__body {
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}
@media (min-width: 1024px) {
    .form-template__body {
        flex-direction: row;
    }
}

.form-template__form {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-6);
}

.form-template__header {
    padding-bottom: var(--dl-space-4);
    border-bottom: 1px solid var(--dl-warm-border);
}
.form-template__title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dl-text);
}

/* Step Indicator */
.form-steps {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
}
.form-step {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    cursor: pointer;
    flex: 1;
}
.form-step__indicator {
    display: flex;
    width: 28px; height: 28px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    font-size: 12px; font-weight: 700;
    background: var(--dl-warm-border);
    color: var(--dl-text-muted);
    flex-shrink: 0;
    transition: var(--dl-transition-fast);
}
.form-step--active .form-step__indicator {
    background: var(--role-color, var(--dl-brand));
    color: white;
}
.form-step--completed .form-step__indicator {
    background: var(--dl-brand-surface);
    color: var(--dl-brand);
}
.form-step__label {
    font-size: 13px;
    font-weight: 500;
    color: var(--dl-text-muted);
    white-space: nowrap;
    display: none;
}
@media (min-width: 640px) {
    .form-step__label { display: inline; }
}
.form-step--active .form-step__label {
    color: var(--dl-text);
    font-weight: 600;
}
.form-step__line {
    flex: 1;
    height: 2px;
    background: var(--dl-warm-border);
    margin: 0 var(--dl-space-2);
}
.form-step--completed .form-step__line {
    background: var(--dl-brand-light);
}

/* Content */
.form-template__content {
    border-radius: var(--dl-radius-2xl);
    background: var(--dl-warm-surface);
    border: 1px solid var(--dl-warm-border);
    padding: var(--dl-space-6);
    box-shadow: var(--dl-shadow-sm);
}

/* Nav Buttons */
.form-template__nav {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
}
.form-nav-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--dl-space-2);
    padding: var(--dl-space-3) var(--dl-space-5);
    border-radius: var(--dl-radius-xl);
    font-size: 14px;
    font-weight: 600;
    transition: var(--dl-transition-fast);
}
.form-nav-btn--primary {
    background: var(--role-color, var(--dl-brand));
    color: white;
}
.form-nav-btn--primary:hover {
    filter: brightness(0.9);
}
.form-nav-btn--secondary {
    background: var(--dl-warm-surface);
    border: 1px solid var(--dl-warm-border);
    color: var(--dl-text-muted);
}
.form-nav-btn--secondary:hover {
    background: var(--dl-warm-bg);
}

/* Preview */
.form-template__preview {
    width: 100%;
    border-radius: var(--dl-radius-2xl);
    background: var(--dl-warm-surface);
    border: 1px solid var(--dl-warm-border);
    padding: var(--dl-space-6);
    box-shadow: var(--dl-shadow-sm);
}
@media (min-width: 1024px) {
    .form-template__preview {
        width: 360px;
        flex-shrink: 0;
        position: sticky;
        top: calc(var(--dl-space-8) + 60px);
        align-self: flex-start;
    }
}
</style>
