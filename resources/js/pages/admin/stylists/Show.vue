<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    stylist: any;
    pendingCommissions: number;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="stylist.user?.name" />

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Link href="/admin/stylists" class="text-sm text-pink-600">← Volver</Link>
                <div class="mt-2 flex items-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-pink-200 to-purple-200 text-2xl">👩‍🎨</div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ stylist.user?.name }}</h1>
                        <p class="text-sm text-gray-500">{{ stylist.specialty }} · {{ stylist.branch?.name }}</p>
                    </div>
                </div>
            </div>
            <Link :href="`/admin/stylists/${stylist.id}/edit`" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Editar
            </Link>
        </div>

        <div class="grid gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 text-center shadow-sm">
                <div class="text-2xl">💵</div>
                <div class="mt-1 text-lg font-bold">{{ formatPrice(stylist.base_salary) }}</div>
                <div class="text-xs text-gray-500">Sueldo base</div>
            </div>
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-center shadow-sm">
                <div class="text-2xl">⏳</div>
                <div class="mt-1 text-lg font-bold text-yellow-700">{{ formatPrice(pendingCommissions) }}</div>
                <div class="text-xs text-gray-500">Comisiones pendientes</div>
            </div>
            <div class="rounded-xl border border-pink-200 bg-pink-50 p-4 text-center shadow-sm">
                <div class="text-2xl">💇</div>
                <div class="mt-1 text-lg font-bold text-pink-700">{{ stylist.service_commission }}%</div>
                <div class="text-xs text-gray-500">Comisión servicio</div>
            </div>
            <div class="rounded-xl border border-purple-200 bg-purple-50 p-4 text-center shadow-sm">
                <div class="text-2xl">📦</div>
                <div class="mt-1 text-lg font-bold text-purple-700">{{ stylist.product_commission }}%</div>
                <div class="text-xs text-gray-500">Comisión producto</div>
            </div>
        </div>

        <div v-if="stylist.commissions?.length" class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-4">
                <h3 class="font-semibold text-gray-900">Comisiones recientes</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <div v-for="comm in stylist.commissions" :key="comm.id" class="flex items-center gap-3 p-4">
                    <div class="text-sm text-gray-500">{{ new Date(comm.created_at).toLocaleDateString('es-MX') }}</div>
                    <span :class="['rounded-full px-2 py-1 text-xs', comm.type === 'service' ? 'bg-pink-100 text-pink-700' : 'bg-purple-100 text-purple-700']">
                        {{ comm.type }}
                    </span>
                    <div class="flex-1 text-sm">{{ comm.percentage }}%</div>
                    <div class="text-sm font-medium">{{ formatPrice(comm.amount) }}</div>
                    <span :class="['rounded-full px-2 py-1 text-xs font-medium', comm.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">
                        {{ comm.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>