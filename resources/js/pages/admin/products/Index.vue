<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, Package, ChevronDown } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    products: any;
    categories: any[];
    filters: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');

const filter = () => {
    router.get('/admin/products', {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <Head title="Productos" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Catálogo</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Productos</h2>
                <p class="mt-1 text-sm text-mercury">{{ products.total }} productos en catálogo</p>
            </div>
            <Link href="/admin/products/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo producto
            </Link>
        </div>

        <div class="flex gap-3 rounded-xl border border-smoke bg-card p-4">
            <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                <input v-model="search" type="text" placeholder="Buscar por nombre o SKU..." class="input-elegant pl-10" @input="filter" />
            </div>
            <select v-model="categoryId" class="input-elegant appearance-none pr-9" @change="filter">
                <option value="">Todas las categorías</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <ChevronDown class="pointer-events-none -ml-7 h-4 w-4 self-center text-mercury" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div v-for="product in products.data" :key="product.id" class="card-elegant card-elegant-hover p-5 transition hover:-translate-y-1">
                <div class="mb-4 flex aspect-square items-center justify-center rounded-lg border border-smoke bg-gradient-to-br from-graphite to-ink">
                    <Package class="h-12 w-12 text-silver/40" />
                </div>

                <div class="text-eyebrow">{{ product.category?.name }}</div>
                <h3 class="mt-1 truncate font-serif text-base font-semibold text-cream">{{ product.name }}</h3>
                <div v-if="product.sku" class="mt-0.5 font-mono text-[10px] text-mercury">{{ product.sku }}</div>

                <div class="mt-4 flex items-end justify-between border-t border-smoke pt-3">
                    <div>
                        <div class="font-serif text-2xl font-semibold text-glitter">{{ formatPrice(product.price) }}</div>
                        <div class="text-[10px] uppercase tracking-wider text-mercury">Precio venta</div>
                    </div>
                    <div :class="[
                        'text-right text-xs',
                        Number(product.stocks_sum_stock) <= product.min_stock ? 'text-red-400' : 'text-emerald-400'
                    ]">
                        <div class="font-serif text-lg font-semibold">{{ product.stocks_sum_stock || 0 }}</div>
                        <div class="text-[10px] uppercase tracking-wider text-mercury">Stock total</div>
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <Link :href="`/admin/products/${product.id}/edit`" class="flex-1 rounded-md border border-smoke bg-graphite py-2 text-center text-xs font-medium text-pearl transition hover:border-silver/40 hover:bg-silver/10 hover:text-silver-bright">
                        Editar
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="products.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in products.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>