<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { MapPin, Plus } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

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

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar esta ciudad?',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/cities/${id}`);
};

const showEditModal = ref(false);
const editForm = useForm({ id: 0, name: '', state: '', country: '', is_active: true });

const edit = (city: any) => {
    editForm.id = city.id;
    editForm.name = city.name;
    editForm.state = city.state || '';
    editForm.country = city.country;
    editForm.is_active = city.is_active;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(`/admin/cities/${editForm.id}`, {
        onSuccess: () => (showEditModal.value = false),
    });
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

        <Dialog v-model:open="showEditModal">
            <DialogContent class="border-smoke bg-card sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="font-serif text-xl font-medium text-cream">Editar ciudad</DialogTitle>
                </DialogHeader>
                <div class="space-y-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre</label>
                        <input v-model="editForm.name" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estado</label>
                        <input v-model="editForm.state" class="input-elegant" />
                    </div>
                    <label class="inline-flex items-center gap-3">
                        <input v-model="editForm.is_active" type="checkbox" class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver" />
                        <span class="text-sm font-medium text-cream">Ciudad activa</span>
                    </label>
                </div>
                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" class="btn-ghost-elegant h-11 px-6" @click="showEditModal = false">Cancelar</button>
                    <button type="button" :disabled="editForm.processing" class="btn-primary-elegant h-11 px-6 disabled:opacity-50" @click="submitEdit">
                        Guardar
                    </button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>