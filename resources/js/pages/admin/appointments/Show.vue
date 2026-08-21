<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Calendar, User, Phone, MapPin, Scissors, Clock, CheckCircle2, XCircle, AlertTriangle, ChevronLeft, DollarSign, Sparkles } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    appointment: any;
}>();

const statusConfig: Record<string, { label: string; class: string; icon: any }> = {
    pending: { label: 'Pendiente', class: 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30', icon: Clock },
    confirmed: { label: 'Confirmada', class: 'bg-blue-500/15 text-blue-400 border-blue-500/30', icon: CheckCircle2 },
    in_progress: { label: 'En progreso', class: 'bg-purple-500/15 text-purple-400 border-purple-500/30', icon: Sparkles },
    completed: { label: 'Completada', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30', icon: CheckCircle2 },
    cancelled: { label: 'Cancelada', class: 'bg-red-500/15 text-red-400 border-red-500/30', icon: XCircle },
    no_show: { label: 'No asistió', class: 'bg-smoke/50 text-mercury border-smoke', icon: AlertTriangle },
};

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const confirm = () => router.post(`/admin/appointments/${props.appointment.id}/confirm`);
const complete = () => router.post(`/admin/appointments/${props.appointment.id}/complete`);
const noShow = async () => {
    if (await confirmDialog({
        title: '¿Marcar como no-show?',
        description: 'Se registrará que la clienta no se presentó a su cita.',
        confirmText: 'Marcar no-show',
    })) router.post(`/admin/appointments/${props.appointment.id}/no-show`);
};

const showCancelModal = ref(false);
const cancelReason = ref('');
const cancel = () => {
    cancelReason.value = '';
    showCancelModal.value = true;
};
const confirmCancel = () => {
    router.post(`/admin/appointments/${props.appointment.id}/cancel`, { reason: cancelReason.value }, {
        onSuccess: () => (showCancelModal.value = false),
    });
};
</script>

<template>
    <Head :title="`Cita ${appointment.code}`" />

    <div class="mx-auto max-w-7xl space-y-6 p-4 lg:p-8">
        <!-- Header con gradiente -->
        <div class="relative overflow-hidden rounded-2xl border border-smoke bg-gradient-to-br from-graphite via-ink to-graphite p-8 animate-fade-in">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-blue-500/10 to-transparent blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-gradient-to-tl from-purple-500/5 to-transparent blur-3xl"></div>

            <div class="relative">
                <Link href="/admin/appointments" class="inline-flex items-center gap-2 text-sm font-medium text-silver-bright transition-colors hover:text-cream">
                    <ChevronLeft class="h-4 w-4" />
                    Volver a citas
                </Link>
                <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-start gap-6">
                        <div class="group relative flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-500/10 shadow-lg shadow-blue-500/10 transition-all duration-300 hover:scale-105">
                            <Calendar class="h-10 w-10 text-blue-400" />
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </div>
                        <div class="flex-1">
                            <h1 class="font-serif text-3xl font-bold text-cream lg:text-4xl">Cita {{ appointment.code }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <component
                                    :is="statusConfig[appointment.status]?.icon"
                                    class="h-4 w-4"
                                />
                                <span :class="['inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm font-medium', statusConfig[appointment.status]?.class]">
                                    {{ statusConfig[appointment.status]?.label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-if="appointment.status === 'pending'"
                            @click="confirm"
                            class="group relative overflow-hidden rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/20 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-500 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <span class="relative">Confirmar</span>
                        </button>
                        <button
                            v-if="['confirmed', 'in_progress'].includes(appointment.status)"
                            @click="complete"
                            class="group relative overflow-hidden rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-emerald-500/20 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/30"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-emerald-500 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <span class="relative">Completar</span>
                        </button>
                        <button
                            v-if="appointment.status === 'confirmed'"
                            @click="noShow"
                            class="group relative overflow-hidden rounded-lg border border-smoke bg-graphite px-4 py-2.5 text-sm font-medium text-mercury shadow-lg transition-all duration-300 hover:scale-105 hover:border-silver/30 hover:text-silver-bright"
                        >
                            <span class="relative">No-show</span>
                        </button>
                        <button
                            v-if="!['cancelled', 'completed', 'no_show'].includes(appointment.status)"
                            @click="cancel"
                            class="group relative overflow-hidden rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2.5 text-sm font-medium text-red-400 transition-all duration-300 hover:scale-105 hover:border-red-500/50 hover:bg-red-500/20"
                        >
                            <span class="relative">Cancelar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Detalles -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-silver/5 lg:col-span-2" style="animation-delay: 100ms">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
                <div class="relative">
                    <h2 class="font-serif text-xl font-medium text-cream">Detalles de la cita</h2>
                    <dl class="mt-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <User class="h-4 w-4" />
                                Cliente:
                            </dt>
                            <dd class="font-medium text-cream">
                                <Link :href="`/admin/clients/${appointment.client?.id}`" class="transition-colors hover:text-gold">
                                    {{ appointment.client?.name }}
                                </Link>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-emerald-500/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Phone class="h-4 w-4" />
                                Teléfono:
                            </dt>
                            <dd>
                                <a
                                    v-if="appointment.client?.phone"
                                    :href="`https://wa.me/${appointment.client.phone.replace(/\D/g, '')}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 font-medium text-emerald-400 transition-all hover:border-emerald-500/50 hover:bg-emerald-500/20"
                                >
                                    {{ appointment.client.phone }}
                                </a>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <MapPin class="h-4 w-4" />
                                Sucursal:
                            </dt>
                            <dd class="font-medium text-cream">{{ appointment.branch?.name }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Scissors class="h-4 w-4" />
                                Estilista:
                            </dt>
                            <dd class="font-medium text-cream">{{ appointment.stylist?.user?.name || 'Sin asignar' }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Calendar class="h-4 w-4" />
                                Fecha:
                            </dt>
                            <dd class="font-medium text-cream">{{ new Date(appointment.date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' }) }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Clock class="h-4 w-4" />
                                Hora:
                            </dt>
                            <dd class="font-medium text-cream">{{ appointment.start_time?.slice(0,5) }} - {{ appointment.end_time?.slice(0,5) }}</dd>
                        </div>
                        <div v-if="appointment.notes" class="rounded-lg border border-smoke bg-graphite p-4">
                            <dt class="mb-2 text-xs font-medium uppercase tracking-wider text-mercury">Notas:</dt>
                            <dd class="text-sm leading-relaxed text-pearl">{{ appointment.notes }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <dt class="flex items-center gap-2 font-medium text-emerald-400">
                                <DollarSign class="h-5 w-5" />
                                Total:
                            </dt>
                            <dd class="font-serif text-2xl font-bold text-emerald-400">{{ formatPrice(appointment.total) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Servicios -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/10" style="animation-delay: 200ms">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-gradient-to-br from-purple-500/10 to-transparent blur-3xl"></div>
                <div class="relative">
                    <h2 class="font-serif text-xl font-medium text-cream">Servicios</h2>
                    <div class="mt-6 space-y-3">
                        <div
                            v-for="service in appointment.services"
                            :key="service.id"
                            class="group/service rounded-lg border border-smoke bg-graphite p-4 transition-all duration-300 hover:scale-[1.02] hover:border-purple-500/30 hover:shadow-lg hover:shadow-purple-500/5"
                        >
                            <div class="font-medium text-cream">{{ service.service?.name }}</div>
                            <div class="mt-2 flex items-center justify-between text-sm">
                                <span class="inline-flex items-center gap-1.5 rounded border border-smoke/50 bg-ink px-2 py-1 text-xs text-mercury">
                                    <Clock class="h-3 w-3" />
                                    {{ service.duration_minutes }} min
                                </span>
                                <span class="font-serif font-semibold text-purple-400">{{ formatPrice(service.price) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-smoke pt-4">
                        <Link
                            :href="`/admin/sales/create?appointment_id=${appointment.id}`"
                            class="group/link flex items-center justify-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-400 transition-all duration-300 hover:scale-105 hover:border-emerald-500/50 hover:bg-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/20"
                        >
                            <DollarSign class="h-4 w-4 transition-transform group-hover/link:scale-110" />
                            Registrar venta
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="showCancelModal">
            <DialogContent class="border-smoke bg-card sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="font-serif text-xl font-medium text-cream">¿Cancelar esta cita?</DialogTitle>
                    <DialogDescription class="pt-1 text-sm text-mercury">
                        Indica el motivo de la cancelación (opcional).
                    </DialogDescription>
                </DialogHeader>
                <textarea
                    v-model="cancelReason"
                    rows="3"
                    placeholder="Motivo de cancelación..."
                    class="input-elegant"
                ></textarea>
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" class="btn-ghost-elegant h-11 px-6" @click="showCancelModal = false">
                        Volver
                    </button>
                    <button type="button" class="h-11 rounded-full bg-red-500 px-6 text-sm font-semibold text-white transition-all hover:bg-red-400 active:scale-[0.98]" @click="confirmCancel">
                        Cancelar cita
                    </button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>