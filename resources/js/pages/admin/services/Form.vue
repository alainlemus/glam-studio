<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Scissors } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    service?: any;
    categories: any[];
}>();

const form = useForm({
    service_category_id: props.service?.service_category_id || '',
    name: props.service?.name || '',
    description: props.service?.description || '',
    price: props.service?.price || '',
    commission_percentage: props.service?.commission_percentage || 25,
    duration_minutes: props.service?.duration_minutes || 60,
    is_active: props.service?.is_active ?? true,
});

const submit = () => {
    if (props.service) form.put(`/admin/services/${props.service.id}`);
    else form.post('/admin/services');
};
</script>

<template>
    <Head :title="service ? 'Editar servicio' : 'Nuevo servicio'" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/services" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Catálogo</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ service ? 'Editar' : 'Nuevo' }} servicio</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                    <input v-model="form.name" required class="input-elegant" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">{{ form.errors.name }}</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Categoría *</label>
                    <select v-model="form.service_category_id" required class="input-elegant appearance-none">
                        <option value="">Selecciona...</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción</label>
                    <textarea v-model="form.description" rows="3" class="input-elegant"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Precio *</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Duración (min) *</label>
                    <input v-model="form.duration_minutes" type="number" min="5" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">% Comisión</label>
                    <input v-model="form.commission_percentage" type="number" step="0.01" min="0" max="100" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estatus</label>
                    <select v-model="form.is_active" class="input-elegant appearance-none">
                        <option :value="true">Activo</option>
                        <option :value="false">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/services" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Scissors class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>