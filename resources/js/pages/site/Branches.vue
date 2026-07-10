<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import AppLogoPublic from '@/components/AppLogoPublic.vue';
import { MapPin, Phone, ChevronRight } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    groupedBranches: Record<string, any[]>;
}>();
</script>

<template>
    <Head title="Sucursales" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/5 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Encuentra tu salón</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                Nuestras <span class="italic text-glitter">sucursales</span>
            </h1>
            <p class="mt-4 text-base text-mercury">
                Presencia en las principales ciudades del país
            </p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-for="(branches, city) in groupedBranches" :key="city" class="mb-14">
                <div class="mb-8 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full border border-silver/30 bg-silver/10 text-silver-bright">
                        <MapPin class="h-5 w-5" />
                    </div>
                    <h2 class="font-serif text-3xl font-medium text-cream">{{ city }}</h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="branch in branches"
                        :key="branch.id"
                        :href="`/sucursales/${branch.slug}`"
                        class="card-elegant card-elegant-hover group overflow-hidden transition hover:-translate-y-1"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-graphite to-ink">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(212,175,55,0.15),transparent_70%)]"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-7xl opacity-30 transition group-hover:opacity-60 group-hover:scale-110">
                                💇‍♀️
                            </div>
                            <span class="absolute right-3 top-3 chip border border-silver/30 bg-ink/80 text-xs text-silver-bright">
                                {{ branch.city?.name }}
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                                {{ branch.name }}
                            </h3>
                            <div class="mt-3 space-y-2 text-sm text-pearl">
                                <div class="flex items-start gap-2">
                                    <MapPin class="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold/70" />
                                    <span>{{ branch.address }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Phone class="h-3.5 w-3.5 shrink-0 text-gold/70" />
                                    <span>{{ branch.phone }}</span>
                                </div>
                            </div>
                            <div v-if="branch.whatsapp" class="mt-4 border-t border-smoke pt-3">
                                <span class="chip bg-emerald-500/15 text-xs text-emerald-400">
                                    📱 WhatsApp disponible
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>