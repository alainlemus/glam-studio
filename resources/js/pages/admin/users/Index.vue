<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Search, ChevronDown, ShieldCheck, Mail } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/composables/useConfirm';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    users: any;
    filters: any;
}>();

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');

const roleLabels: Record<string, string> = {
    admin: 'Administrador',
    manager: 'Gerente',
    receptionist: 'Recepción',
};

const roleColors: Record<string, string> = {
    admin: 'bg-gold/15 text-gold-bright',
    manager: 'bg-silver/15 text-silver-bright',
    receptionist: 'bg-blue-500/15 text-blue-400',
};

const filter = () => {
    router.get('/admin/users', {
        search: search.value || undefined,
        role: role.value || undefined,
    }, { preserveState: true });
};

const destroy = async (id: number) => {
    if (await confirmDialog({
        title: '¿Eliminar este usuario?',
        description: 'Esta acción no se puede deshacer.',
        variant: 'destructive',
        confirmText: 'Eliminar',
    })) router.delete(`/admin/users/${id}`);
};
</script>

<template>
    <Head title="Usuarios" />

    <div class="space-y-6 p-4 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-eyebrow">Sistema</p>
                <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Usuarios</h2>
                <p class="mt-1 text-sm text-mercury">Administradores, gerentes y recepción. Los estilistas se gestionan desde "Estilistas".</p>
            </div>
            <Link href="/admin/users/create" class="btn-primary-elegant h-12 px-5 text-sm">
                <Plus class="h-4 w-4" />
                Nuevo usuario
            </Link>
        </div>

        <div class="card-elegant p-4">
            <div class="grid gap-3 md:grid-cols-3">
                <div class="relative md:col-span-2">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre o correo..."
                        class="w-full rounded-lg border border-smoke bg-graphite pl-10 pr-3 py-3 text-sm text-cream placeholder:text-mercury focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20"
                        @input="filter"
                    />
                </div>
                <div class="relative">
                    <select v-model="role" class="w-full appearance-none rounded-lg border border-smoke bg-graphite px-3 pr-9 py-3 text-sm text-cream focus:border-silver focus:outline-none focus:ring-2 focus:ring-gold/20" @change="filter">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="manager">Gerente</option>
                        <option value="receptionist">Recepción</option>
                    </select>
                    <ChevronDown class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-mercury" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-smoke bg-card">
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-smoke bg-graphite">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Usuario</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Rol</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Sucursal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-silver/80">Estado</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-silver/80">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-smoke">
                    <tr v-for="user in users.data" :key="user.id" class="transition hover:bg-graphite/50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-silver/20 bg-silver/10">
                                    <ShieldCheck class="h-4 w-4 text-silver-bright" />
                                </div>
                                <div>
                                    <div class="font-medium text-cream">{{ user.name }}</div>
                                    <div class="flex items-center gap-1 text-xs text-mercury">
                                        <Mail class="h-3 w-3" />
                                        {{ user.email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span :class="['chip', roleColors[user.role] || 'bg-smoke text-pearl']">
                                {{ roleLabels[user.role] || user.role }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-pearl">{{ user.branch?.name || '—' }}</td>
                        <td class="px-5 py-4">
                            <span :class="['chip', user.is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400']">
                                <span :class="['h-1.5 w-1.5 rounded-full', user.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                                {{ user.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <Link :href="`/admin/users/${user.id}/edit`" class="text-sm font-medium text-silver-bright hover:text-silver-bright-bright">Editar</Link>
                            <button @click="destroy(user.id)" class="ml-3 text-sm font-medium text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div v-if="users.data.length === 0" class="px-6 py-16 text-center text-sm text-mercury">
                No hay usuarios registrados
            </div>
        </div>

        <div v-if="users.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="link in users.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="['flex h-11 items-center justify-center rounded-lg border px-4 text-sm transition', link.active ? 'border-silver bg-silver-bright text-ink font-semibold' : 'border-smoke bg-graphite text-pearl hover:border-silver/40 hover:text-cream']"
                v-html="link.label"
            />
        </div>
    </div>
</template>
