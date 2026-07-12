<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps<{
    branches: any[];
    services: any[];
    categories: any[];
    preselected: { branch_id?: any; service_id?: any };
}>();

const form = ref({
    name: '',
    phone: '',
    email: '',
    branch_id: props.preselected.branch_id || '',
    service_id: props.preselected.service_id || '',
    stylist_id: '' as string | number,
    date: '',
    start_time: '',
    notes: '',
});

const slots = ref<any[]>([]);
const availableStylists = ref<any[]>([]);
const loadingSlots = ref(false);
const submitting = ref(false);

const today = new Date().toISOString().split('T')[0];

const selectedService = computed(() => {
    return props.services.find(s => s.id == form.value.service_id);
});

const selectedBranch = computed(() => {
    return props.branches.find(b => b.id == form.value.branch_id);
});

const canShowSlots = computed(() => {
    return form.value.branch_id && form.value.service_id && form.value.date;
});

const canSubmit = computed(() => {
    return form.value.name &&
        form.value.phone &&
        form.value.branch_id &&
        form.value.service_id &&
        form.value.date &&
        form.value.start_time;
});

const fetchSlots = async () => {
    if (!form.value.branch_id || !form.value.service_id || !form.value.date) {
        slots.value = [];
        availableStylists.value = [];
        return;
    }
    loadingSlots.value = true;
    try {
        // Obtener el token CSRF actualizado de la cookie XSRF-TOKEN
        // (que Laravel setea automáticamente y rota en cada request)
        const getCookie = (name: string) => {
            const match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]*)'));
            return match ? decodeURIComponent(match[2]) : '';
        };
        const xsrfToken = getCookie('XSRF-TOKEN');

        const response = await fetch('/api/slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                branch_id: form.value.branch_id,
                service_id: form.value.service_id,
                stylist_id: form.value.stylist_id || null,
                date: form.value.date,
            }),
        });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const data = await response.json();
        slots.value = data.slots || [];
        availableStylists.value = data.stylists || [];
    } catch (e) {
        console.error('fetchSlots error:', e);
        slots.value = [];
        availableStylists.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

watch(
    () => [form.value.branch_id, form.value.service_id, form.value.date, form.value.stylist_id],
    () => {
        form.value.start_time = '';
        fetchSlots();
    },
    { deep: true }
);

const formatPrice = (price: string | number) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(price));
};

const submit = () => {
    if (!canSubmit.value) return;
    submitting.value = true;
    router.post('/agendar', form.value, {
        onFinish: () => submitting.value = false,
    });
};

const getMinDate = () => today;
</script>

