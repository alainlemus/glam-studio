<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    CalendarDays,
    TrendingUp,
    TrendingDown,
    DollarSign,
    Wallet,
    Users,
    Plus,
    ArrowRight,
    Sparkles,
    Crown,
    ChevronRight,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stats: any;
    upcomingAppointments: any[];
    topStylists: any[];
    salesLast7Days: any[];
    appointmentsByStatus: any;
    isAdmin: boolean;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(price);
};

const statusColors: Record<string, { bg: string; text: string; dot: string; label: string }> = {
    pending: { bg: 'bg-amber-500/15', text: 'text-amber-400', dot: 'bg-amber-400', label: 'Pendientes' },
    confirmed: { bg: 'bg-blue-500/15', text: 'text-blue-400', dot: 'bg-blue-400', label: 'Confirmadas' },
    in_progress: { bg: 'bg-purple-500/15', text: 'text-purple-400', dot: 'bg-purple-400', label: 'En curso' },
    completed: { bg: 'bg-emerald-500/15', text: 'text-emerald-400', dot: 'bg-emerald-400', label: 'Completadas' },
    cancelled: { bg: 'bg-red-500/15', text: 'text-red-400', dot: 'bg-red-400', label: 'Canceladas' },
    no_show: { bg: 'bg-zinc-500/15', text: 'text-zinc-400', dot: 'bg-zinc-400', label: 'No show' },
};

const maxSale = computed(() => {
    const totals = props.salesLast7Days?.map((d: any) => Number(d.total)) || [];
    return Math.max(1, ...totals);
});

const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Buenos días';
    if (h < 19) return 'Buenas tardes';
    return 'Buenas noches';
});

const today = new Date().toLocaleDateString('es-MX', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
});

const dayName = computed(() => {
    return new Date(props.salesLast7Days?.[0]?.date || new Date())
        .toLocaleDateString('es-MX', { weekday: 'short' });
});
</script>

