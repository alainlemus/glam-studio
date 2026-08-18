<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Sparkles, Award, Users, Heart, Star } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    stats: any;
    cities: any[];
}>();
</script>

<template>
    <Head title="Nosotros" />

    <!-- HERO CON IMAGEN HERO -->
    <section class="relative overflow-hidden border-b border-smoke bg-ink py-24 lg:py-32">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=1920&q=80"
                alt="About hero"
                class="h-full w-full object-cover opacity-20"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-ink via-ink/95 to-ink"></div>
        </div>

        <!-- Mesh gradients -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl" style="background: var(--color-gold); opacity: 0.12;"></div>
        </div>

        <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center animate-blur-fade-in">
            <div class="mb-6 inline-flex items-center gap-2 glass-effect rounded-full px-4 py-1.5 text-xs font-medium tracking-wider text-silver-bright uppercase">
                <Heart class="h-3 w-3" />
                Nuestra historia
            </div>

            <h1 class="mt-3 font-serif text-5xl font-bold tracking-tight text-cream lg:text-6xl">
                Sobre <span class="italic" style="color: var(--color-silver-bright)">nosotros</span>
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-pearl">
                En <span class="font-serif italic text-silver-bright">Glam Studio</span> somos un equipo de profesionales
                apasionados por la belleza y el bienestar. Con presencia en las principales ciudades del país.
            </p>
        </div>
    </section>

    <!-- ESTADÍSTICAS CON GLASSMORPHISM -->
    <section class="py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105 animate-fade-in">
                    <Sparkles class="mx-auto h-10 w-10 animate-sparkle" style="color: var(--color-spa-lavender)" />
                    <div class="mt-4 font-serif text-5xl font-bold" style="color: var(--color-gold)">{{ stats.branches }}+</div>
                    <div class="mt-2 text-xs uppercase tracking-wider text-mercury">Sucursales</div>
                </div>
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105 animate-fade-in animation-delay-100">
                    <Users class="mx-auto h-10 w-10" style="color: var(--color-spa-lavender)" />
                    <div class="mt-4 font-serif text-5xl font-bold" style="color: var(--color-gold)">{{ stats.stylists }}+</div>
                    <div class="mt-2 text-xs uppercase tracking-wider text-mercury">Estilistas</div>
                </div>
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105 animate-fade-in animation-delay-200">
                    <Award class="mx-auto h-10 w-10" style="color: var(--color-spa-lavender)" />
                    <div class="mt-4 font-serif text-5xl font-bold" style="color: var(--color-gold)">{{ stats.services }}+</div>
                    <div class="mt-2 text-xs uppercase tracking-wider text-mercury">Servicios</div>
                </div>
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105 animate-fade-in animation-delay-300">
                    <Star class="mx-auto h-10 w-10" style="color: var(--color-spa-lavender)" />
                    <div class="mt-4 font-serif text-5xl font-bold" style="color: var(--color-gold)">{{ stats.clients }}+</div>
                    <div class="mt-2 text-xs uppercase tracking-wider text-mercury">Clientes felices</div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUESTRA HISTORIA CON IMÁGENES -->
    <section class="relative overflow-hidden bg-gradient-dark py-20 lg:py-28">
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/3 top-1/3 h-[500px] w-[500px] rounded-full blur-3xl" style="background: var(--color-spa-lavender); opacity: 0.08;"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <!-- Texto -->
                <div class="animate-blur-fade-in">
                    <p class="text-eyebrow">Nuestra misión</p>
                    <h2 class="mt-3 font-serif text-4xl font-bold lg:text-5xl">
                        Pasión por la <span class="italic" style="color: var(--color-silver-bright)">belleza</span>
                    </h2>
                    <div class="mt-6 space-y-4 text-lg leading-relaxed text-pearl">
                        <p>
                            Desde nuestros inicios, nos hemos dedicado a ofrecer servicios de belleza de la más alta calidad,
                            combinando técnicas profesionales con los productos más innovadores del mercado.
                        </p>
                        <p>
                            Nuestro equipo de estilistas certificados está en constante capacitación para traerte las últimas
                            tendencias y técnicas de la industria de la belleza.
                        </p>
                        <p class="font-serif italic text-silver-bright">
                            "Tu confianza es nuestro mayor logro"
                        </p>
                    </div>
                </div>

                <!-- Imagen -->
                <div class="relative animate-blur-fade-in animation-delay-200">
                    <div class="relative overflow-hidden rounded-3xl">
                        <img
                            src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&q=80"
                            alt="Nuestro equipo"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent"></div>

                        <!-- Badge flotante -->
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="glass-card p-4">
                                <div class="flex items-center gap-3">
                                    <Sparkles class="h-6 w-6 animate-sparkle" style="color: var(--color-gold)" />
                                    <div>
                                        <p class="text-sm font-bold text-cream">Calidad Certificada</p>
                                        <p class="text-xs text-mercury">Estilistas profesionales</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sparkles decorativos -->
                    <Sparkles class="absolute -top-4 -right-4 h-8 w-8 animate-sparkle" style="color: var(--color-spa-lavender)" />
                    <Sparkles class="absolute -bottom-4 -left-4 h-6 w-6 animate-sparkle animation-delay-500" style="color: var(--color-gold)" />
                </div>
            </div>
        </div>
    </section>

    <!-- GALERÍA DEL EQUIPO -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <p class="text-eyebrow">Conoce al equipo</p>
                <h2 class="mt-3 font-serif text-4xl font-bold lg:text-5xl">
                    Nuestros <span class="italic" style="color: var(--color-silver-bright)">profesionales</span>
                </h2>
            </div>

            <!-- Grid de estilistas -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img
                            src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=400&q=80"
                            alt="Stylist 1"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="font-serif text-lg font-bold text-cream">María González</p>
                            <p class="text-xs text-mercury">Especialista en Color</p>
                        </div>
                    </div>
                </div>

                <div class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in animation-delay-100">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img
                            src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=400&q=80"
                            alt="Stylist 2"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="font-serif text-lg font-bold text-cream">Ana Martínez</p>
                            <p class="text-xs text-mercury">Maestra en Corte</p>
                        </div>
                    </div>
                </div>

                <div class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in animation-delay-200">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img
                            src="https://images.unsplash.com/photo-1595475038663-8d9f14b0eb7e?w=400&q=80"
                            alt="Stylist 3"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="font-serif text-lg font-bold text-cream">Laura Pérez</p>
                            <p class="text-xs text-mercury">Experta en Peinados</p>
                        </div>
                    </div>
                </div>

                <div class="group soft-ui overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-2 animate-fade-in animation-delay-300">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img
                            src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=400&q=80"
                            alt="Stylist 4"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="font-serif text-lg font-bold text-cream">Sofía Torres</p>
                            <p class="text-xs text-mercury">Especialista en Novias</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CIUDADES -->
    <section class="bg-gradient-dark py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <p class="text-eyebrow">Presencia</p>
                <h2 class="mt-3 font-serif text-4xl font-bold lg:text-5xl">
                    Ciudades donde <span class="italic" style="color: var(--color-silver-bright)">estamos</span>
                </h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="(city, index) in cities" :key="city.id" class="glass-card p-6 text-center transition-all duration-300 hover:scale-105 animate-fade-in" :style="{ animationDelay: `${index * 100}ms` }">
                    <div class="text-4xl">📍</div>
                    <div class="mt-3 font-serif text-xl font-bold text-cream">{{ city.name }}</div>
                    <div class="mt-1 text-sm text-mercury">{{ city.branches_count }} sucursal{{ city.branches_count > 1 ? 'es' : '' }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="bg-ink py-20 lg:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-card rounded-3xl p-10 lg:p-16">
                <Heart class="mx-auto h-12 w-12 animate-pulse" style="color: var(--color-gold)" />
                <h2 class="mt-6 font-serif text-4xl font-bold lg:text-5xl">
                    Únete a nuestra familia
                </h2>
                <p class="mx-auto mt-4 max-w-md text-lg text-mercury">
                    Miles de clientas satisfechas confían en nosotros. ¡Sé parte de nuestra historia!
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/agendar" class="btn-primary-elegant h-14 px-8 text-base">
                        Agendar cita
                    </Link>
                    <Link href="/contacto" class="btn-accent-elegant h-14 px-8 text-base">
                        Contáctanos
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
