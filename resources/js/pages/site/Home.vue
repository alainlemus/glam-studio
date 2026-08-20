<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import AppLogoPublic from '@/components/AppLogoPublic.vue';
import {
    ChevronRight,
    Sparkles,
    MapPin,
    Phone,
    Calendar,
    ArrowRight,
    Crown,
} from '@lucide/vue';

defineOptions({ layout: SiteLayout });

const props = defineProps<{
    branches: any[];
    serviceCategories: any[];
    featuredServices: any[];
    campaigns: any[];
    cities: any[];
}>();

const stats = [
    { value: `${props.branches.length}+`, label: 'Sucursales' },
    { value: '12+', label: 'Estilistas' },
    { value: '1000+', label: 'Clientas felices' },
    { value: '5★', label: 'Calidad' },
];

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};

// Fotos para cada categoría de servicio
const categoryImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600&q=80',
    2: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=600&q=80',
    3: 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=600&q=80',
    4: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=600&q=80',
};

// Fotos únicas para cada sucursal (mismo mapeo que Branches.vue)
const branchImages: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=800&q=80',
    2: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&q=80',
    3: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80',
    4: 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=800&q=80',
    5: 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=800&q=80',
    6: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=800&q=80',
};
</script>

<template>
    <Head title="Glam Studio · Belleza y Estilo" />

    <!-- HERO MODERNO CON IMAGEN DE FONDO -->
    <section class="relative overflow-hidden bg-ink pt-12 pb-20 lg:pt-16 lg:pb-32">
        <!-- Background Image con Parallax -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1920&q=80"
                alt="Salon hero"
                class="h-full w-full object-cover opacity-25"
            />
            <!-- Gradient overlays -->
            <div class="absolute inset-0 bg-gradient-to-b from-ink via-ink/95 to-ink"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-ink via-transparent to-ink"></div>
        </div>

        <!-- Mesh Gradients animados -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/3 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/10 blur-[120px] animate-pulse"></div>
            <div class="absolute left-1/4 top-1/4 h-[400px] w-[400px] rounded-full animate-sparkle" style="background: var(--color-spa-lavender); opacity: 0.08; filter: blur(100px);"></div>
            <div class="absolute right-1/4 bottom-1/3 h-[450px] w-[450px] rounded-full animate-sparkle animation-delay-300" style="background: var(--color-silver-bright); opacity: 0.1; filter: blur(110px);"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <!-- Texto -->
                <div class="text-center lg:text-left animate-blur-fade-in">
                    <div class="mb-6 inline-flex items-center gap-2 glass-effect rounded-full px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                        <Sparkles class="h-3 w-3" />
                        Agenda en línea · WhatsApp
                    </div>

                    <h1 class="font-serif text-5xl font-bold leading-[1.1] tracking-tight lg:text-7xl animate-blur-fade-in animation-delay-100">
                        Tu belleza,
                        <br />
                        <span class="relative inline-block italic" style="color: var(--color-silver-bright)">
                            nuestra pasión
                            <svg class="absolute -bottom-2 left-0 w-full animate-fade-in animation-delay-300" height="12" viewBox="0 0 400 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10C100 4 300 4 398 10" stroke="url(#gradient-home)" stroke-width="3" stroke-linecap="round"/>
                                <defs>
                                    <linearGradient id="gradient-home" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:var(--color-silver-bright);stop-opacity:0.5" />
                                        <stop offset="50%" style="stop-color:var(--color-gold);stop-opacity:0.9" />
                                        <stop offset="100%" style="stop-color:var(--color-silver-bright);stop-opacity:0.5" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-pearl animate-blur-fade-in animation-delay-200">
                        Agenda tu cita en cualquiera de nuestras sucursales. Estilistas profesionales,
                        productos premium y la mejor atención personalizada.
                    </p>

                    <div class="mt-10 flex flex-wrap justify-center gap-3 lg:justify-start animate-blur-fade-in animation-delay-300">
                        <Link href="/agendar" class="btn-primary-elegant h-14 px-8 text-base shadow-gold-lg">
                            <Calendar class="h-4 w-4" />
                            Reservar cita
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link href="/servicios" class="btn-accent-elegant h-14 px-8 text-base">
                            Ver servicios
                        </Link>
                    </div>

                    <!-- Social Proof Stats - Glass cards -->
                    <div class="mt-12 grid grid-cols-4 gap-4 animate-blur-fade-in animation-delay-400">
                        <div v-for="stat in stats" :key="stat.label" class="glass-effect rounded-xl p-4 text-center lg:text-left transition-all duration-300 hover:scale-105">
                            <div class="font-serif text-2xl font-bold lg:text-3xl" style="color: var(--color-spa-lavender)">{{ stat.value }}</div>
                            <div class="mt-1 text-xs uppercase tracking-wider text-mercury">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image con efecto -->
                <div class="relative flex items-center justify-center animate-blur-fade-in animation-delay-200">
                    <!-- Imagen principal -->
                    <div class="relative h-[500px] w-full overflow-hidden rounded-3xl lg:h-[600px]">
                        <img
                            src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=800&q=80"
                            alt="Salon transformation"
                            class="h-full w-full object-cover transition-transform duration-700 hover:scale-105"
                        />
                        <!-- Overlay gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>

                        <!-- Badge flotante -->
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="glass-card p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2">
                                        <img src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=100&q=80" class="h-10 w-10 rounded-full object-cover ring-2 ring-ink" alt="Stylist 1" />
                                        <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=100&q=80" class="h-10 w-10 rounded-full object-cover ring-2 ring-ink" alt="Stylist 2" />
                                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" class="h-10 w-10 rounded-full object-cover ring-2 ring-ink" alt="Stylist 3" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-cream">+12 Estilistas Certificados</p>
                                        <p class="text-xs text-mercury">Expertos en tendencias 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative elements -->
                    <Sparkles class="absolute -top-4 right-12 h-8 w-8 animate-sparkle" style="color: var(--color-gold)" />
                    <Sparkles class="absolute bottom-12 -left-4 h-6 w-6 animate-sparkle animation-delay-500" style="color: var(--color-spa-lavender)" />
                </div>
            </div>
        </div>
    </section>

    <!-- GALERÍA DE TRANSFORMACIONES -->
    <section class="relative border-b border-smoke bg-charcoal py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center animate-fade-in">
                <p class="text-eyebrow">Transformaciones reales</p>
                <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                    Antes y <span class="italic" style="color: var(--color-silver-bright)">después</span>
                </h2>
            </div>

            <!-- Grid de transformaciones -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:gap-6">
                <!-- Transformación 1 -->
                <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] animate-fade-in">
                    <img
                        src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600&q=80"
                        alt="Transformation 1"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-spa-lavender)">Balayage</p>
                            <p class="mt-1 text-sm text-cream">Platinado perfecto</p>
                        </div>
                    </div>
                </div>

                <!-- Transformación 2 -->
                <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] animate-fade-in animation-delay-100">
                    <img
                        src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=600&q=80"
                        alt="Transformation 2"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-gold)">Peinado</p>
                            <p class="mt-1 text-sm text-cream">Recogido de novia</p>
                        </div>
                    </div>
                </div>

                <!-- Transformación 3 -->
                <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] animate-fade-in animation-delay-200">
                    <img
                        src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=600&q=80"
                        alt="Transformation 3"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-spa-lavender)">Keratina</p>
                            <p class="mt-1 text-sm text-cream">Alisado brasileño</p>
                        </div>
                    </div>
                </div>

                <!-- Transformación 4 -->
                <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] animate-fade-in animation-delay-300">
                    <img
                        src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=600&q=80"
                        alt="Transformation 4"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-silver-bright)">Corte</p>
                            <p class="mt-1 text-sm text-cream">Bob asimétrico</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS CON IMÁGENES -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-eyebrow">Lo que ofrecemos</p>
                <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                    Servicios <span class="italic" style="color: var(--color-spa-lavender)">premium</span>
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-mercury">
                    Una experiencia completa de belleza y bienestar, diseñada para consentirte
                </p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="(category, index) in serviceCategories.slice(0, 8)"
                    :key="category.id"
                    href="/servicios"
                    class="group soft-ui relative overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 hover:border-silver/30 animate-fade-in"
                    :style="{ animationDelay: `${index * 100}ms` }"
                >
                    <!-- Imagen de fondo -->
                    <div class="relative h-48 overflow-hidden">
                        <img
                            :src="categoryImages[category.id] || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600&q=80'"
                            :alt="category.name"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/80 to-transparent"></div>

                        <!-- Icono flotante -->
                        <div class="absolute left-4 top-4 flex h-12 w-12 items-center justify-center rounded-xl glass-effect text-2xl">
                            {{ category.icon }}
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-6">
                        <h3 class="font-serif text-xl font-semibold text-cream transition group-hover:text-silver-bright">
                            {{ category.name }}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-mercury">{{ category.description }}</p>

                        <div class="mt-5 flex items-center justify-between border-t border-smoke pt-4 text-xs">
                            <span class="text-mercury">{{ category.services?.length || 0 }} servicios</span>
                            <ChevronRight class="h-4 w-4 transition group-hover:translate-x-1" style="color: var(--color-gold)" />
                        </div>
                    </div>
                </Link>
            </div>

            <div class="mt-12 text-center">
                <Link href="/servicios" class="btn-accent-elegant h-12 px-8">
                    Ver todos los servicios
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- SUCURSALES CON IMÁGENES -->
    <section class="bg-gradient-onyx py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-end justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-eyebrow">Encuentra tu salón</p>
                    <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                        Nuestras <span class="italic" style="color: var(--color-silver-bright)">sucursales</span>
                    </h2>
                </div>
                <Link href="/sucursales" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">
                    Ver todas →
                </Link>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="(branch, index) in branches.slice(0, 6)"
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
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-transparent"></div>

                        <span class="absolute right-3 top-3 chip glass-effect text-silver-bright text-xs">
                            {{ branch.city?.name }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="p-5">
                        <h3 class="font-serif text-xl font-semibold text-cream transition group-hover:text-silver-bright">
                            {{ branch.name }}
                        </h3>
                        <div class="mt-3 space-y-1.5 text-xs text-mercury">
                            <div class="flex items-center gap-2">
                                <MapPin class="h-3 w-3 shrink-0" style="color: var(--color-spa-lavender)" />
                                <span>{{ branch.address }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Phone class="h-3 w-3 shrink-0" style="color: var(--color-gold)" />
                                <span>{{ branch.phone }}</span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <!-- PROMOCIONES -->
    <section v-if="campaigns.length" class="relative overflow-hidden bg-gradient-dark py-20 lg:py-28">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background: var(--color-spa-lavender); opacity: 0.05;"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-eyebrow">Ofertas especiales</p>
                <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                    <span class="italic" style="color: var(--color-gold)">Promociones</span> vigentes
                </h2>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-3">
                <div
                    v-for="campaign in campaigns"
                    :key="campaign.id"
                    class="glass-card relative overflow-hidden p-8 transition-all duration-300 hover:scale-105"
                >
                    <div class="absolute right-0 top-0 h-32 w-32 -translate-y-1/2 translate-x-1/2 rounded-full blur-2xl" style="background: var(--color-spa-lavender); opacity: 0.1;"></div>
                    <span class="chip glass-effect text-silver-bright text-xs uppercase tracking-widest">
                        {{ campaign.type }}
                    </span>
                    <h3 class="mt-4 font-serif text-2xl font-semibold text-cream">{{ campaign.name }}</h3>
                    <p class="mt-3 text-sm text-mercury">{{ campaign.description }}</p>
                    <div v-if="campaign.discount_percentage" class="mt-6">
                        <div class="font-serif text-5xl font-bold" style="color: var(--color-gold)">
                            -{{ campaign.discount_percentage }}%
                        </div>
                    </div>
                    <div class="mt-6 border-t border-smoke pt-4 text-xs text-mercury">
                        Válido del {{ campaign.start_date }} al {{ campaign.end_date || 'sin fecha' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl glass-card p-10 text-center shadow-gold-lg lg:p-16">
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full blur-3xl" style="background: var(--color-spa-lavender); opacity: 0.1;"></div>
                <div class="pointer-events-none absolute -bottom-20 -left-20 h-64 w-64 rounded-full blur-3xl" style="background: var(--color-gold); opacity: 0.12;"></div>

                <Crown class="mx-auto h-10 w-10 text-silver-bright" />

                <h2 class="mt-6 font-serif text-4xl font-medium leading-tight tracking-tight lg:text-5xl">
                    ¿Lista para renovar <br />
                    tu <span class="italic" style="color: var(--color-silver-bright)">look</span>?
                </h2>

                <p class="mx-auto mt-4 max-w-md text-mercury">
                    Agenda tu cita en menos de 1 minuto. Confirmación inmediata por WhatsApp.
                </p>

                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <Link href="/agendar" class="btn-primary-elegant h-14 px-8 text-base shadow-gold-lg">
                        <Calendar class="h-4 w-4" />
                        Reservar en línea
                    </Link>
                    <a
                        v-if="branches[0]?.whatsapp"
                        :href="`https://wa.me/${branches[0].whatsapp.replace(/\D/g, '')}`"
                        target="_blank"
                        class="btn-accent-elegant h-14 px-8 text-base"
                    >
                        WhatsApp directo
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
