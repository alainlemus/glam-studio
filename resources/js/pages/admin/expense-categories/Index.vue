<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    categories: any[];
}>();

const form = useForm({ name: '', type: 'variable', description: '', is_active: true });

const submit = () => {
    form.post('/admin/expense-categories', {
        onSuccess: () => form.reset(),
    });
};

const destroy = (id: number) => {
    if (confirm('¿Eliminar?')) router.delete(`/admin/expense-categories/${id}`);
};
</script>

<template>
    <Head title="Categorías de egresos" />

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categorías de Egresos</h1>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap gap-3">
                <input v-model="form.name" required placeholder="Nombre" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <select v-model="form.type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="fixed">Fijo</option>
                    <option value="variable">Variable</option>
                </select>
                <input v-model="form.description" placeholder="Descripción" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <button type="submit" class="rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white">Agregar</button>
            </div>
        </form>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="cat in categories" :key="cat.id" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-900">{{ cat.name }}</div>
                    <span :class="['rounded-full px-2 py-0.5 text-xs', cat.type === 'fixed' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700']">
                        {{ cat.type }}
                    </span>
                </div>
                <div v-if="cat.description" class="mt-1 text-xs text-gray-500">{{ cat.description }}</div>
                <button @click="destroy(cat.id)" class="mt-2 text-xs text-red-600">Eliminar</button>
            </div>
        </div>
    </div>
</template>