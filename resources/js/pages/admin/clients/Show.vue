<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    client: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="client.name" />

    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <Link href="/admin/clients" class="text-sm text-pink-600">← Volver</Link>
            <div class="mt-1 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-pink-200 to-purple-200 text-2xl">
                    👤
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ client.name }}</h1>
                    <p class="text-sm text-gray-500">
                        <a v-if="client.phone" :href="`https://wa.me/${client.phone.replace(/\D/g, '')}`" target="_blank" class="text-green-600 hover:text-green-700">
                            {{ client.phone }} 📱
                        </a>
                        <span v-if="client.email"> · {{ client.email }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Stats -->
            <div class="space-y-3">
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-center shadow-sm">
                    <div class="text-3xl">📅</div>
                    <div class="mt-1 text-2xl font-bold">{{ client.appointments?.length || 0 }}</div>
                    <div class="text-xs text-gray-500">Citas registradas</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-center shadow-sm">
                    <div class="text-3xl">⚠️</div>
                    <div class="mt-1 text-2xl font-bold" :class="client.no_show_count >= 3 ? 'text-red-600' : 'text-gray-900'">
                        {{ client.no_show_count }}
                    </div>
                    <div class="text-xs text-gray-500">No-shows</div>
                </div>
            </div>

            <!-- Loyalty card -->
            <div class="rounded-xl border border-pink-200 bg-gradient-to-br from-pink-50 to-purple-50 p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Tarjeta de Lealtad</h2>
                <div v-if="client.loyalty_card" class="mt-4">
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Código</div>
                        <div class="font-mono text-sm font-bold">{{ client.loyalty_card.code }}</div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span>Progreso</span>
                            <span class="font-medium">{{ client.loyalty_card.stamps_current }}/{{ client.loyalty_card.stamps_required }}</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full bg-gradient-to-r from-pink-500 to-purple-600" :style="{ width: client.loyalty_card.progress_percent + '%' }"></div>
                        </div>
                    </div>
                    <div class="mt-3 text-center text-xs text-gray-500">
                        Recompensas canjeadas: {{ client.loyalty_card.total_rewards_claimed }}
                    </div>
                </div>
                <div v-else class="mt-4 text-center text-sm text-gray-500">
                    Sin tarjeta de lealtad
                </div>
            </div>

            <!-- Notes -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Información</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="text-gray-500">Cumpleaños:</dt><dd>{{ client.birthday ? new Date(client.birthday).toLocaleDateString('es-MX') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Estatus:</dt><dd>{{ client.is_blocked ? 'Bloqueado' : 'Activo' }}</dd></div>
                    <div v-if="client.notes"><dt class="text-gray-500">Notas:</dt><dd>{{ client.notes }}</dd></div>
                </dl>
            </div>
        </div>

        <!-- Citas recientes -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-4">
                <h2 class="text-base font-semibold text-gray-900">Citas recientes</h2>
            </div>
            <div v-if="!client.appointments?.length" class="p-8 text-center text-sm text-gray-500">Sin citas</div>
            <div v-else class="divide-y divide-gray-100">
                <div v-for="appt in client.appointments" :key="appt.id" class="flex items-center gap-3 p-4">
                    <div class="text-sm text-gray-500">{{ new Date(appt.date).toLocaleDateString('es-MX') }}</div>
                    <div class="flex-1 text-sm font-medium">{{ appt.services?.[0]?.service?.name }}</div>
                    <div class="text-sm text-gray-500">{{ appt.branch?.name }}</div>
                    <div class="text-sm font-medium">{{ formatPrice(appt.total) }}</div>
                    <Link :href="`/admin/appointments/${appt.id}`" class="text-sm text-pink-600">Ver</Link>
                </div>
            </div>
        </div>
    </div>
</template>