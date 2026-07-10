<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Building2, ChevronDown, MapPin, Phone } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    branches: any;
    cities: any[];
    filters: any;
}>();

const search = ref(props.filters.search || '');
const cityId = ref(props.filters.city_id || '');

const filter = () => {
    router.get('/admin/branches', {
        search: search.value || undefined,
        city_id: cityId.value || undefined,
    }, { preserveState: true });
};

const destroy = (id: number) => {
    if (confirm('¿Eliminar esta sucursal?')) router.delete(`/admin/branches/${id}`);
};
</script>

<template>
    <Head title="Sucursales" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Ubicaciones</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Sucursales</h2>
                <p class="mt-1 text-sm text-mercury">Gestiona todas las ubicaciones</p>
            </div>
            <Link href="/admin/branches/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nueva sucursal
            </Link>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar sucursal..."
                        class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream placeholder:text-mercury focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @input="filter"
                    />
                </div>
                <div class="relative">
                    <select v-model="cityId" class="w-full appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-9 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @change="filter">
                        <option value="">Todas las ciudades</option>
                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Ciudad</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Teléfono</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="branch in branches.data" :key="branch.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-silver/20 bg-silver/10">
                                    <Building2 class="h-5 w-5 text-silver-bright" />
                                </div>
                                <div>
                                    <Link :href="`/admin/branches/${branch.id}`" class="font-medium text-cream hover:text-silver-bright">
                                        {{ branch.name }}
                                    </Link>
                                    <div class="flex items-center gap-1 text-xs text-mercury">
                                        <MapPin class="h-3 w-3" />
                                        {{ branch.address }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ branch.city?.name }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5 text-sm text-pearl">
                                <Phone class="h-3 w-3 text-silver/60" />
                                {{ branch.phone }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', branch.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                                <span :class="['h-1.5 w-1.5 rounded-full', branch.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                                {{ branch.is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <Link :href="`/admin/branches/${branch.id}/edit`" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">Editar</Link>
                            <button @click="destroy(branch.id)" class="ml-3 text-sm font-medium text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="branches.data.length === 0" class="px-6 py-16 text-center text-sm text-mercury">
                No hay sucursales registradas
            </div>
        </div>

        <div v-if="branches.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="link in branches.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40 hover:text-cream']"
                v-html="link.label"
            />
        </div>
    </div>
</template>