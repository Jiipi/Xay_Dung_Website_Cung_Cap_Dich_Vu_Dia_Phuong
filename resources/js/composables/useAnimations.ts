/**
 * useAnimations.ts — Animation Design System
 *
 * Provides reusable, lifecycle-safe GSAP animation primitives.
 * All animations:
 *   - Auto-cleanup on component unmount (no memory leaks)
 *   - Wait for nextTick (Inertia DOM readiness)
 *   - Respect prefers-reduced-motion
 *   - SSR-safe (guard window/document)
 *   - GPU-only properties (transform + opacity)
 */

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { nextTick, onMounted, onUnmounted, type Ref } from 'vue';

// Register plugin once
if (typeof window !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
}

// ─── Types ───────────────────────────────────────────────────────
interface AnimationOptions {
    duration?: number;
    delay?: number;
    ease?: string;
    start?: string;       // ScrollTrigger start position
    end?: string;         // ScrollTrigger end position
    scrub?: boolean | number;
    once?: boolean;       // play only once (default: true)
    mobile?: boolean;     // enable on mobile (default: true)
}

interface StaggerOptions extends AnimationOptions {
    stagger?: number;
    from?: 'start' | 'end' | 'center' | 'edges' | 'random';
}

interface ParallaxOptions {
    speed?: number;       // -1 to 1 (negative = opposite direction)
    start?: string;
    end?: string;
}

// ─── Helpers ─────────────────────────────────────────────────────