<template>
    <Head title="Panel" />

    <div class="space-y-6 p-4 lg:p-8">
        <!-- Header saludo -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="animate-fade-in">
                <p class="text-eyebrow">{{ today }}</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight lg:text-4xl">
                    {{ greeting }}, <span class="italic text-glitter">{{ $page.props.auth.user?.name?.split(' ')[0] }}</span>
                </h2>
                <p class="mt-1 text-sm text-mercury">
                    Aquí tienes el resumen de hoy
                </p>
            </div>
            <div class="flex gap-2">
                <Link
                    href="/admin/appointments/calendar"
                    class="btn-ghost-elegant h-12 px-5 text-sm"
                >
                    <CalendarDays class="h-4 w-4" />
                    Ver agenda
                </Link>
                <Link href="/admin/appointments/create" class="btn-primary-elegant h-12 px-5 text-sm">
                    <Plus class="h-4 w-4" />
                    Nueva cita
                </Link>
            </div>
        </div>

        <!-- Métricas principales -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card-elegant card-elegant-hover group relative overflow-hidden p-6 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-silver/10 animate-fade-in" style="animation-delay: 0ms">
                <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-silver/5 blur-3xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-silver/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-silver/20 bg-silver/10 shadow-lg shadow-silver/10 backdrop-blur-sm transition-all duration-500 group-hover:scale-110 group-hover:border-silver/40">
                            <CalendarDays class="h-5 w-5 text-silver-bright transition-transform duration-500 group-hover:rotate-12" />
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-mercury">Citas hoy</p>
                    <p class="mt-1 font-serif text-4xl font-semibold text-cream transition-all duration-300 group-hover:text-silver-bright">{{ stats.appointmentsToday }}</p>
                    <p class="mt-2 text-xs text-mercury">
                        <span class="font-semibold text-silver-bright">{{ stats.pendingAppointments }}</span> por confirmar
                    </p>
                </div>
            </div>

            <div class="card-elegant card-elegant-hover group relative overflow-hidden p-6 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-emerald-500/20 animate-fade-in" style="animation-delay: 100ms">
                <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-emerald-500/30 bg-emerald-500/10 shadow-lg shadow-emerald-500/20 backdrop-blur-sm transition-all duration-500 group-hover:scale-110 group-hover:border-emerald-500/50">
                            <DollarSign class="h-5 w-5 text-emerald-400 transition-transform duration-500 group-hover:rotate-12" />
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-mercury">Ventas del mes</p>
                    <p class="mt-1 font-serif text-3xl font-semibold text-cream transition-all duration-300 group-hover:text-emerald-400">{{ formatPrice(stats.monthlySales) }}</p>
                    <p :class="['mt-2 inline-flex items-center gap-1 text-xs font-semibold', stats.salesGrowth >= 0 ? 'text-emerald-400' : 'text-red-400']">
                        <component :is="stats.salesGrowth >= 0 ? TrendingUp : TrendingDown" class="h-3 w-3" />
                        {{ stats.salesGrowth >= 0 ? '+' : '' }}{{ Math.abs(stats.salesGrowth).toFixed(1) }}% vs mes anterior
                    </p>
                </div>
            </div>

            <div class="card-elegant card-elegant-hover group relative overflow-hidden p-6 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-silver/10 animate-fade-in" style="animation-delay: 200ms">
                <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-silver-bright/5 blur-3xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-silver-bright/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-silver/20 bg-silver/10 shadow-lg shadow-silver/10 backdrop-blur-sm transition-all duration-500 group-hover:scale-110 group-hover:border-silver/40">
                            <TrendingUp class="h-5 w-5 text-silver-bright transition-transform duration-500 group-hover:rotate-12" />
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-mercury">Utilidad del mes</p>
                    <p :class="['mt-1 font-serif text-3xl font-semibold transition-all duration-300', stats.monthlyProfit >= 0 ? 'text-cream group-hover:text-silver-bright' : 'text-red-400']">
                        {{ formatPrice(stats.monthlyProfit) }}
                    </p>
                    <p class="mt-2 text-xs text-mercury">
                        Comisiones: <span class="font-semibold text-cream">{{ formatPrice(stats.monthlyCommissions) }}</span>
                    </p>
                </div>
            </div>

            <div class="card-elegant card-elegant-hover group relative overflow-hidden p-6 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-red-500/20 animate-fade-in" style="animation-delay: 300ms">
                <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-red-500/10 blur-3xl transition-all duration-500 group-hover:scale-150"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 shadow-lg shadow-red-500/20 backdrop-blur-sm transition-all duration-500 group-hover:scale-110 group-hover:border-red-500/50">
                            <Wallet class="h-5 w-5 text-red-400 transition-transform duration-500 group-hover:rotate-12" />
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-semibold uppercase tracking-wider text-mercury">Egresos del mes</p>
                    <p class="mt-1 font-serif text-3xl font-semibold text-cream transition-all duration-300 group-hover:text-red-400">{{ formatPrice(stats.monthlyExpenses) }}</p>
                    <p class="mt-2 text-xs text-mercury">
                        <span :class="stats.lowStockCount > 0 ? 'font-semibold text-red-400' : 'font-semibold text-cream'">{{ stats.lowStockCount }}</span> productos con stock bajo
                    </p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Ventas 7 días -->
            <div class="card-elegant group relative overflow-hidden p-6 lg:col-span-2 animate-fade-up transition-all duration-300 hover:shadow-xl hover:shadow-silver/5">
                <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-eyebrow">Tendencia</p>
                        <h3 class="mt-1 font-serif text-xl font-medium">Ventas últimos 7 días</h3>
                    </div>
                    <Sparkles class="h-5 w-5 text-silver/40 transition-transform duration-300 group-hover:rotate-12 group-hover:text-silver-bright" />
                </div>
                <div class="mt-8 space-y-3">
                    <div v-for="day in salesLast7Days" :key="day.date" class="flex items-center gap-3">
                        <span class="w-20 text-xs capitalize text-mercury">{{ day.day }}</span>
                        <div class="flex-1">
                            <div class="h-11 rounded-md bg-graphite border border-smoke overflow-hidden">
                                <div
                                    class="h-full rounded-md flex items-center justify-end px-3 transition-all duration-700 ease-out"
                                    :style="{
                                        width: maxSale > 0 ? `${Math.max(8, (Number(day.total) / maxSale) * 100)}%` : '8%',
                                        background: 'linear-gradient(90deg, #D4AF37 0%, #FBBF24 100%)',
                                    }"
                                >
                                    <span v-if="Number(day.total) > 0" class="text-[11px] font-semibold text-ink whitespace-nowrap">
                                        {{ formatPrice(Number(day.total)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatus de citas -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-up transition-all duration-300 hover:shadow-xl hover:shadow-silver/5">
                <div class="absolute -right-8 -top-8 h-48 w-48 rounded-full bg-gradient-to-br from-purple-500/5 to-transparent blur-3xl"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-eyebrow">Mes en curso</p>
                        <h3 class="mt-1 font-serif text-xl font-medium">Estado</h3>
                    </div>
                </div>
                <div class="mt-6 space-y-2">
                    <div
                        v-for="(config, key) in statusColors"
                        :key="key"
                        class="flex items-center justify-between rounded-lg border border-transparent p-3 transition hover:border-smoke hover:bg-graphite"
                    >
                        <div class="flex items-center gap-3">
                            <span :class="['h-2.5 w-2.5 rounded-full shadow-lg', config.dot]" :style="`box-shadow: 0 0 8px currentColor;`"></span>
                            <span class="text-sm font-medium text-pearl">{{ config.label }}</span>
                        </div>
                        <span class="font-serif text-2xl font-semibold tabular-nums text-cream">
                            {{ appointmentsByStatus?.[key] || 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximas citas -->
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="card-elegant group relative overflow-hidden lg:col-span-2 animate-fade-up transition-all duration-300 hover:shadow-xl hover:shadow-silver/5">
                <div class="absolute -right-16 top-0 h-64 w-64 rounded-full bg-gradient-to-bl from-blue-500/5 to-transparent blur-3xl"></div>
                <div class="relative flex items-center justify-between border-b border-smoke/70 px-6 py-4">
                    <div>
                        <p class="text-eyebrow">Agenda inmediata</p>
                        <h3 class="mt-1 font-serif text-xl font-medium">Próximas citas</h3>
                    </div>
                    <Link href="/admin/appointments" class="inline-flex items-center gap-1 text-sm font-medium text-silver-bright hover:text-silver-bright-bright transition">
                        Ver agenda completa
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </div>
                <div v-if="upcomingAppointments.length === 0" class="px-6 py-16 text-center">
                    <Sparkles class="mx-auto h-12 w-12 text-mercury/30" />
                    <p class="mt-3 text-sm text-mercury">No hay citas próximas</p>
                    <Link href="/admin/appointments/create" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-silver-bright hover:text-silver-bright-bright">
                        <Plus class="h-3 w-3" /> Crear nueva cita
                    </Link>
                </div>
                <div v-else class="divide-y divide-smoke">
                    <Link
                        v-for="appointment in upcomingAppointments.slice(0, 6)"
                        :key="appointment.id"
                        :href="`/admin/appointments/${appointment.id}`"
                        class="flex items-center gap-4 px-6 py-4 transition hover:bg-graphite"
                    >
                        <div class="flex h-14 w-14 flex-col items-center justify-center rounded-lg border border-smoke bg-graphite">
                            <span class="font-serif text-lg font-semibold leading-none text-cream">
                                {{ new Date(appointment.date).toLocaleDateString('es-MX', { day: '2-digit' }) }}
                            </span>
                            <span class="mt-0.5 text-[9px] uppercase tracking-widest text-silver/80">
                                {{ new Date(appointment.date).toLocaleDateString('es-MX', { month: 'short' }) }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-cream">{{ appointment.client?.name }}</p>
                            <p class="truncate text-xs text-mercury">
                                {{ appointment.services?.[0]?.service?.name }} ·
                                <span class="font-semibold text-silver-bright">{{ appointment.start_time?.slice(0,5) }}</span> ·
                                {{ appointment.branch?.name }}
                            </p>
                        </div>
                        <span :class="['chip', statusColors[appointment.status]?.bg, statusColors[appointment.status]?.text]">
                            <span :class="['h-1.5 w-1.5 rounded-full', statusColors[appointment.status]?.dot]"></span>
                            {{ statusColors[appointment.status]?.label }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Top estilistas -->
            <div v-if="isAdmin" class="card-elegant group relative overflow-hidden p-6 animate-fade-up transition-all duration-300 hover:shadow-xl hover:shadow-silver/5">
                <div class="absolute -right-8 -top-8 h-48 w-48 rounded-full bg-gradient-to-br from-gold/10 to-transparent blur-3xl"></div>
                <div class="relative">
                    <p class="text-eyebrow">Destacados</p>
                    <h3 class="mt-1 font-serif text-xl font-medium">Top del mes</h3>
                </div>
                <div class="mt-6 space-y-3">
                    <div v-if="topStylists.length === 0" class="py-6 text-center text-sm text-mercury">
                        Sin datos este mes
                    </div>
                    <div
                        v-for="(stylist, index) in topStylists"
                        :key="stylist.id"
                        class="flex items-center gap-3 rounded-lg border border-transparent p-2 transition hover:border-smoke hover:bg-graphite"
                    >
                        <div :class="[
                            'flex h-10 w-10 items-center justify-center rounded-full font-serif text-sm font-bold',
                            index === 0 ? 'bg-gradient-gold text-ink' :
                            index === 1 ? 'border border-silver/40 bg-silver/10 text-silver-bright' :
                            index === 2 ? 'border border-bronze/40 bg-bronze/20 text-bronze' :
                            'border border-smoke bg-graphite text-mercury'
                        ]">
                            <Crown v-if="index === 0" class="h-4 w-4" />
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-cream">{{ stylist.user?.name }}</p>
                            <p class="text-xs text-mercury">{{ stylist.sales_count }} ventas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas para no-admin -->
            <div v-else class="card-elegant group relative overflow-hidden p-6 animate-fade-up transition-all duration-300 hover:shadow-xl hover:shadow-silver/5">
                <div class="absolute -right-8 -top-8 h-48 w-48 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
                <div class="relative">
                    <p class="text-eyebrow">Atajos</p>
                    <h3 class="mt-1 font-serif text-xl font-medium">Acciones rápidas</h3>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <Link href="/admin/appointments/create" class="card-elegant group/item flex flex-col items-center gap-3 p-5 transition-all duration-300 hover:scale-105 hover:border-silver/40 hover:shadow-lg hover:shadow-silver/10">
                            <Plus class="h-5 w-5 text-silver-bright transition-transform duration-300 group-hover/item:rotate-90" />
                            <span class="text-xs font-medium">Nueva cita</span>
                        </Link>
                        <Link href="/admin/sales/create" class="card-elegant group/item flex flex-col items-center gap-3 p-5 transition-all duration-300 hover:scale-105 hover:border-emerald-500/40 hover:shadow-lg hover:shadow-emerald-500/10">
                            <DollarSign class="h-5 w-5 text-emerald-400 transition-transform duration-300 group-hover/item:scale-110" />
                            <span class="text-xs font-medium">Nueva venta</span>
                        </Link>
                        <Link href="/admin/clients" class="card-elegant group/item flex flex-col items-center gap-3 p-5 transition-all duration-300 hover:scale-105 hover:border-blue-500/40 hover:shadow-lg hover:shadow-blue-500/10">
                            <Users class="h-5 w-5 text-blue-400 transition-transform duration-300 group-hover/item:scale-110" />
                            <span class="text-xs font-medium">Clientes</span>
                        </Link>
                        <Link href="/admin/expenses/create" class="card-elegant group/item flex flex-col items-center gap-3 p-5 transition-all duration-300 hover:scale-105 hover:border-red-500/40 hover:shadow-lg hover:shadow-red-500/10">
                            <Wallet class="h-5 w-5 text-red-400 transition-transform duration-300 group-hover/item:scale-110" />
                            <span class="text-xs font-medium">Egreso</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>