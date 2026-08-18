<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Building2, ChevronLeft, MapPin, Phone, Mail, User, Clock, Scissors, Package, Edit } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps<{
    branch: any;
}>();
</script>

<template>
    <Head :title="branch.name" />

    <div class="mx-auto max-w-7xl space-y-6 p-4 lg:p-8">
        <!-- Header con gradiente -->
        <div class="relative overflow-hidden rounded-2xl border border-smoke bg-gradient-to-br from-graphite via-ink to-graphite p-8 animate-fade-in">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-gradient-to-br from-blue-500/10 to-transparent blur-3xl"></div>
            <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-gradient-to-tl from-silver/5 to-transparent blur-3xl"></div>

            <div class="relative">
                <Link href="/admin/branches" class="inline-flex items-center gap-2 text-sm font-medium text-silver-bright transition-colors hover:text-cream">
                    <ChevronLeft class="h-4 w-4" />
                    Volver a sucursales
                </Link>
                <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
                    <div class="flex items-start gap-6">
                        <div class="group relative flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-500/10 shadow-lg shadow-blue-500/10 transition-all duration-300 hover:scale-105">
                            <Building2 class="h-10 w-10 text-blue-400" />
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </div>
                        <div class="flex-1">
                            <h1 class="font-serif text-3xl font-bold text-cream lg:text-4xl">{{ branch.name }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-2 rounded-lg border border-silver/30 bg-silver/10 px-3 py-1.5 text-sm font-medium text-silver-bright">
                                    <MapPin class="h-3.5 w-3.5" />
                                    {{ branch.city?.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <Link
                        :href="`/admin/branches/${branch.id}/edit`"
                        class="group relative overflow-hidden rounded-lg border border-silver/30 bg-gradient-to-r from-silver/10 to-silver/5 px-4 py-2.5 text-sm font-medium text-silver-bright shadow-lg transition-all duration-300 hover:scale-105 hover:border-silver/50 hover:shadow-xl hover:shadow-silver/10"
                    >
                        <div class="absolute inset-0 bg-gradient-to-r from-silver/20 to-silver/10 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        <span class="relative inline-flex items-center gap-2">
                            <Edit class="h-4 w-4" />
                            Editar
                        </span>
                    </Link>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Información -->
            <div class="card-elegant group relative overflow-hidden p-6 animate-fade-in transition-all duration-300 hover:shadow-xl hover:shadow-silver/5" style="animation-delay: 100ms">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-gradient-to-br from-silver/5 to-transparent blur-3xl"></div>
                <div class="relative">
                    <h2 class="font-serif text-xl font-medium text-cream">Información</h2>
                    <dl class="mt-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <MapPin class="h-4 w-4" />
                                Dirección:
                            </dt>
                            <dd class="font-medium text-cream">{{ branch.address }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Phone class="h-4 w-4" />
                                Teléfono:
                            </dt>
                            <dd class="font-medium text-cream">{{ branch.phone }}</dd>
                        </div>
                        <div v-if="branch.whatsapp" class="flex items-center justify-between rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-4">
                            <dt class="flex items-center gap-2 text-emerald-400">
                                <Phone class="h-4 w-4" />
                                WhatsApp:
                            </dt>
                            <dd class="font-medium text-emerald-400">{{ branch.whatsapp }}</dd>
                        </div>
                        <div v-if="branch.email" class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Mail class="h-4 w-4" />
                                Email:
                            </dt>
                            <dd class="font-medium text-cream">{{ branch.email }}</dd>
                        </div>
                        <div v-if="branch.manager_name" class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <User class="h-4 w-4" />
                                Gerente:
                            </dt>
                            <dd class="font-medium text-cream">{{ branch.manager_name }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-smoke bg-graphite p-4 transition-colors hover:border-silver/30">
                            <dt class="flex items-center gap-2 text-mercury">
                                <Clock class="h-4 w-4" />
                                Horario:
                            </dt>
                            <dd class="font-medium text-cream">{{ branch.opening_time?.slice(0,5) }} - {{ branch.closing_time?.slice(0,5) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="space-y-4">
                <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-purple-500/10 animate-fade-in" style="animation-delay: 200ms">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-purple-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-purple-500/30 bg-purple-500/10 shadow-lg shadow-purple-500/10">
                            <Scissors class="h-7 w-7 text-purple-400" />
                        </div>
                        <div class="mt-4 font-serif text-4xl font-bold text-cream">{{ branch.stylists?.length || 0 }}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Estilistas</div>
                    </div>
                </div>
                <div class="card-elegant group relative overflow-hidden p-6 text-center transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/10 animate-fade-in" style="animation-delay: 300ms">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl border border-blue-500/30 bg-blue-500/10 shadow-lg shadow-blue-500/10">
                            <Package class="h-7 w-7 text-blue-400" />
                        </div>
                        <div class="mt-4 font-serif text-4xl font-bold text-cream">{{ branch.product_stocks?.length || 0 }}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-mercury">Productos en stock</div>
                    </div>
                </div>
            </div>

            <!-- Estilistas -->
            <div v-if="branch.stylists?.length" class="card-elegant group relative overflow-hidden p-6 animate-fade-in lg:col-span-2" style="animation-delay: 400ms">
                <div class="absolute -right-16 top-0 h-64 w-64 rounded-full bg-gradient-to-bl from-purple-500/5 to-transparent blur-3xl"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center gap-3">
                        <Scissors class="h-5 w-5 text-purple-400" />
                        <h2 class="font-serif text-xl font-medium text-cream">Equipo de estilistas</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="stylist in branch.stylists"
                            :key="stylist.id"
                            :href="`/admin/stylists/${stylist.id}`"
                            class="group/card rounded-lg border border-smoke bg-graphite p-4 transition-all duration-300 hover:scale-[1.02] hover:border-purple-500/30 hover:shadow-lg hover:shadow-purple-500/5"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-500/10">
                                    <Scissors class="h-5 w-5 text-purple-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-medium text-cream transition-colors group-hover/card:text-purple-400">{{ stylist.user?.name }}</div>
                                    <div class="truncate text-xs text-mercury">{{ stylist.specialty }}</div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>