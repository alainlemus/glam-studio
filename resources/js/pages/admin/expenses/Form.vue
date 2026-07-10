<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Wallet } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    expense?: any;
    categories: any[];
    branches: any[];
}>();

const form = useForm({
    expense_category_id: props.expense?.expense_category_id || '',
    branch_id: props.expense?.branch_id || '',
    description: props.expense?.description || '',
    amount: props.expense?.amount || '',
    expense_date: props.expense?.expense_date?.split('T')[0] || new Date().toISOString().split('T')[0],
    payment_method: props.expense?.payment_method || 'cash',
    receipt_number: props.expense?.receipt_number || '',
    notes: props.expense?.notes || '',
});

const submit = () => {
    if (props.expense) form.put(`/admin/expenses/${props.expense.id}`);
    else form.post('/admin/expenses');
};
</script>

<template>
    <Head :title="expense ? 'Editar egreso' : 'Nuevo egreso'" />

    <div class="mx-auto max-w-2xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/expenses" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Finanzas</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ expense ? 'Editar' : 'Nuevo' }} egreso</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-4 p-6">
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción *</label>
                <input v-model="form.description" required class="input-elegant" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Categoría *</label>
                    <select v-model="form.expense_category_id" required class="input-elegant appearance-none">
                        <option value="">Selecciona...</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal</label>
                    <select v-model="form.branch_id" class="input-elegant appearance-none">
                        <option value="">General</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Monto *</label>
                    <input v-model="form.amount" type="number" step="0.01" min="0" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Fecha *</label>
                    <input v-model="form.expense_date" type="date" required class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Método de pago</label>
                    <select v-model="form.payment_method" class="input-elegant appearance-none">
                        <option value="cash">Efectivo</option>
                        <option value="card">Tarjeta</option>
                        <option value="transfer">Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nº Recibo</label>
                    <input v-model="form.receipt_number" class="input-elegant" />
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Notas</label>
                <textarea v-model="form.notes" rows="2" class="input-elegant"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <Link href="/admin/expenses" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Wallet class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>