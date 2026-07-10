<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    DollarSign,
    Wallet,
    TrendingUp,
    Activity,
    Calendar,
    ChevronDown,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    summary: any;
    expensesByCategory: any[];
    dailyData: any[];
    branches: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const from = ref(props.filters.from);
const to = ref(props.filters.to);
const branchId = ref(props.filters.branch_id || '');

const filter = () => {
    router.get('/admin/finance', {
        from: from.value,
        to: to.value,
        branch_id: branchId.value || undefined,
    }, { preserveState: true });
};

const maxDaily = Math.max(1, ...props.dailyData.map(d => Math.max(Number(d.income), Number(d.expense))));
</script>

<template>
    <Head title="Finanzas" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Reportes</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Finanzas</h2>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-smoke bg-card p-4">
            <input v-model="from" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <input v-model="to" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <div class="relative flex-1 min-w-[200px]">
                <select v-model="branchId" class="input-elegant pr-9 appearance-none" @change="filter">
                    <option value="">Todas las sucursales</option>
                    <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
                <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card-glow p-6">
                <div class="flex items-center justify-between">
                    <div class="text-eyebrow">Ingresos</div>
                    <DollarSign class="h-5 w-5 text-emerald-400" />
                </div>
                <div class="mt-3 font-serif text-3xl font-semibold text-emerald-400">{{ formatPrice(summary.totalIncome) }}</div>
            </div>
            <div class="card-glow p-6">
                <div class="flex items-center justify-between">
                    <div class="text-eyebrow">Egresos</div>
                    <Wallet class="h-5 w-5 text-red-400" />
                </div>
                <div class="mt-3 font-serif text-3xl font-semibold text-red-400">{{ formatPrice(summary.totalExpenses) }}</div>
            </div>
            <div class="card-glow p-6">
                <div class="flex items-center justify-between">
                    <div class="text-eyebrow">Utilidad</div>
                    <TrendingUp class="h-5 w-5 text-silver-bright" />
                </div>
                <div :class="['mt-3 font-serif text-3xl font-semibold', summary.profit >= 0 ? 'text-glitter' : 'text-red-400']">
                    {{ formatPrice(summary.profit) }}
                </div>
            </div>
            <div class="card-glow p-6">
                <div class="flex items-center justify-between">
                    <div class="text-eyebrow">Margen</div>
                    <Activity class="h-5 w-5 text-silver-bright" />
                </div>
                <div class="mt-3 font-serif text-3xl font-semibold text-silver-bright">{{ summary.margin.toFixed(1) }}%</div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="card-elegant p-6">
                <h3 class="mb-4 font-serif text-lg font-medium text-cream">Egresos por categoría</h3>
                <div class="space-y-3">
                    <div v-for="row in expensesByCategory" :key="row.expense_category_id" class="flex items-center gap-3">
                        <span class="w-44 truncate text-sm text-pearl">{{ row.category?.name }}</span>
                        <div class="h-6 flex-1 overflow-hidden rounded-full bg-graphite border border-smoke">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-red-400 to-red-600"
                                :style="{
                                    width: Math.min(100, (Number(row.total) / (Math.max(...expensesByCategory.map(r => Number(r.total) || 0)) || 1)) * 100) + '%'
                                }"
                            ></div>
                        </div>
                        <span class="w-28 text-right font-serif text-sm font-semibold text-cream">{{ formatPrice(row.total) }}</span>
                    </div>
                    <div v-if="!expensesByCategory.length" class="py-6 text-center text-sm text-mercury">Sin egresos en el período</div>
                </div>
            </div>

            <div class="card-elegant p-6">
                <h3 class="mb-4 font-serif text-lg font-medium text-cream">Flujo diario</h3>
                <div class="space-y-2">
                    <div v-for="day in dailyData.slice(-15)" :key="day.date" class="flex items-center gap-2">
                        <span class="w-16 text-[10px] uppercase text-mercury">{{ new Date(day.date).toLocaleDateString('es-MX', { day: 'numeric', month: 'short' }) }}</span>
                        <div class="flex h-6 flex-1 gap-0.5">
                            <div v-if="day.income > 0" class="bg-emerald-500 rounded" :style="{ width: (Number(day.income) / maxDaily) * 50 + '%', minWidth: '4px' }" :title="`Ingreso: ${formatPrice(day.income)}`"></div>
                            <div v-if="day.expense > 0" class="bg-red-500 rounded" :style="{ width: (Number(day.expense) / maxDaily) * 50 + '%', minWidth: '4px' }" :title="`Egreso: ${formatPrice(day.expense)}`"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-xs text-mercury">
                    <span class="flex items-center gap-1"><span class="h-3 w-3 rounded bg-emerald-500"></span> Ingresos</span>
                    <span class="flex items-center gap-1"><span class="h-3 w-3 rounded bg-red-500"></span> Egresos</span>
                </div>
            </div>
        </div>
    </div>
</template>