<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useMotion } from '@vueuse/motion';
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
        // Laravel automáticamente lee la cookie XSRF-TOKEN que el navegador
        // envía en cada request. No necesitamos enviarla manualmente.
        const response = await fetch('/api/slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
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

// Categorizar slots por período del día
const categorizeSlots = (slotsArray: any[]) => {
    const morning: any[] = [];
    const afternoon: any[] = [];
    const evening: any[] = [];

    slotsArray.forEach(slot => {
        const hour = parseInt(slot.start.split(':')[0]);
        if (hour >= 6 && hour < 12) {
            morning.push(slot);
        } else if (hour >= 12 && hour < 18) {
            afternoon.push(slot);
        } else {
            evening.push(slot);
        }
    });

    return { morning, afternoon, evening };
};

const categorizedSlots = computed(() => categorizeSlots(slots.value));

// Detectar slots populares (simulado - en producción vendría del backend)
const isPopularSlot = (slotTime: string) => {
    const popularHours = ['10:00', '11:00', '15:00', '16:00', '17:00'];
    return popularHours.includes(slotTime.slice(0, 5));
};

// Mock de fotos para estilistas (en producción vendrían del backend)
const stylistPhotos: Record<number, string> = {
    1: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=200&q=80',
    2: 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=200&q=80',
    3: 'https://images.unsplash.com/photo-1595475038663-8d9f14b0eb7e?w=200&q=80',
};
</script>

<template>
    <Head title="Reservar Cita" />

    <section class="relative overflow-hidden border-b border-smoke bg-gradient-dark py-20 lg:py-32">
        <!-- Background Image con Overlay -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1920&q=80"
                alt="Salon background"
                class="h-full w-full object-cover opacity-20"
            />
            <!-- Gradient overlays para mezclar con el diseño -->
            <div class="absolute inset-0 bg-gradient-to-b from-ink via-ink/95 to-ink"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-ink via-transparent to-ink"></div>
        </div>

        <!-- Mesh Gradients de fondo - 21st.dev style -->
        <div class="pointer-events-none absolute inset-0 opacity-40">
            <div class="absolute left-1/2 top-1/3 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-silver/10 blur-[120px] animate-pulse"></div>
            <div class="absolute left-1/4 top-1/4 h-[400px] w-[400px] rounded-full bg-gold/8 blur-[100px] animate-sparkle"></div>
            <div class="absolute right-1/4 bottom-1/3 h-[450px] w-[450px] rounded-full bg-silver/6 blur-[110px] animation-delay-300 animate-sparkle"></div>
            <!-- Noise texture overlay -->
            <div class="absolute inset-0 opacity-[0.015] mix-blend-overlay" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 /%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <!-- Eyebrow con badge - Glassmorphism effect -->
            <div class="inline-flex items-center gap-2 animate-blur-fade-in">
                <span class="text-eyebrow">Agenda en línea</span>
                <span class="chip glass-effect text-silver-bright">
                    ✨ 100% Seguro
                </span>
            </div>

            <!-- Hero Title - Mucho más grande y bold -->
            <h1 class="mt-6 font-serif text-hero font-bold tracking-tight animate-blur-fade-in animation-delay-100">
                Tu mejor versión
                <br />
                <span class="relative inline-block">
                    <span class="text-glitter italic">comienza aquí</span>
                    <svg class="absolute -bottom-2 left-0 w-full animate-fade-in animation-delay-300" height="12" viewBox="0 0 400 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10C100 4 300 4 398 10" stroke="url(#gradient)" stroke-width="3" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:0.3" />
                                <stop offset="50%" style="stop-color:#E5C158;stop-opacity:0.8" />
                                <stop offset="100%" style="stop-color:#D4AF37;stop-opacity:0.3" />
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
            </h1>

            <!-- Subtítulo más emocional -->
            <p class="mt-6 text-lg leading-relaxed text-pearl max-w-2xl mx-auto animate-blur-fade-in animation-delay-200">
                Reserva tu transformación en segundos. Selecciona sucursal, servicio y horario.
                <span class="text-silver-bright font-medium">Confirmación inmediata por WhatsApp.</span>
            </p>

            <!-- Social Proof - Glass cards -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm animate-blur-fade-in animation-delay-300">
                <div class="flex items-center gap-2 text-mercury glass-effect px-4 py-2 rounded-full">
                    <div class="flex -space-x-2">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-silver to-silver-dark border-2 border-ink"></div>
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-gold to-gold-dark border-2 border-ink"></div>
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-silver-bright to-silver border-2 border-ink"></div>
                    </div>
                    <span class="text-cream font-medium">+234 citas este mes</span>
                </div>
                <div class="flex items-center gap-1.5 glass-effect px-4 py-2 rounded-full">
                    <svg class="h-5 w-5 text-gold-bright fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-cream font-medium">4.9/5</span>
                    <span class="text-mercury">(1,203 reseñas)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Galería de Trabajos Recientes -->
    <section class="relative border-b border-smoke bg-charcoal py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <p class="text-eyebrow">Nuestro trabajo habla por sí mismo</p>
                <h2 class="mt-2 font-serif text-subsection font-bold text-cream">
                    Transformaciones <span class="text-glitter italic">recientes</span>
                </h2>
            </div>

            <!-- Grid de trabajos con efecto parallax -->
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:gap-4">
                <!-- Trabajo 1 -->
                <div class="group relative overflow-hidden rounded-xl aspect-[3/4] animate-fade-in">
                    <img
                        src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=600&q=80"
                        alt="Corte y color"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-bright">Corte + Color</p>
                            <p class="mt-1 text-sm text-cream">Balayage platinado</p>
                        </div>
                    </div>
                </div>

                <!-- Trabajo 2 -->
                <div class="group relative overflow-hidden rounded-xl aspect-[3/4] animate-fade-in animation-delay-100">
                    <img
                        src="https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=600&q=80"
                        alt="Peinado elegante"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-bright">Peinado</p>
                            <p class="mt-1 text-sm text-cream">Recogido de novia</p>
                        </div>
                    </div>
                </div>

                <!-- Trabajo 3 -->
                <div class="group relative overflow-hidden rounded-xl aspect-[3/4] animate-fade-in animation-delay-200">
                    <img
                        src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=600&q=80"
                        alt="Tratamiento capilar"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-bright">Tratamiento</p>
                            <p class="mt-1 text-sm text-cream">Keratina brasileña</p>
                        </div>
                    </div>
                </div>

                <!-- Trabajo 4 -->
                <div class="group relative overflow-hidden rounded-xl aspect-[3/4] animate-fade-in animation-delay-300">
                    <img
                        src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=600&q=80"
                        alt="Corte moderno"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-bright">Corte</p>
                            <p class="mt-1 text-sm text-cream">Bob asimétrico</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA para ver más -->
            <div class="mt-8 text-center">
                <a href="/galeria" class="inline-flex items-center gap-2 text-sm text-silver-bright transition-colors hover:text-cream">
                    <span>Ver más transformaciones</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="overflow-hidden rounded-2xl border border-smoke bg-card shadow-gold animate-scale-in animation-delay-300 transition-all duration-500 hover:shadow-[0_0_40px_rgba(209,213,219,0.15)]">
                <div class="grid lg:grid-cols-5">
                    <!-- COLUMNA IZQUIERDA: Datos, sucursal, servicio -->
                    <div class="space-y-6 border-b border-smoke p-6 lg:col-span-2 lg:border-b-0 lg:border-r bg-graphite animate-slide-in-left animation-delay-400">
                        <!-- Progress Stepper Visual -->
                        <div class="mb-6 hidden lg:flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-silver-bright text-xs font-bold text-ink shadow-gold animate-scale-in">1</div>
                                <div class="h-px w-12 bg-gradient-to-r from-silver to-smoke"></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-smoke text-xs font-bold text-silver border border-smoke">2</div>
                                <div class="h-px w-12 bg-smoke"></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-smoke text-xs font-bold text-silver border border-smoke">3</div>
                                <div class="h-px w-12 bg-smoke"></div>
                            </div>
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-smoke text-xs font-bold text-silver border border-smoke">4</div>
                        </div>

                        <div>
                            <h2 class="mb-3 flex items-center gap-3 font-serif text-lg font-medium text-cream">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-silver-bright font-serif text-sm font-bold text-ink shadow-gold relative">
                                    1
                                    <span class="absolute inset-0 rounded-full bg-silver-bright animate-ping opacity-20"></span>
                                </span>
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

                                    <!-- Preview visual del servicio seleccionado - Glass card -->
                                    <div v-if="selectedService" class="mt-3 overflow-hidden rounded-lg glass-card p-3 animate-fade-in">
                                        <div class="flex gap-3">
                                            <div class="relative h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg">
                                                <img
                                                    src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&q=80"
                                                    alt="Servicio"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div class="absolute inset-0 bg-gradient-to-t from-ink/60 to-transparent"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-cream">{{ selectedService.name }}</p>
                                                <div class="mt-1 flex items-center gap-3 text-xs text-mercury">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ selectedService.duration_minutes }}min
                                                    </span>
                                                    <span class="font-semibold" style="color: var(--color-spa-lavender)">{{ formatPrice(selectedService.price) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="availableStylists.length > 1">
                                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wider text-mercury">Estilista (opcional)</label>

                                    <!-- Grid de estilistas con fotos - Soft UI -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- Opción: Cualquiera -->
                                        <button
                                            type="button"
                                            @click="form.stylist_id = ''"
                                            :class="[
                                                'group relative overflow-hidden rounded-xl border-2 p-3 text-left transition-all duration-300',
                                                form.stylist_id === ''
                                                    ? 'border-silver shadow-gold scale-105'
                                                    : 'soft-ui border-transparent hover:border-silver/20'
                                            ]"
                                        >
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-silver to-silver-dark">
                                                    <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-cream">Cualquiera</p>
                                                    <p class="text-xs text-mercury">Disponible</p>
                                                </div>
                                            </div>
                                            <div v-if="form.stylist_id === ''" class="absolute top-2 right-2">
                                                <svg class="h-5 w-5 text-silver-bright" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- Estilistas con fotos - Soft UI -->
                                        <button
                                            v-for="stylist in availableStylists"
                                            :key="stylist.id"
                                            type="button"
                                            @click="form.stylist_id = stylist.id"
                                            :class="[
                                                'group relative overflow-hidden rounded-xl border-2 p-3 text-left transition-all duration-300',
                                                form.stylist_id === stylist.id
                                                    ? 'border-silver shadow-gold scale-105'
                                                    : 'soft-ui border-transparent hover:border-silver/20'
                                            ]"
                                        >
                                            <div class="flex items-center gap-3">
                                                <img
                                                    :src="stylistPhotos[stylist.id] || 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=200&q=80'"
                                                    :alt="stylist.name"
                                                    class="h-12 w-12 rounded-full object-cover ring-2 ring-smoke"
                                                />
                                                <div>
                                                    <p class="text-sm font-semibold text-cream">{{ stylist.name }}</p>
                                                    <p class="text-xs text-mercury">Estilista</p>
                                                </div>
                                            </div>
                                            <div v-if="form.stylist_id === stylist.id" class="absolute top-2 right-2">
                                                <svg class="h-5 w-5 text-silver-bright" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </div>
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
                    <div class="p-6 lg:col-span-3 bg-ink animate-slide-in-right animation-delay-400">
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

                            <div v-if="!canShowSlots" class="rounded-xl border border-dashed border-smoke bg-graphite p-12 text-center transition-all duration-300 hover:border-silver/30">
                                <div class="text-4xl animate-fade-in">📅</div>
                                <p class="mt-3 text-sm text-mercury animate-fade-in animation-delay-100">
                                    Selecciona la fecha para ver horarios disponibles
                                </p>
                            </div>

                            <div v-else-if="loadingSlots" class="space-y-6">
                                <!-- Skeleton Screens - 21st.dev style -->
                                <div class="animate-fade-in">
                                    <div class="mb-3 flex items-center gap-2">
                                        <div class="h-5 w-5 skeleton"></div>
                                        <div class="h-4 w-20 skeleton"></div>
                                        <div class="h-5 w-8 skeleton rounded-full"></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                        <div v-for="i in 6" :key="`skeleton-morning-${i}`" class="h-12 skeleton"></div>
                                    </div>
                                </div>
                                <div class="animate-fade-in animation-delay-100">
                                    <div class="mb-3 flex items-center gap-2">
                                        <div class="h-5 w-5 skeleton"></div>
                                        <div class="h-4 w-16 skeleton"></div>
                                        <div class="h-5 w-8 skeleton rounded-full"></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                        <div v-for="i in 6" :key="`skeleton-afternoon-${i}`" class="h-12 skeleton"></div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <p class="text-sm text-mercury animate-pulse">✨ Buscando los mejores horarios para ti...</p>
                                </div>
                            </div>

                            <div v-else-if="slots.length === 0" class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-8 text-center transition-all duration-300 hover:border-amber-500/30">
                                <div class="text-4xl animate-scale-in">😔</div>
                                <p class="mt-3 text-sm font-medium text-amber-400 animate-fade-in animation-delay-100">
                                    No hay horarios disponibles
                                </p>
                                <p class="mt-1 text-xs text-amber-400/70 animate-fade-in animation-delay-200">
                                    Intenta con otra fecha o sucursal
                                </p>
                            </div>

                            <!-- Selector visual categorizado - 21st.dev style -->
                            <div v-else class="space-y-6">
                                <!-- Mañana -->
                                <div v-if="categorizedSlots.morning.length > 0" class="animate-fade-in">
                                    <div class="mb-3 flex items-center gap-2">
                                        <svg class="h-5 w-5" style="color: var(--color-spa-lavender)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-silver">Mañana</h3>
                                        <span class="chip glass-effect text-silver text-xs">{{ categorizedSlots.morning.length }}</span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                        <button
                                            v-for="slot in categorizedSlots.morning"
                                            :key="slot.start"
                                            type="button"
                                            @click="form.start_time = slot.start"
                                            :class="[
                                                'group relative rounded-lg border-2 px-3 py-3 text-sm font-semibold transition-all duration-300 ease-out',
                                                form.start_time === slot.start
                                                    ? 'border-silver bg-silver-bright text-ink shadow-gold scale-105'
                                                    : 'soft-ui border-transparent text-pearl hover:border-silver/30 hover:text-cream hover:scale-105',
                                            ]"
                                        >
                                            {{ slot.start.slice(0,5) }}
                                            <span v-if="isPopularSlot(slot.start)" class="absolute -top-1 -right-1 flex h-4 w-4">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 bg-gold-bright text-[8px] items-center justify-center text-ink font-bold">★</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Tarde -->
                                <div v-if="categorizedSlots.afternoon.length > 0" class="animate-fade-in animation-delay-100">
                                    <div class="mb-3 flex items-center gap-2">
                                        <svg class="h-5 w-5" style="color: var(--color-spa-pink)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-silver">Tarde</h3>
                                        <span class="chip glass-effect text-silver text-xs">{{ categorizedSlots.afternoon.length }}</span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                        <button
                                            v-for="slot in categorizedSlots.afternoon"
                                            :key="slot.start"
                                            type="button"
                                            @click="form.start_time = slot.start"
                                            :class="[
                                                'group relative rounded-lg border-2 px-3 py-3 text-sm font-semibold transition-all duration-300 ease-out',
                                                form.start_time === slot.start
                                                    ? 'border-silver bg-silver-bright text-ink shadow-gold scale-105'
                                                    : 'soft-ui border-transparent text-pearl hover:border-silver/30 hover:text-cream hover:scale-105',
                                            ]"
                                        >
                                            {{ slot.start.slice(0,5) }}
                                            <span v-if="isPopularSlot(slot.start)" class="absolute -top-1 -right-1 flex h-4 w-4">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 bg-gold-bright text-[8px] items-center justify-center text-ink font-bold">★</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Noche -->
                                <div v-if="categorizedSlots.evening.length > 0" class="animate-fade-in animation-delay-200">
                                    <div class="mb-3 flex items-center gap-2">
                                        <svg class="h-5 w-5" style="color: var(--color-spa-soft-pink)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                        <h3 class="text-sm font-semibold uppercase tracking-wider text-silver">Noche</h3>
                                        <span class="chip glass-effect text-silver text-xs">{{ categorizedSlots.evening.length }}</span>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5 md:grid-cols-6">
                                        <button
                                            v-for="slot in categorizedSlots.evening"
                                            :key="slot.start"
                                            type="button"
                                            @click="form.start_time = slot.start"
                                            :class="[
                                                'group relative rounded-lg border-2 px-3 py-3 text-sm font-semibold transition-all duration-300 ease-out',
                                                form.start_time === slot.start
                                                    ? 'border-silver bg-silver-bright text-ink shadow-gold scale-105'
                                                    : 'soft-ui border-transparent text-pearl hover:border-silver/30 hover:text-cream hover:scale-105',
                                            ]"
                                        >
                                            {{ slot.start.slice(0,5) }}
                                            <span v-if="isPopularSlot(slot.start)" class="absolute -top-1 -right-1 flex h-4 w-4">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 bg-gold-bright text-[8px] items-center justify-center text-ink font-bold">★</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div v-if="selectedService && selectedBranch" class="mt-6 space-y-4">
                            <!-- Card principal del resumen - Glass card -->
                            <div class="rounded-xl glass-card p-5 shadow-gold transition-all duration-500 hover:shadow-[0_0_30px_rgba(209,213,219,0.15)]">
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
                                    <span class="font-serif text-2xl font-semibold" style="color: var(--color-spa-pink)">
                                        {{ formatPrice(selectedService.price) }}
                                    </span>
                                </div>
                            </div>
                            </div>

                            <!-- Testimonial con foto - Glass card -->
                            <div class="overflow-hidden rounded-xl glass-card p-4 animate-fade-in">
                                <div class="flex items-start gap-3">
                                    <img
                                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80"
                                        alt="Cliente satisfecha"
                                        class="h-12 w-12 rounded-full object-cover ring-2"
                                        style="--tw-ring-color: var(--color-spa-soft-pink)"
                                    />
                                    <div class="flex-1">
                                        <div class="mb-2 flex items-center gap-1">
                                            <svg v-for="i in 5" :key="i" class="h-3 w-3 text-gold-bright fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs leading-relaxed text-mercury italic">
                                            "El mejor salón que he visitado. El resultado superó mis expectativas. ¡100% recomendado!"
                                        </p>
                                        <p class="mt-2 text-xs font-medium text-silver">— María G.</p>
                                    </div>
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