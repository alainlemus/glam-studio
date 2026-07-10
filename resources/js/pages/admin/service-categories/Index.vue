<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

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

const destroy = (id: number) => {
    if (confirm('¿Eliminar?')) router.delete(`/admin/service-categories/${id}`);
};
</script>

<template>
    <Head title="Categorías de servicios" />

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categorías de Servicios</h1>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap gap-3">
                <input v-model="form.name" required placeholder="Nombre" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <input v-model="form.icon" placeholder="Ícono (emoji)" class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <input v-model="form.description" placeholder="Descripción" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <button type="submit" class="rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white">Agregar</button>
            </div>
        </form>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="cat in categories" :key="cat.id" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="text-3xl">{{ cat.icon }}</div>
                <div class="mt-2 font-semibold text-gray-900">{{ cat.name }}</div>
                <div class="text-xs text-gray-500">{{ cat.services_count }} servicios</div>
                <button @click="destroy(cat.id)" class="mt-2 text-xs text-red-600">Eliminar</button>
            </div>
        </div>
    </div>
</template>