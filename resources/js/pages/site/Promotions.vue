<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { Sparkles, MapPin, ChevronRight, Crown } from '@lucide/vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    campaigns: any[];
    seo?: { title: string; description: string };
}>();
</script>

<template>
    <Head :title="seo?.title ?? 'Promociones'" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/5 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Ofertas especiales</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                <span class="italic text-glitter">Promociones</span> vigentes
            </h1>
            <p class="mt-4 text-base text-mercury">
                Aprovecha nuestras ofertas especiales del mes
            </p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-if="campaigns.length === 0" class="rounded-xl border border-smoke bg-graphite py-16 text-center">
                <Sparkles class="mx-auto h-12 w-12 text-gold/30" />
                <p class="mt-3 text-mercury">No hay promociones activas en este momento.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(campaign, index) in campaigns"
                    :key="campaign.id"
                    class="card-glow relative overflow-hidden p-8"
                    v-reveal="index * 100"
                >
                    <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-silver/10 blur-2xl"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <span class="chip border border-silver/30 bg-silver/10 text-[10px] uppercase tracking-widest text-silver-bright">
                                {{ campaign.type }}
                            </span>
                            <Crown class="h-5 w-5 text-silver-bright" />
                        </div>

                        <h3 class="mt-4 font-serif text-2xl font-semibold text-cream">{{ campaign.name }}</h3>
                        <p class="mt-3 text-sm text-mercury">{{ campaign.description }}</p>

                        <div v-if="campaign.discount_percentage" class="mt-8">
                            <div class="font-serif text-6xl font-semibold text-glitter">
                                -{{ campaign.discount_percentage }}%
                            </div>
                        </div>

                        <div class="mt-6 border-t border-smoke pt-4 text-xs text-mercury">
                            Válido del {{ campaign.start_date }} al {{ campaign.end_date || 'sin fecha' }}
                        </div>

                        <Link href="/agendar" class="btn-primary-elegant mt-6 h-11 w-full text-sm">
                            Aprovechar oferta
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>