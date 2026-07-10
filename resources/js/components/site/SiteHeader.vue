<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    variant?: 'transparent' | 'solid';
}>();

const page = usePage();
const site = computed(() => page.props.site as any);
const branches = computed(() => (site.value?.branches as any[]) || []);
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
    <header
        class="sticky top-0 z-50 w-full border-b backdrop-blur"
        :class="props.variant === 'solid' ? 'bg-white border-gray-200' : 'bg-white/90 border-gray-200/50'"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-2">
                    <Link href="/" class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-pink-500 to-purple-600 text-white font-bold">
                            SB
                        </div>
                        <span class="hidden text-lg font-semibold text-gray-900 sm:block">
                            {{ site?.name || 'Salones Belleza' }}
                        </span>
                    </Link>
                </div>

                <nav class="hidden md:flex md:items-center md:gap-6">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="text-sm font-medium text-gray-700 transition hover:text-pink-600"
                        :class="{ 'text-pink-600': page.url === item.href }"
                    >
                        {{ item.name }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Link
                        href="/agendar"
                        class="hidden rounded-full bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:shadow-md sm:inline-block"
                    >
                        Reservar cita
                    </Link>

                    <details class="relative md:hidden">
                        <summary class="cursor-pointer list-none rounded-md p-2 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 rounded-md border border-gray-200 bg-white shadow-lg">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </header>
</template>