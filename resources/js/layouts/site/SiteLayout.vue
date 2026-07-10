<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Menu,
    X,
    Calendar,
    ChevronDown,
} from '@lucide/vue';
import SiteFooter from '@/components/site/SiteFooter.vue';
import AppLogoPublic from '@/components/AppLogoPublic.vue';

const props = defineProps<{
    variant?: 'transparent' | 'solid';
}>();

const page = usePage();
const site = computed(() => page.props.site as any);
const branches = computed(() => (site.value?.branches as any[]) || []);
const mobileOpen = ref(false);

const navigation = [
    { name: 'Inicio', href: '/' },
    { name: 'Servicios', href: '/servicios' },
    { name: 'Sucursales', href: '/sucursales' },
    { name: 'Promociones', href: '/promociones' },
    { name: 'Nosotros', href: '/nosotros' },
    { name: 'Contacto', href: '/contacto' },
];
</script>

<template>
    <div class="min-h-screen bg-ink text-cream">
        <header class="sticky top-0 z-50 w-full border-b border-smoke/60 bg-ink/85 backdrop-blur-xl">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between">
                    <Link href="/" class="transition-transform hover:scale-[1.02]">
                        <AppLogoPublic size="md" />
                    </Link>

                    <nav class="hidden items-center gap-1 md:flex">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="rounded-md px-3 py-2 text-sm font-medium tracking-wide text-pearl transition hover:text-silver-bright"
                            :class="{ 'text-silver-bright': page.url === item.href }"
                        >
                            {{ item.name }}
                        </Link>
                    </nav>

                    <div class="flex items-center gap-3">
                        <Link href="/agendar" class="btn-primary-elegant hidden h-12 px-5 text-sm shadow-gold md:inline-flex">
                            <Calendar class="h-4 w-4" />
                            Reservar cita
                        </Link>

                        <button
                            @click="mobileOpen = !mobileOpen"
                            class="md:hidden flex h-11 w-11 items-center justify-center rounded-full border border-smoke bg-graphite text-cream"
                        >
                            <component :is="mobileOpen ? X : Menu" class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div
                    v-if="mobileOpen"
                    class="border-t border-smoke pb-4 pt-3 md:hidden animate-fade-in"
                >
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        @click="mobileOpen = false"
                        class="block rounded-md px-3 py-3 text-sm font-medium text-pearl hover:bg-graphite hover:text-silver-bright"
                    >
                        {{ item.name }}
                    </Link>
                    <Link href="/agendar" class="btn-primary-elegant mt-3 h-11 w-full justify-center">
                        <Calendar class="h-4 w-4" />
                        Reservar cita
                    </Link>
                </div>
            </div>
        </header>

        <main class="min-h-[calc(100vh-200px)]">
            <slot />
        </main>

        <SiteFooter />
    </div>
</template>