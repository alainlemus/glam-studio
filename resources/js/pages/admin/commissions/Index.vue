<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    DollarSign,
    Calendar,
    ChevronDown,
    CheckCircle2,
    TrendingUp,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    commissions: any;
    stylists: any[];
    summary: any;
    byStylist: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const stylistId = ref(props.filters.stylist_id || '');
const status = ref(props.filters.status || '');
const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');

const filter = () => {
    router.get('/admin/commissions', {
        stylist_id: stylistId.value || undefined,
        status: status.value || undefined,
        from: from.value || undefined,
        to: to.value || undefined,
    }, { preserveState: true });
};

const payOne = async (id: number) => {
    if (await confirmDialog({
        title: '¿Marcar comisión como pagada?',
        confirmText: 'Marcar como pagada',
    })) router.post(`/admin/commissions/${id}/pay`);
};

const payBatch = async () => {
    const stylistName = stylists.find(s => s.id == stylistId.value)?.user?.name || 'este estilista';
    const rangeFrom = from.value || '2000-01-01';
    const rangeTo = to.value || new Date().toISOString().slice(0, 10);
    const range = from.value && to.value
        ? `del ${from.value} al ${to.value}`
        : 'de todo su historial pendiente';

    if (await confirmDialog({
        title: '¿Liquidar comisiones pendientes?',
        description: `Se marcarán como pagadas todas las comisiones pendientes de ${stylistName} ${range}.`,
        confirmText: 'Liquidar',
    })) {
        router.post('/admin/commissions/pay-batch', {
            stylist_id: stylistId.value,
            from: rangeFrom,
            to: rangeTo,
        });
    }
};

const maxTotal = computed => Math.max(1, ...(props.byStylist || []).map(r => Number(r.total) || 0));
</script>

<template>
    <Head title="Comisiones" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Liquidación</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Comisiones</h2>
            <p class="mt-1 text-sm text-mercury">Gestión de comisiones por estilista</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card-glow relative overflow-hidden p-6">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-silver/10 blur-2xl"></div>
                <div class="relative">
                    <div class="text-eyebrow">Pendientes</div>
                    <div class="mt-2 font-serif text-3xl font-semibold text-glitter">{{ formatPrice(summary.pending) }}</div>
                </div>
            </div>
            <div class="card-glow relative overflow-hidden p-6">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl"></div>
                <div class="relative">
                    <div class="text-eyebrow">Pagadas</div>
                    <div class="mt-2 font-serif text-3xl font-semibold text-emerald-400">{{ formatPrice(summary.paid) }}</div>
                </div>
            </div>
            <div class="card-glow relative overflow-hidden p-6">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-silver/10 blur-2xl"></div>
                <div class="relative">
                    <div class="text-eyebrow">Total comisiones</div>
                    <div class="mt-2 font-serif text-3xl font-semibold text-cream">{{ summary.count }}</div>
                </div>
            </div>
        </div>

        <div v-if="byStylist.length" class="card-elegant p-6">
            <h3 class="mb-4 font-serif text-lg font-medium text-cream">Por estilista</h3>
            <div class="space-y-2">
                <div v-for="row in byStylist" :key="row.stylist_id" class="flex items-center gap-3">
                    <span class="w-44 truncate text-sm text-pearl">{{ row.stylist?.user?.name || 'N/A' }}</span>
                    <div class="h-7 flex-1 overflow-hidden rounded-full bg-graphite border border-smoke">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-silver-bright to-white-bright transition-all"
                            :style="{ width: Math.min(100, (Number(row.total) / Math.max(...byStylist.map(r => Number(r.total) || 0))) * 100) + '%' }"
                        ></div>
                    </div>
                    <span class="w-28 text-right font-serif text-base font-semibold text-silver-bright">{{ formatPrice(row.total) }}</span>
                    <span class="w-12 text-right text-xs text-mercury">{{ row.count }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-smoke bg-card p-4">
            <select v-model="stylistId" class="input-elegant flex-1 appearance-none pr-9" @change="filter">
                <option value="">Todos los estilistas</option>
                <option v-for="s in stylists" :key="s.id" :value="s.id">{{ s.user?.name }}</option>
            </select>
            <select v-model="status" class="input-elegant appearance-none pr-9" @change="filter">
                <option value="">Todos los estatus</option>
                <option value="pending">Pendientes</option>
                <option value="paid">Pagadas</option>
            </select>
            <input v-model="from" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <input v-model="to" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <button v-if="stylistId" @click="payBatch" class="ml-auto btn-primary-elegant h-11 px-5">
                <DollarSign class="h-4 w-4" />
                Liquidar comisiones
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estilista</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Tipo</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Base</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">%</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Comisión</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estatus</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="comm in commissions.data" :key="comm.id" class="hover:bg-graphite/30">
                        <td class="px-5 py-4 text-sm text-pearl">{{ new Date(comm.created_at).toLocaleDateString('es-MX') }}</td>
                        <td class="px-5 py-4 text-sm text-cream">{{ comm.stylist?.user?.name }}</td>
                        <td class="px-5 py-4">
                            <span :class="['chip text-xs', comm.type === 'service' ? 'bg-silver/15 text-silver-bright' : 'bg-silver/15 text-silver-bright']">
                                {{ comm.type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right text-sm text-pearl">{{ formatPrice(comm.base_amount) }}</td>
                        <td class="px-5 py-4 text-right text-sm text-silver-bright">{{ comm.percentage }}%</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-serif text-base font-semibold text-glitter">{{ formatPrice(comm.amount) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', comm.status === 'paid' ? 'bg-emerald-500/15 text-emerald-400' : comm.status === 'pending' ? 'bg-amber-500/15 text-amber-400' : 'bg-red-500/15 text-red-400']">
                                {{ comm.status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button v-if="comm.status === 'pending'" @click="payOne(comm.id)" class="rounded-md bg-silver/10 px-3 py-1.5 text-xs font-medium text-silver-bright transition hover:bg-silver/20">
                                Pagar
                            </button>
                            <span v-else-if="comm.status === 'paid'" class="inline-flex items-center gap-1 text-xs text-emerald-400">
                                <CheckCircle2 class="h-3.5 w-3.5" />
                                Pagado
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <div v-if="commissions.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in commissions.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>