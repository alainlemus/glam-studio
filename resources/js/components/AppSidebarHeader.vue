<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="relative flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/30 bg-gradient-to-r from-sidebar-background/95 via-sidebar-background/98 to-sidebar-background/95 px-6 backdrop-blur-md transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <!-- Subtle gradient decoration -->
        <div class="pointer-events-none absolute inset-0 opacity-20">
            <div class="absolute right-0 top-0 h-full w-64 bg-gradient-to-l from-silver/5 to-transparent"></div>
        </div>

        <div class="relative flex flex-1 items-center gap-2">
            <SidebarTrigger class="-ml-1 transition-transform hover:scale-110" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="relative">
            <NotificationBell />
        </div>
    </header>
</template>
