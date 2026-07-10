<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Scissors } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
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

const defaultSchedules = days.map(d => ({
    day_of_week: d.value,
    start_time: '09:00',
    end_time: '19:00',
    active: true,
}));

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    branch_id: '',
    specialty: '',
    bio: '',
    base_salary: 8000,
    service_commission: 25,
    product_commission: 10,
    is_active: true,
    schedules: defaultSchedules,
});

const submit = () => form.post('/admin/stylists');
</script>

<template>
    <Head title="Nuevo estilista" />

    <div class="mx-auto max-w-3xl space-y-6 p-4 lg:p-8">
        <div>
            <Link href="/admin/stylists" class="mb-2 inline-flex items-center gap-1 text-sm text-mercury hover:text-silver-bright">← Volver</Link>
            <p class="text-eyebrow">Equipo</p>
            <h2 class="mt-1 font-serif text-3xl font-medium tracking-tight">Nuevo estilista</h2>
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
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Contraseña *</label>
                        <input v-model="form.password" type="password" required minlength="6" class="input-elegant" />
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
                    {{ form.processing ? 'Guardando...' : 'Crear estilista' }}
                </button>
            </div>
        </form>
    </div>
</template>