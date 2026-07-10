<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { MapPin, Plus } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    cities: any[];
}>();

const form = useForm({ name: '', state: '', country: 'México', is_active: true });

const submit = () => {
    form.post('/admin/cities', {
        onSuccess: () => form.reset(),
    });
};

const destroy = (id: number) => {
    if (confirm('¿Eliminar esta ciudad?')) router.delete(`/admin/cities/${id}`);
};

const edit = (city: any) => {
    const name = prompt('Nombre:', city.name);
    if (name === null) return;
    const state = prompt('Estado:', city.state || '');
    if (state === null) return;
    const isActive = confirm('¿Activa?');
    router.put(`/admin/cities/${city.id}`, { name, state, country: city.country, is_active: isActive });
};
</script>

<template>
    <Head title="Ciudades" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Ubicación</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Ciudades</h2>
            <p class="mt-1 text-sm text-mercury">{{ cities.length }} ciudades registradas</p>
        </div>

        <form @submit.prevent="submit" class="card-elegant p-5">
            <h3 class="mb-4 font-serif text-base font-medium text-cream">Agregar nueva ciudad</h3>
            <div class="grid gap-3 sm:grid-cols-4">
                <input v-model="form.name" required placeholder="Nombre" class="input-elegant sm:col-span-2" />
                <input v-model="form.state" placeholder="Estado" class="input-elegant" />
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-11 px-5 disabled:opacity-50">
                    <Plus class="h-4 w-4" />
                    Agregar
                </button>
            </div>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="city in cities" :key="city.id" class="card-elegant card-elegant-hover p-5">
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full border border-silver/30 bg-silver/10 text-silver-bright">
                        <MapPin class="h-5 w-5" />
                    </div>
                    <span :class="['chip text-xs', city.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                        {{ city.is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <h3 class="mt-4 font-serif text-lg font-medium text-cream">{{ city.name }}</h3>
                <p class="text-xs text-mercury">{{ city.state }} · {{ city.country }}</p>
                <div class="mt-3 border-t border-smoke pt-3">
                    <div class="text-xs text-mercury">
                        <span class="text-cream font-semibold">{{ city.branches_count }}</span> sucursal(es)
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button @click="edit(city)" class="text-xs font-medium text-silver-bright hover:text-silver-bright-bright">Editar</button>
                        <button @click="destroy(city.id)" class="text-xs font-medium text-red-400 hover:text-red-300">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>