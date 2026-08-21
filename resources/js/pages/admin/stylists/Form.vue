<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Scissors } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stylist?: any;
    branches: any[];
}>();

const days = [
    { value: 1, label: 'Lunes' },
    { value: 2, label: 'Martes' },
    { value: 3, label: 'Miércoles' },
    { value: 4, label: 'Jueves' },
    { value: 5, label: 'Viernes' },
    { value: 6, label: 'Sábado' },
];

const buildSchedules = () => days.map(d => {
    const existing = props.stylist?.schedules?.find((s: any) => s.day_of_week === d.value);
    return {
        day_of_week: d.value,
        start_time: existing?.start_time?.slice(0, 5) || '09:00',
        end_time: existing?.end_time?.slice(0, 5) || '19:00',
        active: existing ? existing.is_active : !props.stylist,
    };
});

const form = useForm({
    name: props.stylist?.user?.name || '',
    email: props.stylist?.user?.email || '',
    phone: props.stylist?.user?.phone || '',
    password: '',
    password_confirmation: '',
    branch_id: props.stylist?.branch_id || '',
    specialty: props.stylist?.specialty || '',
    bio: props.stylist?.bio || '',
    base_salary: props.stylist?.base_salary ?? 8000,
    service_commission: props.stylist?.service_commission ?? 25,
    product_commission: props.stylist?.product_commission ?? 10,
    is_active: props.stylist?.is_active ?? true,
    schedules: buildSchedules(),
});

const submit = () => {
    if (props.stylist) form.put(`/admin/stylists/${props.stylist.id}`);
    else form.post('/admin/stylists');
};
</script>

<template>
    <Head :title="stylist ? 'Editar estilista' : 'Nuevo estilista'" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/stylists" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Equipo</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">{{ stylist ? 'Editar' : 'Nuevo' }} estilista</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="card-elegant p-6 space-y-4">
                <h3 class="font-serif text-lg font-medium text-cream">Datos personales</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre *</label>
                        <input v-model="form.name" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Email *</label>
                        <input v-model="form.email" type="email" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Teléfono</label>
                        <input v-model="form.phone" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">
                            {{ stylist ? 'Nueva contraseña' : 'Contraseña *' }}
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            :required="!stylist"
                            minlength="8"
                            class="input-elegant"
                            :placeholder="stylist ? 'Dejar vacío para no cambiar' : ''"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
                    </div>
                    <div v-if="form.password">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Confirmar contraseña *</label>
                        <input v-model="form.password_confirmation" type="password" required class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal *</label>
                        <select v-model="form.branch_id" required class="input-elegant">
                            <option value="">Selecciona...</option>
                            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Especialidad</label>
                        <input v-model="form.specialty" class="input-elegant" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Biografía</label>
                        <textarea v-model="form.bio" rows="2" class="input-elegant"></textarea>
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6 space-y-4">
                <h3 class="font-serif text-lg font-medium text-cream">Compensación</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sueldo base</label>
                        <input v-model="form.base_salary" type="number" step="0.01" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">% Comisión servicios</label>
                        <input v-model="form.service_commission" type="number" step="0.01" class="input-elegant" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">% Comisión productos</label>
                        <input v-model="form.product_commission" type="number" step="0.01" class="input-elegant" />
                    </div>
                </div>
            </div>

            <div class="card-elegant p-6 space-y-3">
                <h3 class="font-serif text-lg font-medium text-cream">Horario de trabajo</h3>
                <div class="space-y-2">
                    <div v-for="(schedule, idx) in form.schedules" :key="schedule.day_of_week" class="flex items-center gap-3 rounded-lg border border-smoke bg-graphite p-3">
                        <input type="checkbox" :checked="schedule.active" @change="schedule.active = $event.target.checked" class="h-5 w-5 rounded border-smoke bg-graphite text-silver-bright focus:ring-gold" />
                        <span class="w-28 text-sm font-medium text-cream">{{ days.find(d => d.value === schedule.day_of_week)?.label }}</span>
                        <input v-model="schedule.start_time" type="time" class="rounded border border-smoke bg-graphite px-3 py-2 text-sm text-cream focus:border-silver focus:outline-none disabled:opacity-40" :disabled="!schedule.active" />
                        <span class="text-mercury">a</span>
                        <input v-model="schedule.end_time" type="time" class="rounded border border-smoke bg-graphite px-3 py-2 text-sm text-cream focus:border-silver focus:outline-none disabled:opacity-40" :disabled="!schedule.active" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/stylists" class="btn-ghost-elegant h-12 px-6">Cancelar</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary-elegant h-12 px-7 disabled:opacity-50">
                    <Scissors class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : (stylist ? 'Guardar cambios' : 'Crear estilista') }}
                </button>
            </div>
        </form>
    </div>
</template>