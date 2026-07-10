<script setup lang="ts">
withDefaults(defineProps<{
    size?: 'sm' | 'md' | 'lg' | 'xl';
}>(), {
    size: 'md',
});

const sizeMap = {
    sm: { wrap: 'h-12', text: 'text-base', subtitle: 'text-[8px]', width: 'w-44' },
    md: { wrap: 'h-16', text: 'text-lg', subtitle: 'text-[9px]', width: 'w-56' },
    lg: { wrap: 'h-24', text: 'text-2xl', subtitle: 'text-[10px]', width: 'w-72' },
    xl: { wrap: 'h-32', text: 'text-4xl', subtitle: 'text-xs', width: 'w-96' },
};
</script>

<template>
    <!-- Logo AM Glam Studio - usando asset real con corona y AM -->
    <div class="flex items-center gap-4">
        <!-- Logo principal con corona -->
        <div :class="['relative shrink-0', sizeMap[size].wrap, sizeMap[size].wrap]">
            <img
                src="/images/logo.jpg"
                alt="AM Glam Studio"
                class="relative h-full w-full object-contain filter-glitter animate-fade-in"
            />
            <!-- Destellos -->
            <div class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-silver-bright animate-sparkle"></div>
            <div class="absolute -bottom-0.5 -left-0.5 h-1.5 w-1.5 rounded-full bg-silver-bright animate-sparkle" style="animation-delay: 0.5s"></div>
        </div>

        <!-- Texto al lado -->
        <div class="flex flex-col leading-none" :class="sizeMap[size].width">
            <span :class="['font-serif italic font-semibold tracking-wide', sizeMap[size].text, 'text-glitter']">
                Glam Studio
            </span>
            <span :class="['mt-1 uppercase tracking-[0.3em] text-silver/80 font-medium', sizeMap[size].subtitle]">
                Beauty & More
            </span>
        </div>
    </div>
</template>
