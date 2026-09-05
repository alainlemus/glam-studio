<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { ChevronRight, Clock, Sparkles } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    categories: any[];
    seo?: { title: string; description: string };
}>();

const activeCategory = ref<number | null>(null);

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};

// Fotos para cada categoría
const categoryImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200&q=80',
    2: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=1200&q=80',
    3: 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=1200&q=80',
    4: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=1200&q=80',
    5: 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=1200&q=80',
    6: 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=1200&q=80',
    7: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=1200&q=80',
    8: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=1200&q=80',
};

// Fotos individuales para servicios
const serviceImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&q=80',
    2: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80',
    3: 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=400&q=80',
};
</script>

<template>
    <Head :title="seo?.title ?? 'Servicios'" />

    <!-- HERO CON IMAGEN DE FONDO -->
    <section class="relative overflow-hidden border-b border-smoke bg-ink py-24 lg:py-32">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1920&q=80"
                alt="Servicios hero"
                class="h-full w-full object-cover opacity-20"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-ink via-ink/95 to-ink"></div>
        </div>

        <!-- Mesh gradients -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background: var(--color-spa-lavender); opacity: 0.1;"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-blur-fade-in">
            <div class="mb-6 inline-flex items-center gap-2 glass-effect rounded-full px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                <Sparkles class="h-3 w-3" />
                Catálogo completo
            </div>

            <h1 class="mt-3 font-serif text-5xl font-bold tracking-tight text-cream lg:text-6xl">
                Nuestros <span class="italic" style="color: var(--color-silver-bright)">servicios</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-pearl">
                Transforma tu look con nuestros tratamientos profesionales. Cada servicio está diseñado para realzar tu belleza natural.
            </p>
        </div>
    </section>

    <!-- FILTRO POR CATEGORÍA -->
    <div class="sticky top-20 z-30 border-b border-smoke bg-ink/90 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-center gap-2 lg:justify-start">
                <button
                    type="button"
                    @click="activeCategory = null"
                    class="chip border transition"
                    :class="activeCategory === null ? 'border-silver-bright bg-silver-bright text-ink' : 'border-smoke text-mercury hover:border-silver/40 hover:text-cream'"
                >
                    Todas
                </button>
                <button
                    v-for="category in categories"
                    :key="category.id"
                    type="button"
                    @click="activeCategory = category.id"
                    class="chip border transition"
                    :class="activeCategory === category.id ? 'border-silver-bright bg-silver-bright text-ink' : 'border-smoke text-mercury hover:border-silver/40 hover:text-cream'"
                >
                    {{ category.icon }} {{ category.name }}
                </button>
            </div>
        </div>
    </div>

    <!-- SERVICIOS POR CATEGORÍA -->
    <section class="py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-for="(category, catIndex) in categories" v-show="activeCategory === null || activeCategory === category.id" :key="category.id" class="mb-24 last:mb-0">
                <!-- Header de categoría con imagen -->
                <div class="mb-12 overflow-hidden rounded-3xl">
                    <div class="relative h-64 lg:h-80">
                        <img
                            :src="categoryImages[category.id] || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200&q=80'"
                            :alt="category.name"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/80 to-ink/40"></div>

                        <!-- Contenido sobre la imagen -->
                        <div class="absolute inset-0 flex items-end p-8 lg:p-12">
                            <div class="animate-blur-fade-in" :style="{ animationDelay: `${catIndex * 100}ms` }">
                                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl glass-card text-4xl">
                                    {{ category.icon }}
                                </div>
                                <h2 class="font-serif text-4xl font-bold text-cream lg:text-5xl">{{ category.name }}</h2>
                                <p class="mt-2 max-w-2xl text-lg text-pearl">{{ category.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de servicios -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(service, index) in category.services"
                        :key="service.id"
                        class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 hover:border-silver/30"
                        v-reveal="index * 50"
                    >
                        <!-- Imagen del servicio -->
                        <div class="relative h-48 overflow-hidden">
                            <img
                                :src="serviceImages[service.id] || categoryImages[category.id] || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&q=80'"
                                :alt="service.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-transparent"></div>

                            <!-- Badge de precio -->
                            <div class="absolute bottom-3 right-3">
                                <div class="glass-card px-3 py-1.5">
                                    <span class="font-serif text-sm font-bold" style="color: var(--color-gold)">
                                        {{ formatPrice(service.price) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                                        {{ service.name }}
                                    </h3>
                                    <p v-if="service.description" class="mt-2 text-sm leading-relaxed text-mercury line-clamp-2">
                                        {{ service.description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Meta info -->
                            <div class="mt-4 flex items-center gap-3 text-xs text-mercury">
                                <span class="inline-flex items-center gap-1 glass-effect rounded-full px-2.5 py-1">
                                    <Clock class="h-3 w-3" style="color: var(--color-spa-lavender)" />
                                    {{ service.duration_minutes }} min
                                </span>
                                <span class="chip glass-effect text-xs text-silver-bright">
                                    {{ service.commission_percentage }}% comisión
                                </span>
                            </div>

                            <!-- Botón de reserva -->
                            <Link
                                :href="`/agendar?service=${service.id}`"
                                class="mt-5 flex items-center justify-center gap-2 rounded-xl border-2 border-transparent bg-gradient-to-r from-silver/10 to-silver/5 px-4 py-3 text-sm font-semibold text-cream transition-all hover:border-silver/30 hover:scale-105"
                                style="background: linear-gradient(135deg, var(--color-silver) 0%, var(--color-gold) 100%); opacity: 0.9;"
                            >
                                Reservar ahora
                                <ChevronRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="relative overflow-hidden bg-gradient-dark py-20 lg:py-28">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background: var(--color-gold); opacity: 0.1;"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-card rounded-3xl p-10 lg:p-16">
                <Sparkles class="mx-auto h-12 w-12 animate-sparkle" style="color: var(--color-spa-lavender)" />
                <h2 class="mt-6 font-serif text-4xl font-bold lg:text-5xl">
                    ¿No encuentras lo que buscas?
                </h2>
                <p class="mx-auto mt-4 max-w-md text-lg text-mercury">
                    Contáctanos directamente y te ayudaremos a encontrar el servicio perfecto para ti
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/contacto" class="btn-primary-elegant h-14 px-8 text-base">
                        Contáctanos
                    </Link>
                    <Link href="/agendar" class="btn-accent-elegant h-14 px-8 text-base">
                        Agendar cita
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
