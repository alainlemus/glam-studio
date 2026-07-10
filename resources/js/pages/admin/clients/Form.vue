<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserPlus } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    client?: any;
}>();

const form = useForm({
    name: props.client?.name || '',
    phone: props.client?.phone || '',
    email: props.client?.email || '',
    birthday: props.client?.birthday?.split('T')[0] || '',
    notes: props.client?.notes || '',
    is_active: props.client?.is_active ?? true,
});

const submit = () => {
    if (props.client) form.put(`/admin/clients/${props.client.id}`);
    else form.post('/admin/clients');
};
</script>

<template>
    <Head :title="client ? 'Editar cliente' : 'Nuevo cliente'" />

    <div class="mx-auto max-w-2xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/clients" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">CRM</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ client ? 'Editar' : 'Nuevo' }} cliente</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                    <input v-model="form.name" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Teléfono (WhatsApp) *</label>
                    <input v-model="form.phone" required class="input-elegant" placeholder="55 1234 5678" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Email</label>
                    <input v-model="form.email" type="email" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Cumpleaños</label>
                    <input v-model="form.birthday" type="date" class="input-elegant" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-3">
                        <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver" />
                        <span class="text-sm font-medium text-cream">Cliente activo</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Notas</label>
                    <textarea v-model="form.notes" rows="3" class="input-elegant"></textarea>
                </div>
            </div>

            <div v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/clients" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <UserPlus class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>