<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

defineProps<{
    categories: any[];
}>();

const form = useForm({ name: '', icon: '✨', description: '', is_active: true });

const submit = () => {
    form.post('/admin/service-categories', {
        onSuccess: () => form.reset(),
    });
};

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar esta categoría?',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/service-categories/${id}`);
};
</script>

<template>
    <Head title="Categorías de servicios" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Catálogo</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Categorías de servicios</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant p-4">
            <div class="flex flex-wrap gap-3">
                <input v-model="form.name" required placeholder="Nombre" class="input-elegant min-w-[160px] flex-1" />
                <input v-model="form.icon" placeholder="Ícono (emoji)" class="input-elegant w-20 text-center" />
                <input v-model="form.description" placeholder="Descripción" class="input-elegant min-w-[160px] flex-1" />
                <button type="submit" class="btn-primary-elegant h-11 px-5 text-sm">Agregar</button>
            </div>
        </form>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="cat in categories" :key="cat.id" class="card-elegant p-4">
                <div class="text-3xl">{{ cat.icon }}</div>
                <div class="mt-2 font-semibold text-cream">{{ cat.name }}</div>
                <div class="text-xs text-mercury">{{ cat.services_count }} servicios</div>
                <button @click="destroy(cat.id)" class="mt-2 text-xs font-medium text-red-400 hover:text-red-300">Eliminar</button>
            </div>
        </div>
    </div>
</template>