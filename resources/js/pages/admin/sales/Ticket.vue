<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    sale: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="`Ticket ${sale.ticket_number}`" />

    <div class="mx-auto max-w-sm p-4">
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-4 font-mono text-xs" id="ticket">
            <div class="text-center">
                <div class="text-lg font-bold">SALONES BELLEZA</div>
                <div>{{ sale.branch?.name }}</div>
                <div>{{ sale.branch?.address }}</div>
                <div>Tel: {{ sale.branch?.phone }}</div>
            </div>

            <div class="my-3 border-t border-dashed border-gray-300 pt-2">
                <div>Ticket: <strong>{{ sale.ticket_number }}</strong></div>
                <div>Fecha: {{ new Date(sale.created_at).toLocaleString('es-MX') }}</div>
                <div>Cliente: {{ sale.client?.name || 'Público' }}</div>
                <div>Atendió: {{ sale.user?.name }}</div>
            </div>

            <div class="my-3 border-t border-dashed border-gray-300 pt-2">
                <div v-for="item in sale.items" :key="item.id" class="mb-1">
                    <div class="flex justify-between">
                        <span>{{ item.name }}</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span>{{ item.quantity }} x {{ formatPrice(item.unit_price) }}</span>
                        <span>{{ formatPrice(item.subtotal) }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-300 pt-2">
                <div class="flex justify-between"><span>Subtotal:</span><span>{{ formatPrice(sale.subtotal) }}</span></div>
                <div v-if="sale.discount > 0" class="flex justify-between"><span>Descuento:</span><span>-{{ formatPrice(sale.discount) }}</span></div>
                <div class="flex justify-between text-base font-bold"><span>TOTAL:</span><span>{{ formatPrice(sale.total) }}</span></div>
                <div class="mt-2 text-center">Método: {{ sale.payment_method }}</div>
            </div>

            <div class="mt-3 border-t border-dashed border-gray-300 pt-2 text-center">
                ¡Gracias por su preferencia! 💇‍♀️✨
            </div>
        </div>

        <div class="mt-4 flex justify-center gap-2 print:hidden">
            <button onclick="window.print()" class="rounded-lg bg-pink-600 px-4 py-2 text-sm font-medium text-white">
                🖨️ Imprimir
            </button>
            <a href="/admin/sales" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">
                Volver
            </a>
        </div>
    </div>
</template>

<style>
@media print {
    body { background: white !important; }
    .print\:hidden { display: none !important; }
    aside, header { display: none !important; }
}
</style>