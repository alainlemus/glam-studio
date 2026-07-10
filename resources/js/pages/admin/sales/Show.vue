<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Receipt,
    Printer,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    sale: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="`Venta ${sale.ticket_number}`" />

    <div class="mx-auto max-w-5xl space-y-6 p-4 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <Link href="/admin/sales" class="inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">
                    <ChevronLeft class="h-4 w-4" />
                    Volver
                </Link>
                <div class="mt-2 flex items-center gap-3">
                    <h1 class="font-serif text-3xl font-medium tracking-tight">Venta {{ sale.ticket_number }}</h1>
                    <span :class="['chip', sale.status === 'paid' ? 'bg-emerald-500/15 text-emerald-400' : sale.status === 'cancelled' ? 'bg-red-500/15 text-red-400' : 'bg-amber-500/15 text-amber-400']">
                        {{ sale.status }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-mercury">{{ new Date(sale.created_at).toLocaleString('es-MX') }}</p>
            </div>
            <Link :href="`/admin/sales/${sale.id}/ticket`" target="_blank" class="btn-accent-elegant h-11 px-5">
                <Printer class="h-4 w-4" />
                Imprimir ticket
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card-elegant p-6 lg:col-span-2">
                <h2 class="mb-4 font-serif text-lg font-medium text-cream">Items de la venta</h2>
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-smoke">
                            <th class="py-2 text-left text-xs uppercase tracking-wider text-mercury">Item</th>
                            <th class="py-2 text-right text-xs uppercase tracking-wider text-mercury">Cant.</th>
                            <th class="py-2 text-right text-xs uppercase tracking-wider text-mercury">Precio</th>
                            <th class="py-2 text-right text-xs uppercase tracking-wider text-mercury">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-smoke">
                        <tr v-for="item in sale.items" :key="item.id">
                            <td class="py-3">
                                <div class="font-medium text-cream">{{ item.name }}</div>
                                <div class="text-xs text-mercury">
                                    {{ item.itemable_type?.split('\\').pop() }}
                                    <span v-if="item.stylist"> · {{ item.stylist.user?.name }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-right text-pearl">{{ item.quantity }}</td>
                            <td class="py-3 text-right text-pearl">{{ formatPrice(item.unit_price) }}</td>
                            <td class="py-3 text-right">
                                <span class="font-serif text-lg font-semibold text-glitter">{{ formatPrice(item.subtotal) }}</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="pt-3 text-right text-sm text-mercury">Subtotal:</td><td class="pt-3 text-right text-sm font-medium text-pearl">{{ formatPrice(sale.subtotal) }}</td></tr>
                        <tr v-if="sale.discount > 0"><td colspan="3" class="text-right text-sm text-red-400">Descuento:</td><td class="text-right text-sm text-red-400">-{{ formatPrice(sale.discount) }}</td></tr>
                        <tr><td colspan="3" class="pt-3 text-right font-serif text-base font-medium text-cream">Total:</td><td class="pt-3 text-right font-serif text-3xl font-semibold text-glitter">{{ formatPrice(sale.total) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="space-y-4">
                <div class="card-elegant p-6">
                    <h3 class="font-serif text-lg font-medium text-cream">Información</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-mercury">Cliente</dt>
                            <dd class="font-medium text-cream">{{ sale.client?.name || 'Público general' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-mercury">Sucursal</dt>
                            <dd class="font-medium text-cream">{{ sale.branch?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-mercury">Estilista</dt>
                            <dd class="font-medium text-cream">{{ sale.stylist?.user?.name || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-mercury">Atendido por</dt>
                            <dd class="font-medium text-cream">{{ sale.user?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-mercury">Método de pago</dt>
                            <dd class="font-medium text-cream capitalize">{{ sale.payment_method }}</dd>
                        </div>
                        <div v-if="sale.appointment_id">
                            <dt class="text-xs uppercase tracking-wider text-mercury">Cita relacionada</dt>
                            <dd>
                                <Link :href="`/admin/appointments/${sale.appointment_id}`" class="font-mono text-sm text-silver-bright hover:text-silver-bright-bright">{{ sale.appointment?.code }}</Link>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>