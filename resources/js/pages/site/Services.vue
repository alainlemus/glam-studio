<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { ChevronRight, Clock } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    categories: any[];
}>();

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};
</script>

<template>
    <Head title="Servicios" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/5 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Servicios</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                Nuestros <span class="italic text-glitter">servicios</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-base text-mercury">
                Una experiencia completa de belleza y bienestar
            </p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-for="category in categories" :key="category.id" class="mb-16">
                <div class="mb-8 flex items-center gap-4 border-b border-smoke pb-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl border border-silver/30 bg-silver/10 text-3xl">
                        {{ category.icon }}
                    </div>
                    <div>
                        <h2 class="font-serif text-2xl font-medium text-cream">{{ category.name }}</h2>
                        <p class="text-sm text-mercury">{{ category.description }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="service in category.services"
                        :key="service.id"
                        class="card-elegant card-elegant-hover p-5"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <h3 class="font-medium text-cream">{{ service.name }}</h3>
                                <p v-if="service.description" class="mt-1 text-xs text-mercury line-clamp-2">
                                    {{ service.description }}
                                </p>
                            </div>
                            <div class="font-serif text-lg font-semibold text-glitter whitespace-nowrap">
                                {{ formatPrice(service.price) }}
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-3 text-xs text-mercury">
                            <span class="inline-flex items-center gap-1">
                                <Clock class="h-3 w-3 text-silver-bright" />
                                {{ service.duration_minutes }} min
                            </span>
                            <span class="chip bg-silver/10 text-xs text-silver-bright">
                                {{ service.commission_percentage }}% comisión
                            </span>
                        </div>

                        <Link
                            :href="`/agendar?service=${service.id}`"
                            class="mt-4 block rounded-md border border-smoke bg-graphite px-3 py-2 text-center text-xs font-medium text-cream transition hover:border-silver/50 hover:bg-silver/10 hover:text-silver-bright"
                        >
                            Reservar →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>