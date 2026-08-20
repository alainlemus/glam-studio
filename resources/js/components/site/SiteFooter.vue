<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoPublic from '@/components/AppLogoPublic.vue';
import { Mail, Phone, MapPin, Sparkles } from '@lucide/vue';

const page = usePage();
const site = computed(() => page.props.site as any);
const branches = computed(() => (site.value?.branches as any[]) || []);
</script>

<template>
    <footer class="relative overflow-hidden border-t border-smoke bg-ink py-16 text-pearl">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 md:grid-cols-4">
                <div>
                    <AppLogoPublic size="md" />
                    <p class="mt-6 max-w-xs text-sm leading-relaxed text-mercury">
                        Belleza, estilo y bienestar en cada visita. Tu salón de confianza.
                    </p>
                    <div class="mt-6 flex gap-3">
                        <a href="#" aria-label="Instagram" class="flex h-10 w-10 items-center justify-center rounded-full border border-smoke bg-graphite text-pearl transition hover:border-silver hover:text-silver-bright">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Facebook" class="flex h-10 w-10 items-center justify-center rounded-full border border-smoke bg-graphite text-pearl transition hover:border-silver hover:text-silver-bright">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="TikTok" class="flex h-10 w-10 items-center justify-center rounded-full border border-smoke bg-graphite text-pearl transition hover:border-silver hover:text-silver-bright">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5.8 20.1a6.34 6.34 0 0 0 10.86-4.43V8.5a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.83 4.83 0 0 1-1.84-.93Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-serif text-sm font-semibold uppercase tracking-[0.2em] text-silver/80">Navegación</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="/" class="text-pearl transition hover:text-silver-bright">Inicio</a></li>
                        <li><a href="/servicios" class="text-pearl transition hover:text-silver-bright">Servicios</a></li>
                        <li><a href="/sucursales" class="text-pearl transition hover:text-silver-bright">Sucursales</a></li>
                        <li><a href="/agendar" class="text-pearl transition hover:text-silver-bright">Reservar cita</a></li>
                        <li><a href="/promociones" class="text-pearl transition hover:text-silver-bright">Promociones</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-serif text-sm font-semibold uppercase tracking-[0.2em] text-silver/80">Empresa</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="/nosotros" class="text-pearl transition hover:text-silver-bright">Nosotros</a></li>
                        <li><a href="/contacto" class="text-pearl transition hover:text-silver-bright">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-serif text-sm font-semibold uppercase tracking-[0.2em] text-silver/80">Sucursales</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li v-for="branch in branches.slice(0, 4)" :key="branch.id">
                            <a :href="`/sucursales/${branch.slug}`" class="text-pearl transition hover:text-silver-bright">
                                {{ branch.name }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-smoke pt-8 text-xs text-mercury sm:flex-row">
                <p>© {{ new Date().getFullYear() }} Glam Studio · Todos los derechos reservados</p>
                <div class="flex items-center gap-1.5 text-silver/60">
                    <Sparkles class="h-3 w-3" />
                    <span>Hecho con elegancia</span>
                </div>
            </div>
        </div>
    </footer>
</template>