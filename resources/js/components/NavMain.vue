<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
    label?: string;
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="mb-3 px-2 py-0">
        <SidebarGroupLabel
            v-if="label"
            class="px-3 pb-2 pt-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-sidebar-foreground/60"
        >
            {{ label }}
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group h-11 rounded-xl text-sm transition-all duration-300 hover:shadow-md hover:shadow-silver/5 data-[active=true]:bg-gradient-to-r data-[active=true]:from-sidebar-accent data-[active=true]:to-sidebar-accent/80 data-[active=true]:text-sidebar-accent-foreground data-[active=true]:font-medium data-[active=true]:shadow-lg data-[active=true]:shadow-silver/10 data-[active=true]:border data-[active=true]:border-silver/20"
                >
                    <Link :href="item.href" class="flex items-center gap-3">
                        <component :is="item.icon" class="size-4 transition-transform duration-300 group-hover:scale-110" />
                        <span class="transition-colors">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>