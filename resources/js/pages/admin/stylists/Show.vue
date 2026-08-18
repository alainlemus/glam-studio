<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Scissors, ChevronLeft, DollarSign, MapPin, Briefcase, TrendingUp, Package, Calendar, CheckCircle2, Clock, Edit } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    stylist: any;
    pendingCommissions: number;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const commissionTypeConfig: Record<string, { label: string; class: string }> = {
    service: { label: 'Servicio', class: 'bg-purple-500/15 text-purple-400 border-purple-500/30' },
    product: { label: 'Producto', class: 'bg-blue-500/15 text-blue-400 border-blue-500/30' },
};

const commissionStatusConfig: Record<string, { label: string; class: string; icon: any }> = {
    paid: { label: 'Pagada', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30', icon: CheckCircle2 },
    pending: { label: 'Pendiente', class: 'bg-amber-500/15 text-amber-400 border-amber-500/30', icon: Clock },
};
</script>

<template>
    <Head :title="stylist.user?.name" />

    <div class="mx-auto max-w-7xl space-y-6 p-4 lg:p-8">
        <!-- Header con gradiente -->
        <div class="relative overflow-hidden rounded-2xl border border-smoke bg-gradient-to-br from-graphite via-ink to-graphite p-8 animate-fade-in">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-purple-500/10 to-transparent blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-gradient-to-tl from-pink-500/5 to-transparent blur-3xl"></div>

            <div class="relative">
                <Link href="/admin/stylists" class="inline-flex items-center gap-2 text-sm font-medium text-silver-bright transition-colors hover:text-cream">
                    <ChevronLeft class="h-4 w-4" />
                    Volver a estilistas
                </Link>
                <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-start gap-6">
                        <div class="group relative flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/20 to-purple-500/10 shadow-lg shadow-purple-500/10 transition-all duration-300 hover:scale-105">
                            <Scissors class="h-10 w-10 text-purple-400" />
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </div>
                        <div class="flex-1">
                            <h1 class="font-serif text-3xl font-bold text-cream lg:text-4xl">{{ stylist.user?.name }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-2 rounded-lg border border-purple-500/30 bg-purple-500/10 px-3 py-1.5 text-sm font-medium text-purple-400">
                                    <Briefcase class="h-3.5 w-3.5" />
                                    {{ stylist.specialty }}
                                </span>
                                <span class="inline-flex items-center gap-2 text-sm text-pearl">
                                    <MapPin class="h-3.5 w-3.5" />
                                    {{ stylist.branch?.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <Link
                        :href="`/admin/stylists/${stylist.id}/edit`"
                        class="group relative overflow-hidden rounded-lg border border-silver/30 bg-gradient-to-r from-silver/10 to-silver/5 px-4 py-2.5 text-sm font-medium text-silver-bright shadow-lg transition-all duration-300 hover:scale-105 hover:border-silver/50 hover:shadow-xl hover:shadow-silver/10"
                    >
                        <div class="absolute inset-0 bg-gradient-to-r from-silver/20 to-silver/10 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        <span class="relative inline-flex items-center gap-2">
                            <Edit class="h-4 w-4" />
                            Editar
                        </span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-emerald-500/10 animate-fade-in" style="animation-delay: 0ms">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="relative">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-emerald-500/30 bg-emerald-500/10 shadow-lg shadow-emerald-500/10">
                        <DollarSign class="h-7 w-7 text-emerald-400" />
                    </div>
                    <div class="mt-4 font-serif text-3xl font-bold text-cream">{{ formatPrice(stylist.base_salary) }}</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Sueldo base</div>
                </div>
            </div>
            <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-amber-500/10 animate-fade-in" style="animation-delay: 100ms">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-amber-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="relative">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-amber-500/30 bg-amber-500/10 shadow-lg shadow-amber-500/10">
                        <Clock class="h-7 w-7 text-amber-400" />
                    </div>
                    <div class="mt-4 font-serif text-3xl font-bold text-amber-400">{{ formatPrice(pendingCommissions) }}</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Comisiones pendientes</div>
                </div>
            </div>
            <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-purple-500/10 animate-fade-in" style="animation-delay: 200ms">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-purple-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="relative">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-purple-500/30 bg-purple-500/10 shadow-lg shadow-purple-500/10">
                        <Scissors class="h-7 w-7 text-purple-400" />
                    </div>
                    <div class="mt-4 font-serif text-3xl font-bold text-purple-400">{{ stylist.service_commission }}%</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Comisión servicio</div>
                </div>
            </div>
            <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/10 animate-fade-in" style="animation-delay: 300ms">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="relative">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-blue-500/30 bg-blue-500/10 shadow-lg shadow-blue-500/10">
                        <Package class="h-7 w-7 text-blue-400" />
                    </div>
                    <div class="mt-4 font-serif text-3xl font-bold text-blue-400">{{ stylist.product_commission }}%</div>
                    <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Comisión producto</div>
                </div>
            </div>
        </div>

        <!-- Comisiones recientes -->
        <div v-if="stylist.commissions?.length" class="card-elegant group relative overflow-hidden animate-fade-in" style="animation-delay: 400ms">
            <div class="absolute -right-16 top-0 h-64 w-64 rounded-full bg-gradient-to-bl from-gold/5 to-transparent blur-3xl"></div>
            <div class="relative border-b border-smoke/70 p-6">
                <div class="flex items-center gap-3">
                    <TrendingUp class="h-5 w-5 text-gold" />
                    <h2 class="font-serif text-xl font-medium text-cream">Comisiones recientes</h2>
                </div>
            </div>
            <div v-if="!stylist.commissions?.length" class="relative p-12 text-center">
                <TrendingUp class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">Sin comisiones registradas</p>
            </div>
            <div v-else class="relative divide-y divide-smoke/50">
                <div
                    v-for="comm in stylist.commissions"
                    :key="comm.id"
                    class="flex flex-wrap items-center gap-4 p-5 transition-colors hover:bg-graphite"
                >
                    <div class="flex items-center gap-2 text-sm text-pearl">
                        <Calendar class="h-4 w-4" />
                        {{ new Date(comm.created_at).toLocaleDateString('es-MX') }}
                    </div>
                    <span :class="['inline-flex rounded-lg border px-2.5 py-1 text-xs font-medium', commissionTypeConfig[comm.type]?.class]">
                        {{ commissionTypeConfig[comm.type]?.label }}
                    </span>
                    <div class="flex items-center gap-2 text-sm text-pearl">
                        <TrendingUp class="h-3.5 w-3.5" />
                        {{ comm.percentage }}%
                    </div>
                    <div class="flex-1 text-right font-serif text-lg font-semibold text-gold lg:flex-none">{{ formatPrice(comm.amount) }}</div>
                    <component
                        :is="commissionStatusConfig[comm.status]?.icon"
                        class="h-4 w-4"
                    />
                    <span :class="['inline-flex items-center gap-2 rounded-lg border px-2.5 py-1 text-xs font-medium', commissionStatusConfig[comm.status]?.class]">
                        {{ commissionStatusConfig[comm.status]?.label }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>