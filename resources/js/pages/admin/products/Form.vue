<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Package } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    product?: any;
    categories: any[];
    branches?: any[];
}>();

const form = useForm({
    product_category_id: props.product?.product_category_id || '',
    name: props.product?.name || '',
    sku: props.product?.sku || '',
    description: props.product?.description || '',
    cost: props.product?.cost || 0,
    price: props.product?.price || '',
    commission_percentage: props.product?.commission_percentage || 10,
    min_stock: props.product?.min_stock || 5,
    is_active: props.product?.is_active ?? true,
    initial_stock: {} as Record<number, number>,
});

const submit = () => {
    if (props.product) form.put(`/admin/products/${props.product.id}`);
    else form.post('/admin/products');
};

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="product ? 'Editar producto' : 'Nuevo producto'" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/products" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Catálogo</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ product ? 'Editar' : 'Nuevo' }} producto</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="card-elegant p-6 space-y-4">
                <h3 class="font-serif text-lg font-medium text-cream">Información general</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                        <input v-model="form.name" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Categoría *</label>
                        <select v-model="form.product_category_id" required class="input-elegant appearance-none">
                            <option value="">Selecciona...</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">SKU</label>
                        <input v-model="form.sku" class="input-elegant" placeholder="Auto-generado" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descripción</label>
                        <textarea v-model="form.description" rows="2" class="input-elegant"></textarea>
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6 space-y-4">
                <h3 class="font-serif text-lg font-medium text-cream">Precios e inventario</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Costo</label>
                        <input v-model="form.cost" type="number" step="0.01" min="0" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Precio venta *</label>
                        <input v-model="form.price" type="number" step="0.01" min="0" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">% Comisión</label>
                        <input v-model="form.commission_percentage" type="number" step="0.01" min="0" max="100" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Stock mínimo</label>
                        <input v-model="form.min_stock" type="number" min="0" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estatus</label>
                        <select v-model="form.is_active" class="input-elegant appearance-none">
                            <option :value="true">Activo</option>
                            <option :value="false">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div v-if="branches && !product" class="card-elegant p-6">
                <h3 class="mb-3 font-serif text-lg font-medium text-cream">Stock inicial</h3>
                <div class="space-y-2">
                    <div v-for="branch in branches" :key="branch.id" class="flex items-center gap-3">
                        <span class="flex-1 text-sm text-cream">{{ branch.name }}</span>
                        <input v-model.number="form.initial_stock[branch.id]" type="number" min="0" placeholder="0" class="w-24 input-elegant text-center" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/products" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Package class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>