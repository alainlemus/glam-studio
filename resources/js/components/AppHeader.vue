<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Search, Bell, Plus } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const currentBreadcrumb = computed(() => {
    return props.breadcrumbs[props.breadcrumbs.length - 1]?.title || 'Panel';
});

const today = new Date().toLocaleDateString('es-MX', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
});
</script>

<template>
    <header class="sticky top-0 z-30 border-b border-border/60 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/80">
        <div class="flex h-16 items-center gap-3 px-4 lg:px-8">
            <!-- Mobile menu trigger -->
            <Sheet>
                <SheetTrigger :as-child="true">
                    <Button variant="ghost" size="icon" class="h-11 w-11 lg:hidden">
                        <Menu class="h-5 w-5" />
                    </Button>
                </SheetTrigger>
                <SheetContent side="left" class="w-72 p-0">
                    <SheetHeader class="border-b border-border p-4">
                        <SheetTitle class="sr-only">Menú</SheetTitle>
                        <AppLogo />
                    </SheetHeader>
                </SheetContent>
            </Sheet>

            <!-- Title & Breadcrumbs (tablet/desktop) -->
            <div class="flex flex-1 items-center gap-4">
                <div>
                    <h1 class="font-serif text-xl font-semibold tracking-tight text-foreground">
                        {{ currentBreadcrumb }}
                    </h1>
                    <p class="hidden text-xs capitalize text-muted-foreground sm:block">
                        {{ today }}
                    </p>
                </div>
                <div v-if="props.breadcrumbs.length > 1" class="hidden xl:block">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>
            </div>

            <!-- Search (tablet/desktop) -->
            <div class="relative hidden md:block">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="search"
                    placeholder="Buscar..."
                    class="h-11 w-56 rounded-full border border-border bg-card pl-10 pr-4 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 lg:w-72"
                />
            </div>

            <!-- Quick action -->
            <Button
                as-child
                class="hidden h-11 rounded-full bg-espresso px-5 text-sm font-medium text-cream hover:bg-espresso/90 sm:inline-flex"
            >
                <Link href="/admin/appointments/create">
                    <Plus class="mr-1 h-4 w-4" />
                    <span class="hidden lg:inline">Nueva cita</span>
                </Link>
            </Button>

            <!-- Notifications -->
            <Button variant="ghost" size="icon" class="relative h-11 w-11">
                <Bell class="h-5 w-5" />
                <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-accent"></span>
            </Button>

            <!-- User menu -->
            <DropdownMenu>
                <DropdownMenuTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        class="relative h-11 w-11 rounded-full p-0 focus-within:ring-2 focus-within:ring-accent"
                    >
                        <Avatar class="h-9 w-9 overflow-hidden rounded-full">
                            <AvatarImage v-if="auth.user?.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                            <AvatarFallback class="rounded-full bg-accent font-serif text-sm font-medium text-espresso">
                                {{ getInitials(auth.user?.name) }}
                            </AvatarFallback>
                        </Avatar>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent :user="auth.user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>