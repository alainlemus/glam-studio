<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Receipt,
    Printer,
    User,
    MapPin,
    Scissors,
    CreditCard,
    Calendar,
    ShoppingCart,
    DollarSign,
    CheckCircle2,
    XCircle,
    Clock,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    sale: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const statusConfig: Record<string, { label: string; class: string; icon: any }> = {
    paid: { label: 'Pagada', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30', icon: CheckCircle2 },
    pending: { label: 'Pendiente', class: 'bg-amber-500/15 text-amber-400 border-amber-500/30', icon: Clock },
    cancelled: { label: 'Cancelada', class: 'bg-red-500/15 text-red-400 border-red-500/30', icon: XCircle },
};
</script>

<template>
    <Head :title="`Venta ${sale.ticket_number}`" />

    <div class="mx-auto max-w-7xl space-y-6 p-4 lg:p-8">
        <!-- Header con gradiente -->
        <div class="relative overflow-hidden rounded-2xl border border-smoke bg-gradient-to-br from-graphite via-ink to-graphite p-8 animate-fade-in">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-emerald-500/10 to-transparent blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-gradient-to-tl from-gold/5 to-transparent blur-3xl"></div>

            <div class="relative">
                <Link href="/admin/sales" class="inline-flex items-center gap-2 text-sm font-medium text-silver-bright transition-colors hover:text-cream">
                    <ChevronLeft class="h-4 w-4" />
                    Volver a ventas
                </Link>
                <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-start gap-6">
                        <div class="group relative flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/10 shadow-lg shadow-emerald-500/10 transition-all duration-300 hover:scale-105">
                            <Receipt class="h-10 w-10 text-emerald-400" />
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </div>
                        <div class="flex-1">
                            <h1 class="font-serif text-3xl font-bold text-cream lg:text-4xl">{{ sale.ticket_number }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <component
                                    :is="statusConfig[sale.status]?.icon"
                                    class="h-4 w-4"
                                />
                                <span :class="['inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm font-medium', statusConfig[sale.status]?.class]">
                                    {{ statusConfig[sale.status]?.label }}
                                </span>
                                <span class="inline-flex items-center gap-2 text-sm text-pearl">
                                    <Calendar class="h-3.5 w-3.5" />
                                    {{ new Date(sale.created_at).toLocaleString('es-MX') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <Link
                        :href="`/admin/sales/${sale.id}/ticket`"
                        target="_blank"
                        class="group relative overflow-hidden rounded-lg border border-silver/30 bg-gradient-to-r from-silver/10 to-silver/5 px-4 py-2.5 text-sm font-medium text-silver-bright shadow-lg transition-all duration-300 hover:scale-105 hover:border-silver/50 hover:shadow-xl hover:shadow-silver/10"
                    >
                        <div class="absolute inset-0 bg-gradient-to-r from-silver/20 to-silver/10 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        <span class="relative inline-flex items-center gap-2">
                            <Printer class="h-4 w-4" />
                            Imprimir ticket
                        </span>
                    </Link>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Items de la venta -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in lg:col-span-2" style="animation-delay: 100ms">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-gradient-to-br from-gold/5 to-transparent blur-3xl"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center gap-3">
                        <ShoppingCart class="h-5 w-5 text-gold" />
                        <h2 class="font-serif text-xl font-medium text-cream">Items de la venta</h2>
                    </div>
                    <div class="overflow-hidden rounded-lg border border-smoke">
                        <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-smoke bg-graphite">
                                    <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider text-mercury">Item</th>
                                    <th class="py-3 px-4 text-right text-xs font-medium uppercase tracking-wider text-mercury">Cant.</th>
                                    <th class="py-3 px-4 text-right text-xs font-medium uppercase tracking-wider text-mercury">Precio</th>
                                    <th class="py-3 px-4 text-right text-xs font-medium uppercase tracking-wider text-mercury">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-smoke bg-ink">
                                <tr v-for="item in sale.items" :key="item.id" class="transition-colors hover:bg-graphite">
                                    <td class="py-4 px-4">
                                        <div class="font-medium text-cream">{{ item.name }}</div>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-mercury">
                                            <span class="rounded border border-smoke/50 bg-graphite px-2 py-0.5">
                                                {{ item.itemable_type?.split('\\').pop() }}
                                            </span>
                                            <span v-if="item.stylist" class="flex items-center gap-1">
                                                <Scissors class="h-3 w-3" />
                                                {{ item.stylist.user?.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="inline-flex items-center justify-center rounded-full bg-graphite px-2.5 py-1 text-sm font-medium text-pearl">{{ item.quantity }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right text-pearl">{{ formatPrice(item.unit_price) }}</td>
                                    <td class="py-4 px-4 text-right">
                                        <span class="font-serif text-lg font-semibold text-gold">{{ formatPrice(item.subtotal) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t border-smoke bg-graphite">
                                <tr>
                                    <td colspan="3" class="py-3 px-4 text-right text-sm text-mercury">Subtotal:</td>
                                    <td class="py-3 px-4 text-right text-sm font-medium text-pearl">{{ formatPrice(sale.subtotal) }}</td>
                                </tr>
                                <tr v-if="sale.discount > 0">
                                    <td colspan="3" class="py-2 px-4 text-right text-sm text-red-400">Descuento:</td>
                                    <td class="py-2 px-4 text-right text-sm font-medium text-red-400">-{{ formatPrice(sale.discount) }}</td>
                                </tr>
                                <tr class="border-t border-emerald-500/20 bg-emerald-500/5">
                                    <td colspan="3" class="py-4 px-4 text-right font-serif text-lg font-medium text-emerald-400">Total:</td>
                                    <td class="py-4 px-4 text-right font-serif text-3xl font-semibold text-emerald-400">{{ formatPrice(sale.total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="space-y-4">
                <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-silver/5" style="animation-delay: 200ms">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-2xl"></div>
                    <div class="relative">
                        <h3 class="font-serif text-lg font-medium text-cream">Información</h3>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3 transition-colors hover:border-silver/30">
                                <dt class="flex items-center gap-2 text-mercury">
                                    <User class="h-4 w-4" />
                                    Cliente:
                                </dt>
                                <dd class="font-medium text-cream">{{ sale.client?.name || 'Público general' }}</dd>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3 transition-colors hover:border-silver/30">
                                <dt class="flex items-center gap-2 text-mercury">
                                    <MapPin class="h-4 w-4" />
                                    Sucursal:
                                </dt>
                                <dd class="font-medium text-cream">{{ sale.branch?.name }}</dd>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3 transition-colors hover:border-silver/30">
                                <dt class="flex items-center gap-2 text-mercury">
                                    <Scissors class="h-4 w-4" />
                                    Estilista:
                                </dt>
                                <dd class="font-medium text-cream">{{ sale.stylist?.user?.name || '—' }}</dd>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3 transition-colors hover:border-silver/30">
                                <dt class="flex items-center gap-2 text-mercury">
                                    <User class="h-4 w-4" />
                                    Atendido por:
                                </dt>
                                <dd class="font-medium text-cream">{{ sale.user?.name }}</dd>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3 transition-colors hover:border-silver/30">
                                <dt class="flex items-center gap-2 text-mercury">
                                    <CreditCard class="h-4 w-4" />
                                    Método de pago:
                                </dt>
                                <dd class="font-medium capitalize text-cream">{{ sale.payment_method }}</dd>
                            </div>
                            <div v-if="sale.appointment_id" class="rounded-lg border border-blue-500/20 bg-blue-500/5 p-3">
                                <dt class="mb-2 flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-blue-400">
                                    <Calendar class="h-3.5 w-3.5" />
                                    Cita relacionada:
                                </dt>
                                <dd>
                                    <Link
                                        :href="`/admin/appointments/${sale.appointment_id}`"
                                        class="font-mono text-sm font-medium text-blue-400 transition-colors hover:text-blue-300"
                                    >
                                        {{ sale.appointment?.code }}
                                    </Link>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>