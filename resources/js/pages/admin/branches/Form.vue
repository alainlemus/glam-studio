<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Building2 } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    branch?: any;
    cities: any[];
}>();

const form = useForm({
    city_id: props.branch?.city_id || '',
    name: props.branch?.name || '',
    address: props.branch?.address || '',
    phone: props.branch?.phone || '',
    whatsapp: props.branch?.whatsapp || '',
    email: props.branch?.email || '',
    manager_name: props.branch?.manager_name || '',
    opening_time: props.branch?.opening_time?.slice(0, 5) || '09:00',
    closing_time: props.branch?.closing_time?.slice(0, 5) || '20:00',
    latitude: props.branch?.latitude || '',
    longitude: props.branch?.longitude || '',
    description: props.branch?.description || '',
    is_active: props.branch?.is_active ?? true,
});

const submit = () => {
    if (props.branch) form.put(`/admin/branches/${props.branch.id}`);
    else form.post('/admin/branches');
};
</script>

<template>
    <Head :title="branch ? 'Editar sucursal' : 'Nueva sucursal'" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/branches" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Ubicación</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ branch ? 'Editar' : 'Nueva' }} sucursal</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                    <input v-model="form.name" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Ciudad *</label>
                    <select v-model="form.city_id" required class="input-elegant appearance-none">
                        <option value="">Selecciona...</option>
                        <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Gerente</label>
                    <input v-model="form.manager_name" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Teléfono *</label>
                    <input v-model="form.phone" required class="input-elegant" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Dirección *</label>
                    <input v-model="form.address" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">WhatsApp</label>
                    <input v-model="form.whatsapp" placeholder="5215512345678" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Email</label>
                    <input v-model="form.email" type="email" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Hora apertura *</label>
                    <input v-model="form.opening_time" type="time" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Hora cierre *</label>
                    <input v-model="form.closing_time" type="time" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Latitud</label>
                    <input v-model="form.latitude" type="number" step="0.0000001" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Longitud</label>
                    <input v-model="form.longitude" type="number" step="0.0000001" class="input-elegant" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción</label>
                    <textarea v-model="form.description" rows="3" class="input-elegant"></textarea>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-3">
                        <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver" />
                        <span class="text-sm font-medium text-cream">Sucursal activa</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/branches" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Building2 class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>