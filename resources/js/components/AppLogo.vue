<script setup lang="ts">
withDefaults(defineProps<{
    variant?: 'sidebar' | 'compact';
}>(), {
    variant: 'sidebar',
});
</script>

<template>
    <!-- Logo AM Glam Studio - usando el asset real -->
    <div class="flex items-center gap-3" :class="variant === 'compact' ? 'gap-2' : ''">
        <!-- Imagen real del logo con corona y AM plateadas -->
        <div class="relative shrink-0" :class="variant === 'compact' ? 'h-12 w-12' : 'h-16 w-16'">
            <img
                src="/images/logo.jpg"
                alt="AM Glam Studio"
                class="relative h-full w-full object-contain"
            />
            <!-- Destellos decorativos plateados -->
            <div class="absolute -right-0.5 -top-0.5 h-1.5 w-1.5 rounded-full bg-silver-bright animate-sparkle"></div>
            <div class="absolute -bottom-0.5 -left-0.5 h-1 w-1 rounded-full bg-silver animate-sparkle" style="animation-delay: 0.5s"></div>
        </div>

        <!-- Texto "Glam Studio" -->
        <div v-if="variant === 'sidebar'" class="flex flex-col leading-none">
            <span class="font-serif text-base font-semibold italic tracking-wide text-glitter">
                Glam Studio
            </span>
            <span class="mt-0.5 text-[9px] uppercase tracking-[0.3em] text-silver/70 font-medium">
                Beauty & More
            </span>
        </div>
    </div>
</template>
