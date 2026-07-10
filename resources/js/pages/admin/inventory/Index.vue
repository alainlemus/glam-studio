<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Package, ChevronDown, AlertTriangle, DollarSign } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stocks: any;
    branches: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const search = ref(props.filters.search || '');
const branchId = ref(props.filters.branch_id || '');
const lowStock = ref(props.filters.low_stock === '1');

const filter = () => {
    router.get('/admin/inventory', {
        search: search.value || undefined,
        branch_id: branchId.value || undefined,
        low_stock: lowStock.value ? '1' : undefined,
    }, { preserveState: true });
};

const adjust = (productId: number, branchId: number) => {
    const adj = prompt('Ajuste (+ añadir, - restar):');
    if (adj === null) return;
    const reason = prompt('Motivo:') || '';
    router.post(`/admin/inventory/${productId}/${branchId}/adjust`, {
        adjustment: parseInt(adj),
        reason,
    });
};
</script>

<template>
    <Head title="Inventario" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Catálogo</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Inventario</h2>
                <p class="mt-1 text-sm text-mercury">Stock por sucursal</p>
            </div>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input v-model="search" type="text" placeholder="Buscar..." class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream placeholder:text-mercury focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @input="filter" />
                </div>
                <div class="flex gap-2">
                    <select v-model="branchId" class="flex-1 appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-9 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @change="filter">
                        <option value="">Todas</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                    <ChevronDown class="-ml-7 pointer-events-none h-4 w-4 self-center text-mercury" />
                </div>
            </div>
            <label class="mt-3 inline-flex items-center gap-2 text-sm text-pearl">
                <input v-model="lowStock" type="checkbox" @change="filter" class="rounded border-smoke bg-graphite text-silver-bright focus:ring-gold" />
                <AlertTriangle class="h-3 w-3 text-orange-400" />
                Mostrar solo stock bajo
            </label>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Producto</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Stock</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Mínimo</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="stock in stocks.data" :key="stock.id" :class="stock.is_low_stock ? 'bg-red-500/5' : ''">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-silver/20 bg-silver/10">
                                    <Package class="h-5 w-5 text-silver-bright" />
                                </div>
                                <div>
                                    <div class="font-medium text-cream">{{ stock.product?.name }}</div>
                                    <div class="text-xs text-mercury">{{ stock.product?.category?.name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ stock.branch?.name }}</td>
                        <td class="px-5 py-4 text-right">
                            <span :class="['font-serif text-2xl font-semibold tabular-nums', stock.is_low_stock ? 'text-red-400' : 'text-cream']">
                                {{ stock.stock }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right text-sm text-mercury">{{ stock.min_stock }}</td>
                        <td class="px-5 py-4">
                            <span v-if="stock.is_low_stock" class="chip bg-red-500/20 text-red-400">
                                <AlertTriangle class="h-3 w-3" />
                                Stock bajo
                            </span>
                            <span v-else class="chip bg-emerald-500/15 text-emerald-400">
                                ✓ OK
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button @click="adjust(stock.product_id, stock.branch_id)" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">
                                Ajustar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="stocks.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in stocks.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>