<template>
    <Head title="Reservar Cita" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/5 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center animate-fade-up">
            <p class="text-eyebrow">Agenda en línea</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-tight text-cream lg:text-5xl">
                Reservar <span class="italic text-glitter">cita</span>
            </h1>
            <p class="mt-4 text-base text-mercury">
                Selecciona sucursal, servicio, día y hora. Confirmación inmediata por WhatsApp.
            </p>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="overflow-hidden rounded-2xl border border-smoke bg-card shadow-gold">
                <div class="grid lg:grid-cols-5">
                    <!-- COLUMNA IZQUIERDA: Datos, sucursal, servicio -->
                    <div class="space-y-6 border-b border-smoke p-6 lg:col-span-2 lg:border-b-0 lg:border-r bg-graphite">
                        <div>
                            <h2 class="mb-3 flex items-center gap-3 font-serif text-lg font-medium text-cream">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-silver-bright font-serif text-sm font-bold text-ink shadow-gold">1</span>
                                Tus datos
                            </h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Nombre completo *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="input-elegant"
                                        placeholder="Tu nombre"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Teléfono WhatsApp *</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="input-elegant"
                                        placeholder="55 1234 5678"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Email (opcional)</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="input-elegant"
                                        placeholder="tu@email.com"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-smoke pt-6">
                            <h2 class="mb-3 flex items-center gap-3 font-serif text-lg font-medium text-cream">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-silver-bright font-serif text-sm font-bold text-ink shadow-gold">2</span>
                                Sucursal y servicio
                            </h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Sucursal *</label>
                                    <select
                                        v-model="form.branch_id"
                                        required
                                        class="input-elegant"
                                    >
                                        <option value="">Selecciona una sucursal</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                    <p v-if="selectedBranch" class="mt-1.5 text-xs text-silver-bright">
                                        📍 {{ selectedBranch.city?.name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Servicio *</label>
                                    <select
                                        v-model="form.service_id"
                                        required
                                        class="input-elegant"
                                    >
                                        <option value="">Selecciona un servicio</option>
                                        <optgroup v-for="category in categories" :key="category.id" :label="category.name">
                                            <option v-for="service in category.services" :key="service.id" :value="service.id">
                                                {{ service.name }} - {{ formatPrice(service.price) }}
                                            </option>
                                        </optgroup>
                                    </select>
                                    <p v-if="selectedService" class="mt-1.5 text-xs text-silver-bright">
                                        ⏱️ {{ selectedService.duration_minutes }} minutos
                                    </p>
                                </div>
                                <div v-if="availableStylists.length > 1">
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estilista (opcional)</label>
                                    <select
                                        v-model="form.stylist_id"
                                        class="input-elegant"
                                    >
                                        <option value="">Cualquier estilista disponible</option>
                                        <option v-for="stylist in availableStylists" :key="stylist.id" :value="stylist.id">
                                            {{ stylist.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-smoke pt-6">
                            <h2 class="mb-3 flex items-center gap-3 font-serif text-lg font-medium text-cream">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-silver-bright font-serif text-sm font-bold text-ink shadow-gold">4</span>
                                Comentarios
                            </h2>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                class="input-elegant"
                                placeholder="¿Alguna preferencia o indicación?"
                            ></textarea>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA: Fecha y hora -->
                    <div class="p-6 lg:col-span-3 bg-ink">
                        <h2 class="mb-4 flex items-center gap-3 font-serif text-lg font-medium text-cream">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-silver-bright font-serif text-sm font-bold text-ink shadow-gold">3</span>
                            Selecciona día y hora
                        </h2>

                        <!-- Selección de fecha -->
                        <div class="mb-5">
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-mercury">Fecha *</label>
                            <input
                                v-model="form.date"
                                type="date"
                                :min="getMinDate()"
                                :disabled="!form.branch_id || !form.service_id"
                                required
                                class="input-elegant disabled:opacity-40 disabled:cursor-not-allowed"
                            />
                            <p v-if="!form.branch_id || !form.service_id" class="mt-2 text-xs text-amber-400">
                                ⚠ Selecciona primero la sucursal y el servicio
                            </p>
                        </div>

                        <!-- Selección de hora -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-xs font-medium uppercase tracking-wider text-mercury">Horarios disponibles *</label>
                                <span v-if="loadingSlots" class="text-xs text-mercury">Cargando...</span>
                                <span v-else-if="canShowSlots && slots.length > 0" class="chip bg-emerald-500/15 text-emerald-400">
                                    {{ slots.length }} horarios libres
                                </span>
                            </div>

                            <div v-if="!canShowSlots" class="rounded-xl border border-dashed border-smoke bg-graphite p-12 text-center">
                                <div class="text-4xl">📅</div>
                                <p class="mt-3 text-sm text-mercury">
                                    Selecciona la fecha para ver horarios disponibles
                                </p>
                            </div>

                            <div v-else-if="loadingSlots" class="rounded-xl border border-smoke bg-graphite p-12 text-center">
                                <div class="inline-block h-7 w-7 animate-spin rounded-full border-2 border-silver border-t-transparent"></div>
                                <p class="mt-3 text-sm text-mercury">Buscando horarios disponibles…</p>
                            </div>

                            <div v-else-if="slots.length === 0" class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-8 text-center">
                                <div class="text-4xl">😔</div>
                                <p class="mt-3 text-sm font-medium text-amber-400">
                                    No hay horarios disponibles
                                </p>
                                <p class="mt-1 text-xs text-amber-400/70">
                                    Intenta con otra fecha o sucursal
                                </p>
                            </div>

                            <div v-else class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                <button
                                    v-for="slot in slots"
                                    :key="slot.start"
                                    type="button"
                                    @click="form.start_time = slot.start"
                                    :class="[
                                        'rounded-lg border-2 px-3 py-3 text-sm font-semibold transition',
                                        form.start_time === slot.start
                                            ? 'border-silver bg-silver-bright text-ink shadow-gold'
                                            : 'border-smoke bg-graphite text-pearl hover:border-silver/50 hover:text-cream',
                                    ]"
                                >
                                    {{ slot.start.slice(0,5) }}
                                </button>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div v-if="selectedService && selectedBranch" class="mt-6 rounded-xl border border-silver/20 bg-graphite p-5 shadow-gold">
                            <p class="text-eyebrow">Resumen</p>
                            <h3 class="mt-1 font-serif text-lg font-medium text-cream">Tu cita</h3>
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-mercury">Servicio:</span>
                                    <span class="font-medium text-cream">{{ selectedService.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-mercury">Duración:</span>
                                    <span class="font-medium text-cream">{{ selectedService.duration_minutes }} min</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-mercury">Sucursal:</span>
                                    <span class="font-medium text-cream">{{ selectedBranch.name }}</span>
                                </div>
                                <div v-if="form.date" class="flex justify-between">
                                    <span class="text-mercury">Fecha:</span>
                                    <span class="font-medium text-cream">
                                        {{ new Date(form.date).toLocaleDateString('es-MX', { day: 'numeric', month: 'long' }) }}
                                    </span>
                                </div>
                                <div v-if="form.start_time" class="flex justify-between">
                                    <span class="text-mercury">Hora:</span>
                                    <span class="font-medium text-cream">{{ form.start_time.slice(0,5) }}</span>
                                </div>
                                <div class="mt-4 border-t border-smoke pt-3 flex items-center justify-between">
                                    <span class="font-serif text-base font-medium text-cream">Total</span>
                                    <span class="font-serif text-2xl font-semibold text-glitter">
                                        {{ formatPrice(selectedService.price) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Botón submit -->
                        <button
                            type="submit"
                            :disabled="!canSubmit || submitting"
                            class="btn-primary-elegant mt-6 h-14 w-full text-base shadow-gold-lg disabled:shadow-none"
                        >
                            <span v-if="submitting">⏳ Reservando…</span>
                            <span v-else-if="!form.branch_id || !form.service_id">← Selecciona sucursal y servicio</span>
                            <span v-else-if="!form.date">← Selecciona la fecha</span>
                            <span v-else-if="!form.start_time">← Selecciona un horario</span>
                            <span v-else-if="!form.name || !form.phone">← Completa tus datos</span>
                            <span v-else>✓ Confirmar reserva</span>
                        </button>

                        <p class="mt-3 text-center text-xs text-mercury">
                            🔒 Tu información está segura · Sin spam
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>