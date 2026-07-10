<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    campaign?: any;
    branches: any[];
    services: any[];
}>();

const form = useForm({
    name: props.campaign?.name || '',
    description: props.campaign?.description || '',
    type: props.campaign?.type || 'whatsapp',
    start_date: props.campaign?.start_date?.split('T')[0] || new Date().toISOString().split('T')[0],
    end_date: props.campaign?.end_date?.split('T')[0] || '',
    branch_id: props.campaign?.branch_id || '',
    service_id: props.campaign?.service_id || '',
    discount_percentage: props.campaign?.discount_percentage || '',
    message_template: props.campaign?.message_template || '',
    target_audience: props.campaign?.target_audience || 0,
    status: props.campaign?.status || 'draft',
});

const submit = () => {
    if (props.campaign) form.put(`/admin/marketing/${props.campaign.id}`);
    else form.post('/admin/marketing');
};
</script>

<template>
    <Head :title="campaign ? 'Editar campaña' : 'Nueva campaña'" />

    <div class="mx-auto max-w-2xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ campaign ? 'Editar' : 'Nueva' }} Campaña</h1>

        <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nombre *</label>
                    <input v-model="form.name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea v-model="form.description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tipo</label>
                    <select v-model="form.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="promotion">Promoción</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Estatus</label>
                    <select v-model="form.status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="draft">Borrador</option>
                        <option value="scheduled">Programada</option>
                        <option value="active">Activa</option>
                        <option value="finished">Finalizada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Inicio *</label>
                    <input v-model="form.start_date" type="date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Fin</label>
                    <input v-model="form.end_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Sucursal</label>
                    <select v-model="form.branch_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Todas</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Servicio</label>
                    <select v-model="form.service_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">% Descuento</label>
                    <input v-model="form.discount_percentage" type="number" step="0.01" min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Audiencia objetivo</label>
                    <input v-model="form.target_audience" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Plantilla del mensaje</label>
                    <textarea v-model="form.message_template" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Variables: {nombre}, {faltan}"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="/admin/marketing" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white hover:bg-pink-700 disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>