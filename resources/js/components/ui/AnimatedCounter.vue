<script setup lang="ts">
import { ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

const props = defineProps<{
    target: number | string;
    label?: string;
    duration?: number;
}>();

const count = ref(0);
const targetRef = ref(null);
const numericTarget = typeof props.target === 'string' ? parseInt(props.target.replace(/\D/g, '')) || 0 : props.target;

const { stop } = useIntersectionObserver(
    targetRef,
    ([{ isIntersecting }]) => {
        if (isIntersecting) {
            animateValue(0, numericTarget, props.duration || 1500);
            stop(); // only animate once
        }
    },
    { threshold: 0.1 }
);

function animateValue(start: number, end: number, duration: number) {
    let startTimestamp: number | null = null;
    const step = (timestamp: number) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        // Easing out curve
        const easeOutQuad = 1 - (1 - progress) * (1 - progress);
        count.value = Math.floor(easeOutQuad * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
             count.value = end;
        }
    };
    window.requestAnimationFrame(step);
}
</script>

<template>
    <div class="inline-flex flex-col items-center" ref="targetRef">
        <span class="font-serif text-4xl text-inherit">{{ count }}<slot name="suffix"></slot></span>
        <span v-if="label" class="text-sm font-medium text-inherit opacity-80 mt-2">{{ label }}</span>
    </div>
</template>
