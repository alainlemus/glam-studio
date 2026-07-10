<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Plus, Trash2, ShoppingCart, Calculator } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    clients: any[];
    branches: any[];
    services: any[];
    products: any[];
    stylists: any[];
    appointment: any;
}>();

const form = useForm({
    client_id: props.appointment?.client_id || '',
    branch_id: props.appointment?.branch_id || '',
    stylist_id: props.appointment?.stylist_id || '',
    appointment_id: props.appointment?.id || '',
    payment_method: 'cash',
    discount: 0,
    notes: '',
    items: props.appointment?.services?.map((s: any) => ({
        type: 'service', id: s.service_id, quantity: 1, stylist_id: s.stylist_id,
    })) || [],
});

const addItem = (type: 'service' | 'product') => {
    form.items.push({ type, id: 0, quantity: 1, stylist_id: form.stylist_id });
};

const removeItem = (idx: number) => form.items.splice(idx, 1);

const subtotal = computed(() => {
    return form.items.reduce((sum: number, item: any) => {
        const models = item.type === 'service' ? props.services : props.products;
        const model = models.find((m: any) => m.id == item.id);
        return sum + (model ? Number(model.price) * item.quantity : 0);
    }, 0);
});

const total = computed(() => subtotal.value - Number(form.discount || 0));

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const submit = () => form.post('/admin/sales');
</script>

<template>
    <Head title="Nueva venta" />

    <div class="mx-auto max-w-5xl space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Comercial</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Nueva venta</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="card-elegant p-6">
                <h3 class="mb-4 font-serif text-lg font-medium text-cream">Datos generales</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Cliente</label>
                        <select v-model="form.client_id" class="input-elegant appearance-none">
                            <option value="">Público general</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal *</label>
                        <select v-model="form.branch_id" required class="input-elegant appearance-none">
                            <option value="">Selecciona...</option>
                            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estilista principal</label>
                        <select v-model="form.stylist_id" class="input-elegant appearance-none">
                            <option value="">Sin asignar</option>
                            <option v-for="s in stylists" :key="s.id" :value="s.id">{{ s.user?.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Método de pago *</label>
                        <select v-model="form.payment_method" class="input-elegant appearance-none">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="mixed">Mixto</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Descuento</label>
                        <input v-model="form.discount" type="number" step="0.01" min="0" class="input-elegant" />
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-serif text-lg font-medium text-cream">Items</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="addItem('service')" class="rounded-md border border-silver/30 bg-silver/10 px-3 py-1.5 text-xs font-medium text-silver-bright hover:bg-silver/20">
                            + Servicio
                        </button>
                        <button type="button" @click="addItem('product')" class="rounded-md border border-silver/30 bg-silver/10 px-3 py-1.5 text-xs font-medium text-silver-bright hover:bg-silver/20">
                            + Producto
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <div v-for="(item, idx) in form.items" :key="idx" class="flex items-center gap-2 rounded-lg border border-smoke bg-graphite p-3">
                        <span :class="['chip text-xs', item.type === 'service' ? 'bg-silver/15 text-silver-bright' : 'bg-silver/15 text-silver-bright']">
                            {{ item.type }}
                        </span>
                        <select v-model="item.id" required class="flex-1 input-elegant appearance-none">
                            <option value="0">Selecciona...</option>
                            <option v-if="item.type === 'service'" v-for="s in services" :key="s.id" :value="s.id">{{ s.name }} - {{ formatPrice(s.price) }}</option>
                            <option v-if="item.type === 'product'" v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} - {{ formatPrice(p.price) }}</option>
                        </select>
                        <input v-model="item.quantity" type="number" min="1" class="w-20 input-elegant text-center" />
                        <select v-model="item.stylist_id" class="w-36 input-elegant appearance-none">
                            <option value="">Estilista...</option>
                            <option v-for="s in stylists" :key="s.id" :value="s.id">{{ s.user?.name }}</option>
                        </select>
                        <button type="button" @click="removeItem(idx)" class="rounded-md p-2 text-red-400 hover:bg-red-500/10">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                    <div v-if="!form.items.length" class="rounded-lg border-2 border-dashed border-smoke bg-graphite p-8 text-center text-sm text-mercury">
                        Agrega servicios o productos para iniciar la venta
                    </div>
                </div>

                <div class="mt-5 flex items-end justify-between border-t border-smoke pt-5">
                    <div class="space-y-1 text-sm">
                        <div class="flex gap-6 text-mercury">
                            <span>Subtotal: <span class="font-medium text-cream">{{ formatPrice(subtotal) }}</span></span>
                            <span>Descuento: <span class="font-medium text-cream">{{ formatPrice(form.discount) }}</span></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs uppercase tracking-wider text-mercury">Total</div>
                        <div class="font-serif text-4xl font-semibold text-glitter">{{ formatPrice(total) }}</div>
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6">
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Notas</label>
                <textarea v-model="form.notes" rows="2" class="input-elegant"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="/admin/sales" class="btn-ghost-elegant h-12 px-6">Cancelar</a>
                <button type="submit" :disabled="form.processing || !form.items.length" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <ShoppingCart class="h-4 w-4" />
                    {{ form.processing ? 'Procesando...' : 'Registrar venta' }}
                </button>
            </div>
        </form>
    </div>
</template>