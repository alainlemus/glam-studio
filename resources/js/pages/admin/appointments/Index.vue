<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    CalendarDays,
    Plus,
    ChevronDown,
    Calendar,
    Users,
    Search,
    Filter,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    appointments: any;
    branches: any[];
    stylists: any[];
    filters: any;
}>();

const search = ref('');
const date = ref(props.filters.date || '');
const status = ref(props.filters.status || '');
const branchId = ref(props.filters.branch_id || '');

const filter = () => {
    router.get('/admin/appointments', {
        date: date.value || undefined,
        status: status.value || undefined,
        branch_id: branchId.value || undefined,
    }, { preserveState: true });
};

const statusConfig: Record<string, { bg: string; text: string; dot: string; label: string }> = {
    pending: { bg: 'bg-amber-500/15', text: 'text-amber-400', dot: 'bg-amber-400', label: 'Pendiente' },
    confirmed: { bg: 'bg-blue-500/15', text: 'text-blue-400', dot: 'bg-blue-400', label: 'Confirmada' },
    in_progress: { bg: 'bg-purple-500/15', text: 'text-purple-400', dot: 'bg-purple-400', label: 'En curso' },
    completed: { bg: 'bg-emerald-500/15', text: 'text-emerald-400', dot: 'bg-emerald-400', label: 'Completada' },
    cancelled: { bg: 'bg-red-500/15', text: 'text-red-400', dot: 'bg-red-400', label: 'Cancelada' },
    no_show: { bg: 'bg-zinc-500/15', text: 'text-zinc-400', dot: 'bg-zinc-400', label: 'No show' },
};

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));

const formatTime = (time: string) => {
    if (!time) return '';
    const [h, m] = time.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 || 12;
    return `${h12}:${m} ${ampm}`;
};
</script>

<template>
    <Head title="Citas" />

    <div class="space-y-6 p-4 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Gestión</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Citas</h2>
                <p class="mt-1 text-sm text-mercury">{{ appointments.total }} citas registradas</p>
            </div>
            <div class="flex gap-2">
                <Link href="/admin/appointments/calendar" class="btn-ghost-elegant h-12 px-5 text-sm">
                    <Calendar class="h-4 w-4" />
                    Vista calendario
                </Link>
                <Link href="/admin/appointments/create" class="btn-primary-elegant h-12 px-5 text-sm">
                    <Plus class="h-4 w-4" />
                    Nueva cita
                </Link>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Fecha</label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                        <input
                            v-model="date"
                            type="date"
                            class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20 transition"
                            @change="filter"
                        />
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estado</label>
                    <div class="relative">
                        <Filter class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                        <select
                            v-model="status"
                            class="w-full appearance-none rounded-lg border border-smoke bg-graphite pl-10 pr-8 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20 transition"
                            @change="filter"
                        >
                            <option value="">Todos los estados</option>
                            <option v-for="(c, k) in statusConfig" :key="k" :value="k">{{ c.label }}</option>
                        </select>
                        <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal</label>
                    <div class="relative">
                        <select
                            v-model="branchId"
                            class="w-full appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-8 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20 transition"
                            @change="filter"
                        >
                            <option value="">Todas las sucursales</option>
                            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                        <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="card-elegant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-smoke bg-graphite">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Código</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Cliente</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Hora</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Servicio</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estilista</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-smoke">
                        <tr
                            v-for="appointment in appointments.data"
                            :key="appointment.id"
                            class="transition hover:bg-graphite/50 cursor-pointer"
                            @click="$inertia.visit(`/admin/appointments/${appointment.id}`)"
                        >
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs text-silver-bright">{{ appointment.code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-cream">{{ appointment.client?.name }}</div>
                                <div class="text-xs text-mercury">{{ appointment.client?.phone }}</div>
                            </td>
                            <td class="px-5 py-4 text-sm text-cream">
                                <div class="font-medium">
                                    {{ new Date(appointment.date).toLocaleDateString('es-MX', { day: '2-digit', month: 'short' }) }}
                                </div>
                                <div class="text-xs text-mercury">
                                    {{ new Date(appointment.date).toLocaleDateString('es-MX', { weekday: 'short' }) }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-mono text-sm font-semibold text-cream">
                                    {{ formatTime(appointment.start_time) }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-pearl">
                                {{ appointment.services?.[0]?.service?.name }}
                            </td>
                            <td class="px-5 py-4 text-sm text-pearl">
                                {{ appointment.stylist?.user?.name || '—' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-pearl">
                                {{ appointment.branch?.name }}
                            </td>
                            <td class="px-5 py-4">
                                <span :class="['chip', statusConfig[appointment.status]?.bg, statusConfig[appointment.status]?.text]">
                                    <span :class="['h-1.5 w-1.5 rounded-full', statusConfig[appointment.status]?.dot]"></span>
                                    {{ statusConfig[appointment.status]?.label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="font-serif text-base font-semibold text-cream">
                                    {{ formatPrice(appointment.total) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="appointments.data.length === 0" class="px-6 py-16 text-center">
                <CalendarDays class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">No hay citas registradas</p>
                <Link href="/admin/appointments/create" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-silver-bright hover:text-silver-bright-bright">
                    <Plus class="h-3 w-3" /> Crear primera cita
                </Link>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="appointments.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="link in appointments.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                    'flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition',
                    link.active
                        ? 'border-silver bg-silver-bright text-ink font-semibold'
                        : 'border-smoke bg-graphite text-pearl hover:border-silver/40 hover:text-cream',
                ]"
                v-html="link.label"
            />
        </div>
    </div>
</template>