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
</script>

<template>
    <Head title="Glam Studio · Belleza y Estilo" />

    <!-- HERO OSCURO PREMIUM -->
    <section class="relative overflow-hidden bg-gradient-dark pt-12 pb-20 lg:pt-16 lg:pb-32">
        <!-- Destellos decorativos -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/4 top-1/4 h-96 w-96 rounded-full bg-silver/5 blur-3xl"></div>
            <div class="absolute right-1/4 bottom-1/4 h-96 w-96 rounded-full bg-silver-bright/5 blur-3xl"></div>
        </div>

        <!-- Líneas decorativas -->
        <div class="pointer-events-none absolute inset-x-0 top-1/2 h-px bg-gradient-to-r from-transparent via-gold/30 to-transparent"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <!-- Texto -->
                <div class="text-center lg:text-left animate-fade-up">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-silver/30 bg-silver/5 px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                        <Sparkles class="h-3 w-3" />
                        Agenda en línea · WhatsApp
                    </div>

                    <h1 class="font-serif text-5xl font-medium leading-[1.1] tracking-tight lg:text-7xl">
                        Tu belleza,
                        <br />
                        <span class="italic text-glitter">nuestra pasión</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-relaxed text-mercury lg:text-lg">
                        Agenda tu cita en cualquiera de nuestras sucursales. Estilistas profesionales,
                        productos premium y la mejor atención personalizada.
                    </p>

                    <div class="mt-10 flex flex-wrap justify-center gap-3 lg:justify-start">
                        <Link href="/agendar" class="btn-primary-elegant h-14 px-8 text-base shadow-gold-lg">
                            <Calendar class="h-4 w-4" />
                            Reservar cita
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link href="/servicios" class="btn-accent-elegant h-14 px-8 text-base">
                            Ver servicios
                        </Link>
                    </div>

                    <!-- Mini stats -->
                    <div class="mt-12 grid grid-cols-4 gap-4 border-t border-smoke pt-8">
                        <div v-for="stat in stats" :key="stat.label" class="text-center lg:text-left">
                            <div class="font-serif text-2xl font-semibold text-glitter lg:text-3xl">{{ stat.value }}</div>
                            <div class="mt-1 text-xs uppercase tracking-wider text-mercury">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>

                <!-- Logo/imagen central -->
                <div class="relative flex items-center justify-center animate-fade-in">
                    <div class="absolute h-72 w-72 rounded-full bg-silver/10 blur-3xl lg:h-96 lg:w-96"></div>
                    <div class="relative flex h-80 w-80 items-center justify-center lg:h-96 lg:w-96">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-silver-bright/20 via-transparent to-silver/20 animate-pulse"></div>
                        <div class="absolute inset-8 rounded-full border border-silver/20"></div>
                        <div class="absolute inset-16 rounded-full border border-silver/10"></div>
                        <div class="relative">
                            <AppLogoPublic size="xl" />
                        </div>
                        <!-- Estrellas decorativas -->
                        <Sparkles class="absolute -top-2 left-12 h-6 w-6 text-silver-bright animate-sparkle" />
                        <Sparkles class="absolute bottom-12 -right-2 h-5 w-5 text-silver-bright animate-sparkle" style="animation-delay: 0.5s" />
                        <Sparkles class="absolute top-1/3 right-4 h-4 w-4 text-silver animate-sparkle" style="animation-delay: 1s" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICIOS DESTACADOS -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-eyebrow">Lo que ofrecemos</p>
                <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                    Servicios <span class="italic text-glitter">premium</span>
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-mercury">
                    Una experiencia completa de belleza y bienestar, diseñada para consentirte
                </p>
            </div>

            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="category in serviceCategories.slice(0, 8)"
                    :key="category.id"
                    href="/servicios"
                    class="group card-elegant card-elegant-hover relative overflow-hidden p-7 transition hover:-translate-y-1"
                >
                    <!-- Decoración superior -->
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent opacity-0 transition group-hover:opacity-100"></div>

                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg border border-silver/20 bg-silver/5 text-2xl transition group-hover:scale-110">
                        {{ category.icon }}
                    </div>

                    <h3 class="font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                        {{ category.name }}
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-mercury">{{ category.description }}</p>

                    <div class="mt-5 flex items-center justify-between border-t border-smoke pt-4 text-xs">
                        <span class="text-mercury">{{ category.services?.length || 0 }} servicios</span>
                        <ChevronRight class="h-4 w-4 text-silver-bright transition group-hover:translate-x-1" />
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

    <!-- SUCURSALES DORADAS -->
    <section class="bg-gradient-onyx py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-end justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-eyebrow">Encuentra tu salón</p>
                    <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                        Nuestras <span class="italic text-glitter">sucursales</span>
                    </h2>
                </div>
                <Link href="/sucursales" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">
                    Ver todas →
                </Link>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="branch in branches.slice(0, 6)"
                    :key="branch.id"
                    :href="`/sucursales/${branch.slug}`"
                    class="group card-elegant card-elegant-hover overflow-hidden transition hover:-translate-y-1"
                >
                    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-graphite to-ink">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(212,175,55,0.15),transparent_70%)]"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-7xl opacity-30 transition group-hover:opacity-60 group-hover:scale-110">
                            💇‍♀️
                        </div>
                        <span class="absolute right-3 top-3 chip border border-silver/30 bg-ink/80 text-silver-bright text-[10px]">
                            {{ branch.city?.name }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                            {{ branch.name }}
                        </h3>
                        <div class="mt-3 space-y-1.5 text-xs text-mercury">
                            <div class="flex items-center gap-2">
                                <MapPin class="h-3 w-3 shrink-0 text-silver/60" />
                                <span>{{ branch.address }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Phone class="h-3 w-3 shrink-0 text-silver/60" />
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
            <div class="absolute left-1/2 top-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/5 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-eyebrow">Ofertas especiales</p>
                <h2 class="mt-3 font-serif text-4xl font-medium tracking-tight lg:text-5xl">
                    <span class="italic text-glitter">Promociones</span> vigentes
                </h2>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-3">
                <div
                    v-for="campaign in campaigns"
                    :key="campaign.id"
                    class="card-glow relative overflow-hidden p-8"
                >
                    <div class="absolute right-0 top-0 h-32 w-32 -translate-y-1/2 translate-x-1/2 rounded-full bg-silver/10 blur-2xl"></div>
                    <span class="chip border border-silver/30 bg-silver/10 text-silver-bright text-[10px] uppercase tracking-widest">
                        {{ campaign.type }}
                    </span>
                    <h3 class="mt-4 font-serif text-2xl font-semibold text-cream">{{ campaign.name }}</h3>
                    <p class="mt-3 text-sm text-mercury">{{ campaign.description }}</p>
                    <div v-if="campaign.discount_percentage" class="mt-6">
                        <div class="font-serif text-5xl font-semibold text-glitter">
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
            <div class="relative overflow-hidden rounded-2xl border border-silver/20 bg-gradient-onyx p-10 text-center shadow-gold-lg lg:p-16">
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-silver/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-silver/10 blur-3xl"></div>

                <Crown class="mx-auto h-10 w-10 text-silver-bright" />

                <h2 class="mt-6 font-serif text-4xl font-medium leading-tight tracking-tight lg:text-5xl">
                    ¿Lista para renovar <br />
                    tu <span class="italic text-glitter">look</span>?
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