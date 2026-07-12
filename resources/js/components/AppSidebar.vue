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
        title: 'Productos',
        href: '/admin/products',
        icon: Package,
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
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border bg-sidebar-background">
        <SidebarHeader class="border-b border-sidebar-border/50 px-4 py-5">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="hover:bg-transparent">
                        <Link href="/admin" class="flex items-center gap-3 p-1 transition-transform hover:scale-[1.02]">
                            <AppLogo variant="sidebar" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-gradient-onyx px-3 py-4">
            <NavMain :items="operacionNavItems" label="Operación" />
            <NavMain :items="finanzasNavItems" label="Finanzas" />
            <NavMain :items="catalogoNavItems" label="Catálogo" />
            <NavMain :items="clientesNavItems" label="Crecimiento" />
        </SidebarContent>

        <SidebarFooter class="border-t border-sidebar-border/50 p-3">
            <div class="mb-2 rounded-lg border border-sidebar-border bg-sidebar-accent p-3">
                <Link href="/" class="flex items-center gap-2 text-xs text-sidebar-foreground/70 transition hover:text-silver-bright">
                    <Globe class="size-3.5" />
                    Ver sitio público
                </Link>
            </div>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>