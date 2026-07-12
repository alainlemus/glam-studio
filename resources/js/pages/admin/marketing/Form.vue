<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Megaphone } from '@lucide/vue';
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

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/marketing" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Marketing</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ campaign ? 'Editar' : 'Nueva' }} campaña</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                <input v-model="form.name" required class="input-elegant" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción</label>
                <textarea v-model="form.description" rows="2" class="input-elegant"></textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Tipo</label>
                    <select v-model="form.type" class="input-elegant appearance-none">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="promotion">Promoción</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estatus</label>
                    <select v-model="form.status" class="input-elegant appearance-none">
                        <option value="draft">Borrador</option>
                        <option value="scheduled">Programada</option>
                        <option value="active">Activa</option>
                        <option value="finished">Finalizada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Inicio *</label>
                    <input v-model="form.start_date" type="date" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Fin</label>
                    <input v-model="form.end_date" type="date" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal</label>
                    <select v-model="form.branch_id" class="input-elegant appearance-none">
                        <option value="">Todas</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Servicio</label>
                    <select v-model="form.service_id" class="input-elegant appearance-none">
                        <option value="">Todos</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">% Descuento</label>
                    <input v-model="form.discount_percentage" type="number" step="0.01" min="0" max="100" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Audiencia objetivo</label>
                    <input v-model="form.target_audience" type="number" min="0" class="input-elegant" />
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Plantilla del mensaje</label>
                <textarea v-model="form.message_template" rows="4" class="input-elegant" placeholder="Variables: {nombre}, {faltan}"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/marketing" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Megaphone class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>