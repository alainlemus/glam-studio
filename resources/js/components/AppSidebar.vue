<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Building2,
    Users,
    CalendarDays,
    Package,
    Scissors,
    ShoppingCart,
    DollarSign,
    TrendingUp,
    Megaphone,
    Award,
    Wallet,
    MapPin,
    Tags,
    Globe,
    LogOut,
    CalendarCheck,
    Receipt,
    FolderOpen,
    Layers,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const operacionNavItems: NavItem[] = [
    {
        title: 'Panel principal',
        href: '/admin',
        icon: LayoutGrid,
    },
    {
        title: 'Agenda',
        href: '/admin/appointments/calendar',
        icon: CalendarDays,
    },
    {
        title: 'Citas',
        href: '/admin/appointments',
        icon: CalendarCheck,
    },
    {
        title: 'Clientes',
        href: '/admin/clients',
        icon: Users,
    },
    {
        title: 'Estilistas',
        href: '/admin/stylists',
        icon: Scissors,
    },
];

const finanzasNavItems: NavItem[] = [
    {
        title: 'Ventas',
        href: '/admin/sales',
        icon: ShoppingCart,
    },
    {
        title: 'Comisiones',
        href: '/admin/commissions',
        icon: TrendingUp,
    },
    {
        title: 'Finanzas',
        href: '/admin/finance',
        icon: DollarSign,
    },
    {
        title: 'Egresos',
        href: '/admin/expenses',
        icon: Wallet,
    },
];

const catalogoNavItems: NavItem[] = [
    {
        title: 'Servicios',
        href: '/admin/services',
        icon: Tags,
    },
    {
        title: 'Categorías de servicios',
        href: '/admin/service-categories',
        icon: FolderOpen,
    },
    {
        title: 'Productos',
        href: '/admin/products',
        icon: Package,
    },
    {
        title: 'Categorías de productos',
        href: '/admin/product-categories',
        icon: Layers,
    },
    {
        title: 'Inventario',
        href: '/admin/inventory',
        icon: Receipt,
    },
    {
        title: 'Sucursales',
        href: '/admin/branches',
        icon: Building2,
    },
    {
        title: 'Ciudades',
        href: '/admin/cities',
        icon: MapPin,
    },
];

const clientesNavItems: NavItem[] = [
    {
        title: 'Marketing',
        href: '/admin/marketing',
        icon: Megaphone,
    },
    {
        title: 'Tarjeta de lealtad',
        href: '/admin/loyalty',
        icon: Award,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border bg-gradient-to-b from-ink via-graphite/95 to-ink backdrop-blur-xl">
        <!-- Decorative gradient overlay -->
        <div class="pointer-events-none absolute inset-0 opacity-30">
            <div class="absolute left-0 top-1/4 h-64 w-64 -translate-x-1/2 rounded-full blur-3xl" style="background: var(--color-spa-lavender); opacity: 0.08;"></div>
            <div class="absolute right-0 bottom-1/4 h-64 w-64 translate-x-1/2 rounded-full blur-3xl" style="background: var(--color-spa-pink); opacity: 0.06;"></div>
        </div>

        <SidebarHeader class="relative border-b border-sidebar-border/30 px-4 py-6 backdrop-blur-sm">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="hover:bg-transparent">
                        <Link href="/admin" class="flex items-center gap-3 p-1 transition-all duration-300 hover:scale-[1.03]">
                            <AppLogo variant="sidebar" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="relative px-3 py-4">
            <NavMain :items="operacionNavItems" label="Operación" />
            <NavMain :items="finanzasNavItems" label="Finanzas" />
            <NavMain :items="catalogoNavItems" label="Catálogo" />
            <NavMain :items="clientesNavItems" label="Crecimiento" />
        </SidebarContent>

        <SidebarFooter class="relative border-t border-sidebar-border/30 p-3 backdrop-blur-sm">
            <div class="mb-2 overflow-hidden rounded-xl border border-sidebar-border/50 bg-gradient-to-br from-sidebar-accent/80 to-sidebar-accent/40 p-3 backdrop-blur-sm transition-all duration-300 hover:border-silver/30 hover:shadow-lg hover:shadow-silver/5">
                <Link href="/" class="flex items-center gap-2 text-xs text-sidebar-foreground/70 transition-colors hover:text-silver-bright">
                    <Globe class="size-3.5" />
                    Ver sitio público
                </Link>
            </div>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>