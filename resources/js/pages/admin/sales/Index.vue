<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Plus,
    Search,
    ShoppingCart,
    ChevronDown,
    Calendar,
    Users,
    Download,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    sales: any;
    branches: any[];
    filters: any;
    summary: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');
const branchId = ref(props.filters.branch_id || '');

const filter = () => {
    router.get('/admin/sales', {
        from: from.value || undefined,
        to: to.value || undefined,
        branch_id: branchId.value || undefined,
    }, { preserveState: true });
};

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (from.value) params.set('from', from.value);
    if (to.value) params.set('to', to.value);
    if (branchId.value) params.set('branch_id', branchId.value);
    const qs = params.toString();
    return `/admin/sales/export${qs ? `?${qs}` : ''}`;
});

const statusColors: Record<string, string> = {
    paid: 'bg-emerald-500/15 text-emerald-400',
    open: 'bg-amber-500/15 text-amber-400',
    cancelled: 'bg-red-500/15 text-red-400',
    refunded: 'bg-zinc-500/15 text-zinc-400',
};

const statusDots: Record<string, string> = {
    paid: 'bg-emerald-400',
    open: 'bg-amber-400',
    cancelled: 'bg-red-400',
    refunded: 'bg-zinc-400',
};
</script>

<template>
    <Head title="Ventas" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Comercial</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Ventas</h2>
                <p class="mt-1 text-sm text-mercury">
                    {{ summary.count }} ventas · {{ formatPrice(summary.total) }}
                </p>
            </div>
            <div class="flex gap-3">
                <a :href="exportUrl" class="btn-ghost-elegant h-12 px-5 text-sm">
                    <Download class="h-4 w-4" />
                    Exportar CSV
                </a>
                <Link href="/admin/sales/create" class="btn-primary-elegant h-12 px-5 text-sm">
                    <Plus class="h-4 w-4" />
                    Nueva venta
                </Link>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-smoke bg-card p-4">
            <div class="flex items-center gap-2">
                <Calendar class="h-4 w-4 text-gold/70" />
                <input v-model="from" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
                <span class="text-mercury">—</span>
                <input v-model="to" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            </div>
            <div class="relative flex-1 min-w-[200px]">
                <select v-model="branchId" class="input-elegant pr-9 appearance-none" @change="filter">
                    <option value="">Todas las sucursales</option>
                    <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
                <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Ticket</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Cliente</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estilista</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Método</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estatus</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="sale in sales.data" :key="sale.id" class="cursor-pointer transition hover:bg-graphite/50" @click="$inertia.visit(`/admin/sales/${sale.id}`)">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-semibold text-silver-bright">{{ sale.ticket_number }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ new Date(sale.created_at).toLocaleDateString('es-MX') }}</td>
                        <td class="px-5 py-4 text-sm text-cream">{{ sale.client?.name || 'Público' }}</td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ sale.stylist?.user?.name || '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="chip bg-graphite text-pearl capitalize">{{ sale.payment_method }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', statusColors[sale.status]]">
                                <span :class="['h-1.5 w-1.5 rounded-full', statusDots[sale.status]]"></span>
                                {{ sale.status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-serif text-lg font-semibold text-glitter">{{ formatPrice(sale.total) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div v-if="sales.data.length === 0" class="px-6 py-16 text-center">
                <ShoppingCart class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">No hay ventas registradas</p>
            </div>
        </div>

        <div v-if="sales.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in sales.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40 hover:text-cream']" v-html="link.label" />
        </div>
    </div>
</template>