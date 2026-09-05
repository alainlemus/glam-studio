<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, MessageCircle, Home, Calendar, Clock, MapPin, User, Scissors } from '@lucide/vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    appointment: any;
    whatsappUrl: string;
    seo?: { title: string; description: string };
}>();

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};

const formatDate = (date: string) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' });
};
</script>

<template>
    <Head :title="seo?.title ?? '¡Cita reservada!'" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gold/5 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-fade-up">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full border-2 border-gold/30 bg-gradient-to-br from-gold/20 to-graphite shadow-gold-lg">
                    <CheckCircle2 class="h-12 w-12 text-gold" />
                </div>

                <p class="mt-6 text-eyebrow">Reservada con éxito</p>
                <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                    ¡Tu cita está <span class="italic text-glitter">confirmada</span>!
                </h1>
                <p class="mt-4 text-base text-mercury">
                    Te esperamos. Recibirás un recordatorio por WhatsApp antes de tu cita.
                </p>

                <div class="mx-auto mt-4 inline-block">
                    <span class="chip border border-gold/30 bg-gold/10 font-mono text-sm text-gold">
                        {{ appointment.code }}
                    </span>
                </div>
            </div>

            <div class="mt-10 overflow-hidden rounded-2xl border border-silver/20 bg-card shadow-warm-lg">
                <div class="border-b border-smoke bg-graphite p-4">
                    <p class="text-eyebrow">Detalles de tu cita</p>
                </div>

                <div class="divide-y divide-smoke p-6">
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <User class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Cliente</span>
                        </div>
                        <span class="font-medium text-cream">{{ appointment.client?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <MapPin class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Sucursal</span>
                        </div>
                        <span class="font-medium text-cream">{{ appointment.branch?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <Scissors class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Servicio</span>
                        </div>
                        <span class="font-medium text-cream">
                            {{ appointment.services?.[0]?.service?.name }}
                        </span>
                    </div>
                    <div v-if="appointment.stylist" class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <User class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Estilista</span>
                        </div>
                        <span class="font-medium text-cream">{{ appointment.stylist.user?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <Calendar class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Fecha</span>
                        </div>
                        <span class="font-medium capitalize text-cream">{{ formatDate(appointment.date) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 text-mercury">
                            <Clock class="h-4 w-4 text-gold/70" />
                            <span class="text-sm">Hora</span>
                        </div>
                        <span class="font-mono font-medium text-cream">
                            {{ appointment.start_time?.slice(0,5) }} - {{ appointment.end_time?.slice(0,5) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-smoke pt-4">
                        <span class="text-sm font-medium uppercase tracking-wider text-mercury">Total</span>
                        <span class="font-serif text-2xl font-semibold text-glitter">
                            {{ formatPrice(appointment.total) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a
                    :href="whatsappUrl"
                    target="_blank"
                    class="btn-gold-elegant h-12 px-6"
                >
                    <MessageCircle class="h-4 w-4" />
                    Confirmar por WhatsApp
                </a>
                <Link
                    href="/"
                    class="btn-ghost-elegant h-12 px-6"
                >
                    <Home class="h-4 w-4" />
                    Volver al inicio
                </Link>
            </div>

            <div class="mt-8 rounded-xl border border-smoke bg-graphite p-4 text-center">
                <p class="text-xs text-mercury">
                    <span class="text-gold">💡</span> Recibirás un recordatorio por WhatsApp antes de tu cita.
                    <br>
                    Después de 3 inasistencias tu cuenta será bloqueada automáticamente.
                </p>
            </div>
        </div>
    </section>
</template>