<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Pencil, Trash2, Sparkles } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    categories: any[];
}>();

const editingId = ref<number | null>(null);

const form = useForm({ name: '', icon: '✨', description: '', is_active: true });

const startEdit = (cat: any) => {
    editingId.value = cat.id;
    form.name = cat.name;
    form.icon = cat.icon || '✨';
    form.description = cat.description || '';
    form.is_active = cat.is_active;
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
};

const submit = () => {
    if (editingId.value) {
        form.put(`/admin/service-categories/${editingId.value}`, {
            onSuccess: () => {
                editingId.value = null;
                form.reset();
            },
        });
    } else {
        form.post('/admin/service-categories', {
            onSuccess: () => form.reset(),
        });
    }
};

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar esta categoría?',
        description: 'Esta acción no se puede deshacer.',
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
            <p class="mt-1 text-sm text-mercury">{{ categories.length }} categorías registradas</p>
        </div>

        <form @submit.prevent="submit" class="card-elegant p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-[160px] flex-1">
                    <label class="mb-1.5 block text-xs font-medium text-mercury">Nombre</label>
                    <input v-model="form.name" required placeholder="Ej. Cortes" class="input-elegant" />
                </div>
                <div class="w-20">
                    <label class="mb-1.5 block text-xs font-medium text-mercury">Ícono</label>
                    <input v-model="form.icon" placeholder="✨" class="input-elegant text-center" />
                </div>
                <div class="min-w-[160px] flex-1">
                    <label class="mb-1.5 block text-xs font-medium text-mercury">Descripción</label>
                    <input v-model="form.description" placeholder="Opcional" class="input-elegant" />
                </div>
                <label class="flex h-11 shrink-0 items-center gap-2 text-sm text-pearl">
                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-smoke bg-graphite accent-silver-bright" />
                    Activa
                </label>
                <button type="submit" class="btn-primary-elegant h-11 shrink-0 px-5 text-sm" :disabled="form.processing">
                    <Plus v-if="!editingId" class="h-4 w-4" />
                    {{ editingId ? 'Guardar cambios' : 'Agregar' }}
                </button>
                <button v-if="editingId" type="button" @click="cancelEdit" class="btn-ghost-elegant h-11 shrink-0 px-5 text-sm">
                    Cancelar
                </button>
            </div>
        </form>

        <div v-if="categories.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="cat in categories"
                :key="cat.id"
                :class="[
                    'card-elegant card-elegant-hover p-4 transition hover:-translate-y-1',
                    editingId === cat.id ? 'border-silver/50 shadow-[0_0_20px_rgba(209,213,219,0.1)]' : '',
                ]"
            >
                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full border border-silver/20 bg-gradient-to-br from-silver-bright/20 to-graphite text-xl">
                        {{ cat.icon || '✨' }}
                    </div>
                    <span :class="['chip', cat.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                        <span :class="['h-1.5 w-1.5 rounded-full', cat.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                        {{ cat.is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>

                <div class="mt-3">
                    <h3 class="truncate font-serif text-base font-semibold text-cream">{{ cat.name }}</h3>
                    <p v-if="cat.description" class="mt-1 line-clamp-2 text-xs text-mercury">{{ cat.description }}</p>
                    <p class="mt-1 text-xs text-mercury">{{ cat.services_count }} servicios</p>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3 border-t border-smoke pt-3">
                    <button @click="startEdit(cat)" class="flex items-center gap-1 text-xs font-medium text-silver-bright hover:text-white">
                        <Pencil class="h-3 w-3" /> Editar
                    </button>
                    <button @click="destroy(cat.id)" class="flex items-center gap-1 text-xs font-medium text-red-400 hover:text-red-300">
                        <Trash2 class="h-3 w-3" /> Eliminar
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="card-elegant px-6 py-16 text-center">
            <Sparkles class="mx-auto h-12 w-12 text-mercury/30" />
            <p class="mt-3 text-sm text-mercury">No hay categorías de servicios registradas</p>
        </div>
    </div>
</template>
