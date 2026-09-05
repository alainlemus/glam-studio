<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { ShoppingBag, Sparkles, MapPin, Package } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    categories: any[];
    seo?: { title: string; description: string };
}>();

const activeCategory = ref<number | null>(null);

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};

// Imágenes para cada categoría de producto
const categoryImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1200&q=80',
    2: 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=1200&q=80',
    3: 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=1200&q=80',
    4: 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=1200&q=80',
};

// Imágenes para productos individuales
const productImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&q=80',
    2: 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=400&q=80',
    3: 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=400&q=80',
};
</script>

<template>
    <Head :title="seo?.title ?? 'Productos'" />

    <!-- HERO CON IMAGEN DE FONDO -->
    <section class="relative overflow-hidden border-b border-smoke bg-ink py-24 lg:py-32">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1920&q=80"
                alt="Productos hero"
                class="h-full w-full object-cover opacity-20"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-ink via-ink/95 to-ink"></div>
        </div>

        <!-- Mesh gradients -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background: var(--color-gold); opacity: 0.12;"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-blur-fade-in">
            <div class="mb-6 inline-flex items-center gap-2 glass-effect rounded-full px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                <ShoppingBag class="h-3 w-3" />
                Tienda
            </div>

            <h1 class="mt-3 font-serif text-5xl font-bold tracking-tight text-cream lg:text-6xl">
                Nuestros <span class="italic" style="color: var(--color-silver-bright)">productos</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-pearl">
                Descubre productos profesionales de alta calidad disponibles en nuestras sucursales. Cuida tu cabello y piel con lo mejor.
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
                    {{ category.name }}
                </button>
            </div>
        </div>
    </div>

    <!-- PRODUCTOS POR CATEGORÍA -->
    <section class="py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-for="(category, catIndex) in categories" v-show="activeCategory === null || activeCategory === category.id" :key="category.id" class="mb-24 last:mb-0">
                <!-- Header de categoría con imagen -->
                <div class="mb-12 overflow-hidden rounded-3xl">
                    <div class="relative h-64 lg:h-80">
                        <img
                            :src="categoryImages[category.id] || 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1200&q=80'"
                            :alt="category.name"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/80 to-ink/40"></div>

                        <!-- Contenido sobre la imagen -->
                        <div class="absolute inset-0 flex items-end p-8 lg:p-12">
                            <div class="animate-blur-fade-in" :style="{ animationDelay: `${catIndex * 100}ms` }">
                                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl glass-card">
                                    <Package class="h-8 w-8" style="color: var(--color-gold)" />
                                </div>
                                <h2 class="font-serif text-4xl font-bold text-cream lg:text-5xl">{{ category.name }}</h2>
                                <p v-if="category.description" class="mt-2 max-w-2xl text-lg text-pearl">{{ category.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de productos -->
                <div v-if="category.products && category.products.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                        v-for="(product, index) in category.products"
                        :key="product.id"
                        class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 hover:border-silver/30"
                        v-reveal="index * 50"
                    >
                        <!-- Imagen del producto -->
                        <div class="relative h-56 overflow-hidden">
                            <img
                                :src="product.image || productImages[product.id] || categoryImages[category.id] || 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&q=80'"
                                :alt="product.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-transparent"></div>

                            <!-- Badge de precio -->
                            <div class="absolute top-3 right-3">
                                <div class="glass-card px-3 py-2">
                                    <span class="font-serif text-lg font-bold" style="color: var(--color-gold)">
                                        {{ formatPrice(product.price) }}
                                    </span>
                                </div>
                            </div>

                            <!-- SKU Badge -->
                            <div v-if="product.sku" class="absolute bottom-3 left-3">
                                <div class="glass-effect px-2.5 py-1 rounded-full">
                                    <span class="text-xs font-medium text-mercury">
                                        {{ product.sku }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                                        {{ product.name }}
                                    </h3>
                                    <p v-if="product.description" class="mt-2 text-sm leading-relaxed text-mercury line-clamp-2">
                                        {{ product.description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Meta info -->
                            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-mercury">
                                <span class="inline-flex items-center gap-1.5 glass-effect rounded-full px-3 py-1.5">
                                    <MapPin class="h-3 w-3" style="color: var(--color-spa-lavender)" />
                                    Disponible en sucursales
                                </span>
                            </div>

                            <!-- Botón de contacto -->
                            <Link
                                href="/contacto"
                                class="mt-5 flex items-center justify-center gap-2 rounded-xl border-2 border-transparent px-4 py-3 text-sm font-semibold text-cream transition-all hover:border-silver/30 hover:scale-105"
                                style="background: linear-gradient(135deg, var(--color-silver) 0%, var(--color-gold) 100%); opacity: 0.9;"
                            >
                                <ShoppingBag class="h-4 w-4" />
                                Consultar disponibilidad
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Mensaje si no hay productos -->
                <div v-else class="text-center py-12">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl glass-card mb-4">
                        <Package class="h-8 w-8 text-mercury" />
                    </div>
                    <p class="text-mercury">No hay productos disponibles en esta categoría</p>
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
                    ¿Buscas un producto específico?
                </h2>
                <p class="mx-auto mt-4 max-w-md text-lg text-mercury">
                    Visita nuestras sucursales o contáctanos para consultar disponibilidad y recibir asesoría personalizada
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/contacto" class="btn-primary-elegant h-14 px-8 text-base">
                        Contáctanos
                    </Link>
                    <Link href="/sucursales" class="btn-accent-elegant h-14 px-8 text-base">
                        <MapPin class="h-4 w-4" />
                        Ver sucursales
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
