<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    incomes: any;
    filters: any;
    total: number;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');

const filter = () => {
    router.get('/admin/incomes', {
        from: from.value || undefined,
        to: to.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <Head title="Ingresos" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Finanzas</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Ingresos</h2>
            <p class="mt-1 text-sm text-mercury">{{ formatPrice(total) }} · {{ incomes.total }} registros</p>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-smoke bg-card p-4">
            <input v-model="from" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <input v-model="to" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Concepto</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Origen</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="income in incomes.data" :key="income.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4 text-sm text-pearl">{{ new Date(income.income_date).toLocaleDateString('es-MX') }}</td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ income.concept }}</td>
                        <td class="px-5 py-4">
                            <span class="chip bg-silver/15 text-silver-bright">{{ income.source }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ income.branch?.name || '—' }}</td>
                        <td class="px-5 py-4 text-right text-sm font-medium text-emerald-400">{{ formatPrice(income.amount) }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div v-if="incomes.data.length === 0" class="px-6 py-16 text-center text-sm text-mercury">
                No hay ingresos registrados
            </div>
        </div>
    </div>
</template>