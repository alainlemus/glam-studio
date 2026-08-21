<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Star, MessageSquareQuote } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

defineProps<{
    testimonials: any[];
}>();

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar este testimonio?',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/testimonials/${id}`);
};
</script>

<template>
    <Head title="Testimonios" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Crecimiento</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Testimonios</h2>
                <p class="mt-1 text-sm text-mercury">Reseñas de clientas mostradas en el sitio público</p>
            </div>
            <Link href="/admin/testimonials/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo testimonio
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Cliente</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Testimonio</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Calificación</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="testimonial in testimonials" :key="testimonial.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-silver/20 bg-silver/10">
                                    <img v-if="testimonial.photo_url" :src="testimonial.photo_url" class="h-full w-full object-cover" :alt="testimonial.client_name" />
                                    <MessageSquareQuote v-else class="h-4 w-4 text-silver-bright" />
                                </div>
                                <span class="font-medium text-cream">{{ testimonial.client_name }}</span>
                            </div>
                        </td>
                        <td class="max-w-sm px-5 py-4 text-sm text-pearl">
                            <p class="line-clamp-2">{{ testimonial.quote }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-0.5">
                                <Star
                                    v-for="i in 5"
                                    :key="i"
                                    class="h-3.5 w-3.5"
                                    :class="i <= testimonial.rating ? 'fill-gold-bright text-gold-bright' : 'text-smoke'"
                                />
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', testimonial.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                                <span :class="['h-1.5 w-1.5 rounded-full', testimonial.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                                {{ testimonial.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <Link :href="`/admin/testimonials/${testimonial.id}/edit`" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">Editar</Link>
                            <button @click="destroy(testimonial.id)" class="ml-3 text-sm font-medium text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div v-if="testimonials.length === 0" class="px-6 py-16 text-center text-sm text-mercury">
                No hay testimonios registrados
            </div>
        </div>
    </div>
</template>
