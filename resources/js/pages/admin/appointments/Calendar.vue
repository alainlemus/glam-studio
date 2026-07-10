<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Calendar,
    ChevronLeft,
    ChevronRight,
    CalendarCheck,
    Filter,
    ChevronDown,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    appointments: any[];
    branches: any[];
    stylists: any[];
    range: { from: string; to: string };
    filters: any;
}>();

const from = ref(props.range.from);
const to = ref(props.range.to);
const branchId = ref(props.filters.branch_id || '');
const stylistId = ref(props.filters.stylist_id || '');

const statusColors: Record<string, { bg: string; border: string; text: string; dot: string }> = {
    pending: { bg: 'bg-amber-500/20', border: 'border-l-amber-400', text: 'text-amber-200', dot: 'bg-amber-400' },
    confirmed: { bg: 'bg-blue-500/20', border: 'border-l-blue-400', text: 'text-blue-200', dot: 'bg-blue-400' },
    in_progress: { bg: 'bg-purple-500/20', border: 'border-l-purple-400', text: 'text-purple-200', dot: 'bg-purple-400' },
    completed: { bg: 'bg-emerald-500/20', border: 'border-l-emerald-400', text: 'text-emerald-200', dot: 'bg-emerald-400' },
    cancelled: { bg: 'bg-red-500/20', border: 'border-l-red-400', text: 'text-red-200', dot: 'bg-red-400' },
    no_show: { bg: 'bg-zinc-500/20', border: 'border-l-zinc-400', text: 'text-zinc-300', dot: 'bg-zinc-400' },
};

const filter = () => {
    router.get('/admin/appointments/calendar', {
        from: from.value,
        to: to.value,
        branch_id: branchId.value || undefined,
        stylist_id: stylistId.value || undefined,
    }, { preserveState: true });
};

const navigate = (direction: number) => {
    const start = new Date(from.value);
    const end = new Date(to.value);
    start.setDate(start.getDate() + (7 * direction));
    end.setDate(end.getDate() + (7 * direction));
    from.value = start.toISOString().split('T')[0];
    to.value = end.toISOString().split('T')[0];
    filter();
};

const days = computed(() => {
    const start = new Date(from.value);
    const end = new Date(to.value);
    const result = [];
    const cursor = new Date(start);
    while (cursor <= end) {
        result.push(cursor.toISOString().split('T')[0]);
        cursor.setDate(cursor.getDate() + 1);
    }
    return result.slice(0, 7);
});

const appointmentsByDate = computed(() => {
    const map: Record<string, any[]> = {};
    for (const appt of props.appointments) {
        const date = appt.date.split('T')[0];
        if (!map[date]) map[date] = [];
        map[date].push(appt);
    }
    return map;
});

const hours = Array.from({ length: 13 }, (_, i) => i + 8);

const isToday = (date: string) => {
    const today = new Date().toISOString().split('T')[0];
    return date === today;
};

const formatTime = (time: string) => {
    if (!time) return '';
    const [h, m] = time.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12 = hour % 12 || 12;
    return `${h12}:${m}`;
};
</script>

<template>
    <Head title="Calendario" />

    <div class="space-y-6 p-4 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Vista semanal</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Calendario</h2>
            </div>
            <div class="flex gap-2">
                <Link href="/admin/appointments" class="btn-ghost-elegant h-12 px-5 text-sm">
                    📋 Vista lista
                </Link>
                <Link href="/admin/appointments/create" class="btn-primary-elegant h-12 px-5 text-sm">
                    <Plus class="h-4 w-4" />
                    Nueva cita
                </Link>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="card-elegant p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1">
                    <button @click="navigate(-1)" class="flex h-11 w-11 items-center justify-center rounded-lg border border-smoke bg-graphite text-pearl transition hover:border-silver/40 hover:text-silver-bright">
                        <ChevronLeft class="h-4 w-4" />
                    </button>
                    <button @click="navigate(1)" class="flex h-11 w-11 items-center justify-center rounded-lg border border-smoke bg-graphite text-pearl transition hover:border-silver/40 hover:text-silver-bright">
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
                <div class="font-serif text-lg text-cream">
                    {{ new Date(from).toLocaleDateString('es-MX', { day: 'numeric', month: 'long' }) }} -
                    {{ new Date(to).toLocaleDateString('es-MX', { day: 'numeric', month: 'long' }) }}
                </div>
                <div class="flex-1"></div>
                <div class="relative">
                    <select
                        v-model="branchId"
                        class="appearance-none rounded-lg border border-smoke bg-graphite px-4 py-2.5 pr-9 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @change="filter"
                    >
                        <option value="">Todas las sucursales</option>
                        <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
                <div class="relative">
                    <select
                        v-model="stylistId"
                        class="appearance-none rounded-lg border border-smoke bg-graphite px-4 py-2.5 pr-9 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @change="filter"
                    >
                        <option value="">Todos los estilistas</option>
                        <option v-for="s in stylists" :key="s.id" :value="s.id">{{ s.user?.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="card-elegant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] table-fixed">
                    <thead>
                        <tr class="border-b border-smoke bg-graphite">
                            <th class="sticky left-0 z-10 w-20 border-r border-smoke bg-graphite px-3 py-4 text-left">
                                <span class="text-xs font-medium uppercase tracking-wider text-mercury">Hora</span>
                            </th>
                            <th
                                v-for="day in days"
                                :key="day"
                                class="border-r border-smoke px-3 py-4 text-center"
                                :class="isToday(day) ? 'bg-silver/5' : ''"
                            >
                                <div class="text-xs font-medium uppercase tracking-wider" :class="isToday(day) ? 'text-silver-bright' : 'text-mercury'">
                                    {{ new Date(day).toLocaleDateString('es-MX', { weekday: 'short' }) }}
                                </div>
                                <div :class="['mt-1 font-serif text-2xl font-semibold', isToday(day) ? 'text-silver-bright' : 'text-cream']">
                                    {{ new Date(day).getDate() }}
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-smoke">
                        <tr v-for="hour in hours" :key="hour" class="h-20">
                            <td class="sticky left-0 z-10 border-r border-smoke bg-graphite px-3 py-2 align-top">
                                <div class="text-sm font-medium text-mercury">
                                    {{ String(hour).padStart(2, '0') }}:00
                                </div>
                            </td>
                            <td
                                v-for="day in days"
                                :key="day+'-'+hour"
                                class="relative border-r border-smoke/50 p-1 align-top hover:bg-graphite/30"
                            >
                                <div
                                    v-for="appt in (appointmentsByDate[day] || []).filter(a => parseInt(a.start_time.split(':')[0]) === hour)"
                                    :key="appt.id"
                                    :class="[
                                        'cursor-pointer rounded-lg border-l-2 p-2 text-xs transition hover:scale-[1.02]',
                                        statusColors[appt.status]?.bg,
                                        statusColors[appt.status]?.border,
                                    ]"
                                    @click="$inertia.visit(`/admin/appointments/${appt.id}`)"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <span :class="['h-1.5 w-1.5 rounded-full', statusColors[appt.status]?.dot]"></span>
                                        <span class="font-mono font-bold" :class="statusColors[appt.status]?.text">
                                            {{ formatTime(appt.start_time) }}
                                        </span>
                                    </div>
                                    <div :class="['mt-1 truncate font-medium', statusColors[appt.status]?.text]">
                                        {{ appt.client?.name }}
                                    </div>
                                    <div class="truncate text-[10px] text-cream/70">
                                        {{ appt.services?.[0]?.service?.name }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>