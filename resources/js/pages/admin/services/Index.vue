<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Clock, DollarSign, ChevronDown } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    services: any;
    categories: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');

const filter = () => {
    router.get('/admin/services', {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
    }, { preserveState: true });
};

const destroy = (id: number) => {
    if (confirm('¿Eliminar este servicio?')) router.delete(`/admin/services/${id}`);
};
</script>

<template>
    <Head title="Servicios" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Catálogo</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Servicios</h2>
                <p class="mt-1 text-sm text-mercury">{{ services.total }} servicios en catálogo</p>
            </div>
            <Link href="/admin/services/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo servicio
            </Link>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input v-model="search" type="text" placeholder="Buscar..." class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream placeholder:text-mercury focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @input="filter" />
                </div>
                <div class="relative">
                    <select v-model="categoryId" class="w-full appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-9 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @change="filter">
                        <option value="">Todas las categorías</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Servicio</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Categoría</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Precio</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Comisión</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Duración</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="service in services.data" :key="service.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-cream">{{ service.name }}</div>
                            <div class="line-clamp-1 text-xs text-mercury">{{ service.description }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ service.category?.name }}</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-serif text-base font-semibold text-silver-bright">{{ formatPrice(service.price) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="chip bg-silver/15 text-silver-bright">{{ service.commission_percentage }}%</span>
                        </td>
                        <td class="px-5 py-4 text-right text-sm text-pearl">
                            <Clock class="mr-1 inline h-3 w-3 text-silver/60" />
                            {{ service.duration_minutes }} min
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', service.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                                <span :class="['h-1.5 w-1.5 rounded-full', service.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                                {{ service.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <Link :href="`/admin/services/${service.id}/edit`" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">Editar</Link>
                            <button @click="destroy(service.id)" class="ml-3 text-sm font-medium text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="services.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in services.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>