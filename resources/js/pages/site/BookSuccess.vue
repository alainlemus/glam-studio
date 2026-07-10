<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps<{
    appointment: any;
    whatsappUrl: string;
}>();

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};
</script>

<template>
    <Head title="¡Cita reservada!" />

    <section class="py-16">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-5xl">
                ✅
            </div>
            <h1 class="mt-6 text-3xl font-bold text-gray-900">¡Cita reservada con éxito!</h1>
            <p class="mt-3 text-lg text-gray-600">Tu cita ha sido agendada. Te esperamos.</p>

            <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 text-left shadow-sm">
                <div class="text-center">
                    <span class="inline-block rounded-full bg-pink-100 px-3 py-1 text-xs font-semibold text-pink-700">
                        {{ appointment.code }}
                    </span>
                </div>

                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Cliente:</span>
                        <span class="font-medium text-gray-900">{{ appointment.client?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Sucursal:</span>
                        <span class="font-medium text-gray-900">{{ appointment.branch?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Servicio:</span>
                        <span class="font-medium text-gray-900">
                            {{ appointment.services?.[0]?.service?.name }}
                        </span>
                    </div>
                    <div v-if="appointment.stylist" class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Estilista:</span>
                        <span class="font-medium text-gray-900">{{ appointment.stylist.user?.name }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Fecha:</span>
                        <span class="font-medium text-gray-900">
                            {{ new Date(appointment.date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' }) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span class="text-gray-500">Hora:</span>
                        <span class="font-medium text-gray-900">
                            {{ appointment.start_time?.slice(0,5) }} - {{ appointment.end_time?.slice(0,5) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Total:</span>
                        <span class="text-lg font-bold text-pink-600">{{ formatPrice(appointment.total) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a
                    :href="whatsappUrl"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-green-500 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-green-600"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                    </svg>
                    Confirmar por WhatsApp
                </a>
                <Link
                    href="/"
                    class="rounded-full border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition hover:border-pink-300"
                >
                    Volver al inicio
                </Link>
            </div>

            <p class="mt-6 text-xs text-gray-500">
                * Recibirás un recordatorio por WhatsApp antes de tu cita. Después de 3 inasistencias tu cuenta será bloqueada.
            </p>
        </div>
    </section>
</template>