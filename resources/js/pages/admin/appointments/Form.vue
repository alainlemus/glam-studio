<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { CalendarCheck, Plus, Trash2 } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    appointment?: any;
    clients: any[];
    branches: any[];
    services: any[];
    stylists: any[];
}>();

const selectedServices = ref<any[]>(
    props.appointment?.services?.map((s: any) => ({
        id: s.service_id,
        price: s.price,
        duration: s.duration_minutes,
        ...s
    })) || []
);

const form = useForm({
    client_id: props.appointment?.client_id || '',
    branch_id: props.appointment?.branch_id || '',
    stylist_id: props.appointment?.stylist_id || '',
    date: props.appointment?.date?.split('T')[0] || '',
    start_time: props.appointment?.start_time?.slice(0, 5) || '',
    status: props.appointment?.status || 'pending',
    notes: props.appointment?.notes || '',
});

watch(() => [form.client_id, form.branch_id, form.stylist_id, form.date], () => {
    form.start_time = '';
});

const toggleService = (serviceId: number) => {
    const idx = selectedServices.value.findIndex((s: any) => s.id === serviceId);
    if (idx >= 0) {
        selectedServices.value.splice(idx, 1);
    } else {
        const svc = props.services.find((s: any) => s.id === serviceId);
        if (svc) selectedServices.value.push(svc);
    }
};

const total = computed(() => {
    return selectedServices.value.reduce((sum: number, s: any) => sum + Number(s.price || 0), 0);
});

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const submit = () => form.post('/admin/appointments');
</script>

<template>
    <Head :title="appointment ? 'Editar cita' : 'Nueva cita'" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/appointments" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Agenda</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ appointment ? 'Editar' : 'Nueva' }} cita</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="card-elegant p-6">
                <h3 class="mb-4 font-serif text-lg font-medium text-cream">Información general</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Cliente *</label>
                        <select v-model="form.client_id" required class="input-elegant appearance-none">
                            <option value="">Selecciona...</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }} - {{ c.phone }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal *</label>
                        <select v-model="form.branch_id" required class="input-elegant appearance-none">
                            <option value="">Selecciona...</option>
                            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estilista</label>
                        <select v-model="form.stylist_id" class="input-elegant appearance-none">
                            <option value="">Sin asignar</option>
                            <option v-for="s in stylists" :key="s.id" :value="s.id">{{ s.user?.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estatus</label>
                        <select v-model="form.status" class="input-elegant appearance-none">
                            <option value="pending">Pendiente</option>
                            <option value="confirmed">Confirmada</option>
                            <option value="in_progress">En curso</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelada</option>
                            <option value="no_show">No show</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Fecha *</label>
                        <input v-model="form.date" type="date" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Hora inicio *</label>
                        <input v-model="form.start_time" type="time" required class="input-elegant" />
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6">
                <h3 class="mb-4 font-serif text-lg font-medium text-cream">Servicios</h3>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <label v-for="service in services" :key="service.id" class="flex items-center gap-2 rounded-lg border border-smoke bg-graphite p-3 hover:border-silver/40 cursor-pointer">
                        <input type="checkbox" :checked="selectedServices.some((s: any) => s.id === service.id)" @change="toggleService(service.id)" class="h-4 w-4 rounded text-silver focus:ring-silver" />
                        <div class="flex-1">
                            <div class="text-sm font-medium text-cream">{{ service.name }}</div>
                            <div class="text-xs text-mercury">{{ service.duration_minutes }} min</div>
                        </div>
                        <div class="font-serif text-sm font-semibold text-silver-bright">{{ formatPrice(service.price) }}</div>
                    </label>
                </div>
                <div v-if="selectedServices.length" class="mt-4 border-t border-smoke pt-3 flex justify-between text-sm">
                    <span>Servicios seleccionados:</span>
                    <span class="font-semibold text-silver-bright">{{ selectedServices.length }}</span>
                </div>
            </div>

            <div class="card-elegant p-6">
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Notas</label>
                <textarea v-model="form.notes" rows="3" class="input-elegant"></textarea>
            </div>

            <div class="card-glow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-eyebrow">Resumen</p>
                        <h3 class="font-serif text-lg font-medium text-cream">{{ selectedServices.length }} servicio(s) seleccionado(s)</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-xs uppercase tracking-wider text-mercury">Total estimado</div>
                        <div class="font-serif text-3xl font-semibold text-glitter">{{ formatPrice(total) }}</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/appointments" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <CalendarCheck class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar cita' }}
                </button>
            </div>
        </form>
    </div>
</template>