<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Plus,
    Search,
    Scissors,
    Award,
    ChevronDown,
    Sparkles,
    ChevronRight,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stylists: any;
    branches: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const search = ref(props.filters.search || '');
const branchId = ref(props.filters.branch_id || '');

const filter = () => {
    router.get('/admin/stylists', {
        search: search.value || undefined,
        branch_id: branchId.value || undefined,
    }, { preserveState: true });
};

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
};
</script>

<template>
    <Head title="Estilistas" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Equipo</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Estilistas</h2>
                <p class="mt-1 text-sm text-mercury">{{ stylists.total }} estilistas en el equipo</p>
            </div>
            <Link href="/admin/stylists/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo estilista
            </Link>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input v-model="search" type="text" placeholder="Buscar estilista..." class="input-elegant pl-10" @input="filter" />
                </div>
                <div class="relative">
                    <select v-model="branchId" class="input-elegant pr-9 appearance-none" @change="filter">
                        <option value="">Todas las sucursales</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <Link
                v-for="stylist in stylists.data"
                :key="stylist.id"
                :href="`/admin/stylists/${stylist.id}`"
                class="card-elegant card-elegant-hover group p-6 transition hover:-translate-y-1"
            >
                <div class="flex items-start gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border-2 border-silver/30 bg-gradient-to-br from-silver-bright/30 to-graphite font-serif text-lg font-bold text-cream">
                        {{ getInitials(stylist.user?.name) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="truncate font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                            {{ stylist.user?.name }}
                        </h3>
                        <p v-if="stylist.specialty" class="truncate text-xs font-medium text-silver-bright">{{ stylist.specialty }}</p>
                        <p class="mt-1 truncate text-xs text-mercury">{{ stylist.user?.email }}</p>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-3 gap-3 border-t border-smoke pt-4 text-center">
                    <div>
                        <div class="font-serif text-base font-semibold text-cream">{{ formatPrice(stylist.base_salary) }}</div>
                        <div class="text-[10px] uppercase tracking-wider text-mercury">Sueldo</div>
                    </div>
                    <div>
                        <div class="font-serif text-base font-semibold text-silver-bright">{{ stylist.service_commission }}%</div>
                        <div class="text-[10px] uppercase tracking-wider text-mercury">Servicios</div>
                    </div>
                    <div>
                        <div class="font-serif text-base font-semibold text-silver-bright">{{ stylist.product_commission }}%</div>
                        <div class="text-[10px] uppercase tracking-wider text-mercury">Productos</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-smoke pt-3 text-xs">
                    <span class="text-mercury">{{ stylist.branch?.name }}</span>
                    <ChevronRight class="h-4 w-4 text-silver-bright transition group-hover:translate-x-1" />
                </div>
            </Link>
        </div>

        <div v-if="stylists.data.length === 0" class="card-elegant p-12 text-center">
            <Scissors class="mx-auto h-12 w-12 text-mercury/30" />
            <p class="mt-3 text-sm text-mercury">No hay estilistas registrados</p>
        </div>

        <div v-if="stylists.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="link in stylists.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40 hover:text-cream']"
                v-html="link.label"
            />
        </div>
    </div>
</template>