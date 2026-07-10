<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { MapPin } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    city?: any;
}>();

const form = useForm({
    name: props.city?.name || '',
    state: props.city?.state || '',
    country: props.city?.country || 'México',
    is_active: props.city?.is_active ?? true,
});

const submit = () => {
    if (props.city) form.put(`/admin/cities/${props.city.id}`);
    else form.post('/admin/cities');
};
</script>

<template>
    <Head :title="city ? 'Editar ciudad' : 'Nueva ciudad'" />

    <div class="mx-auto max-w-2xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/cities" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Ubicación</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ city ? 'Editar' : 'Nueva' }} ciudad</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                    <input v-model="form.name" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estado</label>
                    <input v-model="form.state" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">País</label>
                    <input v-model="form.country" class="input-elegant" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-3">
                        <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver" />
                        <span class="text-sm font-medium text-cream">Ciudad activa</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/cities" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <MapPin class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script lang="ts">
import { Link } from '@inertiajs/vue3';
</script>