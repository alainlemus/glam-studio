<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';
import { MapPin, Phone, Mail, Clock } from '@lucide/vue';
import BranchMap from '@/components/site/BranchMap.vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    branches: any[];
    seo?: { title: string; description: string };
}>();
</script>

<template>
    <Head :title="seo?.title ?? 'Contacto'" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Estamos para atenderte</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                <span class="italic text-glitter">Contacto</span>
            </h1>
            <p class="mt-4 text-base text-mercury">
                Encuentra la sucursal más cercana o escríbenos directamente
            </p>
        </div>
    </section>

    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(branch, index) in branches"
                    :key="branch.id"
                    class="card-elegant p-6"
                    v-reveal="index * 80"
                >
                    <h3 class="font-serif text-xl font-semibold text-cream">{{ branch.name }}</h3>
                    <p class="mt-1 text-xs text-silver-bright">{{ branch.city?.name }}</p>

                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex items-start gap-2">
                            <MapPin class="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold/70" />
                            <span class="text-pearl">{{ branch.address }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Phone class="h-3.5 w-3.5 shrink-0 text-gold/70" />
                            <a :href="`tel:${branch.phone}`" class="text-pearl transition hover:text-silver-bright">{{ branch.phone }}</a>
                        </div>
                        <div v-if="branch.email" class="flex items-center gap-2">
                            <Mail class="h-3.5 w-3.5 shrink-0 text-gold/70" />
                            <a :href="`mailto:${branch.email}`" class="text-pearl transition hover:text-silver-bright">{{ branch.email }}</a>
                        </div>
                        <div v-if="branch.whatsapp" class="flex items-center gap-2">
                            <span class="text-sm">📱</span>
                            <a
                                :href="`https://wa.me/${branch.whatsapp.replace(/\D/g, '')}`"
                                target="_blank"
                                class="text-sm text-emerald-400 transition hover:text-emerald-300"
                            >
                                WhatsApp directo
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            <Clock class="h-3.5 w-3.5 shrink-0 text-gold/70" />
                            <span class="text-pearl">
                                {{ branch.opening_time?.slice(0,5) }} - {{ branch.closing_time?.slice(0,5) }}
                            </span>
                        </div>
                    </div>

                    <BranchMap :branch="branch" class="mt-5" />
                </div>
            </div>
        </div>
    </section>
</template>