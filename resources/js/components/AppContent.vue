<script setup lang="ts">
import { computed } from 'vue';
import { SidebarInset } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className" class="relative">
        <!-- Decorative background gradients -->
        <div class="pointer-events-none fixed inset-0 z-0 opacity-40">
            <div class="absolute left-1/4 top-1/4 h-96 w-96 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
            <div class="absolute right-1/4 bottom-1/4 h-96 w-96 rounded-full bg-gradient-to-tl from-purple-500/3 to-transparent blur-3xl"></div>
        </div>
        <div class="relative z-10">
            <slot />
        </div>
    </SidebarInset>
    <main
        v-else
        class="relative mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <!-- Decorative background gradients -->
        <div class="pointer-events-none fixed inset-0 z-0 opacity-40">
            <div class="absolute left-1/4 top-1/4 h-96 w-96 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
            <div class="absolute right-1/4 bottom-1/4 h-96 w-96 rounded-full bg-gradient-to-tl from-purple-500/3 to-transparent blur-3xl"></div>
        </div>
        <div class="relative z-10">
            <slot />
        </div>
    </main>
</template>
