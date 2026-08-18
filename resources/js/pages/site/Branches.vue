<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import AppLogoPublic from '@/components/AppLogoPublic.vue';
import { MapPin, Phone, Clock, Sparkles } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    groupedBranches: Record<string, any[]>;
}>();

// Fotos únicas para cada sucursal
const branchImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=800&q=80',
    2: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&q=80',
    3: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80',
    4: 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=800&q=80',
    5: 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=800&q=80',
    6: 'https://images.unsplash.com/photo-1595475038663-8d9f14b0eb7e?w=800&q=80',
};
</script>

<template>
    <Head title="Sucursales" />

    <!-- HERO CON IMAGEN -->
    <section class="relative overflow-hidden border-b border-smoke bg-ink py-24 lg:py-32">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=1920&q=80"
                alt="Sucursales hero"
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
                <MapPin class="h-3 w-3" />
                Presencia nacional
            </div>

            <h1 class="mt-3 font-serif text-5xl font-bold tracking-tight text-cream lg:text-6xl">
                Nuestras <span class="italic" style="color: var(--color-silver-bright)">sucursales</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-pearl">
                Encuentra el salón más cercano a ti. Cada ubicación cuenta con estilistas profesionales y las mejores instalaciones.
            </p>
        </div>
    </section>

    <!-- SUCURSALES POR CIUDAD -->
    <section class="py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-for="(branches, city) in groupedBranches" :key="city" class="mb-20 last:mb-0">
                <!-- Header de ciudad -->
                <div class="mb-10 flex items-center gap-4 animate-fade-in">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl glass-card">
                        <MapPin class="h-6 w-6" style="color: var(--color-spa-lavender)" />
                    </div>
                    <div>
                        <h2 class="font-serif text-4xl font-bold text-cream">{{ city }}</h2>
                        <p class="text-sm text-mercury">{{ branches.length }} sucursal{{ branches.length > 1 ? 'es' : '' }}</p>
                    </div>
                </div>

                <!-- Grid de sucursales -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="(branch, index) in branches"
                        :key="branch.id"
                        :href="`/sucursales/${branch.slug}`"
                        class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 hover:border-silver/30 animate-fade-in"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <!-- Imagen de la sucursal -->
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img
                                :src="branchImages[branch.id] || 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=800&q=80'"
                                :alt="`Sucursal ${branch.name}`"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-transparent"></div>

                            <!-- Badge de ciudad -->
                            <span class="absolute right-3 top-3 chip glass-effect text-silver-bright text-xs">
                                {{ branch.city?.name }}
                            </span>

                            <!-- Horario flotante -->
                            <div class="absolute bottom-3 left-3 right-3">
                                <div class="glass-card p-3 text-xs">
                                    <div class="flex items-center gap-2 text-silver-bright">
                                        <Clock class="h-3 w-3" />
                                        <span>Lun - Sáb: 9:00 - 20:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-5">
                            <h3 class="font-serif text-xl font-bold text-cream transition group-hover:text-silver-bright">
                                {{ branch.name }}
                            </h3>

                            <div class="mt-4 space-y-2.5 text-sm text-pearl">
                                <div class="flex items-start gap-2.5">
                                    <MapPin class="mt-0.5 h-4 w-4 shrink-0" style="color: var(--color-spa-lavender)" />
                                    <span>{{ branch.address }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <Phone class="h-4 w-4 shrink-0" style="color: var(--color-gold)" />
                                    <span>{{ branch.phone }}</span>
                                </div>
                            </div>

                            <!-- WhatsApp badge -->
                            <div v-if="branch.whatsapp" class="mt-4 border-t border-smoke pt-3">
                                <div class="inline-flex items-center gap-2 glass-effect rounded-full px-3 py-1.5 text-xs">
                                    <span class="text-emerald-400">📱</span>
                                    <span class="text-emerald-400 font-medium">WhatsApp disponible</span>
                                </div>
                            </div>

                            <!-- Botón Ver detalles -->
                            <div class="mt-4 rounded-xl border-2 border-transparent bg-gradient-to-r px-4 py-2.5 text-center text-sm font-semibold text-cream transition-all group-hover:border-silver/30 group-hover:scale-105" style="background: linear-gradient(135deg, var(--color-silver) 0%, var(--color-gold) 100%); opacity: 0.9;">
                                Ver detalles →
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- GALERÍA VISUAL -->
    <section class="relative overflow-hidden bg-gradient-dark py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <p class="text-eyebrow">Ambientes únicos</p>
                <h2 class="mt-3 font-serif text-4xl font-bold lg:text-5xl">
                    Visita nuestros <span class="italic" style="color: var(--color-silver-bright)">espacios</span>
                </h2>
            </div>

            <!-- Grid de fotos de ambientes -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-6">
                <div class="group relative aspect-[4/5] overflow-hidden rounded-2xl animate-fade-in">
                    <img
                        src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=600&q=80"
                        alt="Interior salon 1"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-sm font-semibold text-cream">Área de corte</p>
                        </div>
                    </div>
                </div>

                <div class="group relative aspect-[4/5] overflow-hidden rounded-2xl animate-fade-in animation-delay-100">
                    <img
                        src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600&q=80"
                        alt="Interior salon 2"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-sm font-semibold text-cream">Zona de color</p>
                        </div>
                    </div>
                </div>

                <div class="group relative aspect-[4/5] overflow-hidden rounded-2xl animate-fade-in animation-delay-200">
                    <img
                        src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=600&q=80"
                        alt="Interior salon 3"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-sm font-semibold text-cream">Recepción</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-card rounded-3xl p-10 lg:p-16">
                <Sparkles class="mx-auto h-12 w-12 animate-sparkle" style="color: var(--color-spa-lavender)" />
                <h2 class="mt-6 font-serif text-4xl font-bold lg:text-5xl">
                    ¿Lista para tu visita?
                </h2>
                <p class="mx-auto mt-4 max-w-md text-lg text-mercury">
                    Agenda tu cita en cualquiera de nuestras sucursales. ¡Te esperamos!
                </p>
                <div class="mt-8">
                    <Link href="/agendar" class="btn-primary-elegant h-14 px-8 text-base">
                        Reservar ahora
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
