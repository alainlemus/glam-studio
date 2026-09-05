<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { History, ChevronDown } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    logs: any;
    users: any[];
    filters: any;
}>();

const actionLabels: Record<string, string> = {
    deleted: 'Eliminación',
    role_changed: 'Cambio de rol',
    status_changed: 'Cambio de estado',
    price_changed: 'Cambio de precio',
    cancelled: 'Cancelación',
};

const actionColors: Record<string, string> = {
    deleted: 'bg-red-500/15 text-red-400',
    role_changed: 'bg-purple-500/15 text-purple-400',
    status_changed: 'bg-amber-500/15 text-amber-400',
    price_changed: 'bg-blue-500/15 text-blue-400',
    cancelled: 'bg-orange-500/15 text-orange-400',
};

const userId = ref(props.filters.user_id || '');
const action = ref(props.filters.action || '');
const from = ref(props.filters.from || '');
const to = ref(props.filters.to || '');

const filter = () => {
    router.get('/admin/audit-log', {
        user_id: userId.value || undefined,
        action: action.value || undefined,
        from: from.value || undefined,
        to: to.value || undefined,
    }, { preserveState: true });
};
</script>

<template>
    <Head title="Auditoría" />

    <div class="space-y-6 p-4 lg:p-8">
        <div>
            <p class="text-eyebrow">Sistema</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Auditoría</h2>
            <p class="mt-1 text-sm text-mercury">{{ logs.total }} acciones registradas</p>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-4">
                <div class="relative">
                    <select v-model="userId" class="input-elegant appearance-none pr-9" @change="filter">
                        <option value="">Todos los usuarios</option>
                        <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
                <div class="relative">
                    <select v-model="action" class="input-elegant appearance-none pr-9" @change="filter">
                        <option value="">Todas las acciones</option>
                        <option v-for="(label, key) in actionLabels" :key="key" :value="key">{{ label }}</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
                <input v-model="from" type="date" class="input-elegant" @change="filter" />
                <input v-model="to" type="date" class="input-elegant" @change="filter" />
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Fecha</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Usuario</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Acción</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Descripción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="log in logs.data" :key="log.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4 text-sm text-pearl whitespace-nowrap">{{ new Date(log.created_at).toLocaleString('es-MX') }}</td>
                        <td class="px-5 py-4 text-sm text-cream">{{ log.user?.name ?? 'Sistema' }}</td>
                        <td class="px-5 py-4">
                            <span :class="['chip', actionColors[log.action] || 'bg-silver/15 text-silver-bright']">
                                {{ actionLabels[log.action] || log.action }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ log.description }}</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <div v-if="logs.data.length === 0" class="px-6 py-16 text-center">
                <History class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">No hay acciones registradas</p>
            </div>
        </div>

        <div v-if="logs.last_page > 1" class="flex justify-center gap-2">
            <Link v-for="link in logs.links" :key="link.label" :href="link.url || '#'" :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40']" v-html="link.label" />
        </div>
    </div>
</template>
