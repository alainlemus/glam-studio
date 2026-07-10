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

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ingresos</h1>
            <p class="text-sm text-gray-500">{{ formatPrice(total) }} · {{ incomes.total }} registros</p>
        </div>

        <div class="flex gap-3 rounded-xl border border-gray-200 bg-white p-4">
            <input v-model="from" type="date" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" @change="filter" />
            <input v-model="to" type="date" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" @change="filter" />
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Concepto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Origen</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Sucursal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="income in incomes.data" :key="income.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ new Date(income.income_date).toLocaleDateString('es-MX') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ income.concept }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ income.source }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ income.branch?.name || '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-green-600">{{ formatPrice(income.amount) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>