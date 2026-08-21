<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ShieldCheck } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    user?: any;
    branches: any[];
}>();

const page = usePage();
const currentUserId = computed(() => (page.props.auth as any).user.id);
const isEditingSelf = computed(() => props.user && props.user.id === currentUserId.value);

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    password: '',
    password_confirmation: '',
    role: props.user?.role || 'receptionist',
    branch_id: props.user?.branch_id || '',
    is_active: props.user?.is_active ?? true,
});

const roles = [
    { value: 'admin', label: 'Administrador', description: 'Acceso total al panel: catálogo, finanzas, usuarios y configuración.' },
    { value: 'manager', label: 'Gerente', description: 'Gestión operativa de una sucursal: citas, ventas, inventario y equipo.' },
    { value: 'receptionist', label: 'Recepción', description: 'Agenda, citas y atención a clientas en la sucursal asignada.' },
];

const submit = () => {
    if (props.user) form.put(`/admin/users/${props.user.id}`);
    else form.post('/admin/users');
};
</script>

<template>
    <Head :title="user ? 'Editar usuario' : 'Nuevo usuario'" />

    <div class="mx-auto max-w-2xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/users" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Sistema</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ user ? 'Editar' : 'Nuevo' }} usuario</h2>
        </div>

        <form @submit.prevent="submit" class="card-elegant space-y-6 p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                    <input v-model="form.name" required class="input-elegant" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Correo electrónico *</label>
                    <input v-model="form.email" type="email" required class="input-elegant" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Teléfono</label>
                    <input v-model="form.phone" class="input-elegant" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">
                        {{ user ? 'Nueva contraseña' : 'Contraseña *' }}
                    </label>
                    <input v-model="form.password" type="password" :required="!user" class="input-elegant" :placeholder="user ? 'Dejar vacío para no cambiar' : ''" />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
                </div>
                <div v-if="form.password">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Confirmar contraseña *</label>
                    <input v-model="form.password_confirmation" type="password" required class="input-elegant" />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-400">{{ form.errors.password_confirmation }}</p>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-mercury">Rol *</label>
                <div class="grid gap-3 sm:grid-cols-3">
                    <button
                        v-for="r in roles"
                        :key="r.value"
                        type="button"
                        :disabled="isEditingSelf && r.value !== 'admin'"
                        @click="form.role = r.value"
                        :class="[
                            'rounded-xl border-2 p-4 text-left transition-all disabled:cursor-not-allowed disabled:opacity-40',
                            form.role === r.value ? 'border-silver bg-graphite shadow-gold' : 'border-smoke bg-graphite/50 hover:border-silver/30',
                        ]"
                    >
                        <div class="flex items-center gap-2">
                            <ShieldCheck class="h-4 w-4" :class="form.role === r.value ? 'text-silver-bright' : 'text-mercury'" />
                            <span class="text-sm font-semibold text-cream">{{ r.label }}</span>
                        </div>
                        <p class="mt-1.5 text-xs leading-relaxed text-mercury">{{ r.description }}</p>
                    </button>
                </div>
                <p v-if="isEditingSelf" class="mt-2 text-xs text-amber-400">No puedes cambiar tu propio rol de administrador.</p>
                <p v-if="form.errors.role" class="mt-1 text-xs text-red-400">{{ form.errors.role }}</p>
            </div>

            <div v-if="form.role !== 'admin'">
                <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal</label>
                <select v-model="form.branch_id" class="input-elegant appearance-none">
                    <option value="">Todas / sin asignar</option>
                    <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
            </div>

            <div>
                <label class="inline-flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        :disabled="isEditingSelf"
                        class="h-5 w-5 rounded border-smoke bg-graphite text-silver focus:ring-silver disabled:opacity-40"
                    />
                    <span class="text-sm font-medium text-cream">Usuario activo</span>
                </label>
                <p v-if="isEditingSelf" class="mt-1 text-xs text-amber-400">No puedes desactivar tu propia cuenta.</p>
            </div>

            <div class="flex justify-end gap-3 border-t border-smoke pt-6">
                <Link href="/admin/users" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</template>
