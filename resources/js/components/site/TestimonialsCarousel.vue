<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { ChevronLeft, ChevronRight, Quote, Star } from '@lucide/vue';

const props = defineProps<{
    testimonials: any[];
}>();

const active = ref(0);
const paused = ref(false);
let timer: ReturnType<typeof setInterval> | null = null;

const current = computed(() => props.testimonials[active.value]);

const go = (index: number) => {
    const total = props.testimonials.length;
    active.value = ((index % total) + total) % total;
};

const next = () => go(active.value + 1);
const prev = () => go(active.value - 1);

onMounted(() => {
    if (props.testimonials.length <= 1) return;
    timer = setInterval(() => {
        if (!paused.value) next();
    }, 5000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div
        v-if="testimonials.length"
        class="relative mx-auto max-w-3xl"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
    >
        <div class="glass-card relative overflow-hidden rounded-3xl p-8 text-center lg:p-12">
            <Quote class="mx-auto h-8 w-8 opacity-40" style="color: var(--color-gold)" />

            <Transition mode="out-in" enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                <div :key="current.id">
                    <p class="mt-6 font-serif text-xl leading-relaxed text-cream italic lg:text-2xl">
                        "{{ current.quote }}"
                    </p>

                    <div class="mt-6 flex items-center justify-center gap-1">
                        <Star
                            v-for="n in 5"
                            :key="n"
                            class="h-4 w-4"
                            :style="{ color: n <= (current.rating || 5) ? 'var(--color-gold)' : 'var(--color-smoke)' }"
                            :fill="n <= (current.rating || 5) ? 'var(--color-gold)' : 'none'"
                        />
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-3">
                        <img
                            v-if="current.photo_url"
                            :src="current.photo_url"
                            :alt="current.client_name"
                            class="h-10 w-10 rounded-full object-cover ring-2 ring-silver/20"
                        />
                        <span class="font-medium text-silver-bright">{{ current.client_name }}</span>
                    </div>
                </div>
            </Transition>
        </div>

        <template v-if="testimonials.length > 1">
            <button
                type="button"
                aria-label="Testimonio anterior"
                @click="prev"
                class="absolute left-0 top-1/2 hidden -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-smoke bg-graphite/90 p-2.5 text-cream backdrop-blur-xl transition hover:border-silver/40 hover:text-silver-bright sm:flex"
            >
                <ChevronLeft class="h-4 w-4" />
            </button>
            <button
                type="button"
                aria-label="Siguiente testimonio"
                @click="next"
                class="absolute right-0 top-1/2 hidden -translate-y-1/2 translate-x-1/2 items-center justify-center rounded-full border border-smoke bg-graphite/90 p-2.5 text-cream backdrop-blur-xl transition hover:border-silver/40 hover:text-silver-bright sm:flex"
            >
                <ChevronRight class="h-4 w-4" />
            </button>

            <div class="mt-6 flex items-center justify-center gap-2">
                <button
                    v-for="(t, index) in testimonials"
                    :key="t.id"
                    type="button"
                    :aria-label="`Ir al testimonio ${index + 1}`"
                    @click="go(index)"
                    class="h-2 rounded-full transition-all duration-300"
                    :class="index === active ? 'w-6 bg-silver-bright' : 'w-2 bg-smoke hover:bg-silver/40'"
                />
            </div>
        </template>
    </div>
</template>
