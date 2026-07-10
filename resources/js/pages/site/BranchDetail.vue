<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { MapPin, Phone, Clock, ChevronLeft, Scissors } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    branch: any;
    stylists: any[];
}>();
</script>

<template>
    <Head :title="branch.name" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <Link href="/sucursales" class="mb-6 inline-flex items-center gap-1 text-sm text-mercury transition hover:text-silver-bright">
                <ChevronLeft class="h-4 w-4" />
                Ver todas las sucursales
            </Link>

            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="animate-fade-up">
                    <p class="text-eyebrow">{{ branch.city?.name }}</p>
                    <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                        {{ branch.name }}
                    </h1>
                    <p class="mt-5 text-base leading-relaxed text-pearl">{{ branch.description }}</p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-3 rounded-lg border border-smoke bg-graphite p-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-silver/20 bg-silver/10">
                                <MapPin class="h-4 w-4 text-silver-bright" />
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wider text-mercury">Dirección</div>
                                <div class="text-sm text-cream">{{ branch.address }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-smoke bg-graphite p-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-silver/20 bg-silver/10">
                                <Phone class="h-4 w-4 text-silver-bright" />
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wider text-mercury">Teléfono</div>
                                <div class="text-sm text-cream">{{ branch.phone }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-smoke bg-graphite p-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-silver/20 bg-silver/10">
                                <Clock class="h-4 w-4 text-silver-bright" />
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wider text-mercury">Horario</div>
                                <div class="text-sm text-cream">
                                    {{ branch.opening_time?.slice(0,5) }} - {{ branch.closing_time?.slice(0,5) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link :href="`/agendar?branch=${branch.id}`" class="btn-primary-elegant h-14 px-7 text-base shadow-gold-lg">
                            Reservar en esta sucursal
                        </Link>
                        <a
                            v-if="branch.whatsapp"
                            :href="`https://wa.me/${branch.whatsapp.replace(/\D/g, '')}`"
                            target="_blank"
                            class="btn-accent-elegant h-14 px-7 text-base"
                        >
                            📱 WhatsApp
                        </a>
                    </div>
                </div>

                <div class="relative flex items-center justify-center">
                    <div class="absolute h-72 w-72 rounded-full bg-silver/10 blur-3xl lg:h-96 lg:w-96"></div>
                    <div class="relative aspect-square w-full max-w-md overflow-hidden rounded-2xl border border-silver/30 bg-gradient-to-br from-graphite to-ink shadow-gold-lg">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(212,175,55,0.2),transparent_70%)]"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <Scissors class="h-24 w-24 text-silver-bright opacity-30" />
                            <div class="mt-4 font-serif text-2xl text-glitter">{{ branch.name }}</div>
                            <div class="mt-2 text-sm text-mercury">{{ branch.city?.name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section v-if="stylists.length" class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="text-eyebrow">El equipo</p>
                <h2 class="mt-3 font-serif text-3xl font-medium text-cream lg:text-4xl">
                    Nuestros <span class="italic text-glitter">estilistas</span>
                </h2>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="stylist in stylists" :key="stylist.id" class="card-elegant card-elegant-hover p-6 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-silver/30 bg-gradient-to-br from-silver-bright/20 to-graphite text-3xl">
                        👩‍🎨
                    </div>
                    <h3 class="mt-4 font-serif text-lg font-semibold text-cream">{{ stylist.user?.name }}</h3>
                    <p v-if="stylist.specialty" class="mt-1 text-sm font-medium text-silver-bright">{{ stylist.specialty }}</p>
                    <p v-if="stylist.bio" class="mt-3 text-sm leading-relaxed text-mercury line-clamp-3">{{ stylist.bio }}</p>
                </div>
            </div>
        </div>
    </section>
</template>