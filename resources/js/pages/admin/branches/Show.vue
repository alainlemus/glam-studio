<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    branch: any;
}>();
</script>

<template>
    <Head :title="branch.name" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Link href="/admin/branches" class="text-sm text-pink-600 hover:text-pink-700">← Volver</Link>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ branch.name }}</h1>
                <p class="text-sm text-gray-500">{{ branch.city?.name }}</p>
            </div>
            <Link :href="`/admin/branches/${branch.id}/edit`" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Editar
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Información</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Dirección:</dt><dd class="font-medium">{{ branch.address }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Teléfono:</dt><dd class="font-medium">{{ branch.phone }}</dd></div>
                    <div v-if="branch.whatsapp" class="flex justify-between"><dt class="text-gray-500">WhatsApp:</dt><dd class="font-medium">{{ branch.whatsapp }}</dd></div>
                    <div v-if="branch.email" class="flex justify-between"><dt class="text-gray-500">Email:</dt><dd class="font-medium">{{ branch.email }}</dd></div>
                    <div v-if="branch.manager_name" class="flex justify-between"><dt class="text-gray-500">Gerente:</dt><dd class="font-medium">{{ branch.manager_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Horario:</dt><dd class="font-medium">{{ branch.opening_time?.slice(0,5) }} - {{ branch.closing_time?.slice(0,5) }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Estadísticas</h2>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="rounded-lg bg-pink-50 p-4 text-center">
                        <div class="text-2xl font-bold text-pink-600">{{ branch.stylists?.length || 0 }}</div>
                        <div class="text-xs text-gray-600">Estilistas</div>
                    </div>
                    <div class="rounded-lg bg-purple-50 p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ branch.product_stocks?.length || 0 }}</div>
                        <div class="text-xs text-gray-600">Productos en stock</div>
                    </div>
                </div>
            </div>

            <div v-if="branch.stylists?.length" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Estilistas</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="stylist in branch.stylists" :key="stylist.id" :href="`/admin/stylists/${stylist.id}`" class="rounded-lg border border-gray-200 p-3 hover:border-pink-300">
                        <div class="font-medium text-gray-900">{{ stylist.user?.name }}</div>
                        <div class="text-xs text-gray-500">{{ stylist.specialty }}</div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>