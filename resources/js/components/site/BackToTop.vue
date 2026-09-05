<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { ArrowUp } from '@lucide/vue';

const visible = ref(false);

const onScroll = () => {
    visible.value = window.scrollY > 500;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <button
            v-if="visible"
            type="button"
            @click="scrollToTop"
            aria-label="Volver arriba"
            class="fixed bottom-24 right-6 z-40 flex h-11 w-11 items-center justify-center rounded-full border border-smoke bg-graphite/90 text-cream backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:border-silver/40 hover:text-silver-bright"
        >
            <ArrowUp class="h-4 w-4" />
        </button>
    </Transition>
</template>
