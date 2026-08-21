<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-ink p-6 md:p-10">
        <!-- Mesh gradients decorativos -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/3 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/10 blur-[120px]"></div>
            <div class="absolute left-1/4 top-1/4 h-[400px] w-[400px] rounded-full" style="background: var(--color-spa-lavender); opacity: 0.08; filter: blur(100px);"></div>
            <div class="absolute right-1/4 bottom-1/3 h-[450px] w-[450px] rounded-full" style="background: var(--color-gold); opacity: 0.08; filter: blur(110px);"></div>
        </div>

        <div class="relative w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        href="/"
                        class="flex flex-col items-center gap-3 font-medium transition-transform hover:scale-[1.03]"
                    >
                        <div class="flex h-28 w-28 items-center justify-center">
                            <img
                                src="/images/logo.png"
                                alt="Glam Studio"
                                class="h-full w-full object-contain filter-glitter animate-fade-in"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-1.5 text-center">
                        <h1 class="font-serif text-2xl font-medium tracking-tight text-cream">{{ title }}</h1>
                        <p class="text-center text-sm text-mercury">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <div class="glass-card rounded-2xl p-6 shadow-gold-lg sm:p-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
