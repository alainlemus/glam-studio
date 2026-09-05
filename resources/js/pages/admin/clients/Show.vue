<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Phone, Mail, Calendar, AlertTriangle, Award, Gift, ExternalLink, ChevronLeft, Pencil } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    client: any;
}>();

const formatPrice = (p: any) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(p));
</script>

<template>
    <Head :title="client.name" />

    <div class="mx-auto max-w-7xl space-y-6 p-4 lg:p-8">
        <!-- Header con gradiente -->
        <div class="relative overflow-hidden rounded-2xl border border-smoke bg-gradient-to-br from-graphite via-ink to-graphite p-8 animate-fade-in">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-gradient-to-tl from-purple-500/5 to-transparent blur-3xl"></div>

            <div class="relative">
                <div class="flex items-center justify-between">
                    <Link href="/admin/clients" class="inline-flex items-center gap-2 text-sm font-medium text-silver-bright transition-colors hover:text-cream">
                        <ChevronLeft class="h-4 w-4" />
                        Volver a clientes
                    </Link>
                    <Link :href="`/admin/clients/${client.id}/edit`" class="btn-ghost-elegant h-10 px-4 text-sm">
                        <Pencil class="h-3.5 w-3.5" />
                        Editar
                    </Link>
                </div>
                <div class="mt-6 flex items-start gap-6">
                    <div class="group relative flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-silver/20 to-silver/10 shadow-lg shadow-silver/10 transition-all duration-300 hover:scale-105">
                        <User class="h-10 w-10 text-silver-bright" />
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-silver/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                    </div>
                    <div class="flex-1">
                        <h1 class="font-serif text-3xl font-bold text-cream lg:text-4xl">{{ client.name }}</h1>
                        <div class="mt-3 flex flex-wrap items-center gap-4">
                            <a v-if="client.phone" :href="`https://wa.me/${client.phone.replace(/\D/g, '')}`" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-sm font-medium text-emerald-400 transition-all hover:border-emerald-500/50 hover:bg-emerald-500/20">
                                <Phone class="h-3.5 w-3.5" />
                                {{ client.phone }}
                                <ExternalLink class="h-3 w-3" />
                            </a>
                            <span v-if="client.email" class="inline-flex items-center gap-2 text-sm text-pearl">
                                <Mail class="h-3.5 w-3.5" />
                                {{ client.email }}
                            </span>
                            <span v-if="client.is_blocked" class="chip bg-red-500/15 text-red-400">
                                🚫 Bloqueado
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Stats -->
            <div class="space-y-4 animate-fade-in" style="animation-delay: 100ms">
                <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/10">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-blue-500/30 bg-blue-500/10 shadow-lg shadow-blue-500/10">
                            <Calendar class="h-7 w-7 text-blue-400" />
                        </div>
                        <div class="mt-4 font-serif text-4xl font-bold text-cream">{{ client.appointments?.length || 0 }}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Citas registradas</div>
                    </div>
                </div>
                <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-red-500/10">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-red-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 shadow-lg shadow-red-500/10">
                            <AlertTriangle class="h-7 w-7 text-red-400" />
                        </div>
                        <div class="mt-4 font-serif text-4xl font-bold" :class="client.no_show_count >= 3 ? 'text-red-400' : 'text-cream'">
                            {{ client.no_show_count }}
                        </div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">No-shows</div>
                    </div>
                </div>
            </div>

            <!-- Loyalty card -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/10" style="animation-delay: 200ms">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-gradient-to-br from-purple-500/10 to-transparent blur-3xl"></div>
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <Award class="h-5 w-5 text-purple-400" />
                        <h2 class="font-serif text-lg font-medium text-cream">Tarjeta de Lealtad</h2>
                    </div>
                    <div v-if="client.loyalty_card" class="mt-6">
                        <div class="rounded-lg border border-purple-500/20 bg-purple-500/5 p-4 text-center">
                            <div class="text-xs font-medium uppercase tracking-wider text-mercury">Código</div>
                            <div class="mt-1 font-mono text-lg font-bold text-purple-400">{{ client.loyalty_card.code }}</div>
                        </div>
                        <div class="mt-5">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-pearl">Progreso</span>
                                <span class="font-bold text-purple-400">{{ client.loyalty_card.stamps_current }}/{{ client.loyalty_card.stamps_required }}</span>
                            </div>
                            <div class="mt-2 h-3 overflow-hidden rounded-full bg-graphite">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-purple-400 shadow-lg shadow-purple-500/30 transition-all duration-700" :style="{ width: client.loyalty_card.progress_percent + '%' }"></div>
                            </div>
                        </div>
                        <div class="mt-5 flex items-center justify-center gap-2 rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-3">
                            <Gift class="h-4 w-4 text-emerald-400" />
                            <span class="text-sm text-emerald-400">{{ client.loyalty_card.total_rewards_claimed }} recompensas canjeadas</span>
                        </div>
                    </div>
                    <div v-else class="mt-6 rounded-lg border border-dashed border-smoke bg-graphite p-8 text-center">
                        <Award class="mx-auto h-8 w-8 text-mercury/50" />
                        <p class="mt-2 text-sm text-mercury">Sin tarjeta de lealtad</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-silver/5" style="animation-delay: 300ms">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-2xl"></div>
                <div class="relative">
                    <h2 class="font-serif text-lg font-medium text-cream">Información</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3">
                            <dt class="text-mercury">Cumpleaños:</dt>
                            <dd class="font-medium text-cream">{{ client.birthday ? new Date(client.birthday).toLocaleDateString('es-MX') : '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-3">
                            <dt class="text-mercury">Estatus:</dt>
                            <dd>
                                <span v-if="client.is_blocked" class="chip bg-red-500/15 text-red-400">Bloqueado</span>
                                <span v-else class="chip bg-emerald-500/15 text-emerald-400">Activo</span>
                            </dd>
                        </div>
                        <div v-if="client.notes" class="rounded-lg border border-smoke bg-graphite p-3">
                            <dt class="mb-2 text-xs font-medium uppercase tracking-wider text-mercury">Notas:</dt>
                            <dd class="text-sm leading-relaxed text-pearl">{{ client.notes }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Citas recientes -->
        <div class="card-elegant group relative overflow-hidden animate-fade-in" style="animation-delay: 400ms">
            <div class="absolute -right-16 top-0 h-64 w-64 rounded-full bg-gradient-to-bl from-blue-500/5 to-transparent blur-3xl"></div>
            <div class="relative border-b border-smoke/70 p-6">
                <div class="flex items-center gap-3">
                    <Calendar class="h-5 w-5 text-blue-400" />
                    <h2 class="font-serif text-xl font-medium text-cream">Citas recientes</h2>
                </div>
            </div>
            <div v-if="!client.appointments?.length" class="relative p-12 text-center">
                <Calendar class="mx-auto h-12 w-12 text-mercury/30" />
                <p class="mt-3 text-sm text-mercury">Sin citas registradas</p>
            </div>
            <div v-else class="relative divide-y divide-smoke/50">
                <Link
                    v-for="appt in client.appointments"
                    :key="appt.id"
                    :href="`/admin/appointments/${appt.id}`"
                    class="flex items-center gap-4 p-5 transition-colors hover:bg-graphite"
                >
                    <div class="flex h-14 w-14 flex-col items-center justify-center rounded-lg border border-smoke bg-graphite">
                        <span class="font-serif text-lg font-semibold leading-none text-cream">
                            {{ new Date(appt.date).toLocaleDateString('es-MX', { day: '2-digit' }) }}
                        </span>
                        <span class="mt-0.5 text-[9px] uppercase tracking-widest text-silver/80">
                            {{ new Date(appt.date).toLocaleDateString('es-MX', { month: 'short' }) }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-cream">{{ appt.services?.[0]?.service?.name }}</p>
                        <p class="mt-0.5 truncate text-xs text-mercury">{{ appt.branch?.name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-serif text-lg font-semibold text-emerald-400">{{ formatPrice(appt.total) }}</p>
                        <p class="mt-0.5 text-xs text-silver-bright">Ver detalles →</p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>