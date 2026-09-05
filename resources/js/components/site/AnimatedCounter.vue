<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        target: number;
        suffix?: string;
        duration?: number;
    }>(),
    {
        suffix: '',
        duration: 1500,
    },
);

const el = ref<HTMLElement | null>(null);
const display = ref(0);
let started = false;
let observer: IntersectionObserver | null = null;

const animate = () => {
    if (started) return;
    started = true;

    const start = performance.now();

    const step = (now: number) => {
        const progress = Math.min((now - start) / props.duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        display.value = Math.round(props.target * eased);
        if (progress < 1) requestAnimationFrame(step);
    };

    requestAnimationFrame(step);
};

onMounted(() => {
    if (!el.value || typeof IntersectionObserver === 'undefined') {
        display.value = props.target;
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                animate();
                observer?.disconnect();
            }
        },
        { threshold: 0.4 },
    );
    observer.observe(el.value);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <span ref="el">{{ display }}{{ suffix }}</span>
</template>
