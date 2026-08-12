/**
 * useSmoothScroll.ts — Lenis Smooth Scroll + GSAP Sync
 *
 * Provides buttery-smooth scrolling (iOS-like feel).
 * Properly syncs with GSAP ScrollTrigger to avoid trigger offset bugs.
 *
 * Usage:
 *   const { lenis } = useSmoothScroll();
 *   // lenis.value gives you the Lenis instance if needed
 */

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { onMounted, onUnmounted, shallowRef } from 'vue';
import type { ShallowRef } from 'vue';

// Register plugin
if (typeof window !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
}

// Singleton: only one Lenis instance across the app
let lenisInstance: Lenis | null = null;
let refCount = 0;
let tickerCallback: ((time: number) => void) | null = null;

export function useSmoothScroll(): {
    lenis: ShallowRef<Lenis | null>;
    scrollTo: (target: string | number | HTMLElement, opts?: object) => void;
    stop: () => void;
    start: () => void;
} {
    const lenis = shallowRef<Lenis | null>(null);

    onMounted(() => {
        if (typeof window === 'undefined') return;

        // Check reduced motion preference
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        refCount++;

        const shouldUseLenis =
            window.matchMedia('(pointer: fine)').matches &&
            !window.matchMedia('(max-width: 768px)').matches;

        if (!shouldUseLenis) {
            ScrollTrigger.refresh();
            return;
        }

        if (!lenisInstance) {
            lenisInstance = new Lenis({
                duration: 0.65,
                easing: (t: number) =>
                    Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
                syncTouch: false,
                wheelMultiplier: 0.9,
                touchMultiplier: 1.5,
                prevent: (node: Element | null) => {
                    if (!node) return false;
                    return Boolean(
                        node.closest(
                            '[data-lenis-prevent], .overflow-y-auto, .overflow-auto',
                        ),
                    );
                },
            });

            // ─── Critical: Sync Lenis → GSAP ScrollTrigger ─────────
            // Without this, ScrollTrigger positions will be wrong
            lenisInstance.on('scroll', ScrollTrigger.update);

            // Use GSAP ticker for raf (single rAF loop = better perf)
            tickerCallback = (time: number) => {
                lenisInstance?.raf(time * 1000);
            };
            gsap.ticker.add(tickerCallback);

            // Disable GSAP's built-in lag smoothing (conflicts with Lenis)
            gsap.ticker.lagSmoothing(0);
        }

        lenis.value = lenisInstance;
    });

    onUnmounted(() => {
        if (typeof window === 'undefined') return;

        refCount--;

        // Only destroy when last consumer unmounts
        if (refCount <= 0 && lenisInstance) {
            if (tickerCallback) {
                gsap.ticker.remove(tickerCallback);
                tickerCallback = null;
            }
            lenisInstance.destroy();
            lenisInstance = null;
            refCount = 0;
        }
    });

    function scrollTo(
        target: string | number | HTMLElement,
        opts: object = {},
    ) {
        lenisInstance?.scrollTo(target, opts);
    }

    function stop() {
        lenisInstance?.stop();
    }

    function start() {
        lenisInstance?.start();
    }

    return { lenis, scrollTo, stop, start };
}
