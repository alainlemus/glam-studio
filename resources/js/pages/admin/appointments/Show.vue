<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    appointment: any;
}>();

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    no_show: 'bg-gray-100 text-gray-800',
};

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const confirm = () => router.post(`/admin/appointments/${props.appointment.id}/confirm`);
const complete = () => router.post(`/admin/appointments/${props.appointment.id}/complete`);
const noShow = () => {
    if (confirm('¿Marcar como no-show?')) router.post(`/admin/appointments/${props.appointment.id}/no-show`);
};
const cancel = () => {
    const reason = prompt('Motivo de cancelación:');
    if (reason !== null) {
        router.post(`/admin/appointments/${props.appointment.id}/cancel`, { reason });
    }
};
</script>

<template>
    <Head :title="`Cita ${appointment.code}`" />

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Link href="/admin/appointments" class="text-sm text-pink-600">← Volver</Link>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">Cita {{ appointment.code }}</h1>
            </div>
            <div class="flex gap-2">
                <button v-if="appointment.status === 'pending'" @click="confirm" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white">Confirmar</button>
                <button v-if="['confirmed', 'in_progress'].includes(appointment.status)" @click="complete" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white">Completar</button>
                <button v-if="appointment.status === 'confirmed'" @click="noShow" class="rounded-lg bg-gray-600 px-3 py-2 text-sm font-medium text-white">No-show</button>
                <button v-if="!['cancelled', 'completed', 'no_show'].includes(appointment.status)" @click="cancel" class="rounded-lg border border-red-300 px-3 py-2 text-sm font-medium text-red-700">Cancelar</button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Detalles</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Cliente:</dt>
                        <dd class="font-medium">{{ appointment.client?.name }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Teléfono:</dt>
                        <dd>
                            <a v-if="appointment.client?.phone" :href="`https://wa.me/${appointment.client.phone.replace(/\D/g, '')}`" target="_blank" class="font-medium text-green-600 hover:text-green-700">
                                {{ appointment.client.phone }} 📱
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Sucursal:</dt>
                        <dd class="font-medium">{{ appointment.branch?.name }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Estilista:</dt>
                        <dd class="font-medium">{{ appointment.stylist?.user?.name || 'Sin asignar' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Fecha:</dt>
                        <dd class="font-medium">{{ new Date(appointment.date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' }) }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Hora:</dt>
                        <dd class="font-medium">{{ appointment.start_time?.slice(0,5) }} - {{ appointment.end_time?.slice(0,5) }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Estatus:</dt>
                        <dd>
                            <span :class="['inline-flex rounded-full px-2 py-1 text-xs font-medium', statusColors[appointment.status]]">
                                {{ appointment.status }}
                            </span>
                        </dd>
                    </div>
                    <div v-if="appointment.notes" class="flex justify-between border-b border-gray-100 pb-2">
                        <dt class="text-gray-500">Notas:</dt>
                        <dd class="font-medium">{{ appointment.notes }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total:</dt>
                        <dd class="text-lg font-bold text-pink-600">{{ formatPrice(appointment.total) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Servicios</h2>
                <div class="mt-4 space-y-2">
                    <div v-for="service in appointment.services" :key="service.id" class="rounded-lg border border-gray-100 p-3">
                        <div class="font-medium text-gray-900">{{ service.service?.name }}</div>
                        <div class="mt-1 flex justify-between text-sm">
                            <span class="text-gray-500">{{ service.duration_minutes }} min</span>
                            <span class="font-medium">{{ formatPrice(service.price) }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 border-t pt-3 text-right">
                    <Link :href="`/admin/sales/create?appointment_id=${appointment.id}`" class="text-sm font-medium text-pink-600 hover:text-pink-700">
                        💰 Registrar venta →
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>