/** Check if user prefers reduced motion */
function prefersReducedMotion(): boolean {
    if (typeof window === 'undefined') return true;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/** Check if viewport is mobile-sized */
function isMobileViewport(): boolean {
    if (typeof window === 'undefined') return false;
    return window.innerWidth < 768;
}

/** Resolve element from Ref, string selector, or HTMLElement */
function resolveElement(
    el: Ref<HTMLElement | null> | HTMLElement | string | null,
): HTMLElement | string | null {
    if (!el) return null;
    if (typeof el === 'string') return el;
    if (el instanceof HTMLElement) return el;
    // Vue Ref
    return (el as Ref<HTMLElement | null>).value;
}

/** Resolve multiple elements */
function resolveElements(
    els: Ref<HTMLElement | null> | HTMLElement | string | HTMLElement[] | null,
): HTMLElement | HTMLElement[] | string | null {
    if (!els) return null;
    if (typeof els === 'string') return els;
    if (Array.isArray(els)) return els;
    if (els instanceof HTMLElement) return els;
    return (els as Ref<HTMLElement | null>).value;
}

// ─── Main Composable ─────────────────────────────────────────────

export function useAnimations() {
    const triggers: ScrollTrigger[] = [];
    const tweens: gsap.core.Tween[] = [];
    const timelines: gsap.core.Timeline[] = [];

    // Cleanup all animations on unmount
    onUnmounted(() => {
        tweens.forEach((t) => t.kill());
        timelines.forEach((tl) => tl.kill());
        triggers.forEach((st) => st.kill());
        tweens.length = 0;
        timelines.length = 0;
        triggers.length = 0;
    });

    /**
     * Track a ScrollTrigger for auto-cleanup
     */
    function trackTrigger(st: ScrollTrigger) {
        triggers.push(st);
    }

    /**
     * Track a tween for auto-cleanup
     */
    function trackTween(tw: gsap.core.Tween) {
        tweens.push(tw);
    }

    // ─── Animation Primitives ────────────────────────────────────

    /**
     * Fade + slide up when element scrolls into view
     */
    function animateFadeUp(
        el: Ref<HTMLElement | null> | HTMLElement | string,
        opts: AnimationOptions = {},
    ) {
        const {
            duration = 0.8,
            delay = 0,
            ease = 'power3.out',
            start = 'top 85%',
            once = true,
            mobile = true,
        } = opts;

        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;
            if (!mobile && isMobileViewport()) return;

            const target = resolveElement(el);
            if (!target) return;

            const tw = gsap.fromTo(
                target,
                { y: 30, opacity: 0, force3D: true },
                {
                    y: 0,
                    opacity: 1,
                    duration,
                    delay,
                    ease,
                    scrollTrigger: {
                        trigger: target,
                        start,
                        toggleActions: once
                            ? 'play none none none'
                            : 'play none none reverse',
                        onEnter: (self) => trackTrigger(self),
                    },
                },
            );
            trackTween(tw);
        });
    }

    /**
     * Stagger reveal for grids/lists of elements
     */
    function animateStagger(
        container: Ref<HTMLElement | null> | HTMLElement | string,
        childSelector: string,
        opts: StaggerOptions = {},
    ) {
        const {
            duration = 0.7,
            delay = 0,
            ease = 'power3.out',
            start = 'top 85%',
            stagger = 0.08,
            from = 'start',
            once = true,
            mobile = true,
        } = opts;

        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;
            if (!mobile && isMobileViewport()) return;

            const trigger = resolveElement(container);
            if (!trigger) return;

            const triggerEl =
                typeof trigger === 'string'
                    ? document.querySelector(trigger)
                    : trigger;
            if (!triggerEl) return;

            const children = triggerEl.querySelectorAll(childSelector);
            if (!children.length) return;

            // Set initial state - use smaller Y and force GPU acceleration layer ahead of time
            gsap.set(children, { 
                y: 30, 
                opacity: 0,
                force3D: true, // Force GPU layer
            });

            const tw = gsap.to(children, {
                y: 0,
                opacity: 1,
                duration: duration * 0.8, // Slightly faster to feel punchier
                delay,
                ease: 'power2.out', // power2 is cheaper to calculate than power3
                stagger: { each: stagger, from },
                scrollTrigger: {
                    trigger: triggerEl,
                    start,
                    toggleActions: once
                        ? 'play none none none'
                        : 'play none none reverse',
                    onEnter: (self) => trackTrigger(self),
                },
            });
            trackTween(tw);
        });
    }

    /**
     * Parallax — element moves at different speed than scroll
     */
    function animateParallax(
        el: Ref<HTMLElement | null> | HTMLElement | string,
        opts: ParallaxOptions = {},
    ) {
        const {
            speed = -0.3,
            start = 'top bottom',
            end = 'bottom top',
        } = opts;

        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;
            if (isMobileViewport()) return; // parallax disabled on mobile

            const target = resolveElement(el);
            if (!target) return;

            const tw = gsap.to(target, {
                y: () => speed * 200,
                ease: 'none',
                scrollTrigger: {
                    trigger: target,
                    start,
                    end,
                    scrub: 1,
                    onEnter: (self) => trackTrigger(self),
                },
            });
            trackTween(tw);
        });
    }

    /**
     * Scale + fade in from center
     */
    function animateScaleIn(
        el: Ref<HTMLElement | null> | HTMLElement | string,
        opts: AnimationOptions = {},
    ) {
        const {
            duration = 0.9,
            delay = 0,
            ease = 'power3.out',
            start = 'top 85%',
            once = true,
            mobile = true,
        } = opts;

        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;
            if (!mobile && isMobileViewport()) return;

            const target = resolveElement(el);
            if (!target) return;

            const tw = gsap.from(target, {
                scale: 0.92,
                opacity: 0,
                duration,
                delay,
                ease,
                scrollTrigger: {
                    trigger: target,
                    start,
                    toggleActions: once
                        ? 'play none none none'
                        : 'play none none reverse',
                    onEnter: (self) => trackTrigger(self),
                },
            });
            trackTween(tw);
        });
    }

    /**
     * Create a managed GSAP timeline (auto-cleanup)
     */
    function createTimeline(opts?: gsap.TimelineVars): gsap.core.Timeline {
        const tl = gsap.timeline(opts);
        timelines.push(tl);
        return tl;
    }

    /**
     * Hero entrance animation — orchestrated timeline
     * Call inside onMounted, works with template refs
     */
    function animateHeroEntrance(
        elements: {
            badge?: Ref<HTMLElement | null>;
            headline?: Ref<HTMLElement | null>;
            description?: Ref<HTMLElement | null>;
            searchBar?: Ref<HTMLElement | null>;
            stats?: Ref<HTMLElement | null>;
            illustration?: Ref<HTMLElement | null>;
        },
        opts: { delay?: number } = {},
    ) {
        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) {
                // Show everything immediately
                Object.values(elements).forEach((ref) => {
                    if (ref?.value) {
                        gsap.set(ref.value, { opacity: 1, y: 0, scale: 1 });
                    }
                });
                return;
            }

            const tl = createTimeline({ delay: opts.delay ?? 0.2 });

            // Set initial states
            Object.values(elements).forEach((ref) => {
                if (ref?.value) {
                    gsap.set(ref.value, { opacity: 0, y: 30 });
                }
            });

            // Orchestrated entrance
            if (elements.badge?.value) {
                tl.to(elements.badge.value, {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out',
                });
            }
            if (elements.headline?.value) {
                tl.to(
                    elements.headline.value,
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.7,
                        ease: 'power3.out',
                    },
                    '-=0.35',
                );
            }
            if (elements.description?.value) {
                tl.to(
                    elements.description.value,
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        ease: 'power3.out',
                    },
                    '-=0.3',
                );
            }
            if (elements.searchBar?.value) {
                tl.to(
                    elements.searchBar.value,
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        ease: 'power3.out',
                    },
                    '-=0.25',
                );
            }
            if (elements.stats?.value) {
                tl.to(
                    elements.stats.value,
                    {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        ease: 'power3.out',
                    },
                    '-=0.2',
                );
            }
            if (elements.illustration?.value) {
                tl.to(
                    elements.illustration.value,
                    {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        duration: 0.8,
                        ease: 'back.out(1.4)',
                    },
                    '-=0.5',
                );
            }
        });
    }

    /**
     * Auto-hide header on scroll down, show on scroll up
     */
    function animateAutoHideHeader(
        header: Ref<HTMLElement | null>,
        opts: { hideDistance?: number } = {},
    ) {
        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;

            const el = header.value;
            if (!el) return;

            const st = ScrollTrigger.create({
                start: 'top top',
                end: 'max',
                onUpdate: (self) => {
                    const direction = self.direction;
                    const scroll = self.scroll();

                    if (scroll < (opts.hideDistance ?? 100)) {
                        gsap.to(el, {
                            y: 0,
                            duration: 0.3,
                            ease: 'power2.out',
                        });
                        return;
                    }

                    // Scroll down → hide, scroll up → show
                    gsap.to(el, {
                        y: direction === 1 ? -el.offsetHeight : 0,
                        duration: 0.3,
                        ease: 'power2.out',
                    });
                },
            });
            trackTrigger(st);
        });
    }

    /**
     * Magnetic hover effect for buttons
     */
    function animateMagnetic(el: Ref<HTMLElement | null>) {
        let moveFn: ((e: MouseEvent) => void) | null = null;
        let leaveFn: (() => void) | null = null;

        onMounted(async () => {
            await nextTick();
            if (typeof window === 'undefined') return;
            if (prefersReducedMotion()) return;
            if (isMobileViewport()) return;

            const target = el.value;
            if (!target) return;

            moveFn = (e: MouseEvent) => {
                const rect = target.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                gsap.to(target, {
                    x: x * 0.2,
                    y: y * 0.2,
                    duration: 0.3,
                    ease: 'power2.out',
                });
            };

            leaveFn = () => {
                gsap.to(target, {
                    x: 0,
                    y: 0,
                    duration: 0.5,
                    ease: 'elastic.out(1, 0.4)',
                });
            };

            target.addEventListener('mousemove', moveFn);
            target.addEventListener('mouseleave', leaveFn);
        });

        onUnmounted(() => {
            const target = el.value;
            if (target && moveFn) target.removeEventListener('mousemove', moveFn);
            if (target && leaveFn) target.removeEventListener('mouseleave', leaveFn);
        });
    }

    /**
     * Refresh all ScrollTriggers — call after Inertia navigation
     */
    function refreshScrollTriggers() {
        if (typeof window === 'undefined') return;
        requestAnimationFrame(() => {
            ScrollTrigger.refresh();
        });
    }

    return {
        animateFadeUp,
        animateStagger,
        animateParallax,
        animateScaleIn,
        animateHeroEntrance,
        animateAutoHideHeader,
        animateMagnetic,
        createTimeline,
        refreshScrollTriggers,
        trackTween,
        trackTrigger,
    };
}
