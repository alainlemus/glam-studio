<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Sparkles, Award, Users } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    stats: any;
    cities: any[];
}>();
</script>

<template>
    <Head title="Nosotros" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Nuestra historia</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                Sobre <span class="italic text-glitter">nosotros</span>
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-pearl">
                En <span class="font-serif italic text-silver-bright">Glam Studio</span> somos un equipo de profesionales
                apasionados por la belleza y el bienestar. Con presencia en las principales ciudades del país.
            </p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card-glow p-6 text-center">
                    <Sparkles class="mx-auto h-8 w-8 text-silver-bright" />
                    <div class="mt-4 font-serif text-4xl font-semibold text-glitter">{{ stats.branches }}+</div>
                    <div class="mt-1 text-xs uppercase tracking-wider text-mercury">Sucursales</div>
                </div>
                <div class="card-glow p-6 text-center">
                    <Users class="mx-auto h-8 w-8 text-silver-bright" />
                    <div class="mt-4 font-serif text-4xl font-semibold text-glitter">{{ stats.stylists }}+</div>
                    <div class="mt-1 text-xs uppercase tracking-wider text-mercury">Estilistas</div>
                </div>
                <div class="card-glow p-6 text-center">
                    <Award class="mx-auto h-8 w-8 text-silver-bright" />
                    <div class="mt-4 font-serif text-4xl font-semibold text-glitter">{{ stats.services }}+</div>
                    <div class="mt-1 text-xs uppercase tracking-wider text-mercury">Servicios</div>
                </div>
                <div class="card-glow p-6 text-center">
                    <Sparkles class="mx-auto h-8 w-8 text-silver-bright" />
                    <div class="mt-4 font-serif text-4xl font-semibold text-glitter">{{ stats.clients }}+</div>
                    <div class="mt-1 text-xs uppercase tracking-wider text-mercury">Clientes felices</div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-ink py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="text-eyebrow">Presencia</p>
                <h2 class="mt-3 font-serif text-3xl font-medium text-cream">
                    Ciudades donde <span class="italic text-glitter">estamos</span>
                </h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="city in cities" :key="city.id" class="card-elegant p-5 text-center transition hover:border-silver/30">
                    <div class="text-3xl">📍</div>
                    <div class="mt-3 font-serif text-lg font-semibold text-cream">{{ city.name }}</div>
                    <div class="mt-1 text-xs text-mercury">{{ city.branches_count }} sucursal(es)</div>
                </div>
            </div>
        </div>
    </section>
</template>