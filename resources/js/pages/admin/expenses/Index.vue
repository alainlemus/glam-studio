<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Wallet, Calendar, ChevronDown, AlertTriangle } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    expenses: any;
    categories: any[];
    branches: any[];
    filters: any;
    total: number;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');
const categoryId = ref(props.filters.category_id || '');
const branchId = ref(props.filters.branch_id || '');

const filter = () => {
    router.get('/admin/expenses', {
        from: from.value || undefined,
        to: to.value || undefined,
        category_id: categoryId.value || undefined,
        branch_id: branchId.value || undefined,
    }, { preserveState: true });
};

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar este egreso?',
        description: 'Esta acción no se puede deshacer.',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/expenses/${id}`);
};
</script>

<template>
    <Head title="Egresos" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Finanzas</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Egresos</h2>
                <p class="mt-1 text-sm text-mercury">{{ formatPrice(total) }} · {{ expenses.total }} registros</p>
            </div>
            <Link href="/admin/expenses/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo egreso
            </Link>
        </div>

        <div class="flex flex-wrap gap-3 rounded-xl border border-smoke bg-card p-4">
            <input v-model="from" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <input v-model="to" type="date" class="rounded-lg border border-smoke bg-graphite px-3 py-2.5 text-sm text-cream focus:border-silver focus:outline-none" @change="filter" />
            <select v-model="categoryId" class="input-elegant appearance-none pr-9" @change="filter">
                <option value="">Todas las categorías</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <select v-model="branchId" class="input-elegant appearance-none pr-9" @change="filter">
                <option value="">Todas las sucursales</option>
                <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Descripción</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Categoría</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Pago</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Monto</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-graphite/30">
                        <td class="px-5 py-4 text-sm text-pearl">{{ new Date(expense.expense_date).toLocaleDateString('es-MX') }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-cream">{{ expense.description }}</div>
                            <div v-if="expense.receipt_number" class="text-xs text-mercury">Recibo: {{ expense.receipt_number }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="chip bg-silver/10 text-xs text-silver-bright">{{ expense.category?.name }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ expense.branch?.name || '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="chip bg-graphite text-xs text-pearl capitalize">{{ expense.payment_method }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-serif text-lg font-semibold text-red-400">-{{ formatPrice(expense.amount) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <Link :href="`/admin/expenses/${expense.id}/edit`" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">Editar</Link>
                            <button @click="destroy(expense.id)" class="ml-3 text-sm font-medium text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <div v-if="expenses.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in expenses.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>