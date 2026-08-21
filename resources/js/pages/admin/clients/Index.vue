<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    UserPlus,
    Phone,
    Mail,
    Award,
    ChevronDown,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    clients: any;
    filters: any;
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const filter = () => {
    router.get('/admin/clients', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true });
};

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar este cliente?',
        description: 'Esta acción no se puede deshacer.',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/clients/${id}`);
};
</script>

<template>
    <Head title="Clientes" />

    <div class="space-y-6 p-4 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">CRM</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Clientes</h2>
                <p class="mt-1 text-sm text-mercury">{{ clients.total }} clientes registrados</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, teléfono o email..."
                        class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream placeholder:text-mercury focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @input="filter"
                    />
                </div>
                <div class="relative">
                    <select
                        v-model="status"
                        class="w-full appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-9 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @change="filter"
                    >
                        <option value="">Todos los clientes</option>
                        <option value="active">Activos</option>
                        <option value="blocked">Bloqueados</option>
                        <option value="vip">VIP (sin no-shows)</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <!-- Grid de tarjetas de clientes -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <Link
                v-for="client in clients.data"
                :key="client.id"
                :href="`/admin/clients/${client.id}`"
                class="card-elegant card-elegant-hover group overflow-hidden p-5 transition hover:-translate-y-1"
            >
                <div class="flex items-start justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full border border-silver/20 bg-gradient-to-br from-silver-bright/20 to-graphite font-serif text-lg font-semibold text-cream">
                        {{ client.name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() }}
                    </div>
                    <span v-if="client.is_blocked" class="chip bg-red-500/15 text-red-400">
                        Bloqueado
                    </span>
                    <span v-else-if="client.no_show_count === 0 && client.loyalty_card" class="chip bg-silver/15 text-silver-bright">
                        ⭐ VIP
                    </span>
                </div>

                <div class="mt-4">
                    <h3 class="truncate font-serif text-lg font-semibold text-cream transition group-hover:text-silver-bright">
                        {{ client.name }}
                    </h3>
                    <div class="mt-3 space-y-1.5 text-xs">
                        <div v-if="client.phone" class="flex items-center gap-2 text-pearl">
                            <Phone class="h-3 w-3 shrink-0 text-silver/60" />
                            <span class="truncate">{{ client.phone }}</span>
                        </div>
                        <div v-if="client.email" class="flex items-center gap-2 text-pearl">
                            <Mail class="h-3 w-3 shrink-0 text-silver/60" />
                            <span class="truncate">{{ client.email }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-smoke pt-4 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="text-center">
                            <div class="font-serif text-base font-semibold text-cream">{{ client.appointments_count || 0 }}</div>
                            <div class="text-[10px] uppercase tracking-wider text-mercury">Citas</div>
                        </div>
                        <div v-if="client.loyalty_card" class="text-center">
                            <div class="font-serif text-base font-semibold text-silver-bright">
                                {{ client.loyalty_card.stamps_current }}<span class="text-cream/50">/{{ client.loyalty_card.stamps_required }}</span>
                            </div>
                            <div class="text-[10px] uppercase tracking-wider text-mercury">Sellos</div>
                        </div>
                    </div>
                    <span v-if="client.no_show_count > 0" class="chip bg-orange-500/15 text-orange-400 text-[10px]">
                        ⚠ {{ client.no_show_count }}
                    </span>
                </div>
            </Link>
        </div>

        <div v-if="clients.data.length === 0" class="card-elegant px-6 py-16 text-center">
            <UserPlus class="mx-auto h-12 w-12 text-mercury/30" />
            <p class="mt-3 text-sm text-mercury">No hay clientes registrados</p>
        </div>

        <!-- Paginación -->
        <div v-if="clients.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="link in clients.links"